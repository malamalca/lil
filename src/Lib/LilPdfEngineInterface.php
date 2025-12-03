<?php
declare(strict_types=1);

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
     * @param array $enigneOptions Array of options.
     * @return void
     */
    public function __construct($enigneOptions);

    /**
     * Sets or returns object's options
     *
     * @param array $values Options values.
     * @return mixed
     */
    public function options($values = null): mixed;

    /**
     * Add a new HTML page to PDF
     *
     * @param string $html Options values.
     * @param array $options Options array.
     * @return mixed
     */
    public function newPage($html, array $options = []): mixed;

    /**
     * Saves PDF to a file
     *
     * @param string $fileName Options values.
     * @return mixed
     */
    public function saveAs($fileName): mixed;

    /**
     * Get last error.
     *
     * @return string|null
     */
    public function getError(): ?string;

    /**
     * Set page header html.
     *
     * @param string $html Html page content.
     * @return void
     */
    public function setHeaderHtml($html): void;

    /**
     * Set page footer html.
     *
     * @param string $html Html page content.
     * @return void
     */
    public function setFooterHtml($html): void;
}
