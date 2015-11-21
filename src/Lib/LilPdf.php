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

use Cake\Core\Configure;

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
class LilPdf extends \TCPDF
{

    /**
     * PDF options
     *
     * @var array
     */
    private $_options = [];
    
    /**
     * __construct
     * 
     * @param array $options Array of options.
     *
     * @return void
     */
    public function __construct($options) 
    {
        $this->options($options);
        parent::__construct(
            $options['orientation'], $options['unit'], $options['format'],
            $options['unicode'], $options['encoding'], $options['diskcache']
        );
    }
    
    /**
     * Header
     *
     * @return void
     */
    function Header() 
    {
        $this->SetFont(
            $this->_options['font'],
            '',
            $this->_options['header']['font_size']
        );
        
        // data means base64 encoded image
        if (!empty($this->_options['header']['data'])) {
            if (substr($this->_options['header']['data'], 0, 2) == '{"') {
                if ($data = json_decode($this->_options['header']['data'], true)
                ) {
                    $margins = $this->getMargins();
                    $decoded = base64_decode($data['image']);
                    $this->Image(
                        '@' . $decoded,
                        $margins['left'], 0, 
                        $this->getPageWidth() - $margins['left'] - $margins['right']
                    );
                }
            } else {
                $margins = $this->getMargins();
                $this->writeHTMLCell(
                    $this->getPageWidth() - $margins['left'] - $margins['right'],
                    0,
                    $margins['left'], $this->_options['header']['margin'],
                    $this->_options['header']['data']
                );
            }
        }
        
        if (!empty($this->_options['header']['lines'])) {
            foreach ($this->_options['header']['lines'] as $l) {
                if (isset($l['image'])
                    && is_string($l['image'])
                    && file_exists($l['image'])
                ) {
                    $margins = $this->getMargins();
                    $this->Image(
                        $l['image'],
                        $margins['left'],
                        0,
                        $this->getPageWidth() 
                        - $margins['left'] 
                        - $margins['right']
                    );
                } else if (isset($l['image']) 
                    && is_array($l['image']) 
                    && file_exists(reset($l['image']))
                ) {
                    call_user_func_array(array($this, 'Image'), $l['image']);
                } else if (isset($l['text']) && is_array($l['text'])) {
                    call_user_func_array(array($this, 'Text'), $l['text']);
                } else if (isset($l['write']) && is_array($l['write'])) {
                    call_user_func_array(array($this, 'Write'), $l['write']);
                } else if (isset($l['line']) && is_array($l['line'])) {
                    call_user_func_array(array($this, 'Line'), $l['line']);
                }
                
            }
        }
    }
    
    /**
     * Footer
     *
     * @return void
     */
    function Footer() 
    {
        $this->SetFont(
            $this->_options['font'],
            '',
            $this->_options['footer']['font_size']
        );
        
        // data means base64 encoded image
        if (!empty($this->_options['footer']['data'])) {
            if (substr($this->_options['footer']['data'], 0, 2) == '{"') {
                if ($data = json_decode($this->_options['footer']['data'], true)
                ) {
                    $margins = $this->getMargins();
                    $decoded = base64_decode($data['image']);
                    $this->Image(
                        '@' . $decoded,
                        $margins['left'],
                        0, 
                        $this->getPageWidth()
                        - $margins['left']
                        - $margins['right']
                    );
                }
            } else {
                $margins = $this->getMargins();
                $this->writeHTMLCell(
                    $this->getPageWidth() - $margins['left'] - $margins['right'], 20,
                    $margins['left'], $this->getPageHeight() - 20,
                    $this->_options['footer']['data']
                );
            }
        }
        
        if (!empty($this->_options['footer']['lines'])) {
            foreach ($this->_options['footer']['lines'] as $l) {
                if (isset($l['image']) 
                    && is_string($l['image']) 
                    && file_exists($l['image'])
                ) {
                    $margins = $this->getMargins();
                    $this->Image(
                        $l['image'],
                        $margins['left'],
                        275,
                        $this->getPageWidth()
                        - $margins['left']
                        - $margins['right']
                    );
                } else if (isset($l['image']) 
                    && is_array($l['image']) 
                    && file_exists(reset($l['image']))
                ) {
                    call_user_func_array(array($this, 'Image'), $l['image']);
                } else if (isset($l['text']) && is_array($l['text'])) {
                    call_user_func_array(array($this, 'Text'), $l['text']);
                } else if (isset($l['write']) && is_array($l['write'])) {
                    call_user_func_array(array($this, 'Write'), $l['write']);
                } else if (isset($l['line']) && is_array($l['line'])) {
                    call_user_func_array(array($this, 'Line'), $l['line']);
                }
            }
        }
    }
    
    /**
     * Options
     *
     * @param array $values Options values.
     * 
     * @return mixed
     */
    public function options($values = null) 
    {
        if ($values === null) {
            return $this->_options;
        }
        $this->_options = $values;
        return $this;
    }
    
    /**
     * SetHeaderOptions
     *
     * @param array $options Array with options
     * 
     * @return void
     */
    public function setHeaderOptions($options = null) 
    {
        $this->_options['header'] = array_merge(
            (array)$this->_options['header'],
            (array)$options
        );
    }
    
    /**
     * SetFooterOptions
     *
     * @param array $options Array with options.
     * 
     * @return void
     */
    public function setFooterOptions($options = null) 
    {
        $this->_options['footer'] = array_merge(
            (array)$this->_options['footer'],
            (array)$options
        );
    }
}
