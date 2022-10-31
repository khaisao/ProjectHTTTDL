<?php
// error_reporting(0);
$paPDO = initDB();
$paSRID = '4326';
if (isset($_POST['functionname'])) {
    $paPoint = $_POST['paPoint'];

    $functionname = $_POST['functionname'];

    $aResult = "null";
    if ($functionname == 'getGeoARGToAjax')
        $aResult = getGeoARGToAjax($paPDO, $paSRID, $paPoint);
    else if ($functionname == 'getInfoARGToAjax')
        $aResult = getInfoARGToAjax($paPDO, $paSRID, $paPoint);
    else if ($functionname == 'getInfoRailsToAjax')
        $aResult = getInfoRailsToAjax($paPDO, $paSRID, $paPoint);
    else if ($functionname == 'getInfoStationToAjax')
        $aResult = getInfoStationToAjax($paPDO, $paSRID, $paPoint);
    // bệnh viện
    else if ($functionname == 'getInfoHospitalsToAjax')
        $aResult = getInfoHospitalsToAjax($paPDO, $paSRID, $paPoint);    
    else if ($functionname == 'getStationToAjax')
        $aResult = getStationToAjax($paPDO, $paSRID, $paPoint);
    else if ($functionname == 'getRailsToAjax')
        $aResult = getRailsToAjax($paPDO, $paSRID, $paPoint);
    else if ($functionname == 'getRailsToAjax')
        $aResult = getRailsToAjax($paPDO, $paSRID, $paPoint);
    //bệnh viện
    else if ($functionname == 'getHospitalsToAjax')
        $aResult = getHospitalsToAjax($paPDO, $paSRID, $paPoint);
    echo $aResult;

    closeDB($paPDO);
}


function initDB()
{
    $paPDO = new PDO('pgsql:host=localhost;dbname=btl_1;port=5432', 'postgres', 'Khaiden666*');
    return $paPDO;
}
function query($paPDO, $paSQLStr)
{
    try
    {
        // Khai báo exception
        $paPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Sử đụng Prepare 
        $stmt = $paPDO->prepare($paSQLStr);
        // Thực thi câu truy vấn
        $stmt->execute();
        
        // Khai báo fetch kiểu mảng kết hợp
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        
        // Lấy danh sách kết quả
        $paResult = $stmt->fetchAll();   
        return $paResult;                 
    }
    catch(PDOException $e) {
        echo "Thất bại, Lỗi: " . $e->getMessage();
        return null;
    }       
}
function closeDB($paPDO)
{
    // Ngắt kết nối
    $paPDO = null;
}

// hightlight vùng
function getGeoARGToAjax($paPDO, $paSRID, $paPoint)
{
    
    $paPoint = str_replace(',', ' ', $paPoint);
    $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"gadm41_vnm_1\" where ST_Within('SRID=".$paSRID.";".$paPoint."'::geometry,geom)";
    $result = query($paPDO, $mySQLStr);
    if ($result != null) {
        // Lặp kết quả
        foreach ($result as $item) {
            return $item['geo'];
        }
    } else
        return "null";
}
// hightlight trường học
function getStationToAjax($paPDO, $paSRID, $paPoint)
{
    $paPoint = str_replace(',', ' ', $paPoint);
    $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
    $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from hotosm_vnm_north_education_facilities_points ";
    $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from hotosm_vnm_north_education_facilities_points where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.1";
    $result = query($paPDO, $mySQLStr);
    if ($result != null) {
        // Lặp kết quả
        foreach ($result as $item) {
            return $item['geo'];
        }
    } else
        return "null";
}

// hightlight đường
function getRailsToAjax($paPDO, $paSRID, $paPoint)
{
    $paPoint = str_replace(',', ' ', $paPoint);   
    $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
    $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from gis_osm_roads_free_1 ";
    $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from gis_osm_roads_free_1 where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.1";
    $result = query($paPDO, $mySQLStr);

    if ($result != null) {
        // Lặp kết quả
        foreach ($result as $item) {
            return $item['geo'];
        }
    } else
        return "null";
}
//highlight bệnh viện
function getHospitalsToAjax($paPDO, $paSRID, $paPoint)
{
    $paPoint = str_replace(',', ' ', $paPoint);   
    $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
    $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from hotosm_vnm_north_health_facilities_points";
    $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from hotosm_vnm_north_health_facilities_points where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.1";
    $result = query($paPDO, $mySQLStr);

    if ($result != null) {
        // Lặp kết quả
        foreach ($result as $item) {
            return $item['geo'];
        }
    } else
        return "null";
}
// Truy van thong tin vùng
function getInfoARGToAjax($paPDO, $paSRID, $paPoint)
{
   
    $paPoint = str_replace(',', ' ', $paPoint);
    // $mySQLStr = "SELECT gid, name_1, ST_Area(geom) dt, ST_Perimeter(geom) as cv from \"arg_adm1\" where ST_Within('SRID=".$paSRID.";".$paPoint."'::geometry,geom)";
    $mySQLStr = "SELECT gid, name_1 from \"gadm41_vnm_1\" where ST_Within('SRID=".$paSRID.";".$paPoint."'::geometry,geom)";
    
    $result = query($paPDO, $mySQLStr);

    if ($result != null) {
        $resFin = '<table>';
        // Lặp kết quả
        foreach ($result as $item) {
            $resFin = $resFin . '<tr><td>Mã Vùng: ' . $item['gid'] . '</td></tr>';
            $resFin = $resFin . '<tr><td>Tên : ' . $item['name_1'] . '</td></tr>';
            break;
        }
        $resFin = $resFin . '</table>';
        return $resFin;
    } else
        return "null";
}

//Truy van thong tin streest
function getInfoRailsToAjax($paPDO, $paSRID, $paPoint)
{
    $paPoint = str_replace(',', ' ', $paPoint);
    $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
    $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from gis_osm_roads_free_1 ";
    $mySQLStr = "SELECT *  from gis_osm_roads_free_1 where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.5";
    $result = query($paPDO, $mySQLStr);

    if ($result != null) {
        $resFin = '<table>';
        // Lặp kết quả
        foreach ($result as $item) {
            $resFin = $resFin . '<tr><td>G_ID: ' . $item['gid'] . '</td></tr>';
            $resFin = $resFin . '<tr><td>Tên đường : ' . $item['name'] . '</td></tr>';
            $resFin = $resFin . '<tr><td>Loại đường : ' . $item['fclass'] . '</td></tr>';
            break;
        }
        $resFin = $resFin . '</table>';
        return $resFin;
    } else
        return "null";
}

// truy van thong tin trưỜng học
function getInfoStationToAjax($paPDO, $paSRID, $paPoint)
{
    $paPoint = str_replace(',', ' ', $paPoint);
    $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
    $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from hotosm_vnm_north_education_facilities_points ";
    $mySQLStr = "SELECT * from hotosm_vnm_north_education_facilities_points where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.1";
    $result = query($paPDO, $mySQLStr);

    if ($result != null) {
        $resFin = '<table>';
        // Lặp kết quả
        foreach ($result as $item) {
            $resFin = $resFin . '<tr><td>G_id: ' . $item['gid'] . '</td></tr>';
            $resFin = $resFin . '<tr><td>Tên trường học: ' . $item['name'] . '</td></tr>';
            break;
        }
        $resFin = $resFin . '</table>';
        return $resFin;
    } else
        return "null 1";
}

////////////////////////////////        bệnh viện
function getInfoHospitalsToAjax($paPDO, $paSRID, $paPoint)
{
    $paPoint = str_replace(',', ' ', $paPoint);
    $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
    $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from hotosm_vnm_north_health_facilities_points ";
    $mySQLStr = "SELECT * from hotosm_vnm_north_health_facilities_points where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.1";
    $result = query($paPDO, $mySQLStr);

    if ($result != null) {
        $resFin = '<table>';
        // Lặp kết quả
        foreach ($result as $item) {
            $resFin = $resFin . '<tr><td> ID: ' . $item['gid'] . '</td></tr>';
            $resFin = $resFin . '<tr><td> Tên bệnh viện: ' . $item['name'] . '</td></tr>';
            $resFin = $resFin . '<tr><td> Loại: ' . $item['amenity'] . '</td></tr>';
            break;
        }
        $resFin = $resFin . '</table>';
        return $resFin;
    } else
        return "null";
}

