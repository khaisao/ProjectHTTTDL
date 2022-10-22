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
    //bank
    else if ($functionname == 'getInfoBankToAjax')
        $aResult = getInfoBankToAjax($paPDO, $paSRID, $paPoint);
    // bệnh viện
    else if ($functionname == 'getInfoHospitalsToAjax')
        $aResult = getInfoHospitalsToAjax($paPDO, $paSRID, $paPoint);
    // atm_banking
    else if ($functionname == 'getInfoatm_bankingToAjax')
        $aResult = getInfoatm_bankingToAjax($paPDO, $paSRID, $paPoint);
    //markets
    else if ($functionname == 'getInfoMarketsToAjax')
        $aResult = getInfoMarketsToAjax($paPDO, $paSRID, $paPoint);
    
    else if ($functionname == 'getStationToAjax')
        $aResult = getStationToAjax($paPDO, $paSRID, $paPoint);
    else if ($functionname == 'getRailsToAjax')
        $aResult = getRailsToAjax($paPDO, $paSRID, $paPoint);
    else if ($functionname == 'getRailsToAjax')
        $aResult = getRailsToAjax($paPDO, $paSRID, $paPoint);
    //bank
    else if ($functionname == 'getBankToAjax')
        $aResult = getBankToAjax($paPDO, $paSRID, $paPoint);
    //bệnh viện
    else if ($functionname == 'getHospitalsToAjax')
        $aResult = getHospitalsToAjax($paPDO, $paSRID, $paPoint);
    //atm_banking
    else if ($functionname == 'getatm_bankingToAjax')
        $aResult = getatm_bankingToAjax($paPDO, $paSRID, $paPoint);
    //Markets
    else if ($functionname == 'getMarketsToAjax')
        $aResult = getMarketsToAjax($paPDO, $paSRID, $paPoint);


    echo $aResult;

    closeDB($paPDO);
}
if (isset($_POST['name'])) {
    $name = $_POST['name'];
    $aResult = seacherCity($paPDO, $paSRID, $name);
    echo $aResult;
}

function initDB()
{
    // Kết nối CSDL
    $paPDO = new PDO('pgsql:host=localhost;dbname=demo;port=5432', 'postgres', 'Khaiden666*');
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
    $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"wards_from_2012\" where ST_Within('SRID=".$paSRID.";".$paPoint."'::geometry,geom)";
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
    $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from schools ";
    $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from schools where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.1";
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
    $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from roadway";
    $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from roadway where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.1";
    $result = query($paPDO, $mySQLStr);

    if ($result != null) {
        // Lặp kết quả
        foreach ($result as $item) {
            return $item['geo'];
        }
    } else
        return "null";
}
// Hightlight Banking

function getBankToAjax($paPDO, $paSRID, $paPoint)
{
    $paPoint = str_replace(',', ' ', $paPoint);   
    $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
    $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from bank";
    $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from bank where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.1";
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
    $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from hospitals";
    $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from hospitals where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.1";
    $result = query($paPDO, $mySQLStr);

    if ($result != null) {
        // Lặp kết quả
        foreach ($result as $item) {
            return $item['geo'];
        }
    } else
        return "null";
}

//highlight cây atm_banking BANKING
function getatm_bankingToAjax($paPDO, $paSRID, $paPoint)
{
    $paPoint = str_replace(',', ' ', $paPoint);   
    $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
    $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from atm_banking";
    $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from atm_banking where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.1";
    $result = query($paPDO, $mySQLStr);

    if ($result != null) {
        // Lặp kết quả
        foreach ($result as $item) {
            return $item['geo'];
        }
    } else
        return "null";
}

//highlight CHợ
function getMarketsoAjax($paPDO, $paSRID, $paPoint)
{
    $paPoint = str_replace(',', ' ', $paPoint);   
    $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
    $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from atm_banking";
    $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from atm_banking where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.1";
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
    $mySQLStr = "SELECT gid, rep_name, pop_2011_2 from \"wards_from_2012\" where ST_Within('SRID=".$paSRID.";".$paPoint."'::geometry,geom)";
    
    $result = query($paPDO, $mySQLStr);

    if ($result != null) {
        $resFin = '<table>';
        // Lặp kết quả
        foreach ($result as $item) {
            $resFin = $resFin . '<tr><td>Mã Vùng: ' . $item['gid'] . '</td></tr>';
            $resFin = $resFin . '<tr><td>Tên : ' . $item['rep_name'] . '</td></tr>';
            $resFin = $resFin . '<tr><td>Dân số: ' . $item['pop_2011_2'] . ' người ' .'</td></tr>';
            // $resFin = $resFin . '<tr><td>Diện Tích: ' . $item['dientich'] . ' km2 '.'</td></tr>';
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
    $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from roadway";
    $mySQLStr = "SELECT *  from roadway where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.5";
    $result = query($paPDO, $mySQLStr);

    if ($result != null) {
        $resFin = '<table>';
        // Lặp kết quả
        foreach ($result as $item) {
            $resFin = $resFin . '<tr><td>G_ID: ' . $item['gid'] . '</td></tr>';
            $resFin = $resFin . '<tr><td>Tên đường : ' . $item['routename'] . '</td></tr>';
            $resFin = $resFin . '<tr><td>Loại đường : ' . $item['streettype'] . '</td></tr>';
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
    $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from schools ";
    $mySQLStr = "SELECT * from schools where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.1";
    $result = query($paPDO, $mySQLStr);

    if ($result != null) {
        $resFin = '<table>';
        // Lặp kết quả
        foreach ($result as $item) {
            $resFin = $resFin . '<tr><td>G_id: ' . $item['gid'] . '</td></tr>';
            $resFin = $resFin . '<tr><td>Tên trường học: ' . $item['name'] . '</td></tr>';
            $resFin = $resFin . '<tr><td>Địa chỉ : ' . $item['address'] . '</td></tr>';
            break;
        }
        $resFin = $resFin . '</table>';
        return $resFin;
    } else
        return "null 1";
}

////////////////////////////////abnkkkkkkkkkkkkkkkkkkk
function getInfoBankToAjax($paPDO, $paSRID, $paPoint)
{
    $paPoint = str_replace(',', ' ', $paPoint);
    $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
    $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from bank ";
    $mySQLStr = "SELECT gid,name,address,zipcode from bank where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.1";
    $result = query($paPDO, $mySQLStr);

    if ($result != null) {
        $resFin = '<table>';
        // Lặp kết quả
        foreach ($result as $item) {
            $resFin = $resFin . '<tr><td>ID: ' . $item['gid'] . '</td></tr>';
            $resFin = $resFin . '<tr><td>Tên ngân hàng: ' . $item['name'] . '</td></tr>';
            $resFin = $resFin . '<tr><td>Địa chỉ : ' . $item['address'] . '</td></tr>';
            $resFin = $resFin . '<tr><td>ZIP CODE : ' . $item['zipcode'] . '</td></tr>';
            break;
        }
        $resFin = $resFin . '</table>';
        return $resFin;
    } else
        return "null";
}
////////////////////////////////        bệnh viện
function getInfoHospitalsToAjax($paPDO, $paSRID, $paPoint)
{
    $paPoint = str_replace(',', ' ', $paPoint);
    $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
    $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from hospitals ";
    $mySQLStr = "SELECT * from hospitals where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.1";
    $result = query($paPDO, $mySQLStr);

    if ($result != null) {
        $resFin = '<table>';
        // Lặp kết quả
        foreach ($result as $item) {
            $resFin = $resFin . '<tr><td> ID: ' . $item['gid'] . '</td></tr>';
            $resFin = $resFin . '<tr><td> Tên bệnh viện: ' . $item['name'] . '</td></tr>';
            $resFin = $resFin . '<tr><td> Địa chỉ : ' . $item['address'] . '</td></tr>';
            break;
        }
        $resFin = $resFin . '</table>';
        return $resFin;
    } else
        return "null";
}

////////////////////////////////  atm_banking banking
function getInfoatm_bankingToAjax($paPDO, $paSRID, $paPoint)
{
    $paPoint = str_replace(',', ' ', $paPoint);
    $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
    $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from atm_banking ";
    $mySQLStr = "SELECT  gid,name,address,zipcode from atm_banking  where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.1";
    $result = query($paPDO, $mySQLStr);

    if ($result != null) {
        $resFin = '<table>';
        // Lặp kết quả
        foreach ($result as $item) {
            $resFin = $resFin . '<tr><td> ID: ' . $item['gid'] . '</td></tr>';
            $resFin = $resFin . '<tr><td> Tên atm_banking: ' . $item['name'] . '</td></tr>';
            $resFin = $resFin . '<tr><td> Địa chỉ : ' . $item['address'] . '</td></tr>';
            $resFin = $resFin . '<tr><td> ZIPCODE : ' . $item['zipcode'] . '</td></tr>';
            break;
        }
        $resFin = $resFin . '</table>';
        return $resFin;
    } else
        return "null";
}

//////////////////////////////// Chợ
function getInfoMarketsToAjax($paPDO, $paSRID, $paPoint)
{
    $paPoint = str_replace(',', ' ', $paPoint);
    $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
    $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from markets ";
    $mySQLStr = "SELECT  gid,name,address,phone,day from markets  where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.1";
    $result = query($paPDO, $mySQLStr);

    if ($result != null) {
        $resFin = '<table>';
        // Lặp kết quả
        foreach ($result as $item) {
            $resFin = $resFin . '<tr><td> ID: ' . $item['gid'] . '</td></tr>';
            $resFin = $resFin . '<tr><td> Name: ' . $item['name'] . '</td></tr>';
            $resFin = $resFin . '<tr><td> Address : ' . $item['address'] . '</td></tr>';
            $resFin = $resFin . '<tr><td> Phone : ' . $item['phone'] . '</td></tr>';
            $resFin = $resFin . '<tr><td> Open : ' . $item['day'] . '</td></tr>';
            break;
        }
        $resFin = $resFin . '</table>';
        return $resFin;
    } else
        return "null";
}
//tim kiem
function seacherCity($paPDO, $paSRID, $name)
{
    
    $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from wards_from_2012 where rep_name like '$name'";
    $result = query($paPDO, $mySQLStr);

    if ($result != null) {
        // Lặp kết quả
        foreach ($result as $item) {
            return $item['geo'];
        }
    } else
        return "null";
}
