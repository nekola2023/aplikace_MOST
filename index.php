<?php include 'php/db_connection.php';?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diplomová práce</title>
    <script src="https://cesium.com/downloads/cesiumjs/releases/1.105/Build/Cesium/Cesium.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <link href="https://cesium.com/downloads/cesiumjs/releases/1.105/Build/Cesium/Widgets/widgets.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="css/appCesium.css">
    <style>
        #cesiumContainer {
            
        }
        body{
            padding: 0;
            margin: 0;
            
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-3 bg-light">    
        <h2 id="header">Porovnání změny území <b>MOSTECKO</b></h2>
            <form action="index.php" method="post">
                    <div class="col-lg">
                        <small class="form-text text-muted">Zobrazit plochu o</small>
                        <select id="selectupDown" name="upDown" class="custom-select" required>
                            <option class="firstSelect" value="" disabled selected value></option>
                            <option value="minus">poklesu n.v.</option>
                            <option value="plus">navýšení n.v.</option>
                        <small class="form-text text-muted">nadmořské výšky </small>
                        </select>
                    </div> 
                <div class="row">
                    <div class="col-lg-6">
                        <small class="form-text text-muted">jejíž změna je</small>
                        <select id="selectOper" name="operAlt" class="custom-select" required>
                            <option class="firstSelect" value="" disabled selected value></option>
                            <option value=">">větší</option>
                            <option value="<">menší</option>
                        </select>
                    </div>  
                    <div class="col-lg-6">
                        <small class="form-text text-muted">než</small>
                        <input id="selectDiff" class="form-control" type="number" name="diffAlt" required/>
                        <small class="form-text text-muted"> </small>
                    </div>
                </div>  

                    <div class="col-lg">
                        <small class="form-text text-muted">a to mezi lety</small>
                        <select id="selectFirst" name="firstAlt" class="custom-select custom-select" required>
                            <option class="firstSelect" value="" disabled selected value></option>
                            <option value="1">1938</option>
                            <option value="2">1951</option>
                            <option value="3">2000</option>
                        </select>
                        
                        <small class="form-text text-muted">a</small>    
                        <select id="selectSecond" name="secondAlt" class="custom-select custom-select" required>
                            <option class="firstSelect" value="" disabled selected value></option>
                            <option value="2">1951</option>
                            <option value="3">2000</option>
                            <option value="4">2020</option>
                        </select>              
                    </div>
                    <div id="checkFirst" class="col-lg">
                        <input class="form-check-input" value="withoutLanduse" type="radio" name="LLUC" id="flexRadioDefault" checked> 
                        <label class="form-check-label" for="flexRadioDefault">nezobrazovat využití území</label>
                    </div>       
                    <div id="checkSecond" class="col-lg"> 
                        <input class="form-check-input" value="oldLanduse" type="radio" name="LLUC" id="flexRadioDefault2">    
                        <label class="form-check-label" for="flexRadioDefault2">zobrazit využití území 19. století</label>  
                    </div>
                    <div id="checkThird" class="col-lg">
                        <input class="form-check-input" value="newLanduse" type="radio" name="LLUC" id="flexRadioDefault3">  
                        <label class="form-check-label" for="flexRadioDefault3">zobrazit využití území 2019</label>
                    </div>           
                <div id="button">
                    <input type="submit" id="register" value="ZOBRAZIT DATA" class="btn btn-success btn-lg btn-block">
                    
                </div>     
            </form>
            <div id="picture" class="col-lg pt-4 pb-2">
                <a href="https://www.natur.cuni.cz/fakulta">
                    <img src="img/UK_nature.PNG" alt="Přf UK" width="170" height="170">
                </a>
            </div>
            <div class="col-lg" id="footer">
                <p>Tato aplikace je výstupem diplomové práce Lukáše Nekoly v rámci studia na Přírodověděcké Katedře Univerzity Karlovy.</p>
            </div>
        </div>
        <div class="col-lg-9 bg-light">
            <div id="cesiumContainer"></div>
        </div>
    </div>
</div> 

<script>
    var removed;
    $('#selectFirst').change( function() {
        var value = this.value;
        $('#selectSecond').prepend(removed);
        var toKeep = $('#selectSecond option').filter( function( ) {
            return parseInt(this.value, 10) > parseInt( value, 10);
            });
        removed =  $('#selectSecond option').filter( function( ) {
            return parseInt(this.value, 10) < parseInt( value, 10);
        });
        $('#selectSecond').html(toKeep);
    });
</script>

    <?php
        //druhé testování na straně backendu na úplnost formuláře
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
    
    <script>
        Cesium.Ion.defaultAccessToken = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJqdGkiOiJiNDFkMDE3Yi05NTRiLTQ5NmUtYTUzZC0wOWM3NThiZDQxNTYiLCJpZCI6MTI2MTk2LCJpYXQiOjE2NzcyNTUxOTR9.kYrd0bUaaMnpHGJbWi8zHW0krp3qRTraDDPga9ziIww';
        const viewer = new Cesium.Viewer('cesiumContainer', {
            terrainProvider: Cesium.createWorldTerrain()
        });    
    
        viewer.camera.flyTo({
            destination : Cesium.Cartesian3.fromDegrees(13.55, 50.45, 5000),
            orientation : {
                heading : Cesium.Math.toRadians(0.0),
                pitch : Cesium.Math.toRadians(-40.0),
                roll: Cesium.Math.toRadians(0.0),
                }
            });
        const geoServerUrl = "http://localhost:8080/geoserver/PostGIS/wms"
        const altiLayer = "	PostGIS:show_polygon_altitude";
            const parameters = {
            version: '1.1.0',
            format: 'image/png',
            srs: 'EPSG:4326',
            transparent: true,
            };

        const webMapServiceImageryProviderOptions = {
            url: geoServerUrl,
            layers: altiLayer,
            parameters: parameters,
            };
        const imageryLayer = new Cesium.ImageryLayer(new Cesium.WebMapServiceImageryProvider(webMapServiceImageryProviderOptions));
        viewer.imageryLayers.add(imageryLayer);
    </script>
</body>
</html>