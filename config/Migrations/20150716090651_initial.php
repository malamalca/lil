<?php
use Phinx\Migration\AbstractMigration;
use Cake\ORM\TableRegistry;

class Initial extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('settings');
        $table
            ->addColumn('user_id', 'uuid', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('plugin', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('value', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();
        $table = $this->table('users', ['id' => false, 'primary_key' => ['id']]);
        $table
            ->addColumn('id', 'uuid', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('company_id', 'uuid', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('username', 'string', [
                'default' => null,
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('passwd', 'string', [
                'default' => null,
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('email', 'string', [
                'default' => null,
                'limit' => 200,
                'null' => true,
            ])
            ->addColumn('reset_key', 'string', [
                'default' => null,
                'limit' => 200,
                'null' => true,
            ])
            ->addColumn('privileges', 'integer', [
                'default' => 10,
                'limit' => 4,
                'null' => false,
            ])
            ->addColumn('active', 'boolean', [
                'default' => 1,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();
            
        // create user
        $Users = TableRegistry::get('Lil.Users');
        $user = $Users->newEntity();
        $user->id = 'bb4dcb27-2be9-4673-8c2b-c1e823b3c300';
        $user->name = 'Will E. Coyote';
        $user->username = 'test';
        $user->passwd = 'test';
        $user->email = 'info@arhim.si';
        $user->privileges = 5;
        $user->active = 1;
        
        $Users->save($user);
    }

    public function down()
    {
        $this->dropTable('settings');
        $this->dropTable('users');
    }
}