<?php 
        
        include 'db_connection.php';
        
        //druhé testování na straně backendu na úplnost formuláře
        
        if(isset($_POST["firstAlt"]) && isset($_POST["secondAlt"]) && isset($_POST["diffAlt"]) && isset($_POST["operAlt"]) && isset($_POST["upDown"]) && ($_POST["LLUC"])){ //both values must be filled.
            
            
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
            if($secondYear == $firstYear){
                exit("Zadal jsi stejná období pro porovnání");
            }
            //přeno zbylých proměnných
            $diff = $_POST["diffAlt"];
            $oper = $_POST["operAlt"];
            $plusMinus = $_POST["upDown"];
            $landUse = $_POST['LLUC'];
                     
            //rozhodovací pravidla a výběr konkrétních dat
            if(($oper == '<' && $plusMinus == 'plus') || ($oper == '<' && $plusMinus == 'minus')) {
                if($plusMinus == 'minus'){ 
                    $diff = -($diff); 
                    $rulesQuery = "($secondYear - $firstYear) between $diff and 0";
                }
                elseif($plusMinus == 'plus'){ 
                    $rulesQuery = "($secondYear - $firstYear) between 0 and $diff";
                }
            }
            else { 
                if($plusMinus == 'plus') { 
                    $rulesQuery = "($secondYear - $firstYear) > $diff";
                }
                elseif($plusMinus == 'minus') { 
                    $rulesQuery = "($firstYear - $secondYear) > $diff";      
                }   
            }

            //testování existence vyselektovaných dat a první přístup do databáze
            $queryNumberRow = "SELECT * FROM altitudes_holesice where $rulesQuery";
            $resultNumberRow = pg_query($queryNumberRow);    
            
            //ukončení skriptu v případě neexistence dat
            if(pg_num_rows($resultNumberRow) == 0) {
                $landUse = "";
                echo '<span style="color:red;">VÝSTUP PRO ZVOLENÝ VZOREK DAT NEEXISTUJE!</span>';

            }
            else{
                $createConcave =    "(SELECT st_union(st_simplify(st_buffer(st_concavehull(geom_cluster, 0.01, true),7.5),5)) AS final_collection FROM 
                                        (SELECT id_collection, st_collect(geom) as geom_cluster FROM 
                                            (SELECT ST_ClusterDBSCAN(geom, eps:= 20, minpoints := 3) over() AS id_collection, geom FROM 
                                                (SELECT * FROM altitudes_holesice WHERE $rulesQuery) as main_query) as make_clusters
                                            GROUP by id_collection) as geom_from_make_clusters
                                        WHERE (id_collection IS NOT NULL)) as concave_hull";
            
                $createConcaveTesting =     "SELECT st_union(st_simplify(st_buffer(st_concavehull(geom_cluster, 0.01, true),7.5),5)) AS final_collection FROM 
                                                (SELECT id_collection, st_collect(geom) as geom_cluster FROM 
                                                    (SELECT ST_ClusterDBSCAN(geom, eps:= 20, minpoints := 3) over() AS id_collection, geom FROM 
                                                        (SELECT * FROM altitudes_holesice WHERE $rulesQuery) as main_query) as make_clusters
                                                    GROUP by id_collection) as geom_from_make_clusters
                                                WHERE (id_collection IS NOT NULL)";
            
            if(pg_field_is_null(pg_query($createConcaveTesting),NULL,"final_collection") == 1) {
                $landUse = "";
                echo '<span style="color:red;">VÝSTUP PRO ZVOLENÝ VZOREK DAT NEEXISTUJE!fsefs</span>';
            }
            else{
                include 'rewriting_text.php';
                $textDesc = "Zobrazena jsou data reprezentující $plusMinusName $operName než $diffName metrů, a to mezi lety $firstYearName a $secondYearName <br> $landUseName";
                echo $textDesc;
            if($landUse == 'withoutLanduse'){
                $queryDrop = "DROP MATERIALIZED VIEW output_basic_materialized";

                $result =   "CREATE MATERIALIZED VIEW output_basic_materialized AS(
                                SELECT 
                                    round((cast(st_area(final_collection)/1000000 as numeric)),3) as all_area, 
                                    round(cast((st_area(final_collection)/1000000) / 6.04 * 100 as numeric),2) as procent_to_ku, 
                                    st_transform(final_collection,4326) as final_collection FROM (
                                        SELECT
                                            ST_Intersection(concave_hull.final_collection, cadastral_pol.geom) as final_collection
                                                FROM cadastral_pol, $createConcave
                                                   ) as intersected_data)";
  
            }
            else {
                if($landUse == 'oldLanduse') {
                    
                    
                    $queryDrop = "DROP MATERIALIZED VIEW output_old_view_materialized";
                    
                    $result = "CREATE MATERIALIZED VIEW output_old_view_materialized as (     
                        SELECT 
                        st_transform(final_shape,4326) as final_shape,
                        legend_nam,
                        intersected_data.ID,
                        all_area,
                        round((all_area / round((cast(st_area(cadastral_pol.geom)/1000000 as numeric)),3) ) * 100,2) as procent_to_ku,
                        round((cast(st_area(final_shape)/1000000 as numeric)),3) as attr_area, 
                        round((round((cast(st_area(final_shape)/1000000 as numeric)),3) / all_area * 100 ),2) as procent_LLUC_to_output 	
                         FROM cadastral_pol, (
                            SELECT 
                                *,
                                ST_Intersection(output_area.final_collection, lluc.geom) as final_shape
                                    FROM lluc_old as lluc, (  
                                    SELECT 
                                        round((cast(st_area(final_collection)/1000000 as numeric)),3) as all_area, 
                                        final_collection FROM (
                                            SELECT
                                                ST_Intersection(concave_hull.final_collection, cadastral_pol.geom) as final_collection
                                                    FROM cadastral_pol, $createConcave
                                                        ) as intersected_data1             
                                                ) as output_area
                                                   ) as intersected_data)";
                }
                elseif($landUse == 'newLanduse') {
                    $queryDrop = "DROP MATERIALIZED VIEW output_new_view_materialized";
                    $result = "CREATE MATERIALIZED VIEW output_new_view_materialized as (
                        SELECT
    st_transform(data_new.final_shape,4326) as final_shape,                   
    data_new.id::varchar(5),
    data_new.legend_nam,
    data_new.all_area,
    round((data_new.all_area / round((cast(st_area(cadastral_pol.geom)/1000000 as numeric)),3) ) * 100,2) as procent_to_ku,
    data_new.attr_area,
    round((data_new.attr_area / data_new.all_area),3) * 100 as procent_to_attr,
    
    round((data_new.attr_area - coalesce(data_old.attr_area,0)),3) as comparing_area
        
        FROM cadastral_pol, (SELECT 
            legend_nam,
            round((cast(st_area(final_shape)/1000000 as numeric)),3) as attr_area
             FROM(
                SELECT 
                    legend_nam,
                    ST_Intersection(output_area.final_collection, lluc.geom) as final_shape
                        FROM lluc_old as lluc, (  
                        SELECT 
                            final_collection FROM (
                                SELECT
                                    ST_Intersection(concave_hull.final_collection, cadastral_pol.geom) as final_collection
                                        FROM cadastral_pol, $createConcave
                                            ) as intersected_data             
                                    ) as output_area
                                        ) as intersected_data) as data_old
                right outer join (	
                   SELECT 
            *,
            round((cast(st_area(final_shape)/1000000 as numeric)),3) as attr_area 
             FROM(
                SELECT 
                    *,
                    ST_Intersection(output_area.final_collection, lluc.geom) as final_shape
                        FROM lluc_new as lluc, (  
                        SELECT 
                            round((cast(st_area(final_collection)/1000000 as numeric)),3) as all_area, 
                            final_collection FROM (
                                SELECT
                                    ST_Intersection(concave_hull.final_collection, cadastral_pol.geom) as final_collection
                                        FROM cadastral_pol, $createConcave
                                            ) as intersected_data             
                                    ) as output_area
                                        ) as intersected_data) as data_new
                on data_new.legend_nam = data_old.legend_nam)";
                    
                }
            }
            
            pg_query($queryDrop);

            $result = pg_query($result) or die('Error message: ' . pg_last_error());

            while ($row = pg_fetch_row($result)) {
                echo var_dump($row);
            }
        
            //pg_free_result($result);
            pg_close($db_connect);
            }
        }
    }
        //výpis hlášky v případě prozatím nezobrazených dat
        else{
            echo 'Žádná data nejsou prozatím zobrazena';
            
        }  
    ?>