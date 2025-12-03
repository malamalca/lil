<?php
declare(strict_types=1);

/**
 * Users Controller
 *
 * PHP version 5.3
 *
 * @category Controller
 * @package  Lil
 * @author   Arhim d.o.o. <info@arhim.si>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://www.arhint.si
 */
namespace Lil\Controller;

use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Network\Exception\NotFoundException;
use Cake\Utility\Inflector;
use Lil\Auth\LilAuthTrait;

/**
 * Users Controller
 *
 * This controller manages users.
 *
 * @category Controller
 * @package  Lil
 * @author   Arhim d.o.o. <info@arhim.si>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://www.arhint.si
 */
class UsersController extends AppController
{
    use LilAuthTrait;

    /**
     * Cookie key name
     *
     * @var string
     */
    private string $_cookieKey = 'lil_login';

    /**
     * Initialize method.
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
        //$this->loadComponent('Cookie');
    }

    /**
     * BeforeFilter method.
     *
     * @param \Lil\Controller\Cake\Event\Event $event Cake Event object.
     * @return void
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Authorization->authorizeModel('logout', 'reset', 'changePassword');
        if (Configure::read('Lil.enableRegistration')) {
            $this->Authorization->authorizeModel('register');
        }
    }

    /**
     * IsAuthorized method.
     *
     * @param array $user Authenticated user.
     * @return bool
     */
    public function isAuthorized(array $user)
    {
        if (in_array($this->getRequest()->getParam('action'), ['properties'])) {
            return $this->getCurrentUser()->get('id');
        }

        if (in_array($this->getRequest()->getParam('action'), ['index', 'edit', 'add', 'delete'])) {
            return $this->getCurrentUser()->get('id') && $this->userLevel('admin', $user);
        }

        return parent::isAuthorized($user);
    }

    /**
     * Index method
     *
     * @return \Cake\Network\Response|void
     */
    public function index()
    {
        $users = $this->Users->find()
            ->all();

        $this->set(compact('users'));
        $this->set('_serialize', ['users']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $this->setAction('edit');
    }

    /**
     * Edit method
     *
     * @param string|null $id Service Type id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit(?string $id = null)
    {
        $user_fields = Configure::read('Lil.authFields');

        if ($id) {
            $user = $this->Users->get($id);
        } else {
            $user = $this->Users->newEntity([]);
        }
        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity(
                $user,
                $this->getRequest()->getData(),
                ['validate' => ($id ? 'properties' : 'registration')],
            );

            // remove user password when empty
            if (empty($this->getRequest()->getData($user_fields['password']))) {
                unset($user->{$user_fields['password']});
            }

            if ($this->Users->save($user)) {
                $this->Flash->success(__d('lil', 'The user has been saved.'));

                $referer = $this->getRequest()->getData('referer');
                if ($referer) {
                    return $this->redirect($referer);
                }

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__d('lil', 'The user could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Service Type id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete(?string $id = null)
    {
        $this->getRequest()->allowMethod(['post', 'delete', 'get']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__d('lil', 'The user has been deleted.'));
        } else {
            $this->Flash->error(__d('lil', 'The user could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Login method.
     *
     * This method will display login form
     *
     * @return mixed
     */
    public function login()
    {
        if ($this->getCurrentUser()->get('id')) {
            $this->redirect($this->Auth->redirectUrl());
        }

        $user = $this->Auth->identify();
        if ($user) {
            $this->Auth->setUser($user);

            // set cookie
            if (!empty($this->getRequest()->getData('remember_me'))) {
                $CookieAuth = $this->Auth->getAuthenticate('Lil.Cookie');
                if ($CookieAuth) {
                    $CookieAuth->createCookie($this->getRequest()->getData());
                }
            }
        } else {
            if ($this->getRequest()->is('post') || env('PHP_AUTH_USER')) {
                $this->Flash->error(__d('lil', 'Invalid username or password, try again'));
            }
        }

        if ($this->getCurrentUser()->get('id')) {
            $redirect = $this->Auth->redirectUrl();
            $event = new Event('Lil.Auth.afterLogin', $this->Auth, [$redirect]);
            $this->getEventManager()->dispatch($event);

            return $this->redirect($redirect);
        }
    }

    /**
     * Logout method
     *
     * @return mixed
     */
    public function logout()
    {
        $CookieAuth = $this->Auth->getAuthenticate('Lil.Cookie');
        if ($CookieAuth) {
            $CookieAuth->deleteCookie();
        }

        return $this->redirect($this->Auth->logout());
    }

    /**
     * Register method
     *
     * @return mixed
     */
    public function register()
    {
        if (!Configure::read('Lil.enableRegistration')) {
            throw new NotFoundException(__d('lil', 'Cannot register new users.'));
        }

        $user = $this->Users->newEntity($this->getRequest()->getData(), ['validate' => 'registration']);

        if ($this->getRequest()->is('post')) {
            if (!$user->getErrors() && $this->Users->save($user)) {
                $event = new Event(
                    'Lil.Model.Users.afterRegister',
                    $this->Users,
                    [$user, $this->getRequest()->getData()],
                );
                $this->eventManager()->dispatch($event);

                $this->Flash->success(__d('lil', 'The user has been registered.'));

                return $this->redirect('/');
            }
            $this->Flash->error(__d('lil', 'Unable to add the user.'));
        }
        $this->set('user', $user);
    }

    /**
     * Reset method
     *
     * @return void
     */
    public function reset()
    {
        if ($this->getCurrentUser()->get()) {
            $this->redirect($this->Auth->loginRedirect);
        }

        if ($this->getRequest()->is('post')) {
            $emailField = Configure::read('Lil.userEmailField');
            $user = $this->Users->find()
                ->select()
                ->where([$emailField => $this->getRequest()->data($emailField)])
                ->first();

            if ($user) {
                $this->Users->sendResetEmail($user);
                $this->Flash->success(__d('lil', 'An email with password reset instructions has been sent.'));
            } else {
                $this->Flash->error(__d('lil', 'No user with specified email has been found.'));
            }
        }
    }

    /**
     * Change users password
     *
     * @param string $resetKey Auto generated reset key.
     * @return void
     */
    public function changePassword(?string $resetKey = null)
    {
        if (!$resetKey) {
            throw new NotFoundException(__d('lil', 'Reset key does not exist.'));
        }

        $user = $this->Users
            ->{'findBy' . Inflector::camelize(Configure::read('Lil.passwordResetField'))}($resetKey)->first();
        if (!$user) {
            throw new NotFoundException(__d('lil', 'User does not exist.'));
        }

        $user_fields = Configure::read('Lil.authFields');

        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $this->Users->patchEntity($user, $this->getRequest()->getData(), ['validate' => 'resetPassword']);

            if (!$user->getErrors() && $this->Users->save($user)) {
                $this->Flash->success(__d('lil', 'Properties have been saved.'));
                $this->redirect('/');
            } else {
                $this->Flash->error(__d('lil', 'Please verify that the information is correct.'));
            }
        } else {
            $user->{$user_fields['password']} = null;
        }

        $this->set(compact('user'));
    }

    /**
     * Properties method
     *
     * @return void
     */
    public function properties()
    {
        $user = $this->Users->get($this->getCurrentUser()->get('id'));
        if (!$user) {
            throw new NotFoundException(__d('lil', 'User does not exist.'));
        }

        $user_fields = Configure::read('Lil.authFields');

        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $this->Users->patchEntity($user, $this->getRequest()->getData(), ['validate' => 'properties']);

            // remove user password when empty
            if (empty($this->getRequest()->getData($user_fields['password']))) {
                unset($user->{$user_fields['password']});
            }

            if (!$user->getErrors() && $this->Users->save($user)) {
                $this->Flash->success(__d('lil', 'Properties have been saved.'));
                $this->redirect('/');
            } else {
                $this->Flash->error(__d('lil', 'Please verify that the information is correct.'));
            }
        } else {
            $user->{$user_fields['password']} = null;
        }

        $this->set(compact('user'));
    }
}
