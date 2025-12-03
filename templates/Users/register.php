<?php
use Cake\Core\Configure;

$user_fields = Configure::read('Lil.authFields');
$user_edit = [
    'title_for_layout' => __d('lil', 'User Registration'),
    'form' => [
        'pre' => '<div class="form">',
        'post' => '</div>',
        'lines' => [
            'form_start' => [
                'class' => $this->Form,
                'method' => 'create',
                'parameters' => ['model' => $user]
            ],
            'id' => [
                'class' => $this->Form,
                'method' => 'hidden',
                'parameters' => ['field' => 'id']
            ],
            'referer' => [
                'class' => $this->Form,
                'method' => 'hidden',
                'parameters' => ['field' => 'referer']
            ],
            'fs_basics_start' => '<fieldset>',
            'lg_basics' => sprintf('<legend>%s</legend>', __d('lil', 'Basics')),
            'name' => [
                'class' => $this->Form,
                'method' => 'control',
                'parameters' => [
                    'field' => Configure::read('Lil.userDisplayField'),
                    'options' => [
                        'label' => __d('lil', 'Name') . ':',
                        'error' => __d('lil', 'User\'s name is required.'),
                        'class' => 'big'
                    ]
                ]
            ],
            'username' => [
                'class'      => $this->Form,
                'method'     => 'control',
                'parameters' => [
                    'field'   => $user_fields['username'],
                    'options' => [
                        'label' => __d('lil', 'Username') . ':',
                        'error' => [
                            'empty' => __d('lil', 'Username is required, format must be valid.'),
                            'invalid' => __d('lil', 'Username already exists.'),
                        ]
                    ]
                ]
            ],
            'email' => !Configure::read('Lil.userEmailField') ? null : [
                'class'      => $this->Form,
                'method'     => 'control',
                'parameters' => [
                    'field'   => Configure::read('Lil.userEmailField'),
                    'options' => [
                        'label' => __d('lil', 'Email') . ':',
                        'error' => __d('lil', 'Email is required, format must be valid.'),
                    ]
                ]
            ],
            'fs_basics_end' => '</fieldset>',

            'fs_passwords_start' => '<fieldset>',
            'lg_passwords' => sprintf('<legend>%s</legend>', __d('lil', 'Password')),
            'new_pass' => [
                'class'      => $this->Form,
                'method'     => 'control',
                'parameters' => [
                    'field'   => $user_fields['password'],
                    'options' => [
                        'type' => 'password',
                        'label' => __d('lil', 'Password') . ':',
                        'error' => __d('lil', 'Password is required, format must be valid.'),
                    ]
                ]
            ],
            'repeat_pass' => [
                'class'      => $this->Form,
                'method'     => 'control',
                'parameters' => [
                    'field'   => 'repeat_pass',
                    'options' => [
                        'type' => 'password',
                        'label' => __d('lil', 'Repeat Password') . ':',
                        'error' => __d('lil', 'Please retype your password.'),
                    ]
                ]
            ],

            'submit' => [
                'class'      => $this->Form,
                'method'     => 'submit',
                'parameters' => [
                    'label' => __d('lil', 'Save')
                ]
            ],
            'form_end' => [
                'class'      => $this->Form,
                'method'     => 'end',
                'parameters' => []
            ],
        ]
    ]
];

echo $this->Lil->form($user_edit, 'Lil.Users.register');
