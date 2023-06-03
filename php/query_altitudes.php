<?php
//druhé testování na straně backendu na úplnost formuláře
echo $_POST["upDown"];
if(isset($_POST["firstAlt"]) && isset($_POST["secondAlt"]) && isset($_POST["diffAlt"]) && isset($_POST["operAlt"]) && isset($_POST["upDown"])){ //both values must be filled.
    $firstYear = $_POST["firstAlt"];
    $secondYear = $_POST["secondAlt"];
    //přetypování proměnné prvního roku z čísla na string
    if($firstYear == 1) {
        $firstYear = 'altitude_1938';
    }
    elseif($firstYear == 2) {
        $firstYear = 'altitude_1951';
    }
    elseif($firstYear == 3) {
        $firstYear = 'altitude_2000';
    }
    
    //přetypování proměnné druhého roku z čísla na string
    if($secondYear == 2) {
        $secondYear = 'altitude_1951';
    }
    elseif($secondYear == 3) {
        $secondYear = 'altitude_2000';
    }
    elseif($secondYear == 4) {
        $secondYear = 'altitude_2020';
    }

    //přeno zbylých proměnných
    $diff = $_POST["diffAlt"];
    $oper = $_POST["operAlt"];
    $plusMinus = $_POST["upDown"];
    $landUse = $_POST['LLUC'];

    
    
    $queryDrop = "DROP TABLE IF EXISTS show_polygon_altitude";
    $resultOne= pg_query($queryDrop);
    
    //
    if(($oper == '<' && $plusMinus == 'plus') || ($oper == '<' && $plusMinus == 'minus')) {
        //
        if($plusMinus == 'minus'){
            $diff = -($diff); 
            $rulesQuery = "($secondYear - $firstYear) between $diff and 0";
            echo "menší pokles";
        }
        //
        elseif($plusMinus == 'plus'){
            $rulesQuery = "($secondYear - $firstYear) between 0 and $diff";
            echo "menší navýšení";
        }
    }
    else { 
        //zobrazit kladné změny výšky větší, nežli je prahová hodnota
        if($plusMinus == 'plus') { 
            $rulesQuery = "($secondYear - $firstYear) > $diff";
        }
        //zobrazit záporné změny výšky větší, nežli je prahová hodnota
        elseif($plusMinus == 'minus') {  
            $rulesQuery = "($firstYear - $secondYear) > $diff";
            
        }   
    }

    //testování existence vyselektovaných dat a prvotní
    $queryNumberRow = "SELECT * FROM altitudes_holesice where $rulesQuery";
    $resultNumberRow = pg_query($queryNumberRow);    
    
    if(pg_num_rows($resultNumberRow) == 0) {
        exit("Žádná existující data");
    }
    /*
    if() {

    }
    */

    $mainQuery = "CREATE TABLE show_polygon_altitude as(
        SELECT
            ST_NumGeometries(cluster_make),
            st_transform(st_concavehull(cluster_make, 0.005, true),4326) AS final_collection
                FROM (SELECT unnest(ST_ClusterWithin(st_transform(geom, 32633), 16)) as cluster_make
                    FROM (SELECT * 
                        FROM altitudes_holesice as table_main
                            WHERE ($rulesQuery)) as main_query
                ) as cluster_main_query
                    WHERE st_numgeometries(cluster_make) > 20)";
    
    pg_query($mainQuery);

    if($landUse == 'withoutLanduse'){
        
        $cutPolygonQuery = "UPDATE show_polygon_altitude as b
                            SET final_collection = ST_Intersection(b.final_collection, p.geom)
                                FROM cadastral_pol as p
                                    WHERE ST_Intersects(b.final_collection, p.geom)";

    }
    else {
        if($landUse == 'oldLanduse') {
            //proměnné
        }
        elseif($landUse == 'newLanduse') {
            //proměnné
        }

        //skript pro layout
    }
    
}
else{
    echo 'Je třeba vyplnit všechna pole';
    pg_close($db_connect);
}
$result = pg_query($cutPolygonQuery) or die('Error message: ' . pg_last_error());

while ($row = pg_fetch_row($result)) {
    echo var_dump($row);
    
}

//pg_free_result($result);
pg_close($db_connect);
?>