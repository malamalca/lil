<?php
use Cake\Routing\Router;
use Cake\Core\Configure;

$user_fields = Configure::read('Lil.authFields');

$user_properties = [
	'title_for_layout' => __d('lil', 'User Properties'),
	'form' => [
        'defaultHelper' => $this->Form,
		'pre' => '<div class="form">',
		'post' => '</div>',
		'lines' => [
			'form_start' => [
				'method' => 'create',
				'parameters' => [$user]
			],
			'id' => [
				'method' => 'hidden',
				'parameters' => ['id']
			],
			'referer' => [
				'method' => 'hidden',
				'parameters' => ['referer']
			],

			'fs_basics_start' => '<fieldset>',
			'lg_basics' => sprintf('<legend>%s</legend>', __d('lil', 'Basics')),
			'name' => [
				'method' => 'control',
				'parameters' => [Configure::read('Lil.userDisplayField'), [
					'label' => __d('lil', 'Name') . ':',
					'error' => __d('lil', 'User\'s name is required.'),
					'class' => 'big'
				]]
			],
			'fs_basics_end' => '</fieldset>',

			'fs_passwords_start' => '<fieldset>',
			'lg_passwords' => sprintf('<legend>%s</legend>', __d('lil', 'Change password')),
			'old_pass' => [
				'method' => 'control',
				'parameters' => ['old_pass', [
					'label' => __d('lil', 'Old password') . ':',
					'error' => __d('lil', 'Invalid old password.'),
				]]
			],
			'passwd' => [
				'method' => 'control',
				'parameters' => [$user_fields['password'], [
					'label' => __d('lil', 'New password') . ':',
					'error' => __d('lil', 'Password is required, format must be valid.'),
					'value' => ''
                ]]
			],
			'repeat_pass' => [
				'method' => 'control',
				'parameters' => ['repeat_pass', [
                    'label' => __d('lil', 'Repeat password') . ':',
					'error' => __d('lil', 'Passwords do not match.'),
					'value' => ''
				]]
			],
			'fs_passwords_end' => '</fieldset>',

			'submit' => [
				'method'     => 'submit',
				'parameters' => [
					'label' => __d('lil', 'Save')
				]
			],
			'form_end' => [
				'method'     => 'end',
				'parameters' => []
			],
		]
	]
];

echo $this->Lil->form($user_properties, 'Lil.Users.properties');
