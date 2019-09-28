<?php


if (!defined("WHMCS"))
	die("This file cannot be accessed directly");


define('JETCSFMANAGER', true);
define('ITFINDEN_CSF_MANAGER', true);

use WHMCS\Database\Capsule;

function sql_exec($sql){
	$pdo = Capsule::connection()->getPdo();

	$stmt = $pdo->prepare($sql);

	if($stmt){
		$stmt->execute();
	}
}

function sql_select($sql){
	$pdo = Capsule::connection()->getPdo();

	$stmt = $pdo->prepare($sql);

	if($stmt){			
		$stmt->execute($values);

		if($stmt->rowCount() > 0)
			$result[] = $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	return $result ?? false;
}



if(!defined('JCSF_ROOT_PATH'))  define('JCSF_ROOT_PATH', dirname(__FILE__));
if(!defined('WHMCS_ROOT_PATH')) define('WHMCS_ROOT_PATH', realpath(JCSF_ROOT_PATH . '/../'));
	
function itfinden_csf_manager_config() 
{
	return array(
		'name' 		=> 'ITFINDEN CSF Manager',
		'description' 	=> 'Manage your servers ConfigServer Firewall & Security',
		'version' 	=> '2.0.0',
		'author' 	=> 'Itfinden and Jorge Arana',
		'language' 	=> 'english',
	);
}

function itfinden_csf_manager_activate() 
{
	
	$sql = "CREATE TABLE IF NOT EXISTS `mod_csfmanager_config` (
			`name` varchar(255) NOT NULL,
			`value` text NOT NULL,
			PRIMARY KEY (`name`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";

	sql_exec($sql);
	#sql_exec($sql);

	$sql = "INSERT INTO `mod_csfmanager_config` (`name`, `value`) VALUES
		('permission_firewall', '1'),
		('permission_unblock', '1'),
		('permission_allow', '1'),
		('permission_allowemail', '1'),
		('permission_aunblock', '1'),
		('allowlength', '7'),
		('allowlength_type', 'days'),
		('checkbrute', '1'),
		('version_check', '0'),
		('version_new', ''),
		('servers', '')";
	
	sql_exec($sql);
	#sql_exec($sql);

	$sql = "CREATE TABLE IF NOT EXISTS `mod_csfmanager_allow` (
			`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`clientid` int(11) unsigned NOT NULL DEFAULT '0',
			`serverid` int(11) unsigned NOT NULL DEFAULT '0',
			`ip` varchar(255) NOT NULL,
			`time` int(11) unsigned NOT NULL DEFAULT '0',
			`expiration` int(11) unsigned NOT NULL DEFAULT '0',
			`reason` varchar(255) NOT NULL,
			PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
	
	sql_exec($sql);
	#sql_exec($sql);

	$sql = "CREATE TABLE IF NOT EXISTS `mod_csfmanager_allow_keys` (
			`key_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`user_id` int(11) unsigned NOT NULL DEFAULT '0',
			`server_id` int(11) unsigned NOT NULL DEFAULT '0',
			`product_id` int(11) unsigned NOT NULL DEFAULT '0',
			`key_hash` varchar(40) NOT NULL,
			`key_email` varchar(255) NOT NULL,
			`key_recipient` varchar(255) NOT NULL,
			`key_clicks_remained` int(11) unsigned NOT NULL DEFAULT '0',
			`key_expire` int(11) unsigned NOT NULL DEFAULT '0',
			`key_cancelled` tinyint(1) unsigned NOT NULL DEFAULT '0',
			PRIMARY KEY (`key_id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
	
	sql_exec($sql);
	#sql_exec($sql);

	$sql = "INSERT INTO `tblemailtemplates` (`type`, `name`, `subject`, `message`, `attachments`, `fromname`, `fromemail`, `disabled`, `custom`, `language`, `copyto`, `plaintext`) VALUES
		('general', 'CSF Manager Whitelist by Email', 'IP Whitelist request', '<p class=\"MsoNormal\" style=\"text-align: left; direction: ltr;\">Hello {\$emailfullname},</p><p class=\"MsoNormal\" style=\"text-align: left; direction: ltr;\"><span lang=\"HE\" dir=\"RTL\">&nbsp;</span></p><p class=\"MsoNormal\" style=\"text-align: left; direction: ltr;\">This a request on behalf of <strong>{\$firstname} {\$lastname}</strong> to whitelist your ip address in our servers.</p><p class=\"MsoNormal\" style=\"text-align: left; direction: ltr;\">Please visit the following URL to do so:</p><br /><p class=\"MsoNormal\" style=\"text-align: left; direction: ltr;\">{\$whitelist_url}</p><br /><p class=\"MsoNormal\" style=\"text-align: left; direction: ltr;\">The URL will be valid for <strong>{\$valid_days}</strong> days, you may click it for <strong>{\$valid_clicks}</strong> times in that time frame.</p><p class=\"MsoNormal\" style=\"text-align: left; direction: ltr;\">&nbsp;</p><p class=\"MsoNormal\" style=\"text-align: left; direction: ltr;\">This email and any files transmitted with it are confidential and intended solely for the use of the individual or entity to whom they are addressed. If you have received this email in error please notify the system manager by clickng the following URL:</p><br /><p class=\"MsoNormal\" style=\"text-align: left; direction: ltr;\">{\$cancel_url}</p><br /><p class=\"MsoNormal\" style=\"text-align: left; direction: ltr;\">This message contains confidential information and is intended only for the individual named. If you are not the named addressee you should not disseminate, distribute or copy this e-mail. Please notify the sender immediately by e-mail if you have received this e-mail by mistake and delete this e-mail from your system. If you are not the intended recipient you are notified that disclosing, copying, distributing or taking any action in reliance on the contents of this information is strictly prohibited.</p><p class=\"MsoNormal\" style=\"text-align: left; direction: ltr;\">&nbsp;</p><p class=\"MsoNormal\" style=\"text-align: left; direction: ltr;\">{\$signature}</p>', '', '', '', '', '', '', '', 0),
		('general', 'CSF Manager key cancelled', 'CSF Manager Cancelled allow key', 'Dear admin,<br><br>** This is an automated message generated by WHMCS CSF Manager **<br>&nbsp;<br>We would like to notify you that following key has been cancelled by the email recipient -<br><br>Client: {\$client_name}<br>Recipient Name: {\$recipient_name}<br>Recipient Email: {\$recipient_email}<br>Allow Key ID: {\$allow_key_id}<br>Allow Key (MD5): {\$allow_key_md5}<br>Server Hostname: {\$server_hostname}<br>&nbsp;<br>This is just a notification message, and doesn\'t require any action from your side', '', '', '', '', '', '', '', 0)";
	
	sql_exec($sql);
	#sql_exec($sql);

	return array(
		'status'	=> 'success',
		'description'	=> 'Module activated successfully'
	);
}

function itfinden_csf_manager_deactivate() 
{
	
	sql_exec("DROP TABLE IF EXISTS `mod_csfmanager_config`");
	sql_exec("DROP TABLE IF EXISTS `mod_csfmanager_allow`");
	#sql_exec("DROP TABLE IF EXISTS `mod_csfmanager_config`");
	#sql_exec("DROP TABLE IF EXISTS `mod_csfmanager_allow`");

	return array(
		'status'	=> 'success',
		'description'	=> 'Module deactivated successfully'
	);
}

function itfinden_csf_manager_upgrade($vars) 
{
	$version = $vars['version'];

	if(version_compare($version, '1.0.1', '<'))
	{
		$sql = "DELETE
			FROM `mod_csfmanager_config`
			WHERE name = 'server_types'";
		
		sql_exec($sql);
		#sql_exec($sql);

		$sql = "INSERT INTO `mod_csfmanager_config` (`name`, `value`) VALUES
			('servers', '')";
		
		sql_exec($sql);
		#sql_exec($sql);
	}

	if(version_compare($version, '1.0.7', '<'))
	{
		$sql = "CREATE TABLE IF NOT EXISTS `mod_csfmanager_allow_keys` (
				`key_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`user_id` int(11) unsigned NOT NULL DEFAULT '0',
				`server_id` int(11) unsigned NOT NULL DEFAULT '0',
				`product_id` int(11) unsigned NOT NULL DEFAULT '0',
				`key_hash` varchar(40) NOT NULL,
				`key_email` varchar(255) NOT NULL,
				`key_recipient` varchar(255) NOT NULL,
				`key_clicks_remained` int(11) unsigned NOT NULL DEFAULT '0',
				`key_expire` int(11) unsigned NOT NULL DEFAULT '0',
				`key_cancelled` tinyint(1) unsigned NOT NULL DEFAULT '0',
				PRIMARY KEY (`key_id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
		
		sql_exec($sql);
		#sql_exec($sql);

		$sql = "INSERT INTO `mod_csfmanager_config` (`name`, `value`) VALUES
			('permission_allowemail', '1')";
		
		sql_exec($sql);
		#sql_exec($sql);

		$sql = "INSERT INTO `tblemailtemplates` (`type`, `name`, `subject`, `message`, `attachments`, `fromname`, `fromemail`, `disabled`, `custom`, `language`, `copyto`, `plaintext`) VALUES
			('general', 'CSF Manager Whitelist by Email', 'IP Whitelist request', '<p class=\"MsoNormal\" style=\"text-align: left; direction: ltr;\">Hello {\$emailfullname},</p><p class=\"MsoNormal\" style=\"text-align: left; direction: ltr;\"><span lang=\"HE\" dir=\"RTL\">&nbsp;</span></p><p class=\"MsoNormal\" style=\"text-align: left; direction: ltr;\">This a request on behalf of <strong>{\$firstname} {\$lastname}</strong> to whitelist your ip address in our servers.</p><p class=\"MsoNormal\" style=\"text-align: left; direction: ltr;\">Please visit the following URL to do so:</p><br /><p class=\"MsoNormal\" style=\"text-align: left; direction: ltr;\">{\$whitelist_url}</p><br /><p class=\"MsoNormal\" style=\"text-align: left; direction: ltr;\">The URL will be valid for <strong>{\$valid_days}</strong> days, you may click it for <strong>{\$valid_clicks}</strong> times in that time frame.</p><p class=\"MsoNormal\" style=\"text-align: left; direction: ltr;\">&nbsp;</p><p class=\"MsoNormal\" style=\"text-align: left; direction: ltr;\">This email and any files transmitted with it are confidential and intended solely for the use of the individual or entity to whom they are addressed. If you have received this email in error please notify the system manager by clickng the following URL:</p><br /><p class=\"MsoNormal\" style=\"text-align: left; direction: ltr;\">{\$cancel_url}</p><br /><p class=\"MsoNormal\" style=\"text-align: left; direction: ltr;\">This message contains confidential information and is intended only for the individual named. If you are not the named addressee you should not disseminate, distribute or copy this e-mail. Please notify the sender immediately by e-mail if you have received this e-mail by mistake and delete this e-mail from your system. If you are not the intended recipient you are notified that disclosing, copying, distributing or taking any action in reliance on the contents of this information is strictly prohibited.</p><p class=\"MsoNormal\" style=\"text-align: left; direction: ltr;\">&nbsp;</p><p class=\"MsoNormal\" style=\"text-align: left; direction: ltr;\">{\$signature}</p>', '', '', '', '', '', '', '', 0),
			('general', 'CSF Manager key cancelled', 'CSF Manager Cancelled allow key', 'Dear admin,<br><br>** This is an automated message generated by WHMCS CSF Manager **<br>&nbsp;<br>We would like to notify you that following key has been cancelled by the email recipient -<br><br>Client: {\$client_name}<br>Recipient Name: {\$recipient_name}<br>Recipient Email: {\$recipient_email}<br>Allow Key ID: {\$allow_key_id}<br>Allow Key (MD5): {\$allow_key_md5}<br>Server Hostname: {\$server_hostname}<br>&nbsp;<br>This is just a notification message, and doesn\'t require any action from your side', '', '', '', '', '', '', '', 0)";
		
		sql_exec($sql);
		#sql_exec($sql);
	}
}

function itfinden_csf_manager_output($vars) 
{
	global $whmcs, $cc_encryption_hash, $LANG, $CONFIG, $_LANG;

	require_once(JCSF_ROOT_PATH . '/includes/functions.php');
	require_once(JCSF_ROOT_PATH . '/includes/class_firewall.php');
	require_once(JCSF_ROOT_PATH . '/includes/class_cpanel.php');
	require_once(JCSF_ROOT_PATH . '/includes/ganon/ganon.php');

	$modulelink = $vars['modulelink'];
	$LANG = array_merge($_LANG, $vars['_lang']);

	$pages = array('firewall'/*,'broadcast'*/,'allowedlog','allowkeys','generatekey','settings');
	
	$id = csfmanager::request_var('id', 0);
	$pagename = csfmanager::request_var('pagename', 'firewall', $pages);
	$view = csfmanager::request_var('view', '');
	$action = csfmanager::request_var('action', '');
	$page = csfmanager::request_var('page', 0);	
	$global_success = csfmanager::request_var('success', '');
	$global_error = csfmanager::request_var('error', '');
	
	$instance = csfmanager::getInstance();
	$new_version = '';
		
	if($instance->getConfig('version_check') < (time() - (60 * 60 * 24)))
	{
		$newversion = file_get_contents('http://jetlicense.com/versions/jetservercsfmanager.txt');
	
		if(trim($newversion))
		{
			$instance->setConfig('version_new', trim($newversion));
			$instance->setConfig('version_check', time());
		}
	}
	
	if(version_compare($vars['version'], $instance->getConfig('version_new')) < 0)
	{
		$new_version = $instance->getConfig('version_new');
	}

	$view_class = "{$pagename}_default";
	$default_view = JCSF_ROOT_PATH . "/views/{$view_class}.php";
	
	if(file_exists($default_view))
	{
		require_once($default_view);
	
		if($view && $view != 'default')
		{
			// load the requested view
			$view_class = "{$pagename}_{$view}";
			$view_file = JCSF_ROOT_PATH . "/views/{$view_class}.php";
	
			if(file_exists($view_file))
			{
				require_once($view_file);
			}
			else
			{
				csfmanager::trigger_message(false, 'The requested view not exists');
			}
		}
	
		if(!defined('JCSF_TRIGGER'))
		{
			$view_class = "jcsf_{$view_class}";
			$module = new $view_class;
	
			$default_response = $module->_default();
	
			if(isset($default_response['success']) && $default_response['success'])
			{
				if($action)
				{
					if(method_exists($module, $action))
					{
						$action_response = $module->$action();
	
						if(isset($action_response['errormessages']) && sizeof($action_response['errormessages']))
						{
							$template_file = JCSF_ROOT_PATH . "/template/{$pagename}_" . ($view ? $view : 'default') . ".php";
	
							if(file_exists($template_file))
							{
								require_once($template_file);
							}
							else
							{
								csfmanager::trigger_message(false, "The file {$template_file} is missing!");
							}
						}
						else
						{
							header('Location: ' . $modulelink . '&pagename=' . $pagename . ($page ? '&page=' . $page : '') . ($filter ? '&filter=1' : '') . '&' . ($action_response['success'] ? 'success=' : 'error=') . $action_response['message']);
							exit;
						}
					}
					else
					{
						csfmanager::trigger_message(false, "Invalid action provided");
					}
				}
				else
				{
					$action_response['data'] = $default_response['data'];
	
					$template_file = JCSF_ROOT_PATH . "/template/{$pagename}_" . ($view ? $view : 'default') . ".php";
	
					if(file_exists($template_file))
					{
						require_once($template_file);
					}
					else
					{
						csfmanager::trigger_message(false, "The file {$template_file} is missing!");
					}
				}
			}
			else
			{
				csfmanager::trigger_message(false, nl2br($default_response['message']), E_USER_WARNING);
			}
		}
	}
	else
	{
		csfmanager::trigger_message(false, 'The file ' . $default_view . ' is missing!');
	}
	
	if(defined('JCSF_TRIGGER'))
	{
		$template_file = JCSF_ROOT_PATH . "/template/message.php";
	
		if(file_exists($template_file))
		{
			require_once($template_file);
		}
		else
		{
?>
	<div class="errorbox">
		<strong><span class="title">Error!</span></strong><br />
		The file <?php echo $template_file; ?> is missing!
	</div>
	<?php
		}
	}
}

function itfinden_csf_manager_clientarea($vars) 
{
	global $whmcs, $CONFIG, $_LANG;
		
	$modulelink = $vars['modulelink'];
	$version = $vars['version'];
	$LANG = array_merge($_LANG, $vars['_lang']);

	$breadcrumb = array(
		'index.php?m=csfmanager' 	=> $LANG['csfmanagertitle'],
	);

	$output = array();
	$tplfile = '';

	require_once(JCSF_ROOT_PATH . '/includes/functions.php');
	require_once(JCSF_ROOT_PATH . '/includes/class_firewall.php');
	require_once(JCSF_ROOT_PATH . '/includes/class_cpanel.php');
	require_once(JCSF_ROOT_PATH . '/includes/ganon/ganon.php');
	
	$instance = csfmanager::getInstance();

	$pid 	= csfmanager::request_var('id', 0);
	$uid 	= intval($_SESSION['uid']);
	$aid 	= intval($_SESSION['adminid']);

	$submit = isset($_REQUEST['submit']);

	$cip	= trim($_SERVER['REMOTE_ADDR']);
	$page 	= csfmanager::request_var('page');
	$action = csfmanager::request_var('action');
	$email 	= trim($_REQUEST['email']);
	$ip 	= trim($_REQUEST['ip']);
	$key 	= trim($_REQUEST['key']);

	if($instance->getConfig('permission_allowemail') && $action == 'allow' && strlen($key) == 32)
	{

		$sql = "SELECT *
			FROM mod_csfmanager_allow_keys
			WHERE key_hash = '" . mysqli_escape_string($key) . "'
			AND key_clicks_remained > 0
			AND key_expire > '" . time() . "'
			AND key_cancelled = 0";
		$result = sql_exec($sql);
		$key_details = mysqli_fetch_assoc($result);

		if($key_details)
		{
			$ip = $cip;
			$reason = "Added by WHMCS CSF Manager by email key id {$key_details['key_id']}";

			if($ip)
			{
				$sql = "SELECT h.id, h.domain, s.id as server_id, s.name, s.hostname, s.username, s.password, s.accesshash, s.secure, p.type
					FROM tblhosting as h
					INNER JOIN tblproducts as p
					ON h.packageid = p.id
					AND p.type IN ('hostingaccount','reselleraccount','server')
					INNER JOIN tblservers as s
					ON h.server = s.id
					" . (trim($instance->getConfig('servers')) ? "AND s.id IN (" . $instance->getConfig('servers') . ")" : '') . "
					WHERE h.domainstatus = 'Active'
					AND h.id = '{$key_details['product_id']}'"; 
				$result = sql_exec($sql);
				$product_details = mysqli_fetch_assoc($result);

				if($product_details)
				{
					$product_details['password'] = decrypt($product_details['password'], $cc_encryption_hash);
					$response = csfmanager::checkCsfAlive($product_details);

					if(!$response['success'])
					{
						$output['errors'][] = $response['message'];
					}
					else
					{
						$Firewall = new Firewall($LANG);
						$Firewall->setWHMdetails($product_details);

						if($Firewall->setIP($ip))
						{
							if(intval($instance->getConfig('allowlength')) > 0 && in_array($instance->getConfig('allowlength_type'), array('seconds','minutes','hours','days')))
							{
								$allow = $Firewall->temporaryAllow($instance->getConfig('allowlength'), $instance->getConfig('allowlength_type'), $reason);

								if($allow['success'])
								{
									$html = str_get_dom($allow['output']);

									$response = $html('pre', 0)->html();

									if(strpos($response, "{$ip} allowed") !== false)
									{
										logActivity("Jetserver CSF Manager :: The IP {$ip} whitelisted by email key id {$key_details['key_id']}");
										$output['successes'][] = sprintf($LANG['tempallowipok'], $ip, $instance->getConfig('allowlength'), $instance->lang(strtolower($instance->getConfig('allowlength_type'))));

										$allowlength = array(
											'seconds'	=> 1,
											'minutes'	=> 60,
											'hours'		=> (60 * 60),
											'days'		=> (60 * 60 * 24),
										);

										$now = time();

										$sql = "INSERT INTO mod_csfmanager_allow (`clientid`,`serverid`,`ip`,`time`,`expiration`,`reason`) VALUES
											('{$key_details['user_id']}','{$product_details['server_id']}','{$ip}','{$now}','" . ($now + ($allowlength[strtolower($instance->getConfig('allowlength_type'))] * $instance->getConfig('allowlength'))) . "','" . mysqli_escape_string($reason) . "')";
										sql_exec($sql);

										$sql = "UPDATE mod_csfmanager_allow_keys
											SET key_clicks_remained = `key_clicks_remained`-1
											WHERE key_id = '{$key_details['key_id']}'";
										sql_exec($sql);
									}
									else
									{
										$message = str_replace('csf: ', '', strip_tags($response));

										logActivity("Jetserver CSF Manager :: The IP {$ip} wasen't whitelisted by email key id {$key_details['key_id']} :: {$message}");
										$output['errors'][] = $message;
									}
								}
								else
								{
									$output['errors'][] = $allow['message'];
								}
							}
							else
							{
								if(intval($instance->getConfig('allowlength')) <= 0) $output['errors'][] = $LANG['lengthinvalid'];
								if(!in_array($instance->getConfig('allowlength_type'), array('seconds','minutes','hours','days'))) $output['errors'][] = $LANG['lengthtypeinvalid'];
							}
						}
						else
						{
							$output['errors'][] = sprintf($LANG['tempallowiperror'], $ip);
						}
					}
				}
				else
				{
					$output['errors'][] = sprintf($LANG['noservice'], $key_details['product_id']);
				}
			}
			else
			{
				$output['errors'][] = $LANG['noipprovided'];
			}
		}
		else
		{
			$output['errors'][] = "The provided key not exists or already expired";
		}

		$tplfile = 'csfmanagerallowconfirm';
	}
	elseif($instance->getConfig('permission_allowemail') && $action == 'cancel' && strlen($key) == 32)
	{
		$sql = "SELECT *
			FROM mod_csfmanager_allow_keys
			WHERE key_hash = '" . mysqli_escape_string($key) . "'
			AND key_clicks_remained > 0
			AND key_expire > '" . time() . "'
			AND key_cancelled = 0";
		$result = sql_exec($sql);
		$key_details = mysqli_fetch_assoc($result);

		if($key_details)
		{
			$sql = "SELECT h.id, h.domain, s.id as server_id, s.name, s.hostname, s.username, s.password, s.accesshash, s.secure, p.type, c.firstname, c.lastname
				FROM tblhosting as h
				INNER JOIN tblproducts as p
				ON h.packageid = p.id
				AND p.type IN ('hostingaccount','reselleraccount','server')
				INNER JOIN tblservers as s
				ON h.server = s.id
				" . (trim($instance->getConfig('servers')) ? "AND s.id IN (" . $instance->getConfig('servers') . ")" : '') . "
				INNER JOIN tblclients as c
				ON h.userid = c.id
				WHERE h.domainstatus = 'Active'
				AND h.id = '{$key_details['product_id']}'"; 
			$result = sql_exec($sql);
			$product_details = mysqli_fetch_assoc($result);

			sendAdminMessage('CSF Manager key cancelled', array(
				'client_name'		=> $product_details['firstname'] . ' ' . $product_details['lastname'],
				'recipient_name'	=> $key_details['key_recipient'],
				'recipient_email'	=> $key_details['key_email'],
				'allow_key_md5'		=> $key_details['key_hash'],
				'allow_key_id'		=> $key_details['key_id'],
				'server_hostname'	=> $product_details['hostname'],
			));

			$sql = "UPDATE mod_csfmanager_allow_keys
				SET key_cancelled = 1
				WHERE key_id = '{$key_details['key_id']}'";
			sql_exec($sql);

			logActivity("Jetserver CSF Manager :: The allow key {$key_details['key_hash']} (#{$key_details['key_id']}) was cancelled by his recipient");
			$output['successes'][] = "Thank you. the Key was cancelled successfully";
		}
		else
		{
			$output['errors'][] = "The provided key not exists or already expired";
		}

		$tplfile = 'csfmanagererror';
	}
	elseif($uid)
	{
		if($pid)
		{
			$pages = array('firewall','unblock','allow','allowemail');
			$allowed_pages = array();

			foreach($pages as $i => $pagename)
			{
				if($instance->getConfig('permission_' . $pagename))
				{
					$allowed_pages[] = $pagename;
				}
			}

			if($instance->getConfig('permission_allow') || $instance->getConfig('permission_allowemail'))
			{
				$allowed_pages[] = 'whitelisted';
				$allowed_pages[] = 'emailkeys';
			}

			$page = in_array($page, $allowed_pages) ? $page : $allowed_pages[0];

			$menu = Menu::primarySidebar();

			$ipbox = $menu->addChild('Service Details IP Address', array(
				'label' 		=> $LANG['youripaddress'],
				'uri' 			=> '#',
				'icon' 			=> 'fa-lock',
				'childrenAttributes'	=> array(
					'class' 		=> 'list-group-tab-nav',
				),
				'attributes'	=> array(
					'class'			=> 'panel-default panel-actions',
				),
			));

			$ipbox->setBodyHtml($cip);

			$firewall_menu = $menu->addChild('Service Details Firewall', array(
				'label' 		=> $LANG['csfmanagertitle'],
				'uri' 			=> '#',
				'icon' 			=> 'fa-fire',
				'childrenAttributes'	=> array(
					'class' 		=> 'list-group-tab-nav',
				),
				'attributes'	=> array(
					'class'			=> 'panel-default panel-actions',
				),
			));


			foreach($allowed_pages as $i => $allowed_page)
			{
				$firewall_menu->addChild(ucfirst($allowed_page), array(
					'label' 	=> $instance->lang($allowed_page . 'tab'),
					'uri' 		=> $modulelink . '&page=' . $allowed_page . '&id=' . $pid,
					'order' 	=> $i,
					'current'	=> $page == $allowed_page ? true : false,
				));
			}

			if(sizeof($pages))
			{

				$sql = "SELECT h.id, h.domain, s.id as server_id, s.name, s.hostname, s.username, s.password, s.accesshash, s.secure, p.type, c.firstname, c.lastname, c.id as client_id, p.name as product_name, g.name as group_name
					FROM tblhosting as h
					INNER JOIN tblproducts as p
					ON h.packageid = p.id
					AND p.type IN ('hostingaccount','reselleraccount','server')
					INNER JOIN tblproductgroups as g
					ON p.gid = g.id
					INNER JOIN tblservers as s
					ON h.server = s.id
					" . (trim($instance->getConfig('servers')) ? "AND s.id IN (" . $instance->getConfig('servers') . ")" : '') . "
					INNER JOIN tblclients as c
					ON c.id = h.userid
					WHERE h.domainstatus = 'Active'
					AND h.id = '{$pid}'
					AND h.userid = '{$uid}'"; 
				$result = sql_exec($sql);
				$product_details = mysqli_fetch_assoc($result);

				if($product_details)
				{
					$product_details['password'] = decrypt($product_details['password'], $cc_encryption_hash);
					$output['server'] = $product_details['name'];

					$response = csfmanager::checkCsfAlive($product_details);

					if(!$response['success'])
					{
						$output['errors'][] = $response['message'];
						$tplfile = 'csfmanagererror';
					}
					else
					{
						$cgifile = $response['cgifile'];

						$html = str_get_dom($response['data']);

						preg_match("/Firewall Status:([a-zA-Z0-9\s]+)/i", $html('th', 0)->html(), $matches);

						$status = $matches[1] ? $matches[1] : '';
						$canrelease = ($aid || in_array($product_details['type'], array('reselleraccount','server'))) ? true : false;

						$Firewall = new Firewall($LANG);
						$Firewall->setWHMdetails($product_details);

						switch($page)
						{
							case 'firewall':

								$pagetitle = $LANG['firewalltab'];

								$cpanel = new csfmanager_cpanel;

								$password = $product_details['password'] ? $product_details['password'] : $product_details['accesshash'];
								$password_type = $product_details['password'] ? 'plain' : 'hash';

								$cpanel->setServer($product_details['hostname'], $product_details['username'], $password, $password_type);

								$response = $cpanel->request($cgifile, array(
									'action'	=> 'conf',
								));
								
								if($response['success'])
								{
									$html = str_get_dom($response['output']);

									$keys = array(
										'TCP_IN_'		=> 'open_ports',
										'CC_DENY_'		=> 'denied_countries',
										'CC_ALLOW_PORTS_'	=> 'allowed_countries',
										'CC_ALLOW_PORTS_TCP_'	=> 'allowed_countries_ports',
									);

									foreach($html('input') as $input)
									{
										if(in_array($input->name, array_keys($keys)))
										{
											$output[$keys[$input->name]] = $input->value;
										}
									}

									$output['open_ports'] = str_replace(',', ', ', $output['open_ports']);
									$output['allowed_countries_ports'] = str_replace(',', ', ', $output['allowed_countries_ports']);

									$denied_countries = explode(',', strtolower($output['denied_countries']));

									$output['denied_countries'] = array();

									$countries_ary = $LANG['countries_ary'];
										
									foreach($denied_countries as $country)
									{
										if(!trim($country)) continue;
										
										$output['denied_countries'][] = array(
											'name'		=> $countries_ary[strtoupper($country)],
											'code'		=> $country,
											'flag'		=> "modules/addons/csfmanager/images/flags/{$country}.png",
										);
									}

									$allowed_countries = explode(',', strtolower($output['allowed_countries']));

									$output['allowed_countries'] = array();

									foreach($allowed_countries as $country)
									{
										if(!trim($country)) continue;

										$output['allowed_countries'][] = array(
											'name'		=> $countries_ary[strtoupper($country)],
											'code'		=> $country,
											'flag'		=> "modules/addons/csfmanager/images/flags/{$country}.png",
										);
									}
								}
								else
								{
									$output['errors'][] = $response['message'];
								}

							break;

							case 'unblock':

								$pagetitle = $LANG['unblocktab'];

								$Firewall->setIP($cip);

								if($ip)
								{
									if($canrelease && $instance->getConfig('permission_aunblock'))
									{
										if($Firewall->setIP($ip))
										{
											$cip = $ip;
											$custom_ip = true;
										}
									}
									else
									{
										$output['errors'][] = $LANG['nopermissionsunblock'];
									}
								}

								$response = $Firewall->checkIP(null, $instance->getConfig('checkbrute'));

								if($response['success'] && sizeof($response['data']))
								{
									$block_data = $response['data'];
								}
								elseif(!$response['success'] && $response['message'])
								{
									$output['errors'][] = $response['message'];
								}

								if(sizeof($block_data))
								{
									$print_reasons_csf = $print_reasons_brute = true;

									if($action == 'unblock' && (($custom_ip && $canrelease) || !$custom_ip || !$aid))
									{
										$unblock = $Firewall->releaseIP($instance->getConfig('checkbrute'));

										if($unblock['csf']['success'])
										{
											logActivity("Jetserver CSF Manager :: Client ID: {$uid} released the IP {$cip} from CSF");
											$output['successes'][] = sprintf($LANG['ipreleasedok'], $cip, 'CSF');
											$print_reasons_csf = false;
										}
										else
										{
											$output['errors'][] = $unblock['csf']['message'];
										}

										if($instance->getConfig('checkbrute'))
										{
											if($unblock['brute']['success']) 
											{
												logActivity("Jetserver CSF Manager :: Client ID: {$uid} released the IP {$cip} from Brute Force");
												$output['successes'][] = sprintf($LANG['ipreleasedok'], $cip, 'Brute Force');
												$print_reasons_brute = false;
											}
											else
											{
												$output['errors'][] = $unblock['brute']['message'];
											}
										}
									}

									if($print_reasons_csf || $print_reasons_brute)
									{
										if($print_reasons_csf) $output['blockedreasons_csf'] = $block_data['csf'] ? $block_data['csf'] : array();

										if($instance->getConfig('checkbrute') && $print_reasons_brute)
										{
											$output['blockedreasons_logins'] = $block_data['logins'] ? $block_data['logins'] : array();
											$output['blockedreasons_brutes'] = $block_data['brutes'] ? $block_data['brutes'] : array();
										}
									}
								}

							break;

							case 'allow':

								$pagetitle = $LANG['allowtab'];

								if($submit)
								{
									$reason = csfmanager::request_var('reason', '');

									if($ip && $reason && preg_match("/^[a-zA-Z\d\s]+$/", $reason))
									{
										if($Firewall->setIP($ip))
										{
											if(intval($instance->getConfig('allowlength')) > 0 && in_array($instance->getConfig('allowlength_type'), array('seconds','minutes','hours','days')))
											{
												$allow = $Firewall->temporaryAllow($instance->getConfig('allowlength'), $instance->getConfig('allowlength_type'), $reason);

												if($allow['success'])
												{
													$html = str_get_dom($allow['output']);

													$response = $html('pre', 0)->html();

													if(strpos($response, "{$ip} allowed") !== false)
													{
														logActivity("Jetserver CSF Manager :: <a href=\"clientssummary.php?userid={$uid}\">Client ID: {$uid}</a> whitelisted the IP {$ip}");
														$output['successes'][] = sprintf($LANG['tempallowipok'], $ip, $instance->getConfig('allowlength'), $instance->lang(strtolower($instance->getConfig('allowlength_type'))));

														$allowlength = array(
															'seconds'	=> 1,
															'minutes'	=> 60,
															'hours'		=> (60 * 60),
															'days'		=> (60 * 60 * 24),
														);

														$now = time();

														$sql = "INSERT INTO mod_csfmanager_allow (`clientid`,`serverid`,`ip`,`time`,`expiration`,`reason`) VALUES
															('{$uid}','{$product_details['server_id']}','{$ip}','{$now}','" . ($now + ($allowlength[strtolower($instance->getConfig('allowlength_type'))] * $instance->getConfig('allowlength'))) . "','" . mysqli_escape_string($reason) . "')";
														sql_exec($sql);
													}
													else
													{
														$message = str_replace('csf: ', '', strip_tags($response));

														logActivity("Jetserver CSF Manager :: <a href=\"clientssummary.php?userid={$uid}\">Client ID: {$uid}</a> tried to whitelisted the IP {$ip} :: {$message}");
														$output['errors'][] = $message;
													}
												}
												else
												{
													$output['errors'][] = $allow['message'];
												}
											}
											else
											{
												if(intval($instance->getConfig('allowlength')) <= 0) $output['errors'][] = $LANG['lengthinvalid'];
												if(!in_array($instance->getConfig('allowlength_type'), array('seconds','minutes','hours','days'))) $output['errors'][] = $LANG['lengthtypeinvalid'];
											}
										}
										else
										{
											$output['errors'][] = sprintf($LANG['tempallowiperror'], $ip);
										}
									}
									else
									{
										if(!$ip) $output['errors'][] = $LANG['noipprovided'];
										if(!$reason) $output['errors'][] = $LANG['noallowreason'];
										if($reason && !preg_match("/^\w\d\s$/", $reason)) $output['errors'][] = $LANG['allowreasoninvalid'];
									}
								}

							break;

							case 'allowemail':

								$pagetitle = $LANG['allowemailtab'];

								if($submit)
								{
									$fullname = trim(mysqli_escape_string(csfmanager::request_var('fullname', '')));

									if($fullname && $email && csfmanager::csfValidateEmail($email))
									{
										$sql = "SELECT key_id
											FROM mod_csfmanager_allow_keys
											WHERE key_email = '" . mysqli_escape_string($email) . "'
											AND key_clicks_remained > 0
											AND key_expire > '" . time() . "'
											AND user_id = '{$product_details['client_id']}'
											AND server_id = '{$product_details['server_id']}'
											AND key_cancelled = 0";
										$result = sql_exec($sql);
										$key_details = mysqli_fetch_assoc($result);

										if($key_details)
										{
											$output['errors'][] = $LANG['alredyvalidkey'];
										}
										else
										{
											$hashkey = md5($email . rand() . time());
											$sysurl = ($CONFIG["SystemSSLURL"] ? $CONFIG["SystemSSLURL"] : $CONFIG["SystemURL"]);
											$whitelist_url = "{$sysurl}/index.php?m=csfmanager&action=allow&key={$hashkey}";
											$cancel_url = "{$sysurl}/index.php?m=csfmanager&action=cancel&key={$hashkey}";
											$valid_days = 365;
											$valid_clicks = 10;

											$sendmail = csfmanager::sendCSFmail('CSF Manager Whitelist by Email', $email, $fullname, array(
												'emailfullname'		=> $fullname,
												'firstname'		=> $product_details['firstname'],
												'lastname'		=> $product_details['lastname'],
												'whitelist_url'		=> $whitelist_url,
												'valid_days'		=> $valid_days,
												'valid_clicks'		=> $valid_clicks,
												'cancel_url'		=> $cancel_url,
												'signature'		=> nl2br(html_entity_decode($CONFIG['Signature'])),
											));

											if($sendmail['success'])
											{
												logActivity("Jetserver CSF Manager :: <a href=\"clientssummary.php?userid={$uid}\">Client ID: {$uid}</a> sent allow ket to the recipient {$email} ({$fullname})");

												$sql = "INSERT INTO mod_csfmanager_allow_keys (`user_id`,`server_id`,`product_id`,`key_hash`,`key_recipient`,`key_email`,`key_clicks_remained`,`key_expire`) VALUES
													('{$product_details['client_id']}','{$product_details['server_id']}','{$pid}','{$hashkey}','{$fullname}','{$email}',{$valid_clicks},'" . (time() + (60 * 60 * 24 * $valid_days)) . "')";
												sql_exec($sql);

												$output['successes'][] = $LANG['emailsent'];
											}
											else
											{
												$output['errors'][] = $sendmail['message'];
											}
										}
									}
									else
									{
										if(!$fullname) $output['errors'][] = $LANG['emptyrecipientname'];
										if(!$email) $output['errors'][] = $LANG['emptyrecipientemail'];
										if($email && !csfValidateEmail($email)) $output['errors'][] = $LANG['invalidrecipientemail'];
									}
								}
							break;

							case 'emailkeys':
							case 'whitelisted':

								$pagetitle = $instance->lang($page . 'tab');

								$remove = csfmanager::request_var('remove', 0);
								$cancel = csfmanager::request_var('cancel', 0);
								$resend = csfmanager::request_var('resend', 0);

								if($resend)
								{
									$sql = "SELECT *
										FROM mod_csfmanager_allow_keys
										WHERE key_id = '{$resend}'
										AND user_id = '{$uid}'
										AND server_id = '{$product_details['server_id']}'
										AND product_id = '{$pid}'
										AND key_cancelled = 0
										AND key_clicks_remained > 0
										AND key_expire > '" . time() . "'";
									$result = sql_exec($sql);
									$key_details = mysqli_fetch_assoc($result);

									if($key_details)
									{
										$sysurl = ($CONFIG["SystemSSLURL"] ? $CONFIG["SystemSSLURL"] : $CONFIG["SystemURL"]);
										$whitelist_url = "{$sysurl}/index.php?m=csfmanager&action=allow&key={$key_details['key_hash']}";
										$cancel_url = "{$sysurl}/index.php?m=csfmanager&action=cancel&key={$key_details['key_hash']}";

										$sendmail = csfmanager::sendCSFmail('CSF Manager Whitelist by Email', $key_details['key_email'], $key_details['key_recipient'], array(
											'emailfullname'		=> $key_details['key_recipient'],
											'firstname'		=> $product_details['firstname'],
											'lastname'		=> $product_details['lastname'],
											'whitelist_url'		=> $whitelist_url,
											'valid_days'		=> ceil(($key_details['key_expire'] - time()) / (60 * 60 * 24)),
											'valid_clicks'		=> $key_details['key_clicks_remained'],
											'cancel_url'		=> $cancel_url,
											'signature'		=> nl2br(html_entity_decode($CONFIG['Signature'])),
										));

										if($sendmail['success'])
										{
											$output['successes'][] = $LANG['emailsent'];
										}
										else
										{
											$output['errors'][] = $sendmail['message'];
										}
									}
									else
									{
										$output['errors'][] = $LANG['invalidkey'];
									}
								}

								if($cancel)
								{
									$sql = "SELECT key_id
										FROM mod_csfmanager_allow_keys
										WHERE key_id = '{$cancel}'
										AND user_id = '{$uid}'
										AND server_id = '{$product_details['server_id']}'
										AND product_id = '{$pid}'
										AND key_cancelled = 0
										AND key_clicks_remained > 0
										AND key_expire > '" . time() . "'";
									$result = sql_exec($sql);
									$key_details = mysqli_fetch_assoc($result);

									if($key_details)
									{
										$sql = "UPDATE mod_csfmanager_allow_keys
											SET key_cancelled = 1
											WHERE key_id = '{$key_details['key_id']}'";
										sql_exec($sql);

										$output['successes'][] = $LANG['keycancelled'];
									}
									else
									{
										$output['errors'][] = $LANG['invalidkey'];
									}
								}

								if($remove)
								{
									$sql = "SELECT ip
										FROM mod_csfmanager_allow
										WHERE id = '{$remove}'
										AND clientid = '{$uid}'
										AND serverid = '{$product_details['server_id']}'";
									$result = sql_exec($sql);
									$allow_details = mysqli_fetch_assoc($result);

									if($allow_details)
									{
										// delete this ip
										if($Firewall->setIP($allow_details['ip']))
										{
											if($Firewall->quickUnblock())
											{
												$sql = "DELETE
													FROM mod_csfmanager_allow
													WHERE id = '{$remove}'";
												sql_exec($sql);

												$output['successes'][] = $LANG['allowedipremove'];
											}
											else
											{
												$output['errors'][] = $LANG['cantremoveip'];
											}
										}
										else
										{
											$output['errors'][] = $LANG['cantsetip'];
										}
									}
									else
									{
										$output['errors'][] = $LANG['ipnotexists'];
									}
								}

								$output['allowedips'] = array();

								$sql = "SELECT *
									FROM mod_csfmanager_allow 
									WHERE clientid = '{$uid}'
									AND serverid = '{$product_details['server_id']}'
									AND expiration > '" . time() . "'
									ORDER BY id DESC";
								$result = sql_exec($sql);

								while($ip_details = mysqli_fetch_assoc($result))
								{
									$output['allowedips'][$ip_details['id']] = $ip_details;
									$output['allowedips'][$ip_details['id']]['time'] = date("d/m/Y H:i", $ip_details['time']);
									$output['allowedips'][$ip_details['id']]['expiration'] = date("d/m/Y H:i", $ip_details['expiration']);
								}
								mysqli_free_result($result);

								$output['allowkeys'] = array();

								$sql = "SELECT *
									FROM mod_csfmanager_allow_keys
									WHERE user_id = '{$uid}'
									AND server_id = '{$product_details['server_id']}'
									AND product_id = '{$pid}'
									ORDER BY key_id DESC";
								$result = sql_exec($sql);

								while($key_details = mysqli_fetch_assoc($result))
								{
									$output['allowkeys'][$key_details['key_id']] = $key_details;
									$output['allowkeys'][$key_details['key_id']]['key_expire'] = date("d/m/Y H:i", $key_details['key_expire']);

									$output['allowkeys'][$key_details['key_id']]['key_expired'] = ($key_details['key_expire'] <= time());
								}
								mysqli_free_result($result);

							break;
						}

						$breadcrumb = array();

						$breadcrumb['clientarea.php'] = $LANG['clientareatitle'];
						$breadcrumb['clientarea.php?action=products'] = $LANG['clientareaproducts'];
						$breadcrumb['clientarea.php?action=productdetails&id=' . $product_details['id']] = $LANG['clientareaproductdetails'];
						$breadcrumb['index.php?m=csfmanager&id=' . $product_details['id']] = $LANG['csfmanagertitle'];
						$breadcrumb['index.php?m=csfmanager&page=' . $page . '&id=' . $product_details['id']] = $pagetitle;
						$tplfile = 'csfmanager';
					}
				}
				else
				{
					$output['errors'][] = sprintf($LANG['noservice'], $pid);
					$tplfile = 'csfmanagererror';
				}
			}
			else
			{
				$output['errors'][] = $LANG['nopermissionspage'];
				$tplfile = 'csfmanagererror';
			}
		}
		else
		{
			$output['services'] = array();

			$sql = "SELECT h.domain, h.id, g.name as `group`, p.name as product 
				FROM tblhosting as h
				INNER JOIN tblproducts as p
				ON h.packageid = p.id
				AND p.type IN ('hostingaccount','reselleraccount','server')
				INNER JOIN tblproductgroups as g
				ON p.gid = g.id
				INNER JOIN tblservers as s
				ON h.server = s.id
				" . (trim($instance->getConfig('servers')) ? "AND s.id IN (" . $instance->getConfig('servers') . ")" : '') . "
				WHERE h.domainstatus = 'Active'
				AND h.userid = '{$_SESSION['uid']}'
				ORDER BY h.id DESC"; 
			$result = sql_exec($sql);

			while($product_details = mysqli_fetch_assoc($result))
			{
				$output['services'][] = $product_details;
			}
			mysqli_free_result($result);

			$tplfile = 'csfmanagerproducts';
		}
	}
	else
	{
		$output['errors'][] = "You are not logged in";
		$tplfile = 'csfmanagererror';
	}
	
	return array(
		'pagetitle' 	=> $LANG['csfmanagertitle'] . ($pagetitle ? " - {$pagetitle}" : ''),
		'breadcrumb' 	=> $breadcrumb,
		'templatefile' 	=> "clientarea/{$tplfile}",
		'requirelogin' 	=> false,
		'vars' 		=> array_merge($output, array(
			'modulepath'	=> JCSF_ROOT_PATH,
			'modulelink'	=> $modulelink,

			'pid' 		=> $pid,
			'uid' 		=> $uid,
			'aid' 		=> $aid,
			'cip'		=> $cip,
			'sip'		=> $_SERVER['REMOTE_ADDR'],

			'config'	=> array(
				'permission_aunblock'		=> $instance->getConfig('permission_aunblock'),
			),
			'page' 		=> $page,
			'action' 	=> $action,
			'ip' 		=> $ip,
			'email'		=> $email,
			'fullname'	=> $fullname,
			'reason' 	=> isset($reason) ? $reason : '',
			'status' 	=> $status,
			'canrelease' 	=> $canrelease,
			'ADDONLANG' 	=> $LANG,
			'package'	=> isset($product_details) ? $product_details['group_name'] . ' - ' . $product_details['product_name'] : '',
			'domain'	=> isset($product_details) ? $product_details['domain'] : '',
		)),
	);
}


?>