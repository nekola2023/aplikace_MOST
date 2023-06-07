<?php 
        include 'db_connection.php';
        include 'hull.php';
        
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

            /*
            $queryDrop = "DROP TABLE IF EXISTS show_polygon_altitude";
            $queryDropLLUCnew = "DROP TABLE IF EXISTS lluc_new_table";
            $queryDropLLUCold = "DROP TABLE IF EXISTS lluc_old_table";

            
            pg_query($queryDrop);
            pg_query($queryDropLLUCnew);
            pg_query($queryDropLLUCold);
            */

            
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

            $createConcave =    "(SELECT st_union(st_transform(st_simplify(st_buffer(st_concavehull(st_transform(geom_cluster,32633), 0.01, true),7.5),7),4326)) AS final_collection FROM 
                            (SELECT id_collection, st_transform(st_collect(geom),4326) as geom_cluster FROM 
                                (SELECT ST_ClusterDBSCAN(st_transform(geom, 32633), eps:= 20, minpoints := 3) over() AS id_collection, geom FROM 
                                    (SELECT * FROM altitudes_holesice WHERE $rulesQuery) as main_query) as make_clusters
                                    GROUP by id_collection) as geom_from_make_clusters
                                WHERE (id_collection IS NOT NULL) and (st_numgeometries(geom_cluster)) > 8) as concave_hull";
            

            if($landUse == 'withoutLanduse'){
                
                $result =   "CREATE OR REPLACE VIEW output_basic_view AS(
                    SELECT 
                            round((cast(st_area(st_transform(final_collection,32633))/1000000 as numeric)),3) as all_area, 
                            round(cast((st_area(st_transform(final_collection,32633))/1000000) / 6.04 * 100 as numeric),2) as procent_to_ku,
                            final_collection FROM (
                                SELECT
                                    ST_Intersection(concave_hull.final_collection, cadastral_pol.geom) as final_collection
                                        FROM cadastral_pol, $createConcave
                                            WHERE ST_Intersects(concave_hull.final_collection, cadastral_pol.geom)) as intersected_data)";
  
            }
            else {
                if($landUse == 'oldLanduse') {
                    
                    $result = "CREATE OR REPLACE VIEW output_old_view as (     
                        SELECT 
                        final_shape,
                        legend_nam,
                        legend_num,
                        all_area,
                        procent_to_ku,
                        round((cast(st_area(st_transform(final_shape,32633))/1000000 as numeric)),3) as attr_area, 
                        round((round((cast(st_area(st_transform(final_shape,32633))/1000000 as numeric)),3) / all_area * 100 ),2) as procent_LLUC_to_output 	
                         FROM(
                            SELECT 
                                *,
                                ST_Intersection(output_area.final_collection, lluc.geom) as final_shape
                                    FROM lluc_old as lluc, (  
                                    SELECT 
                                        round((cast(st_area(st_transform(final_collection,32633))/1000000 as numeric)),3) as all_area, 
                                        round(cast((st_area(st_transform(final_collection,32633))/1000000) / 6.04 * 100 as numeric),2) as procent_to_ku,
                                        final_collection FROM (
                                            SELECT
                                                ST_Intersection(concave_hull.final_collection, cadastral_pol.geom) as final_collection
                                                    FROM cadastral_pol, (SELECT st_union(st_transform(st_simplify(st_buffer(st_concavehull(st_transform(geom_cluster,32633), 0.01, true),7.5),7),4326)) AS final_collection FROM 
                                                        (SELECT id_collection, st_transform(st_collect(geom),4326) as geom_cluster FROM 
                                                            (SELECT ST_ClusterDBSCAN(st_transform(geom, 32633), eps:= 20, minpoints := 3) over() AS id_collection, geom FROM 
                                                                (SELECT * FROM altitudes_holesice WHERE (altitude_1938 - altitude_2020) > 90) as main_query) as make_clusters
                                                                GROUP by id_collection) as geom_from_make_clusters
                                                            WHERE (id_collection IS NOT NULL) and (st_numgeometries(geom_cluster)) > 8) as concave_hull
                                                        WHERE ST_Intersects(concave_hull.final_collection, cadastral_pol.geom)) as intersected_data             
                                                ) as output_area
                                                    WHERE ST_Intersects(lluc.geom, output_area.final_collection)) as intersected_data)";
                }
                elseif($landUse == 'newLanduse') {
                    $result = "CREATE OR REPLACE VIEW output_new_view as (
                        SELECT
                    final_shape_new as geom, 	
                    data_new.all_area, 	
                    attribute_area_old, 	
                    attribute_area_new,	
                    round((attribute_area_new / data_new.all_area  * 100),3) as procent_to_KU, 	
                    round((data_new.all_area / 6.04 * 100 ),2) as procent_LLUC_to_output, 	 	 	
                    data_new.legend_nam, 	
                    data_new.legend_num::varchar(5),
                    (attribute_area_new/attribute_area_old) * 100 as compare_area
                        FROM (SELECT  	
                            round((cast(st_area(st_transform(final_shape_old,32633))/1000000 as numeric)),3) as attribute_area_old,  	
                            round((cast(st_area(st_transform(final_collection,32633))/1000000 as numeric)),3) as all_area,  	
                            final_shape_old, 	
                            legend_num, 	
                            legend_nam FROM( 		
                                SELECT  			
                                    lluc_old.legend_nam, 			
                                    lluc_old.legend_num, 
                                    final_collection,			
                                    ST_Intersection(concave_hull.final_collection, lluc_old.geom) as final_shape_old 				
                                        FROM lluc_old, $createConcave 					
                                            WHERE ST_Intersects(lluc_old.geom, concave_hull.final_collection)) as intersected_data) as data_old 
                            right outer join (SELECT  	
                                round((cast(st_area(st_transform(final_shape_new,32633))/1000000 as numeric)),3) as attribute_area_new,  	
                                round((cast(st_area(st_transform(final_collection,32633))/1000000 as numeric)),3) as all_area,  	
                                final_shape_new, 	
                                final_collection, 	
                                legend_num, 	
                                legend_nam FROM( 		
                                    SELECT  			
                                        lluc_new.legend_nam, 			
                                        lluc_new.legend_num, 	
                                        final_collection,
                                        ST_Intersection(concave_hull.final_collection, lluc_new.geom) as final_shape_new 				
                                            FROM lluc_new, $createConcave 					
                                                WHERE ST_Intersects(lluc_new.geom, concave_hull.final_collection)) as intersected_data) as data_new 
                                on data_new.legend_nam = data_old.legend_nam)";
                    
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