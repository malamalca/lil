<?php
	use Cake\Routing\Router;
	use Cake\Core\Configure;
	use Cake\Core\Configure\Engine\PhpConfig;
	use Cake\Event\Event;
    use Cake\Event\EventManager;
    use Cake\Network\Request;
    use Cake\Network\Session;
    
	
	Router::plugin('Lil', function ($routes) { $routes->connect('/:controller/:action/*'); });
	
	Configure::load('Lil.config', 'default', true);
	
	EventManager::instance()->on('Controller.beforeRender', 'beforeRender');
	EventManager::instance()->on('Controller.beforeRedirect', 'checkAjaxRedirect');
	
	Request::addDetector('lilPopup', function($request) { return $request->query('lil_submit') == 'dialog'; } );
	
    /**
     * beforeRender hook method.
     *
     * @param object $event Event object.
     * @return void
     */
    function beforeRender(Event $event)
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
     * @return void
     */
    function checkAjaxRedirect($event, $url, $response)
    {
        $controller = $event->subject;
        if ($controller->request->query('lil_submit')) {
            $controller->autoRender = false;
            $controller->set('popupRedirect', true);
            $event->result = $controller->render('Lil.Element' . DS . 'popup_redirect', 'Lil.popup_iframe');
        }
    }