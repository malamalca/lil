<?php
    use Cake\Core\Configure;
    use Cake\Routing\Router;
    use Lil\Lib\LilFloatEngine;
?>
<!DOCTYPE html>
<head>
    <?= $this->Html->charset() ?>
    <?php
        printf(
            '<title>%s</title>'  . PHP_EOL,
            strip_tags(
                implode(
                    ' :: ', array_merge(
                        array(Configure::read('Lil.appTitle')),
                        array($this->fetch('title'))
                    )
                )
            )
        );
    ?>
    <?= $this->fetch('meta') ?>
	
    <?php
    printf($this->Html->css('/lil/css/layout') . PHP_EOL);
    if ($colorScheme = Configure::read('Lil.colorScheme')) {
        printf($this->Html->css('/lil/css/lil_'. $colorScheme) . PHP_EOL);
    }
    
    printf($this->Html->css('/lil/css/jquery-ui.min') . PHP_EOL);
    //printf($this->Html->css('/lil/css/jquery.dataTables.min') . PHP_EOL);
    printf($this->Html->css('/lil/css/Aristo/Aristo') . PHP_EOL);
    printf($this->Html->css('/lil/css/lil_print',  ['media' => 'print']) . PHP_EOL);
    printf($this->Html->css('/lil/css/lil_mobile', ['media' => 'only screen and (max-device-width: 600px)']) . PHP_EOL);
    
    print ($this->fetch('css') . PHP_EOL);
    
    printf($this->Html->script('/lil/js/jquery.min') . PHP_EOL);
    printf($this->Html->script('/lil/js/jquery-ui') . PHP_EOL);
    printf($this->Html->script('/lil/js/jquery.dataTables.min') . PHP_EOL);
    printf($this->Html->script('/lil/js/jquery.mousewheel.min') . PHP_EOL);
    printf($this->Html->script('/lil/js/lil_popups') . PHP_EOL);
    printf($this->Html->script('/lil/js/lil_float') . PHP_EOL);
    
    print ($this->fetch('script') . PHP_EOL);
    
    if ($this->request->is('iphone')) {
        printf($this->Html->css('/lil/css/spinningwheel') . PHP_EOL);
        printf($this->Html->script('/lil/js/spinningwheel-min') . PHP_EOL);
        printf($this->Html->script('/lil/js/lil_date') . PHP_EOL);
    }
    if ($this->request->is('mobile')) {
        printf($this->Html->script('/lil/js/lil_mobile') . PHP_EOL);
    }
?>
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />

</head>
<body>
	<div id="container">
		<div id="header">
    <?php
    if (empty($admin_logo)) {
        $admin_logo = $this->Html->link(
            $this->Html->image('/lil/img/logo.gif'),
            '/',
            ['escape' => false]
        );
    }
            
    printf('<div id="header-logo">%s</div>' . PHP_EOL, $admin_logo);
    if ($this->request->is('mobile')) {
        echo $this->Html->image('/lil/img/menu.png', ['class' => 'popup_link', 'id' => 'popup_header-menu']);
    }
          
    if (empty($admin_title)) {
        $admin_title = __d('lil', 'Welcome');
    }
    printf('<h1>%s</h1>' . PHP_EOL, $admin_title);
    
            
    if ($currentUser) {
        $userTitle = $currentUser[Configure::read('Lil.userDisplayField')];
        if (!$userTitle) {
            $userTitle = __d('lil', 'Unknown'); 
        }
                
        printf(
            '<div id="header-user">%1$s</div>',
            $this->Html->link(
                $userTitle, '#', [
                'class' => 'popup_link',
                'id'    => 'popup_link_user'
                ]
            )
        );
                
        $popup_link_user = ['items' => [
            'settings' => [
                'title' => __d('lil', 'Settings'),
                'url' => [
                    'plugin'     => 'Lil',
                    'controller' => 'Users',
                    'action'     => 'properties'
                ]
            ],
            'logout' => [
                'title' => __d('lil', 'Logout'),
                'url' => [
                    'plugin'     => 'Lil',
                    'controller' => 'Users',
                    'action'     => 'logout'
                ]
            ]
        ]];

        $this->Lil->popup('link_user', $popup_link_user, true);
    }
    ?>
		</div>
		<div id="main-menu">
			<h1>&nbsp;</h1>
    <?php
    if (!empty($main_menu)) {
        $this->Lil->menu($main_menu);
    }
    ?>
		</div>
		<div id="content">
			<div id="sidebar">
                <?= $this->element('Lil.sidebar'); ?>
			</div>
			<div id="main">
				<?= $this->Flash->render() ?>
				<?= $this->Flash->render('auth') ?>
    <?php
    if (!isset($head_for_layout) || ($head_for_layout !== false)) {
        if (!empty($head_for_layout)) {
            if (is_string($head_for_layout)) {
                printf($this->element($head_for_layout));
            } else {
                printf($this->element($head_for_layout['element'], $head_for_layout['params']));
            }
        } else {
            printf('<div class="head"><h1>%s</h1></div>', $this->fetch('title'));
        }
    }
                
                echo $this->fetch('popups');
                echo $this->fetch('content');
                
                printf($this->Html->script('/lil/js/lil_datatables') . PHP_EOL);
    ?>
			</div>
			<div style="clear: both;"></div>
		</div>
	</div>
	<script type="text/javascript">
	    var dataTablesGlobals = {
	        <?php
	           // turn off table scrolling on mobile access
	           if ($this->request->is('mobile')) {
            ?>
                "scrollY": null,
                "drawCallback": null,
            <?php
                }
            ?>
            language: {
                "url": "<?php echo Router::url(['plugin' => 'Lil', 'controller' => 'pages', 'action' => 'datatables']); ?>"
			}
	    };
	    
		$(document).ready(function() {
			$.ajaxSetup ({ cache: false });
			
    <?php
            $locale = ini_get('intl.default_locale') ?: 'en_US';
            $nf = new NumberFormatter($locale, NumberFormatter::PATTERN_DECIMAL);
            $decimalSeparator = $nf->getSymbol(NumberFormatter::DECIMAL_SEPARATOR_SYMBOL);
            $thousandsSeparator = $nf->getSymbol(NumberFormatter::GROUPING_SEPARATOR_SYMBOL);
            
            printf('lilFloatSetup.decimalSeparator = "%s";', $decimalSeparator);
            printf('lilFloatSetup.thousandsSeparator = "%s";', $thousandsSeparator);
            
            echo $this->Lil->jsReadyOut();
    ?>
		});
		
		// Prevent jQuery UI dialog from blocking focus
		$(document).on('focusin', function(e) {
			if ($(event.target).closest(".mce-window").length) {
				e.stopImmediatePropagation();
			}
		});
	</script>
	
	<iframe id="lil_post_iframe" name="lil_post_iframe" src="about:blank" style="display:none;"></iframe>
</body>
</html>