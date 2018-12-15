<?php
use Cake\Routing\Router;

Router::plugin('Lil', function ($routes) {
    $routes->fallbacks();
});
