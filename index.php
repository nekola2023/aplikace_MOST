<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diplomová práce</title>
    <script src="https://cesium.com/downloads/cesiumjs/releases/1.105/Build/Cesium/Cesium.js"></script>
    <link href="https://cesium.com/downloads/cesiumjs/releases/1.105/Build/Cesium/Widgets/widgets.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
    <!--<link rel="stylesheet" href="css/appCesium.css">-->
    <style>
        #cesiumContainer {
            position: absolute;
            top: 0;
            left: 0;
            top: 250px;
            
            width: 100%;
            margin: 0;
            overflow: hidden;
            padding: 0;
        }
        body {
            padding: 0;
            margin: 0;
            overflow: hidden;
        }
    </style>

</head>
<body>
    <form action="index.php" method="post">
        <input type="text" name="name">
        <input type="submit">
    </form>
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
            echo $data;
            }

        $query = "CREATE OR REPLACE VIEW try_view AS (SELECT * FROM altitudes_holesice WHERE altitude_2020 > '$data')";
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



    <div id="cesiumContainer"></div>
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
        const layerName = "PostGIS:try_view";
            const parameters = {
            version: '1.1.0',
            format: 'image/png',
            srs: 'EPSG:4326',
            transparent: true,
            };

        const webMapServiceImageryProviderOptions = {
            url: geoServerUrl,
            layers: layerName,
            parameters: parameters,
            };
        const imageryLayer = new Cesium.ImageryLayer(new Cesium.WebMapServiceImageryProvider(webMapServiceImageryProviderOptions));
        viewer.imageryLayers.add(imageryLayer);
    </script>
</body>
</html>