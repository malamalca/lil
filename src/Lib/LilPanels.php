<?php
declare(strict_types=1);

namespace Lil\Lib;

use Cake\Datasource\EntityInterface;

/**
 * LilPanels Helper class for passing panels by reference.
 *
 * @category Class
 * @package  Lil
 * @author   Arhim d.o.o. <info@arhim.si>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://www.arhint.si
 */
class LilPanels
{
    public ?array $menu = null;
    public ?string $title = null;
    public ?array $actions = null;
    public ?string $pre = null;
    public ?string $post = null;
    public ?EntityInterface $entity = null;
    public array $panels = [];
}
