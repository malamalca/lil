<?php
namespace Lil\Event;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;

class LilEventListener implements EventListenerInterface
{
    /**
     * Application's implemented events
     *
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'Controller.beforeRender' => 'beforeRender',
            'Controller.beforeRedirect' => 'checkAjaxRedirect',
            //'Model.beforeMarshal', 'convertLilFields',
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
        $controller = $event->subject;

        if (empty($controller->viewClass)) {
            $controller->viewBuilder()->layout('Lil.lil');
        }

        if (isset($controller->Auth)) {
            $controller->set('currentUser', $controller->Auth->user());
        }

        if ($controller->request->is('ajax')) {
            $controller->viewBuilder()->layout('Lil.popup');
        }

        if ($controller->request->query('lil_submit')) {
            $controller->viewBuilder()->layout('Lil.popup_iframe');
        }
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
        $controller = $event->subject;
        if ($submitKind = $controller->request->query('lil_submit')) {
            $controller->autoRender = false;
            $controller->set('popupRedirect', true);
            $event->result = $controller->render('Lil.Element' . DS . 'popup_redirect', 'Lil.popup_iframe');
            $event->result->statusCode(200);
            $event->stopPropagation();

            return $event->result;
        }
    }

    /**
     * Convert fields to their actual representation
     *
     * @param object $event Event object.
     * @return void
     */
    public function convertLilFields($event)
    {
        //return $evc
    }
}
