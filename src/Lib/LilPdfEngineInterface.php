<?php
/**
 * Pdf Lib
 * 
 * PHP version 5.3
 *
 * @category Lib
 * @package  Lil
 * @author   Arhim d.o.o. <info@arhim.si>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://www.arhint.si
 */
namespace Lil\Lib;


/**
 * LilPdf Lib
 *
 * This class manages PDF exporting.
 *
 * @category Lib
 * @package  Lil
 * @author   Arhim d.o.o. <info@arhim.si>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://www.arhint.si
 */
interface LilPdfEngineInterface
{

    /**
     * Constructor
     * 
     * @param array $options Array of options.
     *
     * @return void
     */
    public function __construct($options);
    
    /**
     * Sets or returns object's options
     *
     * @param array $values Options values.
     * 
     * @return mixed
     */
    public function options($values = null);
    /*{
        if ($values === null) {
            return $this->_options;
        }
        $this->_options = array_merge($this->_options, $values);
        return $this;
    }*/
    
    /**
     * Add a new HTML page to PDF
     *
     * @param string $html Options values.
     * @param string $options Options array.
     * 
     * @return mixed
     */
    public function newPage($html, $options = []);
    
    /**
     * Saves PDF to a file
     *
     * @param string $html Options values.
     * @param string $options Options array.
     * 
     * @return mixed
     */
    public function saveAs($fileName);
    
}