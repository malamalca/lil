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

use Cake\Core\Configure;
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
     * View instance.
     *
     * @var \Cake\View\View
     */
    protected $view;

    /**
     * Constructor.
     *
     * @param \Cake\View\StringTemplate $templates Templates list.
     * @param \Cake\View\View $view Reference to view.
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
     * @param ContextInterface $context The form context.
     * @return string
     */
    public function render(array $data, ContextInterface $context): string
    {
        $data += [
            'val' => '',
            'name' => '',
        ];

        if (is_a($data['val'], 'Cake\I18n\Time') || is_a($data['val'], 'Cake\I18n\Date')
            || is_a($data['val'], 'Cake\I18n\FrozenTime') || is_a($data['val'], 'Cake\I18n\FrozenDate')
        ) {
            $data['value'] = $data['val']->toDateString();
        } elseif (!empty($data['val']) && ($theDate = Time::parseDate($data['val'], 'yyyy-MM-dd'))) {
            $data['value'] = $theDate->toDateString();
        }

        // default field type is HTML5 date
        $fieldType = 'date';

        // localized date input with jquery date picker
        if (Configure::read('Lil.legacyDateFields')) {
            $fieldType = 'text';
            if ($theDate = Time::parseDate($data['value'], 'yyyy-MM-dd')) {
                $parts = str_split(Configure::read('Lil.dateFormat'));
                $partsCount = count($parts);
                for ($i = 0; $i < $partsCount; $i++) {
                    $parts[$i] = strtr(
                        $parts[$i],
                        [
                            'D' => str_pad($theDate->day, 2, '0', STR_PAD_LEFT),
                            'M' => str_pad($theDate->month, 2, '0', STR_PAD_LEFT),
                            'Y' => $theDate->year
                        ]
                    );
                }

                $data['value'] = implode(Configure::read('Lil.dateSeparator'), $parts);
            }
            $this->view->Lil->jsReady(
                sprintf(
                    '$("#%1$s").datepicker(%2$s);',
                    $data['id'],
                    $this->_jsOptions()
                )
            );
        }

        return $this->templates->format(
            'input',
            [
                'type' => $fieldType,
                'name' => $data['name'],
                'attrs' => $this->templates->formatAttributes($data, ['name', 'val'])
            ]
        );
    }

    /**
     * {@inheritDoc}
     *
     * @param array $data Fields data.
     * @return string
     */
    public function secureFields(array $data): array
    {
        return [$data['name']];
    }

    /**
     * Prepares js parameters for jQuery datepicker object
     *
     * @return string
     */
    private function _jsOptions()
    {
        $jsOptions = [];

        // UI datepicker format
        $dateFormat = implode(
            Configure::read('Lil.dateSeparator'),
            str_split(
                strtr(
                    Configure::read('Lil.dateFormat'),
                    ['D' => 'dd', 'M' => 'mm', 'Y' => 'yy']
                ),
                2
            )
        );
        $jsOptions['dateFormat'] = sprintf('"%s"', $dateFormat);
        $jsOptions['firstDay'] = 1;

        // datepicker methods
        if (isset($options['onSelect'])) {
            $jsOptions['onSelect'] = $options['onSelect'];
            unset($options['onSelect']);
        }

        $jsImploded = '';
        foreach ($jsOptions as $jsK => $jsV) {
            if (!empty($jsImploded)) {
                $jsImploded .= ', ';
            }
            $jsImploded .= $jsK . ': ' . $jsV;
        }
        if (!empty($jsImploded)) {
            $jsImploded = '{' . $jsImploded . '}';
        }

        return $jsImploded;
    }
}
