<?php
/**
 * LilDateWidget Form widget for date input
 * 
 * PHP version 5.3
 *
 * @category Class
 * @package  Lil
 * @author   Arhim d.o.o. <info@arhim.si>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://www.arhint.si
 */
namespace Lil\View\Widget;

use Cake\I18n\Time;
use Cake\View\Form\ContextInterface;
use Cake\View\Widget\WidgetInterface;

/**
 * LilDateWidget Form widget for date input
 *
 * @category Class
 * @package  Lil
 * @author   Arhim d.o.o. <info@arhim.si>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://www.arhint.si
 */
class LilDateWidget implements WidgetInterface
{
    /**
     * StringTemplate instance.
     *
     * @var \Cake\View\StringTemplate
     */
    protected $templates;
    /**
     * Constructor.
     *
     * @param \Cake\View\StringTemplate $templates Templates list.
     */
    public function __construct($templates)
    {
        $this->templates = $templates;
    }
    
    /**
     * Render a LilDate field.
     *
     * This method accepts a number of keys:
     *
     * - `text` The text of the button. Unlike all other form controls, buttons
     *   do not escape their contents by default.
     * - `escape` Set to true to enable escaping on all attributes.
     * - `type` The button type defaults to 'submit'.
     *
     * Any other keys provided in $data will be converted into HTML attributes.
     *
     * @param array            $data    The data to build a button with.
     * @param ContextInterface $context The form context.
     * 
     * @return string
     */
    public function render(array $data, ContextInterface $context)
    {
        $data += [
            'val' => '',
            'name' => '',
        ];
        
        if (is_a($data['val'], 'Cake\I18n\Time')) {
            $data['value'] = $data['val']->toDateString();
        } else if ($theDate = Time::parseDate($data['val'], 'yyyy-MM-dd')) {
            $data['value'] = $theDate->toDateString();
        }
        
        return $this->templates->format(
            'input', [
            'type' => 'date',
            'name' => $data['name'],
            'attrs' => $this->templates->formatAttributes($data, ['name', 'val'])
            ]
        );
    }

    /**
     * {@inheritDoc}
     * 
     * @param array $data Fields data.
     * 
     * @return string
     */
    public function secureFields(array $data)
    {
        return [$data['name']];
    }
}
