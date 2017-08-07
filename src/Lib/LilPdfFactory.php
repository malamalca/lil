<?php

namespace Lil\Lib;

use Lil\Lib\LilTCPDFEngine;
use Lil\Lib\LilWKHTML2PDFEngine;

/**
 * Factory class for PDF library.
 *
 * @category Lib
 * @package  Lil
 * @author   Arhim d.o.o. <info@arhim.si>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://www.arhint.si
 */
 
class LilPdfFactory
{
    public static function create($engine, $enigneOptions, $viewOptions)
    {
        switch ($engine) {
            case 'TCPDF':
                return new LilTCPDFEngine($enigneOptions, $viewOptions);
                break;
            case 'WKHTML2PDF':
                return new LilWKHTML2PDFEngine($enigneOptions, $viewOptions);
                break;
        }
    }
}