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

use \NumberFormatter;
use Cake\Event\EventManager;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\View\View;

use Lil\Lib\LilPdf;
use \TCPDF;

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
     * FPdf Class
     *
     * @var object
     */
    protected $pdf = null;    
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
        
        $this->initPdf($this->passedArgs);
        
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
                $this->pdf->AddPage();
                $this->pdf->writeHTML($page);
            }
        }
        
        $res = $this->pdf->Output(
            $this->options['file_name'], 
            $this->options['dest']
        );
        if (in_array($this->options['dest'], ['S', 'E'])) {
            return $res; 
        }
        
        return $data;
    }
    
    /**
     * Init PDF class
     *
     * @param array $options The pdf options.
     * 
     * @return void
     */
    protected function initPdf($options)
    {
        $_defaults = array(
        'orientation' => 'P',                     // 'P' or 'L'
        'unit'        => 'mm',                   // default 'mm'
        'format'      => 'A4',                  // default 'A4'
        'unicode'     => true,
        'encoding'    => 'UTF-8',
        'diskcache'   => false,
        'creator'     => 'LilIntranet',
        'author'      => 'ARHIM d.o.o.',
        'title'       => 'PDF document',
        'subject'     => '',
        'keywords'    => '',
        'font'        => 'dejavusans',
        'font_size'   => 10,
        'language'    => array(
        'a_meta_charset'  => 'UTF-8',
        'a_meta_dir'      => 'ltr',
        'a_meta_language' => 'sl'
        ),
        'margin'     => array(
        'left'  => 15,
        'top'   => 27,
        'right' => 15
        ),
        'header'      => array(
        'margin' => 5, // minimum distance between header and top page margin
        'font_size' => 8,
        'lines'  => array(
        //0 => array('image' => APP . 'uploads' . DS . 'report_header.png')
        )
        ),
        'footer'      => array(
        'margin' => 10, // minimum distance between footer and bottom page margin
        'font_size' => 8,
        'lines'  => array(
        //0 => array('image' => APP . 'uploads' . DS . 'report_footer.png')
        )
        ),
        'file_name' => 'untitled.pdf',
        'dest' => 'I'
        );
        
        if (isset($options['download']) && $options['download'] == true) {
            $options['dest'] = 'D';
            unset($options['download']);
        }        
        
        $this->options = array_replace_recursive($_defaults, (array)$options);
        
        
        $this->pdf = new LilPdf($this->options);
        
        // set document information
        $this->pdf->SetCreator($this->options['creator']);
        $this->pdf->SetAuthor($this->options['author']);
        $this->pdf->SetTitle($this->options['title']);
        $this->pdf->SetSubject($this->options['subject']);
        $this->pdf->SetKeywords($this->options['keywords']);
        
        // lang
        $this->pdf->setLanguageArray($this->options['language']);
        
        //set auto page breaks
        $this->pdf->SetAutoPageBreak(true, $this->options['footer']['margin']);
        
        // set font
        $this->pdf->SetFont($this->options['font'], '', $this->options['font_size']);
        $this->pdf->SetCellPadding(2);
        
        // margins
        $this->pdf->SetMargins(
            $this->options['margin']['left'],
            $this->options['margin']['top'],
            $this->options['margin']['right'],
            true // keep margins
        );
        
        //if (empty($this->options['header'])) {
        //$this->pdf->SetPrintHeader(false);
        //} else {
        $this->pdf->SetHeaderMargin($this->options['header']['margin']);
        //}
        
        //if (empty($this->options['footer'])) {
        //	$this->pdf->SetPrintFooter(false);
        //} else {
        $this->pdf->SetFooterMargin($this->options['footer']['margin']);
        //}
    }
}
