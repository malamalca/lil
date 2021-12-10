<?php
namespace Lil;

use Cake\Cache\Cache;
use Cake\Core\BasePlugin;
use Cake\Core\Configure;
use Cake\Core\Configure\Engine\PhpConfig;
use Cake\Core\PluginApplicationInterface;
use Cake\Event\EventManager;
use Cake\Http\ServerRequest;
use Cake\Routing\RouteBuilder;
use Lil\Event\LilEventListener;

class Plugin extends BasePlugin
{
    /**
     * Load all the plugin configuration and bootstrap logic.
     *
     * The host application is provided as an argument. This allows you to load
     * additional plugin dependencies, or attach events.
     *
     * @param \Cake\Core\PluginApplicationInterface $app The host application
     * @return void
     */
    public function bootstrap(PluginApplicationInterface $app): void
    {
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
    }

    /**
     * Add routes for the plugin.
     *
     * If your plugin has many routes and you would like to isolate them into a separate file,
     * you can create `$plugin/config/routes.php` and delete this method.
     *
     * @param \Cake\Routing\RouteBuilder $routes The route builder to update.
     * @return void
     */
    public function routes(RouteBuilder $routes): void
    {
        $routes->plugin(
            'Lil',
            ['path' => '/lil'],
            function (RouteBuilder $builder) {
                // Add custom routes here
                $builder->fallbacks();
            }
        );
        parent::routes($routes);
    }
}
