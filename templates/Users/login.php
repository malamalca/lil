<?php
	use Cake\Core\Configure;

	$user_fields = Configure::read('Lil.authFields');

	$user_login = [
		'title_for_layout' => __d('lil', 'Login'),
		'form' => [
			'pre' => '<div class="form">',
			'post' => '</div>',
			'lines' => [
				'form_start' => [
					'class' => $this->Form,
					'method' => 'create',
					'parameters' => [
						Configure::read('Lil.authModel'),
						[
							'url' => [
								'plugin'     => 'Lil',
								'controller' => 'Users',
								'action'     => 'login',
							]
						]
					]
				],
				'username' => [
					'class' => $this->Form,
					'method' => 'control',
					'parameters' => [
						$user_fields['username'],
						[
							'label' => __d('lil', 'Username') . ':',
							'type'  => 'text'
						]
					]
				],
				'password' => [
					'class' => $this->Form,
					'method' => 'control',
					'parameters' => [
						$user_fields['password'],
						[
							'label' => __d('lil', 'Password') . ':',
							'type'  => 'password'
						]
					]
				],
				'remember_me' => [
					'class'      => $this->Form,
					'method'     => 'control',
					'parameters' => [
						'remember_me',
						[
							'type'  => 'checkbox',
							'label' => __d('lil', 'Remember me on this computer'),
						]
					]
				],
				'submit' => [
					'class'      => $this->Form,
					'method'     => 'submit',
					'parameters' => [__d('lil', 'OK')]
				],
				'form_end' => [
					'class'      => $this->Form,
					'method'     => 'end',
				],
				'passwd_reset' => !Configure::read('Lil.enablePasswordReset') ? null :
					sprintf('<div id="UserLoginPasswordReset">%s</div>',
						$this->Html->link(__d('lil', 'Forgot your password?'), [
							'plugin'     => 'Lil',
							'controller' => 'Users',
							'action'     => 'reset',
						])
					),
				'registration' => !Configure::read('Lil.enableRegistration') ? null :
					sprintf('<div id="UserLoginRegister">%s</div>',
						$this->Html->link(__d('lil', 'Register as new user'), [
							'plugin'     => 'Lil',
							'controller' => 'Users',
							'action'     => 'register'
						])
					)
			]
		]
	];

	echo $this->Lil->form($user_login, 'users-login');
