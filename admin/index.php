<?php
/**
 * Mumble PHP Interface by Kissaki
 * Released under Creative Commons Attribution-Noncommercial License
 * http://creativecommons.org/licenses/by-nc/3.0/
 * @author Kissaki
 */

define('MUMPHPI_MAINDIR', '..');
define('MUMPHPI_SECTION', 'admin');

	// Start timer for execution time of script first
	require_once(MUMPHPI_MAINDIR.'/classes/PHPStats.php');
	PHPStats::scriptExecTimeStart();
	
	require_once(MUMPHPI_MAINDIR.'/classes/SettingsManager.php');
	require_once(MUMPHPI_MAINDIR.'/classes/DBManager.php');
	require_once(MUMPHPI_MAINDIR.'/classes/Logger.php');
	require_once(MUMPHPI_MAINDIR.'/classes/SessionManager.php');
	SessionManager::startSession();
	
	require_once(MUMPHPI_MAINDIR.'/classes/TranslationManager.php');
	require_once(MUMPHPI_MAINDIR.'/classes/ServerInterface.php');
	require_once(MUMPHPI_MAINDIR.'/classes/HelperFunctions.php');
	require_once(MUMPHPI_MAINDIR.'/classes/TemplateManager.php');
	
	if(SettingsManager::getInstance()->isDebugMode())
		error_reporting(E_ALL);
	
	if(isset($_GET['ajax'])){
		require_once(MUMPHPI_MAINDIR.'/ajax/admin.ajax.php');
		die();
	}
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	
	<title><?php echo SettingsManager::getInstance()->getSiteTitle(); ?></title>
	<meta name="description" content="<?php echo SettingsManager::getInstance()->getSiteDescription(); ?>" />
	<meta name="keywords" content="<?php echo SettingsManager::getInstance()->getSiteKeywords(); ?>" />
	
	<?php TemplateManager::parseTemplate('HTMLHead');; ?>
</head>
<body>

<?php
	
	// Parse Template
	TemplateManager::parseTemplate('header');
	echo '<div id="content">';
	
	

	
?>
	<h1>Server List</h1>
	<table>
		<thead>
			<tr>
				<th>ID</th>
				<th>Name (in Interface)</th>
				<th>Running?</th>
			</tr>
		</thead>
		<tbody>
			<?php
				$servers = ServerInterface::getInstance()->getServers();
				foreach($servers AS $server){
					$servername = SettingsManager::getInstance()->getServerName($server->id());
					$server_isRunning = $server->isRunning();
			?>
					<tr class="jqserver" id="jq_server_<?php echo $server->id(); ?>">
						<td><?php echo $server->id(); ?></td>
						<td><?php
							if(isset($servername)){
								echo $servername;
							} ?>
						</td>
						<td>
							<?php
								if($server_isRunning){
									echo '<span style="color:green;">Running</span>';
								}else{
									echo '<span style="color:darkgrey;">Not Running</span>';
								}
							?>
						</td>
						<td>
							<?php if($server_isRunning){?>
								<a class="jqlink" onclick="jq_server_stop(<?php echo $server->id(); ?>)">Stop</a>
							<?php }else{ ?>
								<a class="jqlink" onclick="jq_server_start(<?php echo $server->id(); ?>)">Start</a>
							<?php } ?>
							<a class="jqlink" onclick="jq_server_getRegistrations(<?php echo $server->id(); ?>); return false;" href="./?page=users">Show Users</a>
						</td>
					</tr><?php
				} ?>
		</tbody>
	</table>
	
	<a>Delete</a>
	<a>Show Channels</a>
	<a>Show ACLs</a>
	
	<a class="jqlink" id="server_create">Create a new Server</a>
	<div id="jq_information" style="display:none;">
		
	</div>
	<script type="text/javascript">
		$('#server_create').click(
			function(event){
				// $.get("./?ajax=server_create", { name: "John", time: "2pm" } );
				$.post("./?ajax=server_create",
					{ name: "John", time: "2pm" },
					function(data){
						$('#jq_information').show().html('Server created with ID: '+data);
					}
				);
				jq_loadPage('servers');
			});
		function jq_loadPage(page){
			$.get('./?ajax=getPage&page='+page, {},
					function(data){
						$('#content').html(data);
					}
				);
		}
		function jq_server_stop(sid){
			$.post("./?ajax=server_stop",
					{ 'sid': sid },
					function(data){
						$('#jq_information').show().html('stopped');
					}
				);
			jq_loadPage('servers');
		}
		function jq_server_start(sid){
			$.post("./?ajax=server_start",
					{ 'sid': sid },
					function(data){
						$('#jq_information').show().html('stopped');
					}
				);
			jq_loadPage('servers');
		}
		function jq_server_getRegistrations(sid){
			$.post("./?ajax=server_getRegistrations",
					{ 'sid': sid },
					function(data){
						$('body').append(data);
					}
				);
			
		}
		function center(object)
		{
			object.style.marginLeft = "-" + parseInt(object.offsetWidth / 2) + "px";
			object.style.marginTop = "-" + parseInt(object.offsetHeight / 2) + "px";
		}
		//$('#jq_information').show().html($(parent).id());
	</script>
	
<?php	
	
	echo '</div>';
	TemplateManager::parseTemplate('footer');
	
?>
</body></html>