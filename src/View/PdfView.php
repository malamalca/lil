<?php
/**
 * PdfView Pdf view class
 *
 * PHP version 5.3
 *
 * @category Class
 * @package  Lil
 * @author   Arhim d.o.o. <info@arhim.si>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://www.arhint.si
 */
namespace Lil\View;

use App\View\AppView;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\Http\ServerRequest;
use Cake\Http\Response;
use Cake\Utility\Hash;
use Cake\View\View;

use Lil\Lib\LilPdfFactory;

/**
 * PdfView Pdf view class
 *
 * @category Class
 * @package  Lil
 * @author   Arhim d.o.o. <info@arhim.si>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://www.arhint.si
 */
class PdfView extends AppView
{
    /**
     * The name of the layout file
     *
     * @var string
     */
    public $layout = 'Lil.pdf';
    /**
     * Pdf layouts are located in the pdf sub directory of `Layouts/`
     *
     * @var string
     */
    public $layoutPath = 'pdf';
    /**
     * Pdf views are located in the 'pdf' sub directory for controllers' views.
     *
     * @var string
     */
    public $subDir = 'pdf';
    /**
     * pdf Class
     *
     * @var object
     */
    protected $pdf = null;
    /**
     * viewOptions Class
     *
     * @var array
     */
    protected $viewOptions = [];
    /**
     * Constructor
     *
     * @param \Cake\Network\Request    $request      Request instance.
     * @param \Cake\Network\Response   $response     Response instance.
     * @param \Cake\Event\EventManager $eventManager EventManager instance.
     * @param array                    $viewOptions  An array of view options
     */
    public function __construct(
        ServerRequest $request = null,
        Response $response = null,
        EventManager $eventManager = null,
        array $viewOptions = []
    ) {
        parent::__construct($request, $response, $eventManager, $viewOptions);

        $pdfEngine = Configure::read('Lil.pdfEngine');
        $pdfEngineSettings = Configure::read('Lil.' . $pdfEngine);
        $pdfOptions = Configure::read('Lil.pdfOptions');

        $event = new Event('Lil.Pdf.init', $this, ['engine' => $pdfEngine, 'settings' => $pdfEngineSettings, 'options' => $pdfOptions]);
        EventManager::instance()->dispatch($event);

        $pdfEngine = $event->getData('engine');
        $pdfEngineSettings = $event->getData('settings');
        $pdfOptions = $event->getData('options');

        $this->viewOptions = array_merge($pdfOptions, (array)$viewOptions);

        $this->pdf = LilPdfFactory::create($pdfEngine, $pdfEngineSettings, $this->viewOptions);
    }

    /**
     * Magic accessor for pdf.
     *
     * @param string $method Name of the method to execute.
     * @param array  $args   Arguments for called method.
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (is_callable([$this->pdf, $method])) {
            return call_user_func_array([$this->pdf, $method], $args);
        }
    }

    /**
     * Render a PDF view.
     *
     * @param string|null $view   The view being rendered.
     * @param string|null $layout The layout being rendered.
     *
     * @return string|null The rendered view.
     */
    public function render(?string $template = null, $layout = null): string
    {
        $data = parent::render($template, $layout);

        if (!empty($data)) {
            // output body
            $rendered = explode('<!-- NEW PAGE -->', $data);

            foreach ($rendered as $page) {
                $pageHtml = $this->viewOptions['pagePre'] . $page . $this->viewOptions['pagePost'];
                $this->pdf->newPage($pageHtml);
            }
        }
        $tmpFilename = TMP . uniqid('xml2pdf') . '.pdf';
        if (!$this->pdf->saveAs($tmpFilename)) {
            $this->lastError = $this->pdf->getError();

            return false;
        }
        if (!file_exists($tmpFilename)) {
            die('File does not exist ' . $tmpFilename);
            $this->lastError = 'PDF file doesn\'t exist.';

            return false;
        }
        $result = file_get_contents($tmpFilename);

        unlink($tmpFilename);

        return $result;
    }
}
