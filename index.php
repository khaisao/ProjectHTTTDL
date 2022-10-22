<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>OpenStreetMap &amp; OpenLayers - Marker Example</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

    <link rel="stylesheet" href="https://openlayers.org/en/v4.6.5/css/ol.css" type="text/css" />
    <script src="https://openlayers.org/en/v4.6.5/build/ol.js" type="text/javascript"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js" type="text/javascript"></script>
    <script src="dependencies/ol.js"></script>



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
                <input type="textinput" id="ctiy"><br />
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
                <div class="toolbar">
                    <button id="btn1" title="Distance Measurement" geomtype="LineString"></button>
                    <button id="btn2" title="Area Measurement" geomtype="Polygon"></button>
                    <button id="btn3" title="Clear Graphics"></button>
                </div>

            </td>
        </tr>
    </table>
    <?php include 'CMR_pgsqlAPI.php' ?>
    
    <script src="script.js"></script>

</body>

</html>