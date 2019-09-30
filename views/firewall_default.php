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

if (!defined("ITFINDEN_CSF_MANAGER")) {
	die("This file cannot be accessed directly");
}

class jcsf_firewall_default {
	public function _default() {
		global $instance, $cc_encryption_hash;

		$output = array('success' => true, 'message' => '', 'data' => array());

		$instance = csfmanager::getInstance();

		$output['data']['servers'] = array();

		$sql = "SELECT *
			FROM tblservers
			" . (trim($instance->getConfig('servers', '')) ? "WHERE id IN (" . trim($instance->getConfig('servers', '')) . ")" : '');
		$result = sql_select($sql);

		foreach ($result as $server_details) {
			$output['data']['servers'][$server_details['id']] = array_merge($server_details, array('password' => decrypt($server_details['password'], $cc_encryption_hash)));
		}

		return $output;
	}
}

?>