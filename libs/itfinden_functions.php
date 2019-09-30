<?php
use WHMCS\Database\Capsule;

function sql_exec($sql, $action = '') {

	$module = 'ITFINDEN_CSF_MANAGER';
	$requestString = $sql;
	$responseData = '';
	$processedData = '';
	$replaceVars = '';

	$pdo = Capsule::connection()->getPdo();

	$stmt = $pdo->prepare($sql);

	if ($stmt) {
		logModuleCall($module, $action, $requestString, $responseData, $processedData, $replaceVars);
		$stmt->execute();
	}
}

function sql_select($sql, $action = '') {

	$module = 'ITFINDEN_CSF_MANAGER';
	$requestString = $sql;
	$responseData = '';
	$processedData = '';
	$replaceVars = '';

	$pdo = Capsule::connection()->getPdo();

	$stmt = $pdo->prepare($sql);
	if ($stmt) {
		$stmt->execute($values);

		if ($stmt->rowCount() > 0) {

			$result[] = $stmt->fetchAll(PDO::FETCH_ASSOC);
		}
		logModuleCall($module, $action, $requestString, $result ?? 'no result', $processedData, $replaceVars);

	}

	return $result[0] ?? [];
}

?>
