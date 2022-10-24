<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>OpenStreetMap &amp; OpenLayers - Marker Example</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

    <link rel="stylesheet" href="https://openlayers.org/en/v4.6.5/css/ol.css" type="text/css" />
    <link rel="stylesheet" href="style.css" type="text/css" />
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://openlayers.org/en/v4.6.5/build/ol.js" type="text/javascript"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js" type="text/javascript"></script>
    <script src="dependencies/ol.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css?family=Inter&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />

</head>

<body onload="initialize_map();">
    <div>
        <td>
            <div id="map" class="map" >
                <nav class="navbar" style="position:sticky; top:0; z-index:1100;">
                    <div class="dropdown pl-4">
                        <button class="btn btn-dark dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Danh mục
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="#"><input onclick="oncheckarg()" type="checkbox" id="arg" name="layer" value="arg"> Washington DC<br /></a>
                            <a class="dropdown-item" href="#"><input onclick="oncheckrails();" type="checkbox" id="rails" name="layer" value="rails"> Đường phố <br /></a>
                            <a class="dropdown-item" href="#"><input onclick="oncheckstation();" type="checkbox" id="station" name="layer" value="station"> Trường Học <br /></a>
                            <a class="dropdown-item" href="#"><input onclick="oncheckmarkets();" type="checkbox" id="markets" name="layer" value="markets"> Chợ nông sản <br /></a>
                            <a class="dropdown-item" href="#"><input onclick="oncheckbank();" type="checkbox" id="bank" name="layer" value="bank"> Ngân Hàng <br /></a>
                            <a class="dropdown-item" href="#"><input onclick="oncheckhospitals();" type="checkbox" id="hospitals" name="layer" value="hospitals"> Bệnh viện <br /></a>
                            <a class="dropdown-item" href="#"><input onclick="oncheckatm();" type="checkbox" id="atm" name="layer" value="atm"> ATM-BANKING <br /></a>
                        </div>
                    </div>
                    <form class="form-inline">
                        <input id="ctiy" class="form-control" type="textinput" placeholder="Tìm kiếm" aria-label="Search">
                        <button id="btnSeacher" class="btn btn-outline-dark my-2" type="submit"><i class="bi bi-search"></i></button>
                    </form>
                </nav>

            </div>

            <div id="map" style="width: 50vw; height: 50vh;">
                <div id="popup" class="ol-popup">
                    <a href="#" id="popup-closer" class="ol-popup-closer"></a>
                    <div id="popup-content"></div>
                </div>
            </div>

            <div id="map" style="width: 80vw; height: 100vh;">
                
                    <div id="infomation" class="infomation">
                        <div id="infomation_box" class="infomation_box">
                            Đây là thông tin
                        </div>
                        <i class="fas fa-times close_infomation" id="close_infomation"></i>          
                    </div>
                
                

                <div class="row col-1 fixed-bottom" style="position:sticky; ">   
                    <div class="list-icon">
                        <ul class="list-group list-group-horizontal flex-column justify-content">
                            <button class="bi bi-pencil-square" id="btn1" title="Distance Measurement" geomtype="LineString"></button>
                            <button class="bi bi-map " id="btn2" title="Area Measurement" geomtype="Polygon"></button>
                            <button class="bi bi-eraser " id="btn3" title="Clear Graphics"></button>
                            <button class="bi bi-arrow-clockwise"  id="btnRest" ></button>
                        </ul>
                    </div>
                    

                </div>


            </div>
    </div>
    </td>

    <script src="script.js"></script>

</body>

</html>