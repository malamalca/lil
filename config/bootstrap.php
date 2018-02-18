<?php
    use Cake\Cache\Cache;
    use Cake\Core\Configure;
    use Cake\Core\Configure\Engine\PhpConfig;
    use Cake\Event\EventManager;
    use Cake\Network\Request;
    use Cake\Routing\Router;
    use Lil\Event\LilEventListener;

    Router::plugin('Lil', function ($routes) {
        $routes->connect('/:controller/:action/*');
    });

    Configure::load('Lil.config', 'default', true);

    Cache::config('Lil', [
        'className' => 'Cake\Cache\Engine\FileEngine',
        'duration' => '+1 week',
        'probability' => 100,
        'path' => CACHE . 'lil_',
    ]);

    Request::addDetector('lilPopup', function ($request) {
        return $request->query('lil_submit') == 'dialog';
    });

    $LilEventListener = new LilEventListener();
    EventManager::instance()->on($LilEventListener);
