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

use WHMCS\Database\Capsule;

class jcsf_allowedlog_default {
	public function _default() {
		global $cc_encryption_hash, $instance;

		$output = array('success' => true, 'message' => '', 'data' => array());

		$instance = csfmanager::getInstance();

		$id = csfmanager::request_var('id', 0);
		$start = csfmanager::request_var('start', 0);
		$search = csfmanager::request_var('search', array());

		$limit = 10;

		$output['data']['search_url'] = sizeof($search) ? http_build_query($search) : '';

		$output['data']['list'] = array();

		$sql = "SELECT a.*, c.firstname, c.lastname, s.name as server_name
			FROM mod_csfmanager_allow as a
			LEFT JOIN tblclients as c
        		ON c.id = a.clientid
			LEFT JOIN tblservers as s
			ON s.id = a.serverid
			WHERE a.expiration > '" . time() . "'
			" . (trim($search['clientname']) ? "AND UPPER(CONCAT_WS(' ', c.firstname, c.lastname)) LIKE UPPER('%" . trim($search['clientname']) . "%')" : '') . "
			" . (intval($search['server']) ? "AND s.id = '" . intval($search['server']) . "'" : '') . "
			" . (trim($search['ip']) ? "AND a.ip LIKE '%" . trim($search['ip']) . "%'" : '') . "
			" . (trim($search['reason']) ? "AND a.reason LIKE '%" . trim($search['reason']) . "%'" : '') . "
			ORDER BY a.time DESC";
		$result = sql_select($sql);

		$output['data']['total'] = count($result);

		$result = sql_select($sql . " LIMIT {$start}, {$limit}");

		foreach ($result as $allow_details) {
			$output['data']['list'][] = array_merge($allow_details, array('time' => date("d/m/Y H:i", $allow_details['time']), 'expiration' => date("d/m/Y H:i", $allow_details['expiration'])));
		}

		$output['data']['current_page'] = (($start / $limit) + 1);
		$output['data']['total_pages'] = ceil(abs($output['data']['total'] / $limit));
		$output['data']['search'] = $search;
		$output['data']['start'] = $start;
		$output['data']['limit'] = $limit;

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

	public function delete() {
		global $cc_encryption_hash;

		$output = array('success' => false, 'message' => '', 'data' => array());

		$instance = csfmanager::getInstance();

		$id = csfmanager::request_var('id', 0);

		$sql = "SELECT a.ip, s.id as server_id, s.name, s.hostname, s.username, s.password, s.accesshash, s.secure
			FROM mod_csfmanager_allow as a
			LEFT JOIN tblservers as s
			ON s.id = a.serverid
			WHERE a.id = '{$id}'";
		$allow_details = sql_select($sql);

		if (!$allow_details) {
			$output['message'] = $instance->lang('ipnotexists');
			return $output;
		}

		$allow_details['password'] = decrypt($allow_details['password'], $cc_encryption_hash);

		$Firewall = new Firewall($LANG);
		$Firewall->setWHMdetails($allow_details);

		// delete this ip
		if (!$Firewall->setIP($allow_details['ip'])) {
			$output['message'] = $instance->lang('cantsetip');
			return $output;
		}

		if (!$Firewall->quickUnblock()) {
			$output['message'] = $instance->lang('cantremoveip');
			return $output;
		}

		$sql = "DELETE
			FROM mod_csfmanager_allow
			WHERE id = '{$id}'";
		sql_exec($sql);

		$output['success'] = true;
		$output['message'] = $instance->lang('allowedipremove');

		return $output;
	}
}

function sql_exec($sql) {
	$pdo = Capsule::connection()->getPdo();

	$stmt = $pdo->prepare($sql);

	if ($stmt) {
		$stmt->execute();
	}
}

function sql_select($sql) {
	$pdo = Capsule::connection()->getPdo();

	$stmt = $pdo->prepare($sql);

	if ($stmt) {
		$stmt->execute($values);

		if ($stmt->rowCount() > 0) {
			$result[] = $stmt->fetchAll(PDO::FETCH_ASSOC);
		}

	}

	return $result ?? false;
}

?>