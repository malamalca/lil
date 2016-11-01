<?php
/**
 * WKHtml2Pdf LilPdf Engine
 *
 * @category Lib
 * @package  Lil
 * @author   Arhim d.o.o. <info@arhim.si>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://www.arhint.si
 */
namespace Lil\Lib;

use Cake\Core\Configure;
use Lil\Lib\LilPdfEngineInterface;
use mikehaertl\wkhtmlto\Pdf;

/**
 * LilWKHTML2PDFEngine Lib
 *
 * This class manages PDF exporting.
 *
 * @category Lib
 * @package  Lil
 * @author   Arhim d.o.o. <info@arhim.si>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://www.arhint.si
 */
class LilWKHTML2PDFEngine extends Pdf implements LilPdfEngineInterface
{

    /**
     * PDF options
     *
     * @var array
     */
    private $_localOptions = [];

    private $_defaultOptions = [
            'binary' => 'C:\bin\wkhtmltopdf\bin\wkhtmltopdf.exe',
            'no-outline', // Make Chrome not complain
            'print-media-type',
            'margin-top' => 0,
            'margin-right' => 0,
            'margin-bottom' => 0,
            'margin-left' => 0,

            // Default page options
            'disable-smart-shrinking',
            //'user-style-sheet' => dirname(dirname(__FILE__)) . DS . 'webroot' . DS . 'css' . DS . 'lil_pdf.css',
    ];

    /**
     * __construct
     *
     * @param array $options Array of options.
     *
     * @return void
     */
    public function __construct($options)
    {
        $this->options(array_merge($this->_defaultOptions, $options));
        $options = $this->options();
        parent::__construct($options);
    }

    /**
     * Save PDF as file.
     *
     * @param string $fileName Filename.
     *
     * @return bool
     */
    public function saveAs($fileName)
    {
        parent::saveAs($fileName);

        return true;
    }

    /**
     * Add page with html contents
     *
     * @param string $html Html page content.
     * @param array $options Page options.
     *
     * @return void
     */
    public function newPage($html, $options = [])
    {
        $this->addPage($html);
    }

    /**
     * Get last error.
     *
     * @return null|string
     */
    public function getError()
    {
        return null;
    }

    /**
     * Set page header html.
     *
     * @param string $html Html page content.
     * @return void
     */
    public function setHeaderHtml($html)
    {
        $this->setOptions(['header-html' => '<!doctype html><head>' .
                            '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/></head>' .
                            '<html><body><div id="header">' .
                            $html .
                            '</div></body></html>']);
    }

    /**
     * Set page footer html.
     *
     * @param string $html Html page content.
     * @return void
     */
    public function setFooterHtml($html)
    {
        $this->setOptions(['header-html' => '<!doctype html><head>' .
                            '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/></head>' .
                            '<html><body><div id="footer">' .
                            $html .
                            '</div></body></html>']);
    }

    /**
     * Get/set options.
     *
     * @param array $values Options values.
     *
     * @return mixed
     */
    public function options($values = null)
    {
        if ($values === null) {
            return $this->_localOptions;
        }
        $this->_localOptions = $values;

        return $this;
    }
}
