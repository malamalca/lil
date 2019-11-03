<?php
use Cake\Routing\Router;
/**
 * This is areas/admin_dashboard template file. 
 *
 * @copyright     Copyright 2008-2010, LilCake.net
 * @link          http://lilcake.net LilCake.net
 * @package       lil
 * @subpackage    lil.views.areas
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
	$area_dashboard = [
		'title_for_layout' => __d('lil', 'Welcome. What would you like to do?'),
		'menu'  => [],
		'pre'   => '<div id="DashboardPanels">',
		'post'  => '</div>',
		'panels' => [
			'welcome' => [
				'pre' => '<div id="AdminDashboard" class="no-margin">',
				'post' => '</div>',
				'lines' => [
					'preferences' => [
						'text'    => $this->Html->link(
							__d('lil', 'Edit user profile and password'),
							[
								'plugin'     => 'Lil',
								'controller' => 'Users',
								'action'     => 'properties',
							],
							[ 'class' => 'small' ]
						),
					],
					'logout' => [
						'text'    => $this->Html->link(
							__d('lil', 'Logout'),
							[
								'plugin'     => 'Lil',
								'controller' => 'Users',
								'action'     => 'logout',
							],
							[
								'class' => 'small'
							]
						),
					],
				]
			],
		],
	];
	
	///////////////////////////////////////////////////////////////////////////////////////////////
	// call plugin handlers
	//$area_dashboard = $this->callPluginHandlers('admin_dashboard', $area_dashboard);
	
	if (!empty($order)) {
		$order = explode(',', $order);
		$panels = $area_dashboard['panels'];
		$area_dashboard['panels'] = array();
		
		foreach ($order as $panel) {
			if (isset($panels[$panel])) {
				$area_dashboard['panels'][$panel] = $panels[$panel];
				unset($panels[$panel]);
			}
		}
		
		$area_dashboard['panels'] += $panels;
	}
	

	echo $this->Lil->panels($area_dashboard);
?>
<script type="text/javascript">
	var settingsOrderUrl = "<?php echo Router::url(array(
		'plugin'     => 'Lil',
		'controller' => 'Areas',
		'action'     => 'dashboard_order',
		'[[order]]'
	)); ?>";
	
	$(document).ready(function() {
		$("#DashboardPanels").sortable({
			handle: "h1",
			update: function( event, ui ) {
				var panels = $('#DashboardPanels > div').map(function(){
					return this.id;
				}).get();
				
				var rx_order = new RegExp("(\\%5B){2}order(\\%5D){2}", "i");
				
				var jqxhr = $.get(
					settingsOrderUrl
						.replace(rx_order, panels.join(','))
				);
			}
		});
	});
</script>