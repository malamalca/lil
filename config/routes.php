<?php
use Cake\Routing\Router;


Router::connect('/lil/lil_scan_applet.jnlp', array(
	'plugin'     => 'Lil',
	'controller' => 'Pages',
	'action'     => 'acquire',
));

Router::plugin('Lil', function ($routes) {
    $routes->fallbacks();
});