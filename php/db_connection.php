<?php
    $db_connect = pg_connect("host=localhost port=5432 dbname=Cesium_app user=postgres password=heslo_123");

    if ($db_connect) {
        echo 'Connection attempt succeeded.';
       } 
    else {
        echo 'Connection attempt failed.';
        }
    if(isset($_POST["name"])){
        $data = $_POST["name"];
        //echo $data[0];
        }

    $query = "CREATE OR REPLACE VIEW try_view AS (SELECT * FROM altitudes_holesice WHERE altitude_2020 > 250)";
    $result = pg_query($query);
    //$result = pg_query($query) or die('Error message: ' . pg_last_error());
    /*
    while ($row = pg_fetch_row($result)) {
        var_dump($row);
    }
    */
    pg_free_result($result);
    pg_close($db_connect);
?>