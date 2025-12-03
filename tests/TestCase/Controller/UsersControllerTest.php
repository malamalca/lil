<?php
namespace Lil\Test\TestCase\Controller;

use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;
use Lil\Controller\UsersController;

/**
 * Lil\Controller\UsersController Test Case
 */
class UsersControllerTest extends IntegrationTestCase
{
    /**
     * Authentication data
     *
     * @var array
     */
    private $authData = [
        'Auth' => [
            'User' => [
                'id' => '28233ae2-d3ac-4121-aec7-3878ef19fac5',
                'company_id' => '7d5c7465-9487-4203-ae67-ddb191c42816',
                'name' => 'Administrator',
                'username' => 'admin',
                'passwd' => '$2y$10$YwKA7X2NG0RWZvid80akvOKhdhM65ROyyrEJCkUl07Xgxe5nGa61K',
                'email' => 'info@arhim.si',
                'reset_key' => 'xxyyzz',
                'privileges' => 1,
                'active' => 1,
            ]
        ]
    ];

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.lil.users'
    ];

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test beforeFilter method
     *
     * @return void
     */
    public function testBeforeFilter()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test isAuthorized method
     *
     * @return void
     */
    public function testIsAuthorized()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test login method
     *
     * @return void
     */
    public function testLogin()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test logout method
     *
     * @return void
     */
    public function testLogout()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test register method
     *
     * @return void
     */
    public function testRegister()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test reset method
     *
     * @return void
     */
    public function testReset()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test changePassword method
     * vendor\bin\phpunit --filter testChangePassword plugins\Lil\tests\TestCase\Controller\UsersControllerTest.php
     *
     * @return void
     */
    public function testChangePassword()
    {
        $data = [
            'user_id' => '28233ae2-d3ac-4121-aec7-3878ef19fac5',
            'old_pass' => 'test',
            'passwd' => 'testtest2',
            'repeat_pass' => 'testtest2'
        ];
        $this->post('/lil/Users/change_password/xxyyzz', $data);

        $this->assertResponseSuccess();
        $users = TableRegistry::get('Lil.Users');
        $user = $users->get('28233ae2-d3ac-4121-aec7-3878ef19fac5');
        $this->assertTrue((new DefaultPasswordHasher)->check('testtest2', $user->passwd));

        $this->assertRedirect();
    }

    /**
     * Test properties method
     *
     * @return void
     */
    public function testProperties()
    {
        $this->session($this->authData);

        $this->get('/lil/Users/properties');

        //file_put_contents('test.html', $this->_response->getBody());
        $this->assertResponseSuccess();

        $data = [
            'user_id' => '28233ae2-d3ac-4121-aec7-3878ef19fac5',
            'old_pass' => 'test',
            'passwd' => 'testtest2',
            'repeat_pass' => 'testtest2'
        ];
        $this->post('/lil/Users/properties', $data);

        $this->assertResponseSuccess();
        $users = TableRegistry::get('Lil.Users');
        $user = $users->get('28233ae2-d3ac-4121-aec7-3878ef19fac5');
        $this->assertTrue((new DefaultPasswordHasher)->check('testtest2', $user->passwd));

        $this->assertRedirect();
    }
}
