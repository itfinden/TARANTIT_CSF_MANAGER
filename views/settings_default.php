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

class jcsf_settings_default
{
	public function _default()
	{	
		global $cc_encryption_hash, $instance;
		
		$output = array('success' => true, 'message' => '', 'data' => array());

		$instance = csfmanager::getInstance();
		
		$output['data']['servers'] = array();
		
		$sql = "SELECT *
			FROM tblservers
			WHERE hostname != ''
			ORDER BY hostname ASC";
		$result = sql_select($sql);
		
		foreach($result => $server_details)
		{
			$output['data']['servers'][$server_details['id']] = array_merge(array('selected' => in_array($server_details['id'], explode(',', $instance->getConfig('servers'))) ? true : false), $server_details);
		}

		return $output;
	}

	public function save()
	{	
		global $cc_encryption_hash, $instance;
		
		$output = $this->_default();
		$output['success'] = false;

		$instance = csfmanager::getInstance();

		$config_values = csfmanager::request_var('config', array());
				
		if(is_array($config_values) && sizeof($config_values))
		{
			foreach($config_values as $config_name => $config_value)
			{
				$current_value = $instance->getConfig($config_name, null);
				
				if(!isset($current_value)) continue;
		
				if($config_name == 'allowlength') $config_value = intval($config_value);
				if($config_name == 'allowlength_type') $config_value = in_array($config_value, array('seconds','minutes','hours','days')) ? $config_value : 'days';
		
				$instance->setConfig($config_name, $config_value);
			}

			$selectedservers = csfmanager::request_var('selectedservers', array());
			
			if(is_array($selectedservers) && sizeof($selectedservers))
			{
				$newservers = array();
				
				foreach($selectedservers as $server_id)
				{
					if(isset($output['data']['servers'][$server_id])) $newservers[] = $server_id;
				}
		
				$instance->setConfig('servers', (sizeof($newservers) ? implode(',', $newservers) : ''));
			}
			else
			{
				$instance->setConfig('servers', '');
			}
		}
		
		$output['success'] = true;
		$output['message'] = $instance->lang('chagessaved');
		
		return $output;
	}
}

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

?>