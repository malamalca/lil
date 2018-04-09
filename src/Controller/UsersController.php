<?php
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

use Cake\Auth\DefaultPasswordHasher;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\Network\Exception\NotFoundException;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use Cake\Utility\Security;
use Cake\Validation\Validator;

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
    /**
     * Cookie key name
     *
     * @var string
     */
    private $_cookieKey = 'lil_login';

    /**
     * Initialize method.
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Cookie');
    }
    /**
     * BeforeFilter method.
     *
     * @param Cake\Event\Event $event Cake Event object.
     * @return void
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->Auth->allow(['logout', 'reset', 'changePassword']);
        if (Configure::read('Lil.enableRegistration')) {
            $this->Auth->allow(['register']);
        }
    }
    /**
     * IsAuthorized method.
     *
     * @param array $user Authenticated user.
     * @return bool
     */
    public function isAuthorized($user)
    {
        if (in_array($this->request->action, ['properties', 'index', 'edit', 'add', 'delete'])) {
            return $this->Auth->user('id');
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
    public function edit($id = null)
    {
        $user_fields = Configure::read('Lil.authFields');

        if ($id) {
            $user = $this->Users->get($id);
        } else {
            $user = $this->Users->newEntity();
        }
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData(), ['validate' => ($id ? 'properties' : 'registration')]);

            // remove user password when empty
            if (empty($this->request->getData($user_fields['password']))) {
                unset($user->{$user_fields['password']});
            }

            if ($this->Users->save($user)) {
                $this->Flash->success(__d('lil', 'The user has been saved.'));

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
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete', 'get']);
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
     * @return mixed
     */
    public function login()
    {
        if ($this->Auth->user()) {
            $this->redirect($this->Auth->redirectUrl());
        }

        if ($user = $this->Auth->identify()) {
            $this->Auth->setUser($user);

            // set cookie
            if (!empty($this->request->getData('remember_me'))) {
                if ($CookieAuth = $this->Auth->getAuthenticate('Lil.Cookie')) {
                    $CookieAuth->createCookie($this->request->data);
                }
            }
        } else {
            if ($this->request->is('post') || env('PHP_AUTH_USER')) {
                $this->Flash->error(
                    __d(
                        'lil',
                        'Invalid username or password, try again'
                    )
                );
            }
        }

        if ($this->Auth->user()) {
            $redirect = $this->Auth->redirectUrl();
            $event = new Event('Lil.Auth.afterLogin', $this->Auth, [$redirect]);
            $this->eventManager()->dispatch($event);

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
        if ($CookieAuth = $this->Auth->getAuthenticate('Lil.Cookie')) {
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
        $user = $this->Users->newEntity(
            $this->request->getData(),
            ['validate' => 'registration']
        );
        if ($this->request->is('post')) {
            if (!$user->errors() && $this->Users->save($user)) {
                $event = new Event(
                    'Lil.Model.Users.afterRegister',
                    $this->Users,
                    [$user, $this->request->getData()]
                );
                $this->eventManager()->dispatch($event);

                $this->Flash->success(
                    __d('lil', 'The user has been registered.')
                );

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
        if ($this->Auth->user()) {
            $this->redirect($this->Auth->loginRedirect);
        }

        if ($this->request->is('post')) {
            $emailField = Configure::read('Lil.userEmailField');
            $user = $this->Users->find()
                ->select()
                ->where([$emailField => $this->request->data('email')])
                ->first();

            if ($user) {
                $this->Users->sendResetEmail($user);
                $this->Flash->success(
                    __d(
                        'lil',
                        'An email with password reset instructions has been sent.'
                    )
                );
            } else {
                $this->Flash->error(
                    __d(
                        'lil',
                        'No user with specified email has been found.'
                    )
                );
            }
        }
    }
    /**
     * Change users password
     *
     * @param string $resetKey Auto generated reset key.
     * @return void
     */
    public function changePassword($resetKey = null)
    {
        if (!$resetKey) {
            throw new NotFoundException(__d('lil', 'Reset key does not exist.'));
        }

        $user = $this->Users->{'findBy' . Inflector::camelize(
            Configure::read('Lil.passwordResetField')
        )}($resetKey)->first();
        if (!$user) {
            throw new NotFoundException(__d('lil', 'User does not exist.'));
        }

        $user_fields = Configure::read('Lil.authFields');

        if ($this->request->is(['patch', 'post', 'put'])) {
            $this->Users->patchEntity(
                $user,
                $this->request->getData(),
                ['validate' => 'resetPassword']
            );
            if (!$user->errors() && $this->Users->save($user)) {
                $this->Flash->success(__d('lil', 'Properties have been saved.'));
                $this->redirect('/');
            } else {
                $this->Flash->error(
                    __d(
                        'lil',
                        'Please verify that the information is correct.'
                    )
                );
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
        $user = $this->Users->get($this->Auth->user('id'));
        if (!$user) {
            throw new NotFoundException(__d('lil', 'User does not exist.'));
        }

        $user_fields = Configure::read('Lil.authFields');

        if ($this->request->is(['patch', 'post', 'put'])) {
            $this->Users->patchEntity(
                $user,
                $this->request->getData(),
                ['validate' => 'properties']
            );

            // remove user password when empty
            if (empty($this->request->getData($user_fields['password']))) {
                unset($user->{$user_fields['password']});
            }

            if (!$user->errors() && $this->Users->save($user)) {
                $this->Flash->success(__d('lil', 'Properties have been saved.'));
                $this->redirect('/');
            } else {
                $this->Flash->error(
                    __d(
                        'lil',
                        'Please verify that the information is correct.'
                    )
                );
            }
        } else {
            $user->{$user_fields['password']} = null;
        }

        $this->set(compact('user'));
    }
}
