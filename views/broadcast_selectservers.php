<?php
/*
 *
 * JetCSFManager @ whmcs module package
 * Created By Idan Ben-Ezra
 *
 * Copyrights @ Jetserver Web Hosting
 * http://jetserver.net
 *
 **/

if (!defined("ITFINDEN_CSF_MANAGER"))
	die("This file cannot be accessed directly");

use WHMCS\Database\Capsule;

class jcsf_broadcast_selectservers extends jcsf_broadcast_default
{
	public function _default()
	{	
		global $instance, $cc_encryption_hash;
		
		$output = array('success' => true, 'message' => '', 'data' => array());
		
		$instance = csfmanager::getInstance();
		
		$output['data']['servers'] = array();
		
		$sql = "SELECT *
			FROM tblservers
			" . (trim($instance->getConfig('servers', '')) ? "WHERE id IN (" . trim($instance->getConfig('servers', '')) . ")" : '');
		$result = sql_select($sql);
		
		foreach($result => $server_details)
		{
			$output['data']['servers'][$server_details['id']] = array_merge($server_details, array('password' => decrypt($server_details['password'], $cc_encryption_hash)));
		}

		$templateserver = csfmanager::request_var('templateserver', 0);
		
		if(!isset($output['data']['servers'][$templateserver]))
		{
			$output['success'] = false;
			$output['message'] = $instance->lang('notemplateserverselected');
			return $output;
		}
		
		$output['data']['server_details'] = $output['data']['servers'][$templateserver];
		$output['data']['dontusevalues'] = csfmanager::request_var('dontusevalues', 0, array(0,1));
		
		return $output;
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

?>