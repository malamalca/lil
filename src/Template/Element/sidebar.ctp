<?php
use Cake\Event\Event;
use Cake\Event\EventManager;
/**
 * This is elements/admin_sidebar template file. 
 *
 * @copyright     Copyright 2008-2010, LilCake.net
 * @link          http://lilcake.net LilCake.net
 * @package       lil
 * @subpackage    lil.views.elements
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
if (empty($admin_sidebar)) {
	$admin_sidebar = [
		'welcome' => [
			'title'   => __d('lil', 'Dashboard'),
			'visible' => true,
			'active'  => false,
			'url'     => '/',
			'items'   => [],
		],
	];
}

class Sidebar {
	public $sidebar = [];
}
$AdminSidebar = new Sidebar();
$AdminSidebar->sidebar = $admin_sidebar;

$event = new Event('Lil.Sidebar.beforeRender', $this, $AdminSidebar);
$this->eventManager()->dispatch($event);
if (!empty($event->result)) {
    $admin_sidebar = $event->result->sidebar;
}

foreach ($admin_sidebar as $panel_name => $panel) {
	if (!empty($panel['active']) && !empty($panel['items'])) {
		printf('<ul class="sidebar-menu">' . PHP_EOL);
		foreach ($panel['items'] as $li_name => $li) {
			if (!empty($li['visible'])) {
				$classes = array();
				if (isset($li['class'])) $classes = (array)$li['class']; 
				if ($li['active']) $classes[] = 'active';
				
				if (((!isset($li['expand']) || is_null($li['expand'])) && !empty($li['submenu'])) ||
					(!empty($li['expand']))) $classes[] = 'expanded';
				
				printf('<li%s>' . PHP_EOL, 
					!empty($classes) ? ' class="' . implode(' ', $classes) . '"' : ''
				);
				
				// caption
				if (!empty($li['url'])) {
					echo $this->Html->link(
						$li['title'],
						$li['url'],
						(isset($li['params']) ? $li['params'] : array())
					) . PHP_EOL;
				} else {
					if (!isset($li['expand']) || is_null($li['expand'])) {
						printf(h($li['title']));
					} else {
						printf('<span class="expand-toggle">%s</span>', h($li['title']));
					}
				}
				
				// submenu
				if ( !empty($li['submenu'])) {
					printf('<ul%s>' . PHP_EOL, (!isset($li['expand']) || is_null($li['expand'])) ? '' : (empty($li['expand']) ? ' class="folded"' : ' class="expanded"'));
					foreach ($li['submenu'] as $lis) {
						if (!empty($lis['visible'])) {
							echo '<li' . ($lis['active'] ? ' class="active"' : '') . '>';
							echo $this->Html->link(
								$lis['title'],
								$lis['url'],
								(isset($lis['params']) ? $lis['params'] : [])
							);
							echo '</li>' . PHP_EOL;
						}
					}
					echo '</ul>' . PHP_EOL;
				}
				echo '</li>' . PHP_EOL;
			}
		}
		echo '</ul>' . PHP_EOL;
	}
}

echo '<ul id="sidebar-panels">';
foreach ($admin_sidebar as $panel_name => $panel) {
	if (!empty($panel['visible'])) {
		printf('<li%2$s>%1$s</li>', 
			$this->Html->link($panel['title'], (!empty($panel['url'])) ? $panel['url'] : '#'),
			!empty($panel['active']) ? ' class="active"' : ''
		) . PHP_EOL;
	}
}
echo '</ul>';


$this->Lil->jsReady('function adjustSidebar() { $("ul.sidebar-menu").height($("#sidebar").height() - $("ul#sidebar-panels").height() - 15); }');
$this->Lil->jsReady('$(window).resize(function() { adjustSidebar(); });');
$this->Lil->jsReady('adjustSidebar();');
$this->Lil->jsReady('$("span.expand-toggle").click(function(){$(this).next("ul").toggle().closest("li").toggleClass("expanded");});');
$this->Lil->jsReady('$("ul.sidebar-menu").bind("mousewheel", function (e, delta) { $(this).scrollTop($(this).scrollTop()- delta * 8); event.preventDefault(); });');