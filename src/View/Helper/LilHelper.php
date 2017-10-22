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
use Cake\View\Helper;
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
    /**
     * Helpers property
     *
     * @var array
     */
    public $helpers = array('Html');
    /**
     * Cache for jQuery ready script.
     *
     * @var array
     */
    private $_jsReady = array();
    /**
     * Cache for stored popups.
     *
     * @var array
     */
    private $_popups = array();
    /**
     * __call method
     *
     * @param mixed $method Method Called.
     * @param mixed $params Passed Parameters.
     * 
     * @return boolean
     */
    function __call($method, $params) 
    {
        if (!empty($params[0]) && ($model = TableRegistry::get($params[0]))) {
            if (is_callable(array($model, $method))) {
                unset($params[0]);
                return (call_user_func_array(array($model, $method), $params));
            }
        }
        return false;
    }
    
     /**
     * Initialize method
     *
     * @param array $config Config Data.
     * 
     * @return void
     */        
    public function initialize(array $config)
    {
        if (isset($config['Auth'])) $this->setAuth($config['Auth']);
    }
    
    /**
     * JsReady method
     *
     * @param mixed $block JQuery text block.
     * 
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
        return implode(PHP_EOL .CHR(9).CHR(9).CHR(9), $this->_jsReady);
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
            ), ['Y' => 'yyyy', 'M' => 'MM', 'D' => 'dd']
        );
        return $dateFormat;
    }
    /**
     *  Link method
     * 
     * Creates a HTML link. Behaves exactly like Html::link with ability to use 
     * nicer links in form like "[Link] additional data". Parameters are
     * similar to Html->link(), just in arrays.
     * 
     * @return string An <a /> element.
     */
    function link() 
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
     * @param mixed $url_options   Either an array with url or model's id
     * @param mixed $link_options  Array with options applied to link element
     * @param mixed $image_options Array with options applied to image element
     * 
     * @return mixed
     */
    function deleteLink($url_options = [], $link_options = [], $image_options = []) 
    {
        $url_defaults = array(
        'action' => 'delete'
        );
        
        return $this->Html->link(
            $this->Html->image('/lil/img/delete.gif', $image_options),
            array_merge($url_defaults, (array)$url_options),
            array_merge(
                array(
                    'escape' => false,
                    'confirm' => __d(
                        'lil',
                        'Are you sure you want to delete this record?'
                    )
                ),
                $link_options
            )
        );
    }
    /**
     * EditLink method
     *
     * Returns default edit link
     *
     * @param mixed $url_options   Either an array with url or model's id
     * @param mixed $link_options  Array with options applied to link element
     * @param mixed $image_options Array with options applied to image element
     * 
     * @return mixed
     */
    function editLink($url_options = [], $link_options = [], $image_options = []) 
    {
        $url_defaults = array(
            'action' => 'edit'
        );
        
        return $this->Html->link(
            $this->Html->image('/lil/img/edit.gif', $image_options),
            array_merge($url_defaults, (array)$url_options),
            array_merge(
                array(
                    'escape' => false,
                ),
                $link_options
            )
        );
    }
    /**
     * ViewLink method
     *
     * Returns default view link
     *
     * @param mixed $url_options   Either an array with url or model's id
     * @param mixed $link_options  Array with options applied to link element
     * @param mixed $image_options Array with options applied to image element
     * 
     * @return mixed
     */
    function viewLink($url_options = [], $link_options = [], $image_options = []) 
    {
        $url_defaults = array(
            'action' => 'view'
        );
        
        return $this->Html->link(
            $this->Html->image('/lil/img/view.gif', $image_options),
            array_merge($url_defaults, (array)$url_options),
            array_merge(
                array(
                    'escape' => false,
                ),
                $link_options
            )
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
     * 
     * @return string Text which has been converted into correct paragraph tags.
     */
    public function autop($pee, $br = 1) 
    {
        if (trim($pee) === '' ) {
            return ''; 
        }
        $pee = $pee . "\n"; // just to make things a little easier, pad the end
        $pee = preg_replace('|<br />\s*<br />|', "\n\n", $pee);
        // Space things out a little
        $allblocks = '(?:table|thead|tfoot|caption|col|colgroup|tbody|tr|td|th|'.
            'div|dl|dd|dt|ul|ol|li|pre|select|form|map|area|blockquote|address|'.
            'math|style|input|p|h[1-6]|hr)';
        $pee = preg_replace('!(<' . $allblocks . '[^>]*>)!', "\n$1", $pee);
        $pee = preg_replace('!(</' . $allblocks . '>)!', "$1\n\n", $pee);
        $pee = str_replace(array("\r\n", "\r"), "\n", $pee); // cross-platform NL
        if (strpos($pee, '<object') !== false ) {
            $pee = preg_replace('|\s*<param([^>]*)>\s*|', "<param$1>", $pee);
            $pee = preg_replace('|\s*</embed>\s*|', '</embed>', $pee);
        }
        //$pee = preg_replace("/\n\n+/", "\n\n", $pee); // take care of duplicates
        // make paragraphs, including one at the end
        //miha: $pees = preg_split('/\n\s*\n/', $pee, -1, PREG_SPLIT_NO_EMPTY);
        $pees = preg_split('/\n/', $pee, -1);
        $pee = '';
        foreach ( $pees as $tinkle ) {
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
                create_function(
                    '$matches',
                    'return str_replace("\n", "<PreserveNewline />", $matches[0]);'
                ),
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
     * @param boolean $cut   Optional. Cut words or shorten to whole words.
     * 
     * @return string
     */
    function mbWordWrap($str, $width = 75, $break = "\n", $cut = false) 
    {
        $maxlines = 0; $result = 'string'; $prefix = ''; $startWith = 0;
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
                $ret[$prefix.$cnt] = $line;
                $cnt++;
                continue;
            }
            
            $words = explode(' ', $line);
            $line = '';
            $actual = '';
            foreach ($words as $word) {
                if (mb_strlen($actual.$word) <= $width) {
                    $actual .= $word.' '; 
                } else {
                    if ($actual != '') {
                        $line .= rtrim($actual) . $break;
                    }
                    $actual = $word;
                    if ($cut) {
                        while (mb_strlen($actual) > $width) {
                            $line .= mb_substr($actual, 0, $width).$break;
                            $actual = mb_substr($actual, $width);
                        }
                    }
                    $actual .= ' ';
                }
            }
            $line .= trim($actual);
            $wLines = explode($break, $line);
            foreach ($wLines as $wLine) {
                $ret[$prefix.$cnt] = $wLine;
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
     * 
     * @return void
     */
    function insertIntoArray(&$dest, $element, $options = []) 
    {
        if (isset($options['after']) || isset($options['replace'])) {
            $title = isset($options['after']) 
                ? $options['after'] 
                : $options['replace'];
            
            $panels = array_keys($dest);
            $i = 0;
            for ($i = 0; $i < sizeof($dest); $i++) {
                if ($panels[$i] == $title) {
                    break; 
                }
            }
            
            if (isset($panels[$i]) && $panels[$i] == $title) {
                if (isset($options['replace'])) {
                    unset($dest[$title]); $i--; 
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
                        $part1, array_slice(
                            $dest,
                            $i + 1,
                            sizeof($dest) - $i,
                            true
                        )
                    );
                } else {
                    // do this to preserve array keys
                    $dest
                        = array_slice($dest, 0, $i + 1, true) + 
                        $element + 
                        array_slice($dest, $i + 1, sizeof($dest) - $i, true);
                }
            }
        } else if (isset($options['before'])) {
            $panels = array_keys($dest);
            $i = 0;
            for ($i = 0; $i < sizeof($dest); $i++) {
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
                        $part1, array_slice(
                            $dest,
                            $i,
                            sizeof($dest) - $i,
                            true
                        )
                    );
                } else {
                    // do this to preserve array keys
                    $dest 
                        = array_slice($dest, 0, $i, true) + 
                        $element + 
                        array_slice($dest, $i, sizeof($dest) - $i, true);
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
     * @param mixed $data Menu compliant with LilMenu specifications
     * 
     * @return void
     */
    public function menu($data) 
    {
        print('<ul>');
        foreach ($data as $menu_item_name => $menu_item) {
            if ($menu_item && !empty($menu_item['visible'])) {
                $params = array();
                if (!empty($menu_item['submenu'])) {
                    $params['class']  = 'popup_link';
                    $params['id']     = 'popup_' . $menu_item_name;
                }
                if (!empty($menu_item['style'])) {
                    $params['style'] = $menu_item['style']; 
                }
                if (!empty($menu_item['id']) && empty($params['id'])) {
                    $params['id'] = $menu_item['id']; 
                }
                
                $params_str = "";
                foreach ($params as $p_k => $p_v) {
                    $params_str .= ' ' . $p_k . '="' . $p_v . '"';
                }
                
                printf(
                    '<li%2$s>%1$s</li>',
                    $this->Html->link(
                        $menu_item['title'] . (empty($menu_item['submenu']) 
                            ? '' 
                            : ' Ă„â€šĂ˘â‚¬ĹľÄ‚ËĂ˘â€šÂ¬ÄąË‡Ă„â€šĂ˘â‚¬Ä…Ä‚â€šĂ‚ÂÄ‚â€žĂ˘â‚¬ĹˇÄ‚â€ąĂ‚ÂĂ„â€šĂ‹ÂÄ‚ËĂ˘â€šÂ¬ÄąË‡Ä‚â€šĂ‚Â¬Ă„â€šĂ‹ÂÄ‚ËĂ˘â‚¬ĹˇĂ‚Â¬Ă„Ä…Ă˘â‚¬ĹźÄ‚â€žĂ˘â‚¬ĹˇÄ‚ËĂ˘â€šÂ¬ÄąÄľĂ„â€šĂ˘â‚¬Ä…Ă„Ä…Ă„â€ž'
                        ),
                        empty($menu_item['url']) ? '#' : $menu_item['url'],
                        isset($menu_item['params']) 
                        ? $menu_item['params'] 
                        : array()
                    ),
                    $params_str
                );
                
                if (!empty($menu_item['submenu'])) {
                    $popup_data = array();
                    
                    foreach ($menu_item['submenu'] as $submenu_item) {
                        if ($submenu_item) {
                            $popup_data['items'][] = array(
                            'title' => $submenu_item['title'],
                            'url' => empty($submenu_item['url']) 
                                ? '#' 
                                : $submenu_item['url'],
                            'params' => isset($submenu_item['params']) 
                                ? $submenu_item['params'] 
                                : null
                            );
                        }
                    }
                    
                    $this->popup($menu_item_name, $popup_data);
                }
            }
        }
        print('</ul>');
    }
    /**
     * Popup method
     *
     * Display popup from LilArray
     *
     * @param string  $name   Popup name
     * @param array   $data   Popup compliant with LilPopup specifications
     * @param boolean $inline Display popup inline or store in cache
     * 
     * @return void
     */
    public function popup($name, $data, $inline = false) 
    {
        $items = [];
        if (isset($data['items'])) {
            $items = $data['items']; unset($data['items']);
        }
        
        
        $class = 'popup_%1$s popup ui-widget ui-widget-content ' .
            'ui-helper-clearfix ui-corner-all';
        
        $params = '';
        foreach ($data as $key => $param) {
            $params = ' ' . $key . '="' . $param . '"';
        }
        
        $ret = sprintf('<div class="' . $class . '"' . $params . '><ul>', $name);
        
        foreach ($items as $item) {
            if ($item) {
                if (is_string($item)) {
                    $ret .= sprintf('<li>%1$s</li>', $item);
                } else {
                    // li params
                    $params = '';
                    if (!empty($item['active'])) {
                        $params .= ' class="ui-state-active"'; 
                    }
                    
                    if (!empty($item['params'])) {
                        /*if (is_array($item['params'])) {
                        foreach ($item['params'] as $key => $param) {
                        $params = ' ' . $key . '="' . $param . '"';
                        }
                        } else $params = $item['params'];*/
                    }
                    $ret .= sprintf(
                        '<li%2$s>%1$s</li>',
                        $this->Html->link(
                            $item['title'],
                            empty($item['url']) ? '#' : $item['url'],
                            isset($item['params']) ? $item['params'] : array()
                        ),
                        $params
                    );
                }
            }
        }
        $ret .= '</ul></div>';
        
        if (!$inline) {
            $this->_View->append('popups'); 
        }
        echo $ret;
        if (!$inline) {
            $this->_View->end(); 
        }
    }
    /**
     * Form method
     *
     * Display form from LilArray
     *
     * @param mixed  $data      Form compliant with LilForm specifications
     * @param string $eventName Name of the event to be fired
     * 
     * @return void
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
        if (is_string($form->form['pre'])) {
            $ret .= $form->form['pre']; 
        } else {
            foreach ($form->form['pre'] as $line) {
                $ret .= $line;
            }
            
        }
        foreach ($form->form['lines'] as $name => $line) {
            if (is_string($line)) {
                $ret .= $line;
            } else {
                $parameters = array();
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
                        array(
                        $use_object,
                        $line['method']
                        ), $parameters
                    );
                }
            }
        }
        if (is_string($form->form['post'])) {
            $ret .= $form->form['post']; 
        } else {
            foreach ($form->form['post'] as $line) {
                $ret .= $line;
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
    public function index($data, $options = array()) 
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
        
        if (!empty($data['table'])) {
            $ret .= $this->table($data['table']);
        } else if (!empty($data['list'])) {
            if (empty($data['list']['items'])) {
                $ret .= sprintf('<p>%s</p>', __d('lil', 'No records found.'));
            } else {
                $ret .= $this->_list($data['list']);
            }
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
        
        foreach ($panels->panels as $panel) {
            if (is_array($panel)) {
                if (!empty($panel['pre'])) {
                    $ret .= $panel['pre']; 
                }
                
                $params = array('class' => 'view-panel');
                if (isset($panel['id'])) {
                    $params['id'] = $panel['id']; 
                }
                
                if (isset($panel['params']['class'])  
                    && is_string($panel['params']['class'])
                ) {
                    $params['class'] .= ' ' . $panel['params']['class'];
                    unset($panel['params']['class']);
                }
                
                if (!empty($panel['params']) && is_array($panel['params'])) {
                    $params = array_merge($params, (array)$panel['params']);
                }
                
                $params_str = "";
                foreach ($params as $p_k => $p_v) {
                    $params_str .= ' ' . $p_k . '="' . $p_v . '"';
                }
                
                $ret .= sprintf('<div%1$s>', $params_str);
                
                if (isset($panel['lines']) && is_array($panel['lines'])) {
                    foreach ($panel['lines'] as $line) {
                        if (is_array($line)) {
                            $class = ['label-text'];
                            if (!empty($line['params'])) {
                                if (!empty($line['params']['class']))
                                $class = array_merge($class, (array)$line['params']['class']);
                            }
                            
                            if (isset($line['label'])) {
                                $ret .= sprintf('<div class="%s">', implode(' ', $class));
                                $ret .= sprintf(
                                    '<span class="label">%s</span>',
                                    $line['label']
                                ); 
                            }
                            
                            if (!empty($line['text'])) {
                                $ret .= $line['text']; 
                            } else if (!empty($line['html'])) {
                                $ret .= $line['html']; 
                            } else if (!empty($line['table'])) {
                                $ret .= $this->table($line['table']); 
                            } else {
                                $ret .= '&nbsp;'; 
                            }
                            
                            if (isset($line['label'])) {
                                $ret .= '</div>';
                            }
                        } else {
                            $ret .= $line;
                        }
                    }
                } else if (isset($panel['table']) && is_array($panel['table'])) {
                    $ret .= $this->table($panel['table']);
                } else if (isset($panel['html'])) {
                    $ret .= $panel['html'];
                }
                
                $ret .= '</div>' . PHP_EOL;
                if (!empty($panel['post'])) {
                    $ret .= $panel['post']; 
                }
            } else if (!is_null($panel)) {
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
                    $parameters = array();
                    if (!empty($line['parameters'])) {
                        $parameters = (array)$line['parameters'];
                    }
                    $ret .= call_user_func_array(
                        array($line['class'], $line['method']),
                        $parameters
                    );
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
        
        if (isset($data['pre'])) {
            $ret .= $data['pre']; 
        }
        $ret .= '<table';
        
        $parameters = [];
        if (isset($data['parameters'])) $parameters = $data['parameters'];
        if (isset($data['params'])) $parameters = $data['params'];
        foreach ($parameters as $key => $param) {
            $ret .= ' ' . $key . '="' . $param . '"';
        }
        $ret .= '>' . PHP_EOL;
        
        // display thead
        $ret .= '<thead';
        if (!empty($data['head']['parameters'])) {
            foreach ($data['head']['parameters'] as $key => $param) {
                $ret .= ' ' . $key . '="' . $param . '"';
            }
        }
        $ret .= '>' . PHP_EOL;
        
        
        $default_parameters = array('class' => 'ui-toolbar ui-widget-header ' .
            'ui-corner-top');
        if (!empty($data['head']['rows'])) {
            foreach ($data['head']['rows'] as $row) {
                if (!isset($row['column'])) {
                    $row['column'] = 'th'; 
                }
                $ret .= '<tr';
                
                $parameters = [];
                if (isset($row['parameters'])) $parameters = $row['parameters'];
                if (isset($row['params'])) $parameters = $row['params'];
                
                $params = array_merge(
                    $default_parameters,
                    $parameters
                );
                foreach ($params as $key => $param) {
                    $ret .= ' ' . $key . '="' . $param . '"';
                }
                $ret .= '>' . PHP_EOL;
            
                foreach ($row['columns'] as $col) {
                    if (!is_null($col)) {
                        $ret .= '<' . $row['column'];
                        $parameters = [];
                        if (isset($col['parameters'])) $parameters = $col['parameters'];
                        if (isset($col['params'])) $parameters = $col['params'];
                        foreach ($parameters as $key => $param) {
                            $ret .= ' ' . $key . '="' . $param . '"';
                        }
                        $ret .= '>';
                    
                        $ret .= is_string($col) ? $col : $col['html'];
                        $ret .= '</' . $row['column'] . '>' . PHP_EOL;
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
                    $ret .= '<tr';
                    
                    $parameters = [];
                    if (isset($row['parameters'])) $parameters = $row['parameters'];
                    if (isset($row['params'])) $parameters = $row['params'];
                    
                    foreach ($parameters as $key => $param) {
                        $ret .= ' ' . $key . '="' . $param . '"';
                    }
                    $ret .= '>' . PHP_EOL;
                
                    foreach ($row['columns'] as $col) {
                        if (!is_null($col)) {
                            $ret .= '<td';
                            
                            $parameters = [];
                            if (isset($col['parameters'])) $parameters = $col['parameters'];
                            if (isset($col['params'])) $parameters = $col['params'];
                            foreach ($parameters as $key => $param) {
                                $ret .= ' ' . $key . '="' . $param . '"';
                            }
                            
                            $ret .= '>';
                        
                            $col = is_string($col) ? $col : $col['html'];
                            $ret .= empty($col) ? '&nbsp;' : $col;
                            $ret .= '</td>' . PHP_EOL;
                        }
                    }
                
                    $ret .= '</tr>' . PHP_EOL;
                }
            } 
        }
        $ret .= '</tbody>' . PHP_EOL;
        
        // display tfoot
        if (!empty($data['foot'])) {
            $ret .= '<tfoot';
            if (!empty($data['foot']['parameters'])) {
                foreach ($data['foot']['parameters'] as $key => $param) {
                    $ret .= ' ' . $key . '="' . $param . '"';
                }
            }
            $ret .= '>' . PHP_EOL;
            
            $default_parameters = array('class' => 'ui-toolbar ui-widget-header ' .
                'ui-corner-bl ui-corner-br');
            foreach ($data['foot']['rows'] as $row) {
                if (!isset($row['column'])) {
                    $row['column'] = 'th'; 
                }
                if ($row) {
                    $ret .= '<tr';
                    
                    $parameters = [];
                    if (isset($row['parameters'])) $parameters = $row['parameters'];
                    if (isset($row['params'])) $parameters = $row['params'];
                    
                    $params = array_merge(
                        $default_parameters,
                        $parameters
                    );
                    if (!empty($params)) {
                        foreach ($params as $key => $param) {
                            $ret .= ' ' . $key . '="' . $param . '"';
                        }
                    }
                    $ret .= '>' . PHP_EOL;
                    
                    foreach ($row['columns'] as $col) {
                        if (is_string($col)) {
                            $ret .= '<'.$row['column'].'>' . 
                                $col . '</th>' . PHP_EOL;
                        } else {
                            $ret .= '<'.$row['column'];
                            
                            $parameters = [];
                            if (isset($col['parameters'])) $parameters = $col['parameters'];
                            if (isset($col['params'])) $parameters = $col['params'];
                    
                            foreach ($parameters as $key => $param) {
                                $ret .= ' ' . $key . '="' . $param . '"';
                            }
                            $ret .= '>';
                            $ret .= $col['html'];
                            $ret .= '</'.$row['column'].'>' . PHP_EOL;
                        }
                    }
                    
                    $ret .= '</tr>' . PHP_EOL;
                }
            }
            $ret .= '</tfoot>' . PHP_EOL;
        }
        
        $ret .= '</table>' . PHP_EOL;
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
        if (isset($data['pre'])) {
            echo $data['pre']; 
        }
        
        $tag = 'ul';
        if (!empty($data['type']) && ($data['type'] == 'ordered')) {
            $tag = 'ol'; 
        }
        
        $ret = '';
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
    if (is_array($matches) ) {
        $text = $matches[1] . $matches[2] . "</pre>"; 
    } else {
        $text = $matches; 
    }

    $text = str_replace('<br />', '', $text);
    $text = str_replace('<p>', "\n", $text);
    $text = str_replace('</p>', '', $text);

    return $text;
}