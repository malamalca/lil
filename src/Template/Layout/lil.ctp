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

    printf($this->Html->css('/lil/js/jquery-ui/jquery-ui.min') . PHP_EOL);
    printf($this->Html->css('/lil/js/datatables/css/dataTables.jqueryui.min') . PHP_EOL);
    printf($this->Html->css('/lil/js/responsive/css/responsive.jqueryui.min') . PHP_EOL);
    printf($this->Html->css('/lil/css/Aristo/Aristo') . PHP_EOL);
    printf($this->Html->css('/lil/css/spinningwheel') . PHP_EOL);
    printf($this->Html->css('/lil/css/lil_print',  ['media' => 'print']) . PHP_EOL);
    printf($this->Html->css('/lil/css/lil_mobile', ['media' => 'only screen and (max-device-width: 600px)']) . PHP_EOL);

    print ($this->fetch('css') . PHP_EOL);

    printf($this->Html->script('/lil/js/jquery.min') . PHP_EOL);
    printf($this->Html->script('/lil/js/jquery-ui/jquery-ui.min') . PHP_EOL);
    printf($this->Html->script('/lil/js/datatables/js/jquery.dataTables.min') . PHP_EOL);
    printf($this->Html->script('/lil/js/datatables/js/dataTables.jqueryui.min') . PHP_EOL);
    printf($this->Html->script('/lil/js/responsive/js/dataTables.responsive.min') . PHP_EOL);
    printf($this->Html->script('/lil/js/responsive/js/responsive.jqueryui.min') . PHP_EOL);
    printf($this->Html->script('/lil/js/jquery.mousewheel.min') . PHP_EOL);
    printf($this->Html->script('/lil/js/lil_datatables') . PHP_EOL);
    printf($this->Html->script('/lil/js/lil_popups') . PHP_EOL);
    printf($this->Html->script('/lil/js/lil_float') . PHP_EOL);
    printf($this->Html->script('/lil/js/lil_date') . PHP_EOL);

    print ($this->fetch('script') . PHP_EOL);

    if ($this->getRequest()->is('mobile')) {
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
        $admin_logo = $this->Html->link($this->Html->image('/lil/img/logo.gif'), '/', ['escape' => false]);
    }
    printf('<div id="header-logo">%s</div>' . PHP_EOL, $admin_logo);

    if ($this->getRequest()->is('mobile')) {
        echo '<div class="popup_link" id="popup_header-menu">';
        echo $this->Html->image('/lil/img/menu.png');
        echo '</div>';
    }

    if (empty($admin_title)) {
        $admin_title = __d('lil', 'Welcome');
    }
    printf('<h1>%s</h1>' . PHP_EOL, $admin_title);


    if (!empty($currentUser)) {
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
        $main_menu = ['items' => $main_menu];
        echo $this->Lil->menu($main_menu);
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
                <?= $this->element('Lil.head'); ?>
                <?= $this->fetch('popups'); ?>
                <?= $this->fetch('content'); ?>
            </div>
        <div style="clear: both;"></div>
        </div>
    </div>
    <script type="text/javascript">
        var dataTablesGlobals = {
            <?php
               // turn off table scrolling on mobile access
               if ($this->getRequest()->is('mobile')) {
            ?>
                "scrollY": null,
            <?php
                }
            ?>
            dateSettings: {
                "dateFormat": "<?= Configure::read('Lil.dateFormat'); ?>",
                "dateSeparator": "<?= Configure::read('Lil.dateSeparator'); ?>"
            },
            language: {
                "url": "<?php echo Router::url(['plugin' => 'Lil', 'controller' => 'Pages', 'action' => 'datatables']); ?>"
            }
        };

        // lilFloatSetup should be executed before $(document).ready();
        <?php $formatter = $this->Number->formatter(); ?>

        lilFloatSetup.decimalSeparator = "<?= $formatter->getSymbol(\NumberFormatter::DECIMAL_SEPARATOR_SYMBOL); ?>";
        lilFloatSetup.thousandsSeparator = "<?= $formatter->getSymbol(\NumberFormatter::GROUPING_SEPARATOR_SYMBOL); ?>";


        $(document).ready(function() {
            initDatatables();

            $.ajaxSetup ({ cache: false });

            <?= $this->Lil->jsReadyOut(); ?>
        });

        // Prevent jQuery UI dialog from blocking focus
        $(document).on('focusin', function(e) {
            if ($(event.target).closest(".mce-window").length) {
                e.stopImmediatePropagation();
            }
        });
    </script>

    <?php
        echo '<iframe id="lil_post_iframe" name="lil_post_iframe" src="about:blank" style="display:none;"></iframe>';
    ?>
</body>
</html>
