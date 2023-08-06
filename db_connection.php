<?php
    //Parametry pro připojení k databázi
    $host = 'localhost';
    $port = '5432';
    $dbname = 'Cesium_app';
    $user = 'postgres';
    $password = 'heslo_123';
    $db_connect = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

    //kontrola připojení
    if (!$db_connect) {
        echo 'Nepodařilo se připojit k databázi.';
        exit;
    } 
?>