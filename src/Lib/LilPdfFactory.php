<?php
declare(strict_types=1);

namespace Lil\Lib;

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
    /**
     * Create PDF engine instance.
     *
     * @param string $engine PDF engine name.
     * @param array<string, mixed> $enigneOptions PDF engine options.
     * @param array<string, mixed> $viewOptions PDF view options.
     * @return \Lil\Lib\LilPdfEngineInterface PDF engine instance.
     */
    public static function create($engine, $enigneOptions, $viewOptions = []): LilPdfEngineInterface
    {
        switch ($engine) {
            case 'TCPDF':
                return new LilTCPDFEngine($enigneOptions, $viewOptions);
            case 'WKHTML2PDF':
                return new LilWKHTML2PDFEngine($enigneOptions, $viewOptions);
        }
    }
}
