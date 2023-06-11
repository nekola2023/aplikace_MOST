<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapová aplikace Holešice</title>
    <script src="https://cesium.com/downloads/cesiumjs/releases/1.105/Build/Cesium/Cesium.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <link href="https://cesium.com/downloads/cesiumjs/releases/1.105/Build/Cesium/Widgets/widgets.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="css/appCesium.css">
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    <style>
        body{
            background-color: #ececec;
        }
        #cesiumContainer {
            position: relative;
            overflow: hidden;
            width: 100%;
            height: 100%;
            border: 5px solid #c5c5c5;  
            z-index: 0;
        }
        body{
            padding: 0;
            margin: 0;
        }
        #legendImage{
            background-color: #fff;
            padding-left: 10px;
            padding-right: 10px;
            padding-top: 10px;
            padding-bottom: 10px;
            border: 5px solid #c5c5c5;    
        }
        @media (max-width: 992px) {
            .fixed-top {
                position:relative;
                height: 150%;
            }
        }
        #legendDiv{
            top:-15px;
            position: absolute;
            left: 7px;
            z-index: 150;
            opacity: 0.75;
        }

        #legendDiv:hover{
            opacity:1;
        }
        #descDiv{
            display: block;
            position: absolute;
            bottom: 30px;
            z-index: 150;
            padding-left: 10px;
            padding-right: 10px;
            padding-top: 10px;
            margin:0px;
            border-style:solid;
            border-color:#c5c5c5;
            background-color:#fff;
            text-align:center;
            opacity: 0.75;
            left:0;
            right:0;
            
        }
        #descDiv:hover{
            opacity: 1;
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-3 one h-100">    
        <h2 id="header">Porovnání změny území <b>HOLEŠICE</b></h2>
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
                        <input class="form-check-input" value="withoutLanduse" type="radio" name="LLUC" id="Landuse" checked> 
                        <label class="form-check-label" for="flexRadioDefault">nezobrazovat využití území</label>
                    </div>       
                    <div id="checkSecond" class="col-lg"> 
                        <input class="form-check-input" value="oldLanduse" type="radio" name="LLUC" id="Landuse">    
                        <label class="form-check-label" for="flexRadioDefault">zobrazit využití území 19. století</label>  
                    </div>
                    <div id="checkThird" class="col-lg">
                        <input class="form-check-input" value="newLanduse" type="radio" name="LLUC" id="Landuse">  
                        <label class="form-check-label" for="flexRadioDefault3">zobrazit využití území 2019</label>
                    </div>           
                <div id="button">
                    <input type="submit" id="register" value="ZOBRAZIT DATA" class="btn btn-outline-primary btn-lg btn-block">
                    
                </div>     
            </form>
            <div id="picture" class="col-lg pt-5 mt-5 pb-2">
                <a href="https://www.natur.cuni.cz/fakulta">
                    <img src="img/UK_nature.PNG" alt="Přf UK" width="170" height="170">
                </a>
            </div>
            <div class="col-lg" id="footer">
                <p>Tato aplikace je výstupem diplomové práce Lukáše Nekoly v rámci studia na Přírodověděcké Katedře Univerzity Karlovy.</p>
                <p id="inputDiff"></p>
            </div>
            
            
        </div>
        <div class="col-lg-9 offset-lg-3 fixed-top two h-100">
            <div id="cesiumContainer">
            <div class="pt-4" id="legendDiv">
                <img src = imageshown id="legendImage">
            </div>
            <div id="descDiv">
                <p class="text-center font-weight-bold text-uppercase"><?php include("query_altitudes.php"); ?></p>
            </div>
            </div>
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
    <script>
        
        /*
        const element = document.getElementById("checkboxing");
        element.addEventListener('change', (event) => {
            if (event.target.checked) {
                const buildingTileset = viewer.scene.primitives.add(Cesium.createOsmBuildings());
            } else {
                buildingTileset.show = viewer.scene.primitives.remove(Cesium.createOsmBuildings()); 
            }
})*/
        
        Cesium.Ion.defaultAccessToken = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJqdGkiOiJiNDFkMDE3Yi05NTRiLTQ5NmUtYTUzZC0wOWM3NThiZDQxNTYiLCJpZCI6MTI2MTk2LCJpYXQiOjE2NzcyNTUxOTR9.kYrd0bUaaMnpHGJbWi8zHW0krp3qRTraDDPga9ziIww';
        const viewer = new Cesium.Viewer('cesiumContainer', {
            terrainProvider: Cesium.createWorldTerrain(),
            imageryProvider: Cesium.createWorldImagery({
                    style: Cesium.IonWorldImageryStyle.AERIAL_WITH_LABELS
             })
        });    
        
        var altiLayer = "PostGIS:cadastral_line";
        var imageshown = "http://localhost:8080/geoserver/wms?REQUEST=GetLegendGraphic&VERSION=1.0.0&FORMAT=image/png&WIDTH=20&HEIGHT=20&LAYER=PostGIS:cadastral_line&LEGEND_OPTIONS=forceLabels:on";
        
        var layerName = "<?php
                            if(isset($_POST["LLUC"])){
                                echo $landUse;
                            } 
                        ?>";
        
        if(layerName === 'oldLanduse'){
            var layerName = "PostGIS:pol_with_old";
            var altiLayer = layerName;
            var imageshown = "http://localhost:8080/geoserver/wms?REQUEST=GetLegendGraphic&VERSION=1.0.0&FORMAT=image/png&WIDTH=20&HEIGHT=20&LAYER=PostGIS:pol_with_old";
        } else if(layerName === 'withoutLanduse'){
            var layerName = "PostGIS:pol_without_LLUC";
            var altiLayer = layerName;
            var imageshown = "http://localhost:8080/geoserver/wms?REQUEST=GetLegendGraphic&VERSION=1.0.0&FORMAT=image/png&WIDTH=20&HEIGHT=20&LAYER=PostGIS:pol_without_LLUC";
        } else if(layerName === 'newLanduse'){
            var layerName = "PostGIS:pol_with_new";
            var altiLayer = layerName;
            var imageshown = "http://localhost:8080/geoserver/wms?REQUEST=GetLegendGraphic&VERSION=1.0.0&FORMAT=image/png&WIDTH=20&HEIGHT=20&LAYER=PostGIS:pol_with_new";
        }

        document.getElementById('legendImage').src = imageshown;
        


        viewer.camera.flyTo({
            destination : Cesium.Cartesian3.fromDegrees(13.55, 50.47, 1200),
            orientation : {
                heading : Cesium.Math.toRadians(0.0),
                pitch : Cesium.Math.toRadians(-15.0),
                roll: Cesium.Math.toRadians(0.0),
                }
            });

        
        
        const geoServerUrl = "http://localhost:8080/geoserver/PostGIS/wms"
            const parameters = {
            version: '1.1.0',
            format: 'image/png',
            srs: 'EPSG:4326',
            transparent: true,
            tiled: true,
            gridSet: 'EPSG:4326',
            
            };

        const webMapServiceImageryProviderOptions = {
            url: geoServerUrl,
            layers: altiLayer,
            parameters: parameters,
            
            };
        const imageryLayer = new Cesium.ImageryLayer(new Cesium.WebMapServiceImageryProvider(webMapServiceImageryProviderOptions));
        viewer.imageryLayers.add(imageryLayer);
        
        if ( window.history.replaceState ) {
            window.history.replaceState( null, null, window.location.href );
        }
        
    </script>
</body>
</html>