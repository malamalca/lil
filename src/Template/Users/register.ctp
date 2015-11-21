<?php
use Cake\Core\Configure;
	
$user_fields = Configure::read('Lil.authFields');
$user_edit = array(
	'title_for_layout' => __d('lil', 'User Registration'),
	'form' => array(
		'pre' => '<div class="form">',
		'post' => '</div>',
		'lines' => array(
			'form_start' => array(
				'class'      => $this->Form,
				'method'     => 'create',
				'parameters' => array('model' => $user)
			),
			'id' => array(
				'class'      => $this->Form,
				'method'     => 'hidden',
				'parameters' => array('field' => 'id')
			),
			'referer' => array(
				'class'      => $this->Form,
				'method'     => 'hidden',
				'parameters' => array('field' => 'referer')
			),
			'fs_basics_start' => '<fieldset>',
			'lg_basics' => sprintf('<legend>%s</legend>', __d('lil', 'Basics')),
			'name' => array(
				'class'      => $this->Form,
				'method'     => 'input',
				'parameters' => array(
					'field'   => Configure::read('Lil.userDisplayField'),
					'options' => array(
						'label' => __d('lil', 'Name') . ':',
						'error' => __d('lil', 'User\'s name is required.'),
						'class' => 'big'
					)
				)
			),
			'username' => array(
				'class'      => $this->Form,
				'method'     => 'input',
				'parameters' => array(
					'field'   => $user_fields['username'],
					'options' => array(
						'label' => __d('lil', 'Username') . ':',
						'error' => array(
							'empty' => __d('lil', 'Username is required, format must be valid.'),
							'invalid' => __d('lil', 'Username already exists.'),
						)
					)
				)
			),
			'email' => !Configure::read('Lil.userEmailField') ? null : array(
				'class'      => $this->Form,
				'method'     => 'input',
				'parameters' => array(
					'field'   => Configure::read('Lil.userEmailField'),
					'options' => array(
						'label' => __d('lil', 'Email') . ':',
						'error' => __d('lil', 'Email is required, format must be valid.'),
					)
				)
			),
			'fs_basics_end' => '</fieldset>',
			
			'fs_passwords_start' => '<fieldset>',
			'lg_passwords' => sprintf('<legend>%s</legend>', __d('lil', 'Password')),
			'new_pass' => array(
				'class'      => $this->Form,
				'method'     => 'input',
				'parameters' => array(
					'field'   => $user_fields['password'],
					'options' => array(
					    'type' => 'password',
						'label' => __d('lil', 'Password') . ':',
						'error' => __d('lil', 'Password is required, format must be valid.'),
					)
				)
			),
			'repeat_pass' => array(
				'class'      => $this->Form,
				'method'     => 'input',
				'parameters' => array(
					'field'   => 'repeat_pass',
					'options' => array(
					    'type' => 'password',
						'label' => __d('lil', 'Repeat Password') . ':',
						'error' => __d('lil', 'Please retype your password.'),
					)
				)
			),
			
			'submit' => array(
				'class'      => $this->Form,
				'method'     => 'submit',
				'parameters' => array(
					'label' => __d('lil', 'Save')
				)
			),
			'form_end' => array(
				'class'      => $this->Form,
				'method'     => 'end',
				'parameters' => array()
			),
		)
	)
);

echo $this->Lil->form($user_edit, 'lil-users-register');