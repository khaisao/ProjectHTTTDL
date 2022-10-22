<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>OpenStreetMap &amp; OpenLayers - Marker Example</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

    <link rel="stylesheet" href="https://openlayers.org/en/v4.6.5/css/ol.css" type="text/css" />
    <script src="https://openlayers.org/en/v4.6.5/build/ol.js" type="text/javascript"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js" type="text/javascript"></script>

    
    <style>
        /*
            .map, .righ-panel {
                height: 500px;
                width: 80%;
                float: left;
            }
            */
        .map,
        .righ-panel {
            height: 98vh;
            width: 80vw;
            float: left;
        }

        .map {
            border: 1px solid #000;
        }

        .ol-popup {
            position: absolute;
            background-color: white;
            -webkit-filter: drop-shadow(0 1px 4px rgba(0, 0, 0, 0.2));
            filter: drop-shadow(0 1px 4px rgba(0, 0, 0, 0.2));
            padding: 15px;
            border-radius: 10px;
            border: 1px solid #cccccc;
            bottom: 12px;
            left: -50px;
            min-width: 180px;
        }

        .ol-popup:after,
        .ol-popup:before {
            top: 100%;
            border: solid transparent;
            content: " ";
            height: 0;
            width: 0;
            position: absolute;
            pointer-events: none;
        }

        .ol-popup:after {
            border-top-color: white;
            border-width: 10px;
            left: 48px;
            margin-left: -10px;
        }

        .ol-popup:before {
            border-top-color: #cccccc;
            border-width: 11px;
            left: 48px;
            margin-left: -11px;
        }

        .ol-popup-closer {
            text-decoration: none;
            position: absolute;
            top: 2px;
            right: 8px;
        }

        .ol-popup-closer:after {
            content: "✖";
        }
    </style>
</head>

<body onload="initialize_map();">

    <table>

        <tr>

            <td>
                <div id="map" class="map"></div>
                <div id="map" style="width: 50vw; height: 50vh;"></div>
                <div id="popup" class="ol-popup">
                    <a href="#" id="popup-closer" class="ol-popup-closer"></a>
                    <div id="popup-content"></div>
                </div>
                <!--<div id="map" style="width: 80vw; height: 100vh;"></div>-->
            </td>
            <td>
                <input type="textinput" id="ctiy"><br/>
                <button id="btnSeacher"> Tìm kiếm</button>
                <br />
                <br />
                <br />
                <input onclick="oncheckmarkets();" type="checkbox" id="markets" name="layer" value="markets"> Chợ nông sản <br />
                <input onclick="oncheckatm();" type="checkbox" id="atm" name="layer" value="atm"> ATM-BANKING <br />
                <input onclick="oncheckhospitals();" type="checkbox" id="hospitals" name="layer" value="hospitals"> Bệnh viện <br />
                <input onclick="oncheckbank();" type="checkbox" id="bank" name="layer" value="bank"> Ngân Hàng <br />
                <input onclick="oncheckstation();" type="checkbox" id="station" name="layer" value="station"> Trường Học <br />
                <input onclick="oncheckrails();" type="checkbox" id="rails" name="layer" value="rails"> Đường phố <br />
                <input onclick="oncheckarg()" type="checkbox" id="arg" name="layer" value="arg"> Washington DC <br />
                
                <button id="btnRest"> Làm mới </button>
                <a href="http://localhost/BTL_HTTT_DiaLy/BTL_HTTT_DiaLy_2/btl1.php"><button id="btnRest"> Công cụ đo </button></a>

            </td>
        </tr>
    </table>
    <?php include 'CMR_pgsqlAPI.php' ?>

    <script>
        var format = 'image/png';
        var map;
        var minX = -77.120849609375;
        var minY = 38.7906188964844;
        var maxX = -76.9080963134766;
        var maxY = 38.9969940185547;
        var cenX = (minX + maxX) / 2;
        var cenY = (minY + maxY) / 2;
        var mapLat = cenY;
        var mapLng = cenX;
        var mapDefaultZoom = 6;

        var layerARG_adm1;
        var layer_rails;
        var layer_station;
        //bankkkkkkkkkkkkkkkkkkkkk
        var layer_bank;
        //hospitallllllllllllllllllllllllllllllllllllll
        var layer_hospitals;
        /////// atm
        var layer_atm;
        //// chợ nông sản
        var layer_markets

        var vectorLayer;
        var styleFunction;
        var styles;
        var container = document.getElementById('popup');
        var content = document.getElementById('popup-content');
        var closer = document.getElementById('popup-closer');
        var ctiy = document.getElementById("ctiy");
        var chkARG = document.getElementById("arg");
        var chkStation = document.getElementById("station");
        var chkRails = document.getElementById("rails");
        //bank
        var chkBank = document.getElementById("bank")
        //bệnh viện
        varchkHospitals = document.getElementById("hospitals")
        // atm
        varchkATM = document.getElementById('atm')
        // chợ nông sản
        varchkMarkets = document.getElementById('markets')
        
        var value ;
        /**
         * Create an overlay to anchor the popup to the map.
         */
        var overlay = new ol.Overlay( /** @type {olx.OverlayOptions} */ ({
            element: container,
            autoPan: true,
            autoPanAnimation: {
                duration: 250
            }
        }));
        closer.onclick = function() {
            overlay.setPosition(undefined);
            closer.blur();
            return false;
        };
        function handleOnCheck(id, layer) {
            if (document.getElementById(id).checked) {
                value = document.getElementById(id).value;
                // map.setLayerGroup(new ol.layer.Group())
                map.addLayer(layer)
                vectorLayer = new ol.layer.Vector({});
                map.addLayer(vectorLayer);
            } else {
                map.removeLayer(layer);
                map.removeLayer(vectorLayer);
            }
        }
        function myFunction() {
            var popup = document.getElementById("popup");
            popup.classList.toggle("show");
        }
        function oncheckstation() {
            handleOnCheck('station', layer_station);

        }
        function oncheckrails() {
            handleOnCheck('rails', layer_rails);

        }
        function oncheckarg() {
            handleOnCheck('arg', layerARG_adm1);
        }
        //bankkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkk
        function oncheckbank() {
            handleOnCheck('bank', layer_bank);
        }
        // bệnh viện
        function oncheckhospitals() {
            handleOnCheck('hospitals', layer_hospitals);
        }
        // ATM
        function oncheckatm() {
            handleOnCheck('ATM_Banking', layer_atm);
        }
        // CHợ 
        function oncheckmarkets() {
            handleOnCheck('farmers_market_locations', layer_markets);
        }


        function initialize_map() {

            //*
            layerBG = new ol.layer.Tile({
                source: new ol.source.OSM({})
            });

            //*/
            layerARG_adm1 = new ol.layer.Image({
                source: new ol.source.ImageWMS({
                    ratio: 1,
                    url: 'http://localhost:8080/geoserver/cite/wms?',
                    params: {
                        'FORMAT': format,
                        'VERSION': '1.1.1',
                        STYLES: '',
                        LAYERS: 'wards_from_2012',
                    }
                })

            });

            layer_rails = new ol.layer.Image({
                source: new ol.source.ImageWMS({
                    ratio: 1,
                    url: 'http://localhost:8080/geoserver/cite/wms?',
                    params: {
                        'FORMAT': format,
                        'VERSION': '1.1.1',
                        STYLES: '',
                        LAYERS: 'roadway',
                    }
                })

            });

            layer_station = new ol.layer.Image({
                source: new ol.source.ImageWMS({
                    ratio: 1,
                    url: 'http://localhost:8080/geoserver/cite/wms?',
                    params: {
                        'FORMAT': format,
                        'VERSION': '1.1.1',
                        STYLES: '',
                        LAYERS: 'schools',
                    }
                })

            });
///////////////////////// bankkingggggggggggggggggggggggggggggggggg
            layer_bank = new ol.layer.Image({
                source: new ol.source.ImageWMS({
                    ratio: 1,
                    url: 'http://localhost:8080/geoserver/cite/wms?',
                    params: {
                        'FORMAT': format,
                        'VERSION': '1.1.1',
                        STYLES: '',
                        LAYERS: 'bank',
                    }
                })

            });
/////////////////////// bệnh viện
            layer_hospitals = new ol.layer.Image({
                source: new ol.source.ImageWMS({
                    ratio: 1,
                    url: 'http://localhost:8080/geoserver/cite/wms?',
                    params: {
                        'FORMAT': format,
                        'VERSION': '1.1.1',
                        STYLES: '',
                        LAYERS: 'hospitals',
                    }
                })

            });
///////////////// cây ATM
            layer_atm = new ol.layer.Image({
                source: new ol.source.ImageWMS({
                    ratio: 1,
                    url: 'http://localhost:8080/geoserver/cite/wms?',
                    params: {
                        'FORMAT': format,
                        'VERSION': '1.1.1',
                        STYLES: '',
                        LAYERS: 'ATM_Banking',
                    }
                })

            });
/////////////////////// bệnh viện
            layer_markets = new ol.layer.Image({
                source: new ol.source.ImageWMS({
                    ratio: 1,
                    url: 'http://localhost:8080/geoserver/cite/wms?',
                    params: {
                        'FORMAT': format,
                        'VERSION': '1.1.1',
                        STYLES: '',
                        LAYERS: 'farmers_market_locations',
                    }
                })

            });

            var viewMap = new ol.View({
                center: ol.proj.fromLonLat([mapLng, mapLat]),
                zoom: mapDefaultZoom
            });
            map = new ol.Map({
                target: "map",
                layers: [layerBG],
                view: viewMap,
                overlays: [overlay], //them khai bao overlays
            });
            styles = {
                'Point': new ol.style.Style({
                    stroke: new ol.style.Stroke({
                        color: 'yellow',
                        width: 3
                    })
                }),
                'MultiLineString': new ol.style.Style({

                    stroke: new ol.style.Stroke({
                        color: 'red',
                        width: 3
                    })
                }),
                'Polygon': new ol.style.Style({
                    stroke: new ol.style.Stroke({
                        color: 'red',
                        width: 3
                    })
                }),
                'MultiPolygon': new ol.style.Style({
                    fill: new ol.style.Fill({
                        color: 'orange'
                    }),
                    stroke: new ol.style.Stroke({
                        color: 'yellow',
                        width: 2
                    })
                })
            };
            styleFunction = function(feature) {
                return styles[feature.getGeometry().getType()];
            };
            var stylePoint = new ol.style.Style({
                image: new ol.style.Icon({
                    anchor: [0.5, 0.5],
                    anchorXUnits: "fraction",
                    anchorYUnits: "fraction",
                    src: "http://localhost:80/Nhom08-HTTTDL-61HT//Nhom08-HTTTDL-61HT/BTL_HTTT_DiaLy/Yellow_dot.svg"
                })
            });
            vectorLayer = new ol.layer.Vector({
                style: styleFunction
            });
            map.addLayer(vectorLayer);
            var buttonReset = document.getElementById("btnRest").addEventListener("click", () => {
                location.reload();
            })
            var button = document.getElementById("btnSeacher").addEventListener("click",
                () => {
                    vectorLayer.setStyle(styleFunction);
                    ctiy.value.length ?
                        $.ajax({
                            type: "POST",
                            url: "CMR_pgsqlAPI.php",
                            data: {
                                name: ctiy.value
                            },
                            success: function(result, status, erro) {

                                if (result == 'null')
                                    alert("không tìm thấy đối tượng");
                                else
                                    highLightObj(result);
                            },
                            error: function(req, status, error) {
                                alert(req + " " + status + " " + error);
                            }
                        }) : alert("Nhập dữ liệu tìm kiếm")
                });
            function createJsonObj(result) {
                var geojsonObject = '{' +
                    '"type": "FeatureCollection",' +
                    '"crs": {' +
                    '"type": "name",' +
                    '"properties": {' +
                    '"name": "EPSG:4326"' +
                    '}' +
                    '},' +
                    '"features": [{' +
                    '"type": "Feature",' +
                    '"geometry": ' + result +
                    '}]' +
                    '}';
                return geojsonObject;
            }
            function highLightGeoJsonObj(paObjJson) {
                var vectorSource = new ol.source.Vector({
                    features: (new ol.format.GeoJSON()).readFeatures(paObjJson, {
                        dataProjection: 'EPSG:4326',
                        featureProjection: 'EPSG:3857'
                    })
                });
                vectorLayer.setSource(vectorSource);
            }

            function highLightObj(result) {
                var strObjJson = createJsonObj(result);
                var objJson = JSON.parse(strObjJson);
                highLightGeoJsonObj(objJson);
            }

            function displayObjInfo(result, coordinate) {
                $("#popup-content").html(result);
                overlay.setPosition(coordinate);

            }

            map.on('singleclick', function(evt) {
                var myPoint = 'POINT(12,5)';
                var lonlat = ol.proj.transform(evt.coordinate, 'EPSG:3857', 'EPSG:4326');
                var lon = lonlat[0];
                var lat = lonlat[1];
                var myPoint = 'POINT(' + lon + ' ' + lat + ')';
                
                if (value == 'arg') {
                    vectorLayer.setStyle(styleFunction);

                    $.ajax({
                        type: "POST",
                        url: "CMR_pgsqlAPI.php",
                        data: {
                            functionname: 'getInfoARGToAjax',
                            paPoint: myPoint
                        },
                        success: function(result, status, erro) {
                            displayObjInfo(result, evt.coordinate);
                        },
                        error: function(req, status, error) {
                            alert(req + " " + status + " " + error);
                        }
                    });
                    $.ajax({
                        type: "POST",
                        url: "CMR_pgsqlAPI.php",
                        data: {
                            functionname: 'getGeoARGToAjax',
                            paPoint: myPoint
                        },
                        success: function(result, status, erro) {
                            highLightObj(result);
                        },
                        error: function(req, status, error) {
                            alert(req + " " + status + " " + error);
                        }
                    });
                }
                if (value == "rails") {
                    //rails
                    vectorLayer.setStyle(styleFunction);
                    $.ajax({
                        type: "POST",
                        url: "CMR_pgsqlAPI.php",
                        data: {
                            functionname: 'getInfoRailsToAjax',
                            paPoint: myPoint
                        },
                        success: function(result, status, erro) {
                            displayObjInfo(result, evt.coordinate);
                        },
                        error: function(req, status, error) {
                            alert(req + " " + status + " " + error);
                        }
                    });
                    $.ajax({
                        type: "POST",
                        url: "CMR_pgsqlAPI.php",
                        data: {
                            functionname: 'getRailsToAjax',
                            paPoint: myPoint
                        },
                        success: function(result, status, erro) {
                            highLightObj(result);
                        },
                        error: function(req, status, error) {
                            alert(req + " " + status + " " + error);
                        }
                    });
                }
                if (value == "station")
                { // trường học
                    vectorLayer.setStyle(stylePoint);
                    $.ajax({
                        type: "POST",
                        url: "CMR_pgsqlAPI.php",
                        data: {
                            functionname: 'getInfoStationToAjax',
                            paPoint: myPoint
                        },
                        success: function(result, status, erro) {
                            displayObjInfo(result, evt.coordinate);
                        },
                        error: function(req, status, error) {
                            alert(req + " " + status + " " + error);
                        }
                    });

                    $.ajax({
                        type: "POST",
                        url: "CMR_pgsqlAPI.php",
                        data: {
                            functionname: 'getStationToAjax',
                            paPoint: myPoint
                        },
                        success: function(result, status, erro) {
                            highLightObj(result);
                        },
                        error: function(req, status, error) {
                            alert(req + " " + status + " " + error);
                        }
                    });
                }
            /// bankingggggggggggggggg
            if (value == "bank")
                {
                    vectorLayer.setStyle(stylePoint);
                    $.ajax({
                        type: "POST",
                        url: "CMR_pgsqlAPI.php",
                        data: {
                            functionname: 'getInfoBankToAjax',
                            paPoint: myPoint
                        },
                        success: function(result, status, erro) {
                            displayObjInfo(result, evt.coordinate);
                        },
                        error: function(req, status, error) {
                            alert(req + " " + status + " " + error);
                        }
                    });

                    $.ajax({
                        type: "POST",
                        url: "CMR_pgsqlAPI.php",
                        data: {
                            functionname: 'getBankToAjax',
                            paPoint: myPoint
                        },
                        success: function(result, status, erro) {
                            highLightObj(result);
                        },
                        error: function(req, status, error) {
                            alert(req + " " + status + " " + error);
                        }
                    });
                }
            /// ATMMMMMMMMMMMMMMMMMM
            if (value == "atm")
                {
                    vectorLayer.setStyle(stylePoint);
                    $.ajax({
                        type: "POST",
                        url: "CMR_pgsqlAPI.php",
                        data: {
                            functionname: 'getInfoATMToAjax',
                            paPoint: myPoint
                        },
                        success: function(result, status, erro) {
                            displayObjInfo(result, evt.coordinate);
                        },
                        error: function(req, status, error) {
                            alert(req + " " + status + " " + error);
                        }
                    });

                    $.ajax({
                        type: "POST",
                        url: "CMR_pgsqlAPI.php",
                        data: {
                            functionname: 'getATMToAjax',
                            paPoint: myPoint
                        },
                        success: function(result, status, erro) {
                            highLightObj(result);
                        },
                        error: function(req, status, error) {
                            alert(req + " " + status + " " + error);
                        }
                    });
                }
/// CHợ 
            if (value == "markets")
                {
                    vectorLayer.setStyle(stylePoint);
                    $.ajax({
                        type: "POST",
                        url: "CMR_pgsqlAPI.php",
                        data: {
                            functionname: 'getInfoMarketsToAjax',
                            paPoint: myPoint
                        },
                        success: function(result, status, erro) {
                            displayObjInfo(result, evt.coordinate);
                        },
                        error: function(req, status, error) {
                            alert(req + " " + status + " " + error);
                        }
                    });

                    $.ajax({
                        type: "POST",
                        url: "CMR_pgsqlAPI.php",
                        data: {
                            functionname: 'getMarketsoAjax',
                            paPoint: myPoint
                        },
                        success: function(result, status, erro) {
                            highLightObj(result);
                        },
                        error: function(req, status, error) {
                            alert(req + " " + status + " " + error);
                        }
                    });
                }
            //// bệnh viện
            if (value == "hospitals")
                {
                    vectorLayer.setStyle(stylePoint);
                    $.ajax({
                        type: "POST",
                        url: "CMR_pgsqlAPI.php",
                        data: {
                            functionname: 'getInfoHospitalsToAjax',
                            paPoint: myPoint
                        },
                        success: function(result, status, erro) {
                            displayObjInfo(result, evt.coordinate);
                        },
                        error: function(req, status, error) {
                            alert(req + " " + status + " " + error);
                        }
                    });

                    $.ajax({
                        type: "POST",
                        url: "CMR_pgsqlAPI.php",
                        data: {
                            functionname: 'getHospitalsToAjax',
                            paPoint: myPoint
                        },
                        success: function(result, status, erro) {
                            highLightObj(result);
                        },
                        error: function(req, status, error) {
                            alert(req + " " + status + " " + error);
                        }
                    });
                }
                
            });
        };
    </script>

    
</body>

</html>