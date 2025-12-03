<?php

    use Cake\Core\Configure;

    $usersIndex = [
        'title_for_layout' => __d('lil', 'Users'),
        'menu' => [
            'add' => [
                'title' => __d('lil', 'Add'),
                'visible' => true,
                'url' => [
                    'action' => 'add',
                ],
            ],
        ],
        'table' => [
            'parameters' => [
                'width' => '100%', 'cellspacing' => 0, 'cellpadding' => 0,
                'id' => 'UsersIndex', 'class' => 'index',
            ],
            'head' => ['rows' => [['columns' => [
                'title' => __d('lil', 'Title'),
                'username' => __d('lil', 'Username'),
            ]]]],
        ],
    ];

    foreach ($users as $user) {
        $userTitle = $user->{Configure::read('Lil.userDisplayField')};
        if (!$userTitle) {
            $userTitle = __d('lil', 'N/A');
        }

        $usersIndex['table']['body']['rows'][]['columns'] = [
            'title' => [
                'html' => $this->Html->link(
                    $userTitle,
                    [
                        'action' => 'edit',
                        $user->id,
                    ],
                ),
            ],
            'username' => [
                'html' => h($user->{Configure::read('Lil.authFields.username')}),
            ],
        ];
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////
    // call plugin handlers and output data
    echo $this->Lil->index($usersIndex, 'Lil.Users.index');
