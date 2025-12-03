<?php
use Cake\Core\Configure;

$user_edit = [
    'title_for_layout' => __d('lil', 'Password Reset'),
    'form' => [
        'pre' => '<div class="form">',
        'post' => '</div>',
        'lines' => [
            'form_start' => [
                'class' => $this->Form,
                'method' => 'create',
                'parameters' => ['model' => 'User'],
            ],
            'referer' => [
                'class' => $this->Form,
                'method' => 'hidden',
                'parameters' => ['field' => 'referer'],
            ],

            'email' => !Configure::read('Lil.userEmailField') ? null : [
                'class' => $this->Form,
                'method' => 'input',
                'parameters' => [
                    'field' => Configure::read('Lil.userEmailField'),
                    'options' => [
                        'label' => __d('lil', 'Email') . ':',
                        'error' => __d('lil', 'Email is required, format must be valid.'),
                    ],
                ],
            ],

            'submit' => [
                'class' => $this->Form,
                'method' => 'submit',
                'parameters' => [
                    'label' => __d('lil', 'Request new Password'),
                ],
            ],
            'form_end' => [
                'class' => $this->Form,
                'method' => 'end',
                'parameters' => [],
            ],
        ],
    ],
];

echo $this->Lil->form($user_edit, 'lil-users-reset');
