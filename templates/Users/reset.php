<?php
use Cake\Core\Configure;

$user_edit = array(
	'title_for_layout' => __d('lil', 'Password Reset'),
	'form' => array(
		'pre' => '<div class="form">',
		'post' => '</div>',
		'lines' => array(
			'form_start' => array(
				'class'      => $this->Form,
				'method'     => 'create',
				'parameters' => array('model' => 'User')
			),
			'referer' => array(
				'class'      => $this->Form,
				'method'     => 'hidden',
				'parameters' => array('field' => 'referer')
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
			
			'submit' => array(
				'class'      => $this->Form,
				'method'     => 'submit',
				'parameters' => array(
					'label' => __d('lil', 'Request new Password')
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

echo $this->Lil->form($user_edit, 'lil-users-reset');