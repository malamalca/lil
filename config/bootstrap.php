<?php
    use Cake\Cache\Cache;
    use Cake\Core\Configure;
    use Cake\Core\Configure\Engine\PhpConfig;
    use Cake\Event\EventManager;
    use Cake\Http\ServerRequest;
    use Cake\Routing\Router;
    use Lil\Event\LilEventListener;

    Router::plugin('Lil', function ($routes) {
        $routes->connect('/:controller/:action/*');
    });

    Configure::load('Lil.config', 'default', true);

    if (!Cache::getConfig('Lil')) {
        Cache::setConfig('Lil', [
            'className' => 'Cake\Cache\Engine\FileEngine',
            'duration' => '+1 week',
            'probability' => 100,
            'path' => CACHE . 'lil_',
        ]);
    }

    ServerRequest::addDetector('lilPopup', function ($request) {
        return $request->getQuery('lil_submit') == 'dialog' || $request->hasHeader('X-Lil-Submit');
    });

    $LilEventListener = new LilEventListener();
    EventManager::instance()->on($LilEventListener);
