<?php
    $db_connect = pg_connect("host=localhost port=5432 dbname=Cesium_app user=postgres password=heslo_123");

    if ($db_connect) {
        echo 'Connection attempt succeeded.';
       } 
    else {
        echo 'Connection attempt failed.';
        }
    
        pg_close($db_connect);
?>