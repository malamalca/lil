<?php
/**
 * LilDateWidget Form widget for decimal input
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
class LilDecimalWidget implements WidgetInterface
{

    /**
     * StringTemplate instance.
     *
     * @var \Cake\View\StringTemplate
     */
    protected $templates;

    /**
     * View instance.
     *
     * @var \Cake\View\View
     */
    protected $view;

    /**
     * Constructor.
     *
     * @param \Cake\View\StringTemplate $templates Templates list.
     * @param \Cake\View\View           $view      View instance.
     */
    public function __construct($templates, $view)
    {
        $this->templates = $templates;
        $this->view = $view;
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
     * @param ContextInterface $context The current form context.
     *
     * @return string
     */
    public function render(array $data, ContextInterface $context): string
    {
        $data += [
            'val' => '',
            'name' => '',
            'precision' => 2,
            'empty' => true,
            'class' => 'right',
        ];

        if (is_numeric($data['val'])) {
            $data['value'] = $this->view->Number->precision($data['val'], $data['precision']);
        } else {
            $data['value'] = $data['val'];
        }

        $this->view->Lil->jsReady(
            sprintf(
                '$("#%1$s").LilFloat({places:%2$s});',
                $data['id'],
                $data['precision']
            )
        );

        return $this->templates->format(
            'input',
            [
                'type' => 'text',
                'name' => $data['name'],
                'attrs' => $this->templates->formatAttributes(
                    $data,
                    [
                    'name', 'val', 'precision', 'empty'
                    ]
                )
            ]
        );
    }

    /**
     * {@inheritDoc}
     *
     * @param array $data Fields data.
     *
     * @return array
     */
    public function secureFields(array $data): array
    {
        return [$data['name']];
    }
}
