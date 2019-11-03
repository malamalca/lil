<?php
use Cake\Core\Configure;
use Cake\Routing\Router;

$reset_url = Router::url(array(
	'prefix' => null,
	'plugin' => 'Lil',
	'controller' => 'Users',
	'action' =>	'change_password',
	$reset_key), true
);

echo __d('lil', 'Forgot your password?') . PHP_EOL;
echo PHP_EOL;
echo __d(
    'lil',
    'You\'ve received this mail from password reset request on "{0}"',
    [Configure::read('Lil.appTitle')]
) . PHP_EOL;
echo __d('lil', 'Please follow this url to change your password:') . PHP_EOL;
echo '   ' . $reset_url . PHP_EOL;
echo PHP_EOL;
echo PHP_EOL;
echo __d('lil', 'Please discard this email if you did not want to change your password.') . PHP_EOL;
echo PHP_EOL;
echo PHP_EOL;
echo __d('lil', 'Best Regards.') . PHP_EOL;