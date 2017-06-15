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

use Cake\Core\Configure;
use Cake\Event\EventManager;
use Cake\Network\Request;
use Cake\Network\Response;
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
class PdfView extends View
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
        Request $request = null,
        Response $response = null,
        EventManager $eventManager = null,
        array $viewOptions = []
    ) {
        parent::__construct($request, $response, $eventManager, $viewOptions);
        
        $this->viewOptions = $viewOptions;
        
        $pdfEngine = Configure::read('Lil.pdfEngine');
        $pdfOptions = Configure::read('Lil.' . $pdfEngine);
        $this->pdf = LilPdfFactory::create($pdfEngine, (array)$pdfOptions);
        
        if ($response && $response instanceof Response) {
            $response->type('pdf');
        }
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
    public function render($view = null, $layout = null)
    {
        $data = parent::render($view, $layout);
        
        if (!empty($data)) {
            // output body
            $rendered = explode('<!-- NEW PAGE -->', $data);
            
            foreach ($rendered as $page) {
                $this->pdf->newPage($page);
            }
        }
        
        $tmpFilename = TMP . uniqid('xml2pdf') . '.pdf';
        if (!$this->pdf->saveAs($tmpFilename)) {
            $this->lastError = $this->pdf->getError();
            return false;
        }
        $result = file_get_contents($tmpFilename);
        unlink($tmpFilename);
        
        //if (isset($this->viewOptions['dest']) && in_array($this->viewOptions['dest'], ['S', 'E'])) {
        //    return $result; 
        //}
        //return $data;
        return $result;
    }

}