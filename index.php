<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>OpenStreetMap &amp; OpenLayers - Marker Example</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

    <link rel="stylesheet" href="https://openlayers.org/en/v4.6.5/css/ol.css" type="text/css" />
    <link rel="stylesheet" href="style.css">
    <script src="https://openlayers.org/en/v4.6.5/build/ol.js" type="text/javascript"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js" type="text/javascript"></script>
    <script src="dependencies/ol.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

</head>

<body onload="initialize_map();">

    <table>
        <tr>
            <td>
                <div id="map" class="map"></div>
                <div id="popup" class="ol-popup">
                    <a href="#" id="popup-closer" class="ol-popup-closer"></a>
                    <div id="popup-content"></div>
                </div>
                <div class="infomation">
                    <div class="infomation_box" id="infomation_box"></div>
                    <i class="fa fa-cloud close_infomation" id="close_infomation"></i>

                </div>
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