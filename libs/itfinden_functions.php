<?php
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

	return $result ?? [];
}


?>
