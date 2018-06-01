<?php
/**
 * LilHelper Lil View helper.
 *
 * PHP version 5.3
 *
 * @category Class
 * @package  Lil
 * @author   Arhim d.o.o. <info@arhim.si>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://www.arhint.si
 */
namespace Lil\View\Helper;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\Routing\Router;
use Cake\Utility\Hash;
use Cake\View\Helper;
use Cake\View\StringTemplateTrait;
use Cake\View\View;

use Lil\Auth\LilAuthTrait;

/**
 * LilForm Helper class for passing forms by reference.
 *
 * @category Class
 * @package  Lil
 * @author   Arhim d.o.o. <info@arhim.si>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://www.arhint.si
 */
class LilForm
{
    public $menu = null;
    public $title = null;
    public $form = null;
}

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
    public $menu = null;
    public $title = null;
    public $actions = null;
    public $entity = null;
    public $panels = [];
}
/**
 * LilHelper Lil Helper Class.
 *
 * @category Class
 * @package  Lil
 * @author   Arhim d.o.o. <info@arhim.si>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://www.arhint.si
 */
class LilHelper extends Helper
{
    use LilAuthTrait;
    use StringTemplateTrait;
    /**
     * Default config for this class
     *
     * @var array
     */
    protected $_defaultConfig = [
        'templates' => [
            'panelstart' => '<div class="view-panel {{class}}"{{attrs}}>',
            'panellinestart' => '<div class="view-panel-line{{class}}"{{attrs}}>',
            'panellinelabel' => '<span class="label {{class}}">{{content}}</span>',
            'panellineend' => '</div>',
            'panelend' => '</div>',

            'tablestart' => '<table class="lil-table {{class}}"{{attrs}}>',
            'tableend' => '</table>',

            'tableheadstart' => '<thead{{attrs}}>',
            'tableheadrow' => '<tr class="ui-toolbar ui-widget-header ui-corner-top{{class}}"{{attrs}}>',
            'tablebodyrow' => '<tr class="ui-row{{class}}"{{attrs}}>',
            'tablefootrow' => '<tr class="ui-toolbar ui-widget-header ui-corner-bottom{{class}}"{{attrs}}>',

            'td' => '<td{{attrs}}>{{content}}</td>',
            'th' => '<th{{attrs}}>{{content}}</th>',

            'navbar-menu' => '<ul class="nav navbar-nav {{class}}">{{items}}</ul>',
            'navbar-item' => '<li><a href="{{url}}" class="btn {{class}}" {{attrs}}>{{content}}</a></li>',
            'navbar-submenu' => '<li><a href="#{{subid}}" class="popup_link" id="popup_{{subid}}" {{attrs}}>{{content}}<b class="carret"></b></a>' .
                '<div class="popup popup_{{subid}} ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" style="display: none;">' .
                '<ul class="nav nav-sub">{{subitems}}</ul></div>' .
                '</li>',

            'popup' => '<div class="popup_{{name}} popup ui-widget ui-widget-content ui-helper-clearfix ui-corner-all {{class}}"><ul>{{content}}</ul></div>',
            'popup-item' => '<li><a href="{{url}}" {{attrs}}>{{content}}</a></li>',
            'popup-item-plain' => '<li>{{content}}</li>',

            'linkdelete' => '<a href="{{url}}" onclick="return confirm(\'{{confirmation}}\');"{{attrs}}>delete</a>',
            'linkedit' => '<a href="{{url}}" {{attrs}}>edit</a>',
        ]
    ];
    /**
     * Helpers property
     *
     * @var array
     */
    public $helpers = ['Html'];
    /**
     * Cache for jQuery ready script.
     *
     * @var array
     */
    private $_jsReady = [];
    /**
     * Cache for stored popups.
     *
     * @var array
     */
    private $_popups = [];
    /**
     * __call method
     *
     * @param mixed $method Method Called.
     * @param mixed $params Passed Parameters.
     * @return bool
     */
    public function __call($method, $params)
    {
        if (!empty($params[0]) && ($model = TableRegistry::get($params[0]))) {
            if (is_callable([$model, $method])) {
                unset($params[0]);

                return (call_user_func_array([$model, $method], $params));
            }
        }

        return false;
    }
    /**
     * Constructor
     *
     * ### Settings
     *
     * - `templates` Either a filename to a config containing templates.
     *   Or an array of templates to load. See Cake\View\StringTemplate for
     *   template formatting.
     *
     * ### Customizing tag sets
     *
     * Using the `templates` option you can redefine the tag HtmlHelper will use.
     *
     * @param \Cake\View\View $View The View this helper is being attached to.
     * @param array $config Configuration settings for the helper.
     */
    public function __construct(View $View, array $config = [])
    {
        parent::__construct($View, $config);
    }

    /**
     * Initialize method
     *
     * @param array $config Config Data.
     * @return void
     */
    public function initialize(array $config)
    {
        if (isset($config['Auth'])) {
            $this->setAuth($config['Auth']);
        }
    }

    /**
     * JsReady method
     *
     * @param mixed $block JQuery text block.
     * @return void
     */
    public function jsReady($block)
    {
        $this->_jsReady[] = $block;
    }
    /**
     * JsReadyOut method
     *
     * @return string
     */
    public function jsReadyOut()
    {
        return implode(PHP_EOL . CHR(9) . CHR(9) . CHR(9), $this->_jsReady);
    }
    /**
     * Referer method
     *
     * @return string
     */
    public function referer()
    {
        return base64_encode($this->request->referer());
    }
    /**
     * DateFormat method
     *
     * @return string
     */
    public function dateFormat()
    {
        $dateFormat = strtr(
            implode(
                Configure::read('Lil.dateSeparator'),
                str_split(Configure::read('Lil.dateFormat'))
            ),
            ['Y' => 'yyyy', 'M' => 'MM', 'D' => 'dd']
        );

        return $dateFormat;
    }
    /**
     * Link method
     * Creates a HTML link. Behaves exactly like Html::link with ability to use
     * nicer links in form like "[Link] additional data". Parameters are
     * similar to Html->link(), just in arrays.
     *
     * @return string An <a /> element.
     */
    public function link()
    {
        $params = func_get_args();

        if (preg_match_all('/\[(\$(\d))?([^\]]+)\]/i', $params[0], $matches)) {
            $ret = $params[0];
            foreach ($matches[0] as $k => $match) {
                $index = $k;
                if (!empty($matches[2][$k])) {
                    $index = $matches['2'][$k];
                }

                $link = $this->Html->link(
                    $matches[3][$k],
                    isset($params[1][$index][0]) ? $params[1][$index][0] : null,
                    isset($params[1][$index][1]) ? $params[1][$index][1] : [],
                    isset($params[1][$index][2]) ? $params[1][$index][2] : []
                );
                $ret = str_replace($match, $link, $ret);
            }

            return $ret;
        } else {
            return $this->Html->link(
                $params[0],
                isset($params[1]) ? $params[1] : null,
                isset($params[2]) ? $params[2] : null,
                isset($params[3]) ? $params[3] : null
            );
        }
    }
    /**
     * DeleteLink method
     *
     * Returns default delete link
     *
     * @param mixed $url   Either an array with url or model's id
     * @param mixed $params  Array with options applied to link element
     * @return mixed
     */
    public function deleteLink($url = [], $params = [])
    {
        $templater = $this->templater();

        $url_defaults = [
            'action' => 'delete'
        ];

        $defaultConfirmation = __d('lil', 'Are you sure you want to delete this item?');

        $ret = $templater->format(
            'linkdelete',
            [
                'url' => Router::url(array_merge($url_defaults, (array)$url)),
                'class' => isset($params['class']) ? $params['class'] : [],
                'confirmation' => isset($params['confirm']) ? $params['confirm'] : $defaultConfirmation,
                'attrs' => $templater->formatAttributes(
                    $params,
                    ['confirm', 'class']
                ),
            ]
        );

        return $ret;
    }
    /**
     * EditLink method
     *
     * Returns default edit link
     *
     * @param mixed $url   Either an array with url or model's id
     * @param mixed $params  Array with options applied to link element
     * @return mixed
     */
    public function editLink($url = [], $params = [])
    {
        $templater = $this->templater();

        $url_defaults = [
            'action' => 'edit'
        ];

        $ret = $templater->format(
            'linkedit',
            [
                'url' => Router::url(array_merge($url_defaults, (array)$url)),
                'class' => isset($params['class']) ? $params['class'] : [],
                'attrs' => $templater->formatAttributes(
                    $params,
                    ['class']
                )
            ]
        );

        return $ret;
    }
    /**
     * ViewLink method
     *
     * Returns default view link
     *
     * @param mixed $url_options   Either an array with url or model's id
     * @param mixed $link_options  Array with options applied to link element
     * @param mixed $image_options Array with options applied to image element
     * @return mixed
     */
    public function viewLink($url_options = [], $link_options = [], $image_options = [])
    {
        $url_defaults = [
            'action' => 'view'
        ];

        return $this->Html->link(
            $this->Html->image('/lil/img/view.gif', $image_options),
            array_merge($url_defaults, (array)$url_options),
            array_merge(['escape' => false], $link_options)
        );
    }
    /**
     * Replaces double line-breaks with paragraph elements.
     *
     * A group of regex replaces used to identify text formatted with newlines and
     * replace double line-breaks with HTML paragraph tags. The remaining
     * line-breaks after conversion become <<br />> tags, unless $br is set to '0'
     * or 'false'.
     *
     * @param string   $pee The text which has to be formatted.
     * @param int|bool $br  Optional. If set, this will convert all remaining
     * line-breaks after paragraphing. Default true.
     * @return string Text which has been converted into correct paragraph tags.
     */
    public function autop($pee, $br = 1)
    {
        if (trim($pee) === '') {
            return '';
        }
        $pee = $pee . "\n"; // just to make things a little easier, pad the end
        $pee = preg_replace('|<br />\s*<br />|', "\n\n", $pee);
        // Space things out a little
        $allblocks = '(?:table|thead|tfoot|caption|col|colgroup|tbody|tr|td|th|' .
            'div|dl|dd|dt|ul|ol|li|pre|select|form|map|area|blockquote|address|' .
            'math|style|input|p|h[1-6]|hr)';
        $pee = preg_replace('!(<' . $allblocks . '[^>]*>)!', "\n$1", $pee);
        $pee = preg_replace('!(</' . $allblocks . '>)!', "$1\n\n", $pee);
        $pee = str_replace(["\r\n", "\r"], "\n", $pee); // cross-platform NL
        if (strpos($pee, '<object') !== false) {
            $pee = preg_replace('|\s*<param([^>]*)>\s*|', "<param$1>", $pee);
            $pee = preg_replace('|\s*</embed>\s*|', '</embed>', $pee);
        }
        //$pee = preg_replace("/\n\n+/", "\n\n", $pee); // take care of duplicates
        // make paragraphs, including one at the end
        //miha: $pees = preg_split('/\n\s*\n/', $pee, -1, PREG_SPLIT_NO_EMPTY);
        $pees = preg_split('/\n/', $pee, -1);
        $pee = '';
        foreach ($pees as $tinkle) {
            $pee .= '<p>' . trim($tinkle, "\n") . "</p>\n";
        }
        // under certain conditions it could create a P of entirely whitespace
        // miha: $pee = preg_replace('|<p>\s*</p>|', '', $pee);
        $pee = preg_replace('|<p>\s*</p>|', '<br />', $pee);
        $pee = preg_replace(
            '!<p>([^<]+)</(div|address|form)>!',
            "<p>$1</p></$2>",
            $pee
        );
        $pee = preg_replace(
            '!<p>\s*(</?' . $allblocks . '[^>]*>)\s*</p>!',
            "$1",
            $pee
        ); // don't pee all over a tag
        $pee = preg_replace("|<p>(<li.+?)</p>|", "$1", $pee); // nested lists
        $pee = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $pee);
        $pee = str_replace('</blockquote></p>', '</p></blockquote>', $pee);
        $pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)!', "$1", $pee);
        $pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee);
        if ($br) {
            $pee = preg_replace_callback(
                '/<(script|style).*?<\/\\1>/s',
                function ($matches) {
                    return str_replace("\n", "<PreserveNewline />", $matches[0]);
                },
                $pee
            );
            $pee = preg_replace('|(?<!<br />)\s*\n|', "<br />\n", $pee);
            $pee = str_replace('<PreserveNewline />', "\n", $pee);
        }
        $pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*<br />!', "$1", $pee);
        $pee = preg_replace(
            '!<br />(\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)[^>]*>)!',
            '$1',
            $pee
        );
        if (strpos($pee, '<pre') !== false) {
            $pee = preg_replace_callback(
                '!(<pre[^>]*>)(.*?)</pre>!is',
                'cleanPre',
                $pee
            );
        }
        $pee = preg_replace("|\n</p>$|", '</p>', $pee);

        return $pee;
    }
    /**
     * Wraps long text
     *
     * @param string  $str   The text which has to be wrapped.
     * @param int     $width Optional. Max line length.
     * @param string  $break Optional. EOL character or string.
     * @param bool    $cut   Optional. Cut words or shorten to whole words.
     * @return string
     */
    public function mbWordWrap($str, $width = 75, $break = "\n", $cut = false)
    {
        $maxlines = 0;
        $result = 'string';
        $prefix = '';
        $startWith = 0;

        if (is_array($width)) {
            if (isset($width['maxlines'])) {
                $maxlines = $width['maxlines'];
            }
            if (isset($width['result'])) {
                $result = $width['result'];
            }
            if (isset($width['prefix'])) {
                $prefix = $width['prefix'];
            }
            if (isset($width['startwith'])) {
                $startWith = $width['startwith'];
            }
            if (isset($width['break'])) {
                $break = $width['break'];
            }
            if (isset($width['cut'])) {
                $cut = $width['cut'];
            }
            if (isset($width['width'])) {
                $width = $width['width'];
            }
        }

        $ret = [];
        $lines = explode($break, $str);
        $cnt = $startWith;

        foreach ($lines as &$line) {
            $line = rtrim($line);
            if (mb_strlen($line) <= $width) {
                $ret[$prefix . $cnt] = $line;
                $cnt++;
                continue;
            }

            $words = explode(' ', $line);
            $line = '';
            $actual = '';
            foreach ($words as $word) {
                if (mb_strlen($actual . $word) <= $width) {
                    $actual .= $word . ' ';
                } else {
                    if ($actual != '') {
                        $line .= rtrim($actual) . $break;
                    }
                    $actual = $word;
                    if ($cut) {
                        while (mb_strlen($actual) > $width) {
                            $line .= mb_substr($actual, 0, $width) . $break;
                            $actual = mb_substr($actual, $width);
                        }
                    }
                    $actual .= ' ';
                }
            }
            $line .= trim($actual);
            $wLines = explode($break, $line);
            foreach ($wLines as $wLine) {
                $ret[$prefix . $cnt] = $wLine;
                $cnt++;
            }
        }

        if ($maxlines > 0) {
            array_splice($ret, $maxlines);
        }
        if ($result == 'array') {
            return $ret;
        }

        return implode($break, $ret);
    }

    /**
     * InsertIntoArray method
     *
     * Insert a new element into array
     *
     * @param array $dest    Destination for insert operation.
     * @param array $element Element to be inserted.
     * @param array $options Insert options.
     * @return void
     */
    public function insertIntoArray(&$dest, $element, $options = [])
    {
        if (isset($options['after']) || isset($options['replace'])) {
            $title = isset($options['after'])
                ? $options['after']
                : $options['replace'];

            $panels = array_keys($dest);
            $i = 0;
            $panelCount = count($dest);
            for ($i = 0; $i < $panelCount; $i++) {
                if ($panels[$i] == $title) {
                    break;
                }
            }

            if (isset($panels[$i]) && $panels[$i] == $title) {
                if (isset($options['replace'])) {
                    unset($dest[$title]);
                    $i--;
                };

                if (isset($options['preserve']) && $options['preserve'] === false) {
                    $part1 = array_slice($dest, 0, $i + 1, true);
                    foreach ($element as $elk => $elv) {
                        if (is_numeric($elk)) {
                            $part1[] = $elv;
                        } else {
                            $part1[$elk] = $elv;
                        }
                    }
                    $dest = array_merge(
                        $part1,
                        array_slice(
                            $dest,
                            $i + 1,
                            count($dest) - $i,
                            true
                        )
                    );
                } else {
                    // do this to preserve array keys
                    $dest
                        = array_slice($dest, 0, $i + 1, true) +
                        $element +
                        array_slice($dest, $i + 1, count($dest) - $i, true);
                }
            }
        } elseif (isset($options['before'])) {
            $panels = array_keys($dest);
            $i = 0;
            for ($i = 0; $i < count($dest); $i++) {
                if ($panels[$i] == $options['before']) {
                    break;
                }
            }

            if ($panels[$i] == $options['before']) {
                if (isset($options['preserve']) && $options['preserve'] === false) {
                    $part1 = array_slice($dest, 0, $i, true);

                    foreach ($element as $elk => $elv) {
                        if (is_numeric($elk)) {
                            $part1[] = $elv;
                        } else {
                            $part1[$elk] = $elv;
                        }
                    }

                    $dest = array_merge(
                        $part1,
                        array_slice(
                            $dest,
                            $i,
                            count($dest) - $i,
                            true
                        )
                    );
                } else {
                    // do this to preserve array keys
                    $dest = array_slice($dest, 0, $i, true) +
                        $element +
                        array_slice($dest, $i, count($dest) - $i, true);
                }
            }
        } else {
            $dest = $dest + $element;
        }
    }

    /**
     * Menu method
     *
     * Display main menu from LilArray
     *
     * @param mixed $data Menu compliant with LilMenu specifications.
     * @param array $options Options for menu.
     * @return string
     */
    public function menu($data, $options = [])
    {
        $templater = $this->templater();

        $defaultOptions = [
            'prefix' => 'navbar'
        ];

        $options = Hash::merge($defaultOptions, $options);

        $itemsString = '';
        if (isset($data['items'])) {
            foreach ($data['items'] as $menu_item_name => $menu_item) {
                if ($menu_item && !empty($menu_item['visible'])) {
                    $itemTemplate = $options['prefix'] . '-item';

                    $submenuId = '';
                    $subitemsString = '';
                    if (!empty($menu_item['submenu'])) {
                        $itemTemplate = $options['prefix'] . '-submenu';
                        if (!empty($menu_item['expand'])) {
                            $itemTemplate = $options['prefix'] . '-expanded';
                        }

                        $submenuId = $menu_item_name;

                        foreach ($menu_item['submenu'] as $subitem) {
                            $params = isset($subitem['params']) ? $subitem['params'] : [];
                            $params = Hash::merge($params, ['id' => $menu_item_name]);

                            $subitemTemplate = $options['prefix'] . '-item';
                            if (!empty($subitem['active'])) {
                                $subitemTemplate = $options['prefix'] . '-active';
                            }

                            $subitemsString .= $templater->format(
                                $subitemTemplate,
                                [
                                    'class' => isset($subitem['params']['class']) ? $subitem['params']['class'] : [],
                                    'url' => isset($subitem['url']) ? Router::url($subitem['url']) : [],
                                    'content' => isset($subitem['title']) ? $subitem['title'] : [],
                                    'icon' => isset($subitem['icon']) ? $subitem['icon'] : [],
                                    'attrs' => $templater->formatAttributes($params, ['class'])
                                ]
                            ) . PHP_EOL;
                        }
                    } elseif (!empty($menu_item['active'])) {
                        $itemTemplate = $options['prefix'] . '-active';
                    }

                    $attrs = isset($menu_item['params']) ? $menu_item['params'] : [];
                    if (!empty($attrs['confirm'])) {
                        $attrs['data-confirm'] = h($attrs['confirm']);
                    }

                    $itemsString .= $templater->format(
                        $itemTemplate,
                        [
                            'class' => isset($menu_item['params']['class']) ? $menu_item['params']['class'] : [],
                            'url' => isset($menu_item['url']) ? Router::url($menu_item['url']) : [],
                            'content' => isset($menu_item['title']) ? $menu_item['title'] : [],
                            'icon' => isset($menu_item['icon']) ? $menu_item['icon'] : [],
                            'subitems' => $subitemsString,
                            'subid' => $submenuId,
                            'attrs' => $templater->formatAttributes(
                                $attrs,
                                ['class', 'confirm']
                            ),
                        ]
                    ) . PHP_EOL;
                }
            }
        }

        $ret = $templater->format(
            $options['prefix'] . '-menu',
            [
                'class' => isset($data['params']['class']) ? $data['params']['class'] : [],
                'items' => $itemsString,
                'attrs' => $templater->formatAttributes(
                    isset($data['params']) ? $data['params'] : [],
                    ['class']
                ),
            ]
        ) . PHP_EOL;

        return $ret;
    }
    /**
     * Popup method
     *
     * Display popup from LilArray
     *
     * @param string  $name   Popup name
     * @param array   $data   Popup compliant with LilPopup specifications
     * @param bool    $inline Display popup inline or store in cache
     * @return void
     */
    public function popup($name, $data, $inline = false)
    {
        $items = [];
        if (isset($data['items'])) {
            $items = $data['items'];
            unset($data['items']);
        }

        $templater = $this->templater();

        $itemsString = '';
        foreach ($items as $item) {
            if ($item) {
                if (is_string($item)) {
                    $itemsString .= $templater->format('popup-item-plain', ['content' => $item]) . PHP_EOL;
                } else {
                    $params = [];
                    if (!empty($item['params'])) {
                        $params = (array)$item['params'];
                    }
                    $itemsString .= $templater->format('popup-item', [
                        'content' => $item['title'],
                        'url' => isset($item['url']) ? Router::url($item['url']) : null,
                        'attrs' => $templater->formatAttributes($params),
                    ]) . PHP_EOL;
                }
            }
        }

        $ret = $templater->format('popup', [
            'name' => $name,
            'content' => $itemsString
        ]);

        if (!$inline) {
            $this->_View->append('popups');
            echo $ret;
            $this->_View->end();
        } else {
            return $ret;
        }
    }
    /**
     * Form method
     *
     * Display form from LilArray
     *
     * @param mixed  $data      Form compliant with LilForm specifications
     * @param string $eventName Name of the event to be fired
     * @return string
     */
    public function form($data, $eventName = null)
    {
        if (is_array($data)) {
            $form = new LilForm();

            $form->pre = isset($data['pre']) ? $data['pre'] : null;
            $form->post = isset($data['post']) ? $data['post'] : null;

            $form->form = isset($data['form']) ? $data['form'] : null;
            $form->menu = isset($data['menu']) ? $data['menu'] : null;
            $form->title = isset($data['title_for_layout'])
                ? $data['title_for_layout']
                : null;
        } else {
            $form = $data;
        }

        $event = new Event('Lil.Form.' . $eventName, $this->_View, [$form]);
        EventManager::instance()->dispatch($event);
        if (!empty($event->result)) {
            $form = $event->result;
        }

        // display title
        if (isset($form->title)) {
            $this->_View->assign('title', $form->title);
        }

        // display menu
        if (!empty($form->menu)) {
            $this->_View->set('main_menu', $form->menu);
        }

        $ret = '';

        // form display begins
        if (isset($form->form['pre'])) {
            if (is_string($form->form['pre'])) {
                $ret .= $form->form['pre'];
            } else {
                foreach ($form->form['pre'] as $line) {
                    $ret .= $line;
                }
            }
        }

        foreach ($form->form['lines'] as $name => $line) {
            if (is_string($line)) {
                $ret .= $line;
            } else {
                $parameters = [];
                if (!empty($line['parameters'])) {
                    $parameters = (array)$line['parameters'];
                }
                if (!empty($line['params'])) {
                    $parameters = (array)$line['params'];
                }

                if (isset($form->form['defaultHelper'])) {
                    $line['class'] = $form->form['defaultHelper'];
                }

                if (isset($line['class']) && isset($line['method'])) {
                    if (is_object($line['class'])) {
                        $use_object =& $line['class'];
                    } else {
                        $use_object =& $this->_View->{$line['class']};
                    }

                    $ret .= call_user_func_array(
                        [
                            $use_object,
                            $line['method']
                        ],
                        $parameters
                    );
                }
            }
        }

        if (isset($form->form['pre'])) {
            if (is_string($form->form['post'])) {
                $ret .= $form->form['post'];
            } else {
                foreach ($form->form['post'] as $line) {
                    $ret .= $line;
                }
            }
        }

        return $ret;
    }
    /**
     * Index method
     *
     * Display index from LilArray
     *
     * @param mixed $data    Table compliant with LilIndex specifications
     * @param mixed $options Options: showEmpty - display empty table
     *
     * @return void
     */
    public function index($data, $options = [])
    {
        $ret = '';

        // display title
        if (isset($data['title_for_layout'])) {
            $this->_View->assign('title', $data['title_for_layout']);
        }
        if (isset($data['head_for_layout'])) {
            $this->_View->set('head_for_layout', $data['head_for_layout']);
        }

        // display menu
        if (!empty($data['menu'])) {
            $this->_View->set('main_menu', $data['menu']);
        }

        // display actions
        if (!empty($data['actions'])) {
            $ret .= $this->_actions($data['actions']);
        }

        if (isset($data['pre'])) {
            $ret .= $data['pre'];
        }

        if (!empty($data['table'])) {
            $ret .= $this->table($data['table']);
        } elseif (!empty($data['list'])) {
            if (empty($data['list']['items'])) {
                $ret .= sprintf('<p>%s</p>', __d('lil', 'No records found.'));
            } else {
                $ret .= $this->_list($data['list']);
            }
        }

        if (isset($data['post'])) {
            $ret .= $data['post'];
        }

        return $ret;
    }
    /**
     * Panels method
     *
     * Display panels from LilArray
     *
     * @param mixed  $data      View compliant with LilIndex specifications
     * @param string $eventName Event name to be fired
     *
     * @return void
     */
    public function panels($data, $eventName = null)
    {
        if (is_array($data)) {
            $panels = new LilForm();

            $panels->pre = isset($data['pre']) ? $data['pre'] : null;
            $panels->post = isset($data['post']) ? $data['post'] : null;
            $panels->actions = isset($data['actions']) ? $data['actions'] : null;
            $panels->entity = isset($data['entity']) ? $data['entity'] : null;

            $panels->panels = isset($data['panels']) ? $data['panels'] : null;
            $panels->menu = isset($data['menu']) ? $data['menu'] : null;
            $panels->title = isset($data['title_for_layout'])
                ? $data['title_for_layout']
                : null;

            if (isset($data['head_for_layout'])) {
                $this->_View->set('head_for_layout', $data['head_for_layout']);
            }
        } else {
            $panels = $data;
        }

        if (!empty($eventName)) {
            $event = new Event(
                'Lil.Panels.' . $eventName,
                $this->_View,
                [$panels]
            );
            EventManager::instance()->dispatch($event);
            if (!empty($event->result)) {
                $panels = $event->result;
            }
        }

        // display title
        if (isset($panels->title)) {
            $this->_View->assign('title', $panels->title);
        }

        // display menu
        if (!empty($panels->menu)) {
            $this->_View->set('main_menu', $panels->menu);
        }

        $ret = '';

        // actions
        if (!empty($panels->actions)) {
            $ret .= $this->_actions($panels->actions);
        }

        // form display begins
        if (!empty($panels->pre)) {
            $ret .= $panels->pre;
        }

        $templater = $this->templater();
        foreach ($panels->panels as $panel) {
            if (is_array($panel)) {
                if (!empty($panel['pre'])) {
                    $ret .= $panel['pre'];
                }

                $params = [];
                if (isset($panel['id'])) {
                    $params['id'] = $panel['id'];
                }
                if (!empty($panel['params'])) {
                    $params = array_merge($params, (array)$panel['params']);
                }

                $class = [];
                if (isset($panel['params']['class'])) {
                    $class = array_merge($class, (array)$panel['params']['class']);
                    unset($panel['params']['class']);
                }

                $ret .= $templater->format('panelstart', [
                    'attrs' => $templater->formatAttributes($params),
                    'class' => $class
                ]);

                if (isset($panel['lines']) && is_array($panel['lines'])) {
                    foreach ($panel['lines'] as $line) {
                        if (is_array($line)) {
                            $lineClass = [];
                            if (isset($line['params']['class'])) {
                                $lineClass = array_merge($lineClass, (array)$line['params']['class']);
                                unset($line['params']['class']);
                            }

                            $lineParams = [];
                            if (isset($line['params'])) {
                                $lineParams = (array)$line['params'];
                            }

                            if (isset($line['label'])) {
                                $ret .= $templater->format('panellinestart', [
                                    'attrs' => $templater->formatAttributes($lineParams),
                                    'class' => $lineClass
                                ]);

                                $ret .= $templater->format('panellinelabel', [
                                    'content' => $line['label']
                                ]);
                            }

                            if (!empty($line['text'])) {
                                $ret .= $line['text'];
                            } elseif (!empty($line['html'])) {
                                $ret .= $line['html'];
                            } elseif (!empty($line['table'])) {
                                $ret .= $this->table($line['table']);
                            } else {
                                $ret .= '&nbsp;';
                            }

                            if (isset($line['label'])) {
                                $ret .= $templater->format('panellineend', []) . PHP_EOL;
                            }
                        } else {
                            $ret .= $line;
                        }
                    }
                } elseif (isset($panel['table']) && is_array($panel['table'])) {
                    $ret .= $this->table($panel['table']);
                } elseif (isset($panel['html'])) {
                    $ret .= $panel['html'];
                }

                $ret .= $templater->format('panelend', []) . PHP_EOL;
                if (!empty($panel['post'])) {
                    $ret .= $panel['post'];
                }
            } elseif (!is_null($panel)) {
                $ret .= $panel;
            }
        }
        if (!empty($panels->post)) {
            $ret .= $panels->post;
        }

        return $ret;
    }
    /**
     * _actions method
     *
     * Additional lines after heading
     *
     * @param mixed $actions Actions array
     *
     * @return void
     */
    private function _actions($actions)
    {
        $ret = '';
        if (!empty($actions['pre'])) {
            $ret .= $actions['pre'];
        }
        if (!empty($actions['lines'])) {
            foreach ((array)$actions['lines'] as $name => $line) {
                if (is_array($line) && !empty($line['class'])) {
                    $parameters = [];
                    if (!empty($line['parameters'])) {
                        $parameters = (array)$line['parameters'];
                    }
                    $ret .= call_user_func_array([$line['class'], $line['method']], $parameters);
                } else {
                    $ret .= $line;
                }
            }
        }
        if (!empty($actions['post'])) {
            $ret .= $actions['post'] . PHP_EOL;
        }

        return $ret;
    }
    /**
     * Table method
     *
     * Display table from LilTable
     *
     * @param mixed $data Data compliant to LilTable specification.
     *
     * @return void
     */
    public function table($data)
    {
        $ret = '';
        $templater = $this->templater();

        if (isset($data['pre'])) {
            $ret .= $data['pre'];
        }

        $ret .= $this->_formatParams('tablestart', $data, $templater);

        // display thead
        if (!isset($data['head'])) {
            $data['head'] = [];
        }
        $ret .= $this->_formatParams('tableheadstart', $data['head'], $templater, false);
        //$ret .= '<thead>' . PHP_EOL;

        if (!empty($data['head']['rows'])) {
            foreach ($data['head']['rows'] as $row) {
                $ret .= $this->_formatParams('tableheadrow', $row, $templater);
                if (!isset($row['column'])) {
                    $row['column'] = 'th';
                }
                foreach ($row['columns'] as $col) {
                    if (!is_null($col)) {
                        if (is_string($col)) {
                            $col = ['html' => $col];
                        }
                        $content = isset($col['html']) ? $col['html'] : '&nbsp;';
                        $ret .= $this->_formatParams(
                            $row['column'],
                            array_merge((array)$col, ['content' => $content]),
                            $templater,
                            false
                        );
                    }
                }

                $ret .= '</tr>' . PHP_EOL;
            }
        }
        $ret .= '</thead>' . PHP_EOL;

        // display body
        $ret .= '<tbody>' . PHP_EOL;

        if (!empty($data['body']['rows'])) {
            foreach ($data['body']['rows'] as $row) {
                if ($row) {
                    $ret .= $this->_formatParams('tablebodyrow', $row, $templater);

                    foreach ($row['columns'] as $col) {
                        if (!is_null($col)) {
                            if (is_string($col)) {
                                $col = ['html' => $col];
                            }
                            $content = isset($col['html']) ? $col['html'] : '&nbsp;';
                            $ret .= $this->_formatParams(
                                'td',
                                array_merge((array)$col, ['content' => $content]),
                                $templater,
                                false
                            );
                        }
                    }

                    $ret .= '</tr>' . PHP_EOL;
                }
            }
        }
        $ret .= '</tbody>' . PHP_EOL;

        // display tfoot
        if (!empty($data['foot'])) {
            $ret .= '<tfoot>' . PHP_EOL;

            foreach ($data['foot']['rows'] as $row) {
                if ($row) {
                    $ret .= $this->_formatParams('tablefootrow', $row, $templater);
                    if (!isset($row['column'])) {
                        $row['column'] = 'th';
                    }
                    foreach ($row['columns'] as $col) {
                        if (is_string($col)) {
                            $col = ['html' => $col];
                        }
                        $content = isset($col['html']) ? $col['html'] : '&nbsp;';
                        $ret .= $this->_formatParams(
                            $row['column'],
                            array_merge((array)$col, ['content' => $content]),
                            $templater,
                            false
                        );
                    }

                    $ret .= '</tr>' . PHP_EOL;
                }
            }
            $ret .= '</tfoot>' . PHP_EOL;
        }

        $ret .= $templater->format('tableend', []) . PHP_EOL;
        if (isset($data['post'])) {
            $ret .= $data['post'] . PHP_EOL;
        }

        return $ret;
    }
    /**
     * _list method
     *
     * Display list
     *
     * @param mixed $data Data compliant to LilList specifications.
     *
     * @return void
     */
    private function _list($data)
    {
        $ret = '';
        if (isset($data['pre'])) {
            $ret .= $data['pre'];
        }

        $tag = 'ul';
        if (!empty($data['type']) && ($data['type'] == 'ordered')) {
            $tag = 'ol';
        }

        $ret .= '<' . $tag;
        if (!empty($data['parameters'])) {
            foreach ($data['parameters'] as $key => $param) {
                $ret .= ' ' . $key . '="' . $param . '"';
            }
        }
        $ret .= '>' . PHP_EOL;

        // display body
        if (!empty($data['items'])) {
            foreach ($data['items'] as $row) {
                if ($row) {
                    $ret .= '<li';
                    if (!empty($row['parameters'])) {
                        foreach ($row['parameters'] as $key => $param) {
                            $ret .= ' ' . $key . '="' . $param . '"';
                        }
                    }
                    $ret .= '>' . PHP_EOL;

                    $ret .= is_string($row) ? $row : $row['html'];
                    if (!empty($row['list'])) {
                        $this->_list($row['list']);
                    }

                    $ret .= '</li>' . PHP_EOL;
                }
            }
        }

        $ret .= '</' . $tag . '>' . PHP_EOL;
        if (isset($data['post'])) {
            $ret .= $data['post'] . PHP_EOL;
        }

        return $ret;
    }

    /**
     * _formatParams
     *
     * Format tags in form <tag class="{{class}}"{{attrs}}/> replacing {{class}}
     * with data['parameters'] or data['params'] and {{attrs}} with all other attributes.
     *
     * @param string $templateName Template name.
     * @param array $data Array with data
     * @param object $templater Templater object.
     *
     * @return string
     */
    private function _formatParams($templateName, $data, $templater, $hasClassInTemplate = true)
    {
        $attrs = [];

        $parameters = [];
        if (isset($data['parameters'])) {
            $parameters = $data['parameters'];
        }
        if (isset($data['params'])) {
            $parameters = $data['params'];
        }

        $content = null;
        if (isset($data['content'])) {
            $attrs['content'] = $data['content'];
            unset($data['content']);
        }

        if ($hasClassInTemplate) {
            $class = false;
            if (isset($parameters['class'])) {
                $class = (array)$parameters['class'];
                unset($parameters['class']);
            }

            $attrs['attrs'] = $templater->formatAttributes($parameters);
            $attrs['class'] = $class;
        } else {
            $attrs['attrs'] = $templater->formatAttributes($parameters);
        }

        $ret = $templater->format($templateName, $attrs);

        return $ret;
    }
}

/**
 * CleanPre function
 *
 * Callback function from regex which removes new lines
 *
 * @param mixed $matches Regex matches
 *
 * @return string
 */
function cleanPre($matches)
{
    if (is_array($matches)) {
        $text = $matches[1] . $matches[2] . "</pre>";
    } else {
        $text = $matches;
    }

    $text = str_replace('<br />', '', $text);
    $text = str_replace('<p>', "\n", $text);
    $text = str_replace('</p>', '', $text);

    return $text;
}
