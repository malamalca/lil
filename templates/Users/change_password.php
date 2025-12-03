<?php
use Cake\Core\Configure;

$user_fields = Configure::read('Lil.authFields');

$user_form = [
    'title_for_layout' => __d(
        'lil',
        'Set New Password for "{0}"',
        [h($user->{Configure::read('Lil.userDisplayField')})],
    ),
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
                'parameters' => ['referer'],
            ],
            'reset_key' => [
                'method' => 'hidden',
                'parameters' => [
                    'field' => Configure::read('Lil.passwordResetField'),
                    'options' => [
                        'value' => null,
                    ],
                ],
            ],

            'fs_passwords_start' => '<fieldset>',
            'lg_passwords' => sprintf('<legend>%s</legend>', __d('lil', 'Change password')),
            /*'old_pass' => [
                'method' => 'input',
                'parameters' => ['old_pass', [
                    'label' => __d('lil', 'Old password') . ':',
                    'error' => __d('lil', 'Invalid old password.'),
                ]]
            ],*/
            'passwd' => [
                'method' => 'input',
                'parameters' => [$user_fields['password'], [
                    'label' => __d('lil', 'New password') . ':',
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
                    'label' => __d('lil', 'Change'),
                ],
            ],
            'form_end' => [
                'method' => 'end',
                'parameters' => [],
            ],
        ],
    ],
];

echo $this->Lil->form($user_form, 'Users.change_password');
