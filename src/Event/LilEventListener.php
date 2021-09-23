<?php
namespace Lil\Event;

use ArrayObject;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Event\EventManager;

class LilEventListener implements EventListenerInterface
{
    /**
     * Application's implemented events
     *
     * @return array
     */
    public function implementedEvents(): array
    {
        return [
            'Controller.beforeRender' => 'beforeRender',
            'Controller.beforeRedirect' => 'checkAjaxRedirect',
        ];
    }

    /**
     * beforeRender hook method.
     *
     * @param object $event Event object.
     * @return void
     */
    public function beforeRender(Event $event)
    {
        $controller = $event->getSubject();

        if (!$controller->viewBuilder()->getClassName()) {
            $controller->viewBuilder()->setLayout(Configure::read('Lil.layout'));
        }

        if (isset($controller->Auth)) {
            $controller->set('currentUser', $controller->Auth->user());
        }

        if ($controller->getRequest()->is('ajax') && !$controller->getRequest()->is('aht')) {
            $controller->viewBuilder()->setLayout('Lil.popup');
        }

        if ($controller->getRequest()->is('lilPopup')) {
            $controller->viewBuilder()->setLayout('Lil.popup_iframe');
        }

        $adminSidebar = new ArrayObject;
        $adminSidebar['welcome'] = [
            'title' => __d('lil', 'Dashboard'),
            'visible' => true,
            'active' => false,
            'url' => '/',
            'items' => [],
        ];

        $event = new Event('Lil.Sidebar.beforeRender', $controller, ['sidebar' => $adminSidebar]);
        EventManager::instance()->dispatch($event);

        $controller->set('sidebar', (array)$adminSidebar);
    }

    /**
     * checkAjaxRedirect hook method.
     *
     * @param object $event Event object.
     * @param string $url Redirect url.
     * @param object $response Response object.
     * @return object
     */
    public function checkAjaxRedirect($event, $url, $response)
    {
        $controller = $event->getSubject();
        if ($controller->getRequest()->is('lilPopup')) {
            $controller->disableAutoRender();
            $controller->set('popupRedirect', true);
            $event->result = $controller->render('Lil.Element' . DS . 'popup_redirect', 'Lil.popup_iframe')->withStatus(200);
            $event->stopPropagation();

            return $event->result;
        }
    }
}
