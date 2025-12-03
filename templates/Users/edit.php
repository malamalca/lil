<?php
use Cake\Core\Configure;

$user_fields = Configure::read('Lil.authFields');

$userForm = [
    'title_for_layout' => $user->isNew() ? __d('lil', 'Add User') : __d('lil', 'Edit User'),
    'menu' => [
        'delete' => [
            'title' => __d('lil', 'Delete'),
            'visible' => !$user->isNew(),
            'url' => [
                'action' => 'delete',
                $user->id,
            ],
            'params' => ['confirm' => __d('lil', 'Are you sure you want to delete this user?')],
        ],
    ],
    'form' => [
        'defaultHelper' => $this->Form,
        'pre' => '<div class="form">',
        'post' => '</div>',
        'lines' => [
            'form_start' => [
                'method' => 'create',
                'parameters' => [$user],
            ],
            'id' => [
                'method' => 'hidden',
                'parameters' => ['id'],
            ],
            'referer' => [
                'method' => 'hidden',
                'parameters' => ['referer', [
                    'default' => $this->getRequest()->referer(),
                ]],
            ],

            'fs_basics_start' => '<fieldset>',
            'lg_basics' => sprintf('<legend>%s</legend>', __d('lil', 'Basics')),
            'name' => [
                'method' => 'input',
                'parameters' => [Configure::read('Lil.userDisplayField'), [
                    'label' => __d('lil', 'Name') . ':',
                    'error' => __d('lil', 'User\'s name is required.'),
                    'class' => 'big',
                ]],
            ],
            'username' => !$user->isNew() ? null : [
                'method' => 'input',
                'parameters' => [$user_fields['username'], [
                    'label' => __d('lil', 'Username') . ':',
                    'error' => __d('lil', 'Username is required.'),
                    'class' => 'big',
                ]],
            ],
            'fs_basics_end' => '</fieldset>',

            'fs_passwords_start' => '<fieldset>',
            'lg_passwords' => sprintf(
                '<legend>%s</legend>',
                $user->isNew() ? __d('lil', 'Password') : __d('lil', 'Change password'),
            ),
            'old_pass' => $user->isNew() ? null : [
                'method' => 'input',
                'parameters' => ['old_pass', [
                    'label' => __d('lil', 'Old password') . ':',
                    'error' => __d('lil', 'Invalid old password.'),
                ]],
            ],
            'passwd' => [
                'method' => 'input',
                'parameters' => [$user_fields['password'], [
                    'label' => ($user->isNew() ? __d('lil', 'Password') : __d('lil', 'New password')) . ':',
                    'error' => __d('lil', 'Password is required, format must be valid.'),
                    'value' => '',
                ]],
            ],
            'repeat_pass' => [
                'method' => 'input',
                'parameters' => ['repeat_pass', [
                    'label' => __d('lil', 'Repeat password') . ':',
                    'error' => __d('lil', 'Passwords do not match.'),
                    'value' => '',
                ]],
            ],
            'fs_passwords_end' => '</fieldset>',

            'submit' => [
                'method' => 'submit',
                'parameters' => [
                    'label' => __d('lil', 'Save'),
                ],
            ],
            'form_end' => [
                'method' => 'end',
                'parameters' => [],
            ],
        ],
    ],
];

echo $this->Lil->form($userForm, 'Lil.Users.edit');
