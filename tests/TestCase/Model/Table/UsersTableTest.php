<?php
declare(strict_types=1);

namespace Lil\Test\TestCase\Model\Table;

use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * Lil\Model\Table\UsersTable Test Case
 */
class UsersTableTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public array $fixtures = [
        'Users' => 'plugin.lil.users',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Users') ? [] : ['className' => 'Lil\Model\Table\UsersTable'];
        $this->Users = TableRegistry::get('Users', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Users);

        parent::tearDown();
    }

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
     * Test properties save
     *
     * vendor\bin\phpunit --filter testValidateProperties plugins\Lil\tests\TestCase\Model\Table\UsersTableTest.php
     *
     * @return void
     */
    public function testValidateProperties()
    {
        $users = TableRegistry::get('Lil.Users');

        // incorrect old password
        $data = [
            'user_id' => '28233ae2-d3ac-4121-aec7-3878ef19fac5',
            'old_pass' => 'test2',
            'passwd' => 'testtest2',
            'repeat_pass' => 'testtest2',
        ];
        $user = $users->get('28233ae2-d3ac-4121-aec7-3878ef19fac5');
        $users->patchEntity($user, $data, ['validate' => 'properties']);
        $errors = $user->getErrors();
        $this->assertTrue(!empty($errors));
        $this->assertEquals(1, count($errors));
        $this->assertTrue(!empty($errors['old_pass']));

        // passwords do not match
        $data = [
            'user_id' => '28233ae2-d3ac-4121-aec7-3878ef19fac5',
            'old_pass' => 'test',
            'passwd' => '123223',
            'repeat_pass' => 'testtest2',
        ];
        $user = $users->get('28233ae2-d3ac-4121-aec7-3878ef19fac5');
        $users->patchEntity($user, $data, ['validate' => 'properties']);
        $errors = $user->getErrors();
        $this->assertTrue(!empty($errors));
        $this->assertEquals(1, count($errors));
        $this->assertTrue(!empty($errors['repeat_pass']));

        // on empty password minLength
        $data = [
            'user_id' => '28233ae2-d3ac-4121-aec7-3878ef19fac5',
            'old_pass' => '',
            'passwd' => 'a',
            'repeat_pass' => 'a',
        ];
        $user = $users->get('28233ae2-d3ac-4121-aec7-3878ef19fac5');
        $users->patchEntity($user, $data, ['validate' => 'properties']);
        $errors = $user->getErrors();
        $this->assertTrue(!empty($errors));
        //$this->assertTrue((new DefaultPasswordHasher)->check('test', $user->passwd));

        // ok
        $data = [
            'user_id' => '28233ae2-d3ac-4121-aec7-3878ef19fac5',
            'old_pass' => 'test',
            'passwd' => 'testtest2',
            'repeat_pass' => 'testtest2',
        ];

        $user = $users->get('28233ae2-d3ac-4121-aec7-3878ef19fac5');
        $users->patchEntity($user, $data, ['validate' => 'properties']);
        $errors = $user->getErrors();
        $this->assertTrue(empty($errors));
        $this->assertTrue((new DefaultPasswordHasher())->check('testtest2', $user->passwd));
    }

    /**
     * Test password validation method
     *
     * vendor\bin\phpunit --filter testValidateResetPassword plugins\Lil\tests\TestCase\Model\Table\UsersTableTest.php
     *
     * @return void
     */
    public function testValidateResetPassword()
    {
        $users = TableRegistry::get('Lil.Users');

        // incorrect old password
        $data = [
            'user_id' => '28233ae2-d3ac-4121-aec7-3878ef19fac5',
            'old_pass' => 'test2',
            'passwd' => 'testtest2',
            'repeat_pass' => 'testtest2',
        ];
        $user = $users->get('28233ae2-d3ac-4121-aec7-3878ef19fac5');
        $users->patchEntity($user, $data, ['validate' => 'resetPassword']);
        $errors = $user->getErrors();
        $this->assertTrue(!empty($errors));
        $this->assertEquals(1, count($errors));
        $this->assertTrue(!empty($errors['old_pass']));

        // passwords do not match
        $data = [
            'user_id' => '28233ae2-d3ac-4121-aec7-3878ef19fac5',
            'old_pass' => 'test',
            'passwd' => '123223',
            'repeat_pass' => 'testtest2',
        ];
        $user = $users->get('28233ae2-d3ac-4121-aec7-3878ef19fac5');
        $users->patchEntity($user, $data, ['validate' => 'resetPassword']);
        $errors = $user->getErrors();
        $this->assertTrue(!empty($errors));
        $this->assertEquals(1, count($errors));
        $this->assertTrue(!empty($errors['repeat_pass']));

        // empty password
        $data = [
            'user_id' => '28233ae2-d3ac-4121-aec7-3878ef19fac5',
            'old_pass' => 'test',
            'passwd' => '',
            'repeat_pass' => '',
        ];
        $user = $users->get('28233ae2-d3ac-4121-aec7-3878ef19fac5');
        $users->patchEntity($user, $data, ['validate' => 'resetPassword']);
        $errors = $user->getErrors();
        $this->assertTrue(!empty($errors));
        $this->assertEquals(1, count($errors));
        $this->assertTrue(!empty($errors['passwd']));

        // ok
        $data = [
            'user_id' => '28233ae2-d3ac-4121-aec7-3878ef19fac5',
            'old_pass' => 'test',
            'passwd' => 'testtest2',
            'repeat_pass' => 'testtest2',
        ];

        $user = $users->get('28233ae2-d3ac-4121-aec7-3878ef19fac5');
        $users->patchEntity($user, $data, ['validate' => 'resetPassword']);
        $this->assertTrue(empty($user->getErrors()));
    }
}
