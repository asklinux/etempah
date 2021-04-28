<?php

db_connect();
function db_connect() {

    $database = $_POST['database'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $hostname = $_POST['hostname'];

    $resource = mysql_connect($hostname, $username, $password);
    if ($resource) {
	if (mysql_select_db($database, $resource)) {
	    echo 'Success';
	} else {
	    $sql = 'CREATE DATABASE ' . $database;

	    if (mysql_query($sql, $resource)):
		echo 'Success';
	    else:
		echo 'Database creation failed';
	    endif;
	}
    }
    else {
	echo 'Connection Failed';
    }
}
?>