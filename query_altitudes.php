<?php 
        include 'db_connection.php';
        
        //druhé testování na straně backendu na úplnost formuláře
        
        if(isset($_POST["firstAlt"]) && isset($_POST["secondAlt"]) && isset($_POST["diffAlt"]) && isset($_POST["operAlt"]) && isset($_POST["upDown"])){ //both values must be filled.
            $firstYear = $_POST["firstAlt"];
            $secondYear = $_POST["secondAlt"];
            //přetypování proměnné prvního roku z čísla na string
            if($firstYear == 1) {
                $firstYear = 'altitude_1938';
            }
            elseif($firstYear == 2) {
                $firstYear = 'altitude_1950';
            }
            elseif($firstYear == 3) {
                $firstYear = 'altitude_2000';
            }
            
            //přetypování proměnné druhého roku z čísla na string
            if($secondYear == 2) {
                $secondYear = 'altitude_1950';
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

            
            $queryDropBack = "DROP TABLE IF EXISTS show_polygon_altitude_backend";
            $queryDrop = "DROP TABLE IF EXISTS show_polygon_altitude";
            $queryDropLLUCnew = "DROP TABLE IF EXISTS lluc_created_new";
            $queryDropLLUCold = "DROP TABLE IF EXISTS lluc_created_old";

            pg_query($queryDropBack);
            pg_query($queryDrop);
            pg_query($queryDropLLUCnew);
            pg_query($queryDropLLUCold);

            
            //
            if(($oper == '<' && $plusMinus == 'plus') || ($oper == '<' && $plusMinus == 'minus')) {
                //
                if($plusMinus == 'minus'){
                    $diff = -($diff); 
                    $rulesQuery = "($secondYear - $firstYear) between $diff and 0";
                }
                //
                elseif($plusMinus == 'plus'){
                    $rulesQuery = "($secondYear - $firstYear) between 0 and $diff";
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
            else{
                include 'rewriting_text.php';
                $textDesc = "Zobrazena jsou data reprezentující $plusMinusName $operName než $diff metrů, a to mezi lety $firstYearName a $secondYearName";
                echo $textDesc;
            }

            $mainQuery = "CREATE TABLE show_polygon_altitude_backend as(
                SELECT 
                    st_union(st_transform(st_simplify(st_buffer(st_concavehull(st_transform(cluster_make,32633), 0.01, true),7.5),7),4326)) AS final_collection FROM 
			            (SELECT id_collection, st_transform(st_collect(geom),4326) as cluster_make FROM 
				            (SELECT ST_ClusterDBSCAN(st_transform(geom, 32633), eps:= 20, minpoints := 3) over() AS id_collection, geom FROM 
				 	            (SELECT * FROM altitudes_holesice WHERE $rulesQuery) as main_query) as geom_from_cluster
				            GROUP by id_collection) as cluster_main_query
		                WHERE (id_collection IS NOT NULL) and (st_numgeometries(cluster_make)) > 8)";
            
            pg_query($mainQuery);

            if($landUse == 'withoutLanduse'){
                
                $result = "CREATE TABLE show_polygon_altitude as(
                    SELECT 
                        round((cast(st_area(st_transform(final_collection,32633))/1000000 as numeric)),3) as rozloha_vystupu_v_km2, 
                        round(cast((st_area(st_transform(final_collection,32633))/1000000) / 6.04 * 100 as numeric),2) as procento_vystupu_v_KU,
                        final_collection FROM (
                            SELECT
                                ST_Intersection(b.final_collection, p.geom) as final_collection
                                    FROM cadastral_pol as p, show_polygon_altitude_backend as b
                                        WHERE ST_Intersects(b.final_collection, p.geom)) as intersected_data);";
  
            }
            else {
                if($landUse == 'oldLanduse') {
                    $result = "UPDATE show_polygon_altitude_backend as b
                                    SET final_collection = ST_Intersection(b.final_collection, p.geom)
                                        FROM cadastral_pol as p
                                            WHERE ST_Intersects(b.final_collection, p.geom)";
                    pg_query($result);
                    
                    $typeLLUC = "lluc_old";
                    $result = "create TABLE lluc_created_old as (
                        SELECT 
                        round(cast((st_area(st_transform(final_collection,32633))/1000000) / 6.04 * 100 as numeric),2) as procento_vystupu_vKU,
                        round(cast((st_area(st_transform(final_shape,32633))/1000000) / 6.04 * 100 as numeric),2) as procento_vystupu_v_KU,
                        round((cast(st_area(st_transform(final_shape,32633))/1000000 as numeric)),3) as rozloha_vystupu_celkem, 
                        round((cast(st_area(st_transform(final_collection,32633))/1000000 as numeric)),3) as rozloha_vystupu_v_km2, 
                        final_shape,
                        legend_nam,
                        legend_num FROM(
                            SELECT 
                                p.legend_nam,
                                p.legend_num,
                                ST_Intersection(b.final_collection, p.geom) as final_shape
                                    FROM lluc_old as p, show_polygon_altitude_backend as b
                                        WHERE ST_Intersects(p.geom, b.final_collection)) as intersected_data, show_polygon_altitude_backend)";
                }
                elseif($landUse == 'newLanduse') {
                    $typeLLUC = "lluc_new";
                    $result = "create TABLE lluc_created_new as (SELECT ST_Intersection(b.final_collection, p.geom), legend_num
                                FROM $typeLLUC as p, show_polygon_altitude_backend as b
                                    WHERE ST_Intersects(p.geom, b.final_collection))";
                }
                
                
            }
            
        }
        else{
            echo 'Je třeba vyplnit všechna pole';
            pg_close($db_connect);
        }
        $result = pg_query($result) or die('Error message: ' . pg_last_error());

        while ($row = pg_fetch_row($result)) {
            echo var_dump($row);
            
        }
        //pg_free_result($result);
        pg_close($db_connect);
        
                
       
    ?>