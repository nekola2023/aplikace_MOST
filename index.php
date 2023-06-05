<?php ?>
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
            position: relative;
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
                        <input class="form-check-input" value="withoutLanduse" type="radio" name="LLUC" id="Landuse" checked> 
                        <label class="form-check-label" for="flexRadioDefault">nezobrazovat využití území</label>
                    </div>       
                    <div id="checkSecond" class="col-lg"> 
                        <input class="form-check-input" value="oldLanduse" type="radio" name="LLUC" id="Landuse">    
                        <label class="form-check-label" for="flexRadioDefault2">zobrazit využití území 19. století</label>  
                    </div>
                    <div id="checkThird" class="col-lg">
                        <input class="form-check-input" value="newLanduse" type="radio" name="LLUC" id="Landuse">  
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
                <p id="inputDiff"></p>
            </div>
            
        </div>
        <div class="col-lg-9 bg-light">
            <div id="cesiumContainer">
            
                <p class="text-center font-weight-bold text-uppercase"><?php include("query_altitudes.php"); ?></p>
            

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
        var layerName = "<?php echo $landUse; ?>";
        
        if(layerName === 'oldLanduse'){
        var layerName = "PostGIS:pol_with_old";
        var altiLayer = layerName;
        } else if(layerName === 'withoutLanduse'){
            var layerName = "PostGIS:pol_without_LLUC";
            var altiLayer = layerName;
        } else{
            var layerName = "PostGIS:pol_with_old";
            var altiLayer = layerName;
        }
        
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
        
        //const altiLayer = "PostGIS:pol_with_old";

        const geoServerUrl = "http://localhost:8080/geoserver/PostGIS/wms"
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