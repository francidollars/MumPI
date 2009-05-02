<?php
/**
 * Ajax functionality
 * @author Kissaki
 */
//TODO secure this with $_SERVER['HTTP_REFERER']
//TODO secure this, preferably session data

switch($_GET['ajax']){
	case 'getPage':
		TemplateManager::parseTemplate($_GET['page']);
		break;
		
	case 'meta_showDefaultConfig':
		$config = ServerInterface::getInstance()->getDefaultConfig();
		echo '<table>';
		foreach($config AS $key=>$value){
			echo '<tr><td>'.$key.':</td><td>'.$value.'</td></tr>';
		}
		echo '</table>';
		break;
		
	case 'server_create':
		echo ServerInterface::getInstance()->createServer();
		break;
	case 'server_delete':
		ServerInterface::getInstance()->deleteServer($_POST['sid']);
		break;
	case 'server_start':
		ServerInterface::getInstance()->startServer($_POST['sid']);
		break;
	case 'server_stop':
		ServerInterface::getInstance()->stopServer($_POST['sid']);
		break;
		
	case 'server_getRegistrations':
		$users = array();
		try{
			$users = ServerInterface::getInstance()->getServerRegistrations($_POST['sid']);
		}catch(Murmur_ServerBootedException $exc){
			echo '<div class="error">Server is not running</div>';
			break;
		}
		?>
			<h2>Registrations</h2>
			<table>
				<thead>
					<tr>
						<th>User ID</th>
						<th>Username</th>
						<th>email</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
<?php				foreach($users AS $user){	?>
					<tr>
						<td><?php echo $user->playerid; ?></td>
						<td id="user_name_<?php echo $user->playerid; ?>" class="jq_editable"><?php echo $user->name; ?></td>
						<td id="user_email_<?php echo $user->playerid; ?>" class="jq_editable"><?php echo $user->email; ?></td>
						<td><a class="jqlink" onclick="jq_server_registration_remove(<?php echo $user->playerid; ?>)">remove</a></td>
					</tr>
<?php				}	?>
				</tbody>
			</table>
		<?php
		break;
	
	case 'show_onlineUsers':
		$users = array();
		try{
			$users = ServerInterface::getInstance()->getServerUsersConnected($_POST['sid']);
		}catch(Murmur_ServerBootedException $exc){
			echo '<div class="error">Server is not running</div>';
			break;
		}
?>
			<h2>Online Users</h2>
			<table>
				<thead>
					<tr>
						<th>Sess ID</th>
						<th>Reg ID</th>
						<th>Username</th>
						<th>muted?</th>
						<th>deaf?</th>
						<th>Seconds online</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
<?php				foreach($users AS $user){	?>
					<tr>
						<td><?php echo $user->session; ?></td>
						<td><?php if($user->playerid > 0) echo $user->playerid; ?></td>
						<td id="user_name_<?php echo $user->session; ?>" class="jq_editable"><?php echo $user->name; ?></td>
						<td><input id="user_mute_<?php echo $user->session; ?>" class="jq_toggleable" type="checkbox" <?php if($user->mute) echo 'checked=""' ; ?>/></td>
						<td><input id="user_deaf_<?php echo $user->session; ?>" class="jq_toggleable" type="checkbox" <?php if($user->deaf) echo 'checked=""' ; ?>/></td>
						<td id="user_email_<?php echo $user->session; ?>" class="jq_editable"><?php $on = $user->onlinesecs; if($on > 59){ echo sprintf('%.0f', $on/60).'m'; }else{ echo $on.'s'; } ?></td>
						<td><a class="jqlink" onclick="jq_server_user_kick(<?php echo $user->session; ?>)">kick</a></td>
<?php						// <a class="jqlink" onclick="jq_server_user_ban(<?php echo $user->session; ?\>)">ban</a>	?>
					</tr>
<?php				}	?>
				</tbody>
			</table>
			<script type="text/javascript">
				$('.jq_toggleable').click(
						function(event){
							var id = $(this).attr('id');
							var sub = id.substring(0, id.lastIndexOf('_'));
							var id = id.substring(id.lastIndexOf('_')+1, id.length);
							switch(sub){
								case 'user_mute':
									if($(this).attr('checked')){
										jq_server_user_mute(id);
									}else{
										jq_server_user_unmute(id);
									}
									
									break;
								case 'user_deaf':
									if($(this).attr('checked')){
										jq_server_user_deaf(id);
									}else{
										jq_server_user_undeaf(id);
									}
									break;
							}
						}
					);
			</script>
<?php
		break;
		
	case 'server_regstration_remove':
		ServerInterface::getInstance()->removeRegistration($_POST['sid'], $_POST['uid']);
		break;
	case 'server_user_mute':
		ServerInterface::getInstance()->muteUser($_POST['sid'], $_POST['sessid']);
		break;
	case 'server_user_unmute':
		ServerInterface::getInstance()->unmuteUser($_POST['sid'], $_POST['sessid']);
		break;
	case 'server_user_deaf':
		ServerInterface::getInstance()->deafUser($_POST['sid'], $_POST['sessid']);
		break;
	case 'server_user_undeaf':
		ServerInterface::getInstance()->undeafUser($_POST['sid'], $_POST['sessid']);
		break;
	case 'server_user_kick':
		ServerInterface::getInstance()->kickUser($_POST['sid'], $_POST['sessid']);
		break;
	case 'server_user_ban':
		ServerInterface::getInstance()->banUser($_POST['sid'], $_POST['sessid']);
		break;
	case 'show_server_bans':
		$bans = ServerInterface::getInstance()->getServerBans($_POST['sid']);
		echo '<h2>Bans</h2>';
		echo '<p><a>add</a></p>';
		if(count($bans)==0){
			echo 'no bans on this virtual server';
		}else{
			echo '<ul>';
			foreach($bans AS $ban){
				echo '<li>'.$ban->address.'</li>';
			}
			echo '</ul>';
		}
		break;
		
	case 'show_tree':
		$tree = ServerInterface::getInstance()->getServer($_POST['sid'])->getTree();
		HelperFunctions::showChannelTree($tree);
		break;
		
	case 'show_acl':
		
		break;
		
	case 'server_showConfig':
		$conf = ServerInterface::getInstance()->getServerConfig($_POST['sid']);
		function echoEditlink()
		{
			echo '<div class="js_link" style="float:left;"><a class="jqlink" onclick="server_config_value_edit();">edit</a></div>';
		}
?>
		<table><tbody>
			<tr class="table_headline"><td colspan="2">General</td></tr>
			<tr><td>Password</td>		<td><?php echoEditlink(); echo $conf['password']; unset($conf['password']); ?></td></tr>
			<tr><td>Users</td>			<td><?php echoEditlink(); echo $conf['users'];    unset($conf['users']); ?></td></tr>
			<tr><td>Timeout</td>		<td><?php echoEditlink(); echo $conf['timeout'];  unset($conf['timeout']); ?></td></tr>
			<tr><td>Host</td>			<td><?php echoEditlink(); echo $conf['host'];     unset($conf['host']); ?></td></tr>
			<tr><td>Port</td>			<td><?php echoEditlink(); echo $conf['port'];     unset($conf['port']); ?></td></tr>
			<tr><td>Default Channel</td><td><?php echoEditlink(); echo $conf['defaultchannel']; unset($conf['defaultchannel']); ?></td></tr>
			<tr><td>welcometext</td>	<td><?php echoEditlink(); echo $conf['welcometext']; unset($conf['welcometext']); ?></td></tr>
			
			<tr class="table_headline">	<td colspan="2"></td></tr>
			<tr><td>bandwidth</td>		<td><?php echoEditlink(); echo $conf['bandwidth']; unset($conf['bandwidth']); ?></td></tr>
			<tr><td>channelname</td>	<td><?php echoEditlink(); echo $conf['channelname']; unset($conf['channelname']); ?></td></tr>
			<tr><td>playername</td>		<td><?php echoEditlink(); echo $conf['playername']; unset($conf['playername']); ?></td></tr>
			<tr><td>obfuscate</td>		<td><?php echoEditlink(); echo $conf['obfuscate']; unset($conf['obfuscate']); ?></td></tr>
			
			<tr class="table_headline">	<td colspan="2">Server Registration</td></tr>
			<tr><td>registerhostname</td><td><?php echoEditlink(); echo $conf['registerhostname']; unset($conf['registerhostname']); ?></td></tr>
			<tr><td>registername</td>	<td><?php echoEditlink(); echo $conf['registername']; unset($conf['registername']); ?></td></tr>
			<tr><td>registerpassword</td><td><?php echoEditlink(); echo $conf['registerpassword']; unset($conf['registerpassword']); ?></td></tr>
			<tr><td>registerurl</td>	<td><?php echoEditlink(); echo $conf['registerurl']; unset($conf['registerurl']); ?></td></tr>
			
<?php
		foreach($conf AS $key=>$val)
		{
?>
			<tr>
				<td><?php echo $key; ?></td>
				<td><?php echoEditlink(); echo $val; ?></td>
			</tr>
<?php
		}
?>
		</tbody></table>
<?php
		break;
	
		
	case 'server_user_updateUsername':
		ServerInterface::getInstance()->updateUserName($_POST['sid'], $_POST['uid'], $_POST['newValue']);
		break;
	case 'server_user_updateEmail':
		ServerInterface::getInstance()->updateUserEmail($_POST['sid'], $_POST['uid'], $_POST['newValue']);
		break;
	
	case 'server_config_get':
		ServerInterface::getInstance()->getServerConfig($_POST['sid']);
		break;
	
	case 'meta_server_information_edit':
		$server = SettingsManager::getInstance()->getServerInformation($_POST['serverid']);

		echo '<div>';
		if($server === null)
		{
			echo 'new:<br/>';
			$server['name']              = '';
			$server['allowlogin']        = true;
			$server['allowregistration'] = true;
			$server['forcemail']         = true;
			$server['authbymail']        = true;
		}
		echo	'<table>';
		echo		'<tr><td>name</td>	<td><input type="text" id="meta_server_information_name" name="meta_server_information_name" value="'.$server['name'].'" /></td></tr>';
		echo		'<tr><td>Allow Login</td>	<td><input type="checkbox" id="meta_server_information_allowlogin" name="meta_server_information_allowlogin" '.($server['allowlogin'] ? 'checked="checked"' : '').'" /></tr>';
		echo		'<tr><td>Allow Registration</td>	<td><input type="checkbox" id="meta_server_information_allowregistration" name="meta_server_information_allowregistration" '.($server['allowregistration'] ? 'checked="checked"' : '').'" /></tr>';
		echo		'<tr><td>Force eMail</td>	<td><input type="checkbox" id="meta_server_information_forcemail" name="meta_server_information_forcemail" '.($server['forcemail'] ? 'checked="checked"' : '').'" /></tr>';
		echo		'<tr><td>Auth by Mail</td>	<td><input type="checkbox" id="meta_server_information_authbymail" name="meta_server_information_authbymail" '.($server['authbymail'] ? 'checked="checked"' : '').'" /></tr>';
		echo	'</table>';
		echo	'<input type="button" value="update" onclick="jq_meta_server_information_update('.$_POST['serverid'].');" />';
		echo '</div>';
		break;
	
	case 'meta_server_information_update':
		if(isset($_POST['name']) && isset($_POST['allowlogin']) && isset($_POST['allowregistration']) && isset($_POST['forcemail']) && isset($_POST['authbymail']) )
		{
			SettingsManager::getInstance()->setServerInformation($_POST['serverid'], $_POST['name'], $_POST['allowlogin'], $_POST['allowregistration'], $_POST['forcemail'], $_POST['authbymail']);
		}else{
			echo '<div class="error">It seems not all necessary values have been specified.</div>';
		}
		break;
		
		
}

?>