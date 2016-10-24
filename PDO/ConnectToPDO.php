<?php
	require_once('mypdo.php');
	header("Content-Type:text/html; charset=UTF8");

	$pdo = new MyPDO;
	// $pdo->name = 'a28';
	// $pdo->address = '台南市';
	// echo "lastInsertId = ".$pdo->insert('test');

	$pdo->name = 'update42';
	$pdo->address = '台南市';
	$pdo->update('test', [], 'WHERE id = 28');
	$pdo->error();
?>