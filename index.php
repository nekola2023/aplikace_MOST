<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diplomová práce</title>
    <script src="https://cesium.com/downloads/cesiumjs/releases/1.105/Build/Cesium/Cesium.js"></script>
    <link href="https://cesium.com/downloads/cesiumjs/releases/1.105/Build/Cesium/Widgets/widgets.css" rel="stylesheet">
    <style>
        #cesiumContainer {
            position: absolute;
            top: 0;
            left: 0;
            height: 90%;
            width: 100%;
            margin: 0;
            overflow: hidden;
            padding: 0;
            font-family: sans-serif;
        }
        body {
            padding: 0;
            margin: 0;
            overflow: hidden;
        }
    </style>
</head>
<body>
<div id="cesiumContainer"></div>
  <script>
        Cesium.Ion.defaultAccessToken = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJqdGkiOiJiNDFkMDE3Yi05NTRiLTQ5NmUtYTUzZC0wOWM3NThiZDQxNTYiLCJpZCI6MTI2MTk2LCJpYXQiOjE2NzcyNTUxOTR9.kYrd0bUaaMnpHGJbWi8zHW0krp3qRTraDDPga9ziIww';

        const viewer = new Cesium.Viewer('cesiumContainer', {
            terrainProvider: Cesium.createWorldTerrain()
        });    
    
        viewer.camera.flyTo({
            destination : Cesium.Cartesian3.fromDegrees(13.55, 50.48, 800),
            orientation : {
                heading : Cesium.Math.toRadians(0.0),
                pitch : Cesium.Math.toRadians(-20.0),
            }
        });
        const geoServerUrl = "http://localhost:8080/geoserver/PostGIS/wms"
        const layerName = "PostGIS:katastralni_uzemi_84";
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