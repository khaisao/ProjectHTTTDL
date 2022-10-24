/*
Create and Render map on div with zoom and center
*/

var minX = -77.120849609375;
var minY = 38.7906188964844;
var maxX = -76.9080963134766;
var maxY = 38.9969940185547;
var cenX = (minX + maxX) / 2;
var cenY = (minY + maxY) / 2;
var mapLat = cenY;
var mapLng = cenX;

class OLMap {
  //Constructor accepts html div id, zoom level and center coordinaes
  constructor(map_div, zoom) {
    this.map = new ol.Map({
      target: map_div,
      layers: [
        new ol.layer.Tile({
          source: new ol.source.OSM()
        })
      ],
      view: new ol.View({
        center: ol.proj.fromLonLat([mapLng, mapLat]),
        zoom: zoom
      })
    });
  }
}


/*
Create Vector Layer
*/
class VectorLayer {
  //Constructor accepts title of vector layer and map object
  constructor(title, map) {
    this.layer = new ol.layer.Vector({
      title: title,
      source: new ol.source.Vector({
        projection: map.getView().projection
      }),
      style: new ol.style.Style({
        stroke: new ol.style.Stroke({
          color: '#0e97fa',
          width: 4
        })
      })
    });
  }
}


/*
Create overlay
*/
class Overlay {
  //Contrctor accepts map object, overlay html element, overlay offset, overlay positioning and overlay class
  constructor(map, element = document.getElementById("popup"), offset = [0, -15], positioning = 'bottom-center', className = 'ol-tooltip-measure ol-tooltip .ol-tooltip-static') {
    this.map = map;
    this.overlay = new ol.Overlay({
      element: element,
      offset: offset,
      positioning: positioning,
      className: className
    });
    this.overlay.setPosition([0, 0]);
    this.overlay.element.style.display = 'block';
    this.map.addOverlay(this.overlay);
  }
}


/*
Create a Draw interaction for LineString and Polygon
*/
class Draw {
  //Constructor accepts geometry type, map object and vector layer
  constructor(type, map, vector_layer) {
    this.map = map;
    this.vector_layer = vector_layer;
    this.draw = new ol.interaction.Draw({
      type: type,
      stopClick: true
    });
    this.draw.on('drawstart', this.onDrawStart);
    this.draw.on('drawend', this.onDrawEnd);
    this.map.addInteraction(this.draw);
  }


  /*
  This function will be called when you start drawing
  */
  onDrawStart = (e) => {
    //It will store the coordinates length of geometry
    this.coordinates_length = 0;

    //partDistanceOverlay is used to display the label of distance measurements on each segment of Line and Polygon geomtry
    this.partDistanceOverlay = null;

    //totalAreaDistanceOverlay is used to display the total distance if geomtery is LineString or it will display the area if geomtry is Polygon
    this.totalAreaDistanceOverlay = new Overlay(this.map).overlay;

    //lastPartLineOverlay is used to display the distance measurement of last segment of Polygon which is its last two coordinates
    this.lastPartLineOverlay = new Overlay(this.map).overlay;

    //Binding onGeomChange function with drawing feature
    e.feature.getGeometry().on('change', this.onGeomChange);
  }


  /*
  This function will be called when drawing is finished
  */
  onDrawEnd = (e) => {
    //Add drawn geometry to vector layer          
    this.vector_layer.getSource().addFeature(e.feature);
  }


  /*
  This function will called when ever there will be a change in geometry like increase in length, area, position,
  */
  onGeomChange = (e) => {
    let geomType = e.target.getType();
    let coordinates = e.target.getCoordinates();
    if (geomType == "Polygon") {
      coordinates = e.target.getCoordinates()[0];
    }

    //This logic will check if the new coordinates are added to geometry. If yes, then It will create a overlay for the new segment
    if (coordinates.length > this.coordinates_length) {
      this.partDistanceOverlay = new Overlay(this.map).overlay;
      this.coordinates_length = coordinates.length;
    }
    else {
      this.coordinates_length = coordinates.length;
    }

    let partLine = new ol.geom.LineString([coordinates[this.coordinates_length - 2], coordinates[this.coordinates_length - 1]]);

    if (geomType == "Polygon") {
      partLine = new ol.geom.LineString([coordinates[this.coordinates_length - 3], coordinates[this.coordinates_length - 2]]);
    }

    //the calculates the length of a segment and position the overlay at the midpoint of it
    this.calDistance(this.partDistanceOverlay, partLine.getFlatMidpoint(), partLine.getLength());

    //if geometry is LineString and coordinates_length is greater than 2, then calculate the total length of the line and set the position of the overlay at last coordninates
    if (geomType == "LineString" && this.coordinates_length > 2 && e.target.getLength() > new ol.geom.LineString([coordinates[0], coordinates[1]]).getLength()) {
      this.calDistance(this.totalAreaDistanceOverlay, coordinates[this.coordinates_length - 1], e.target.getLength());
    }

    //If geometry is Polygon, then it will create the overlay for area measurement and last segment of it which is its first and last coordinates.
    if (geomType == "Polygon" && this.coordinates_length > 3) {
      this.calArea(this.totalAreaDistanceOverlay, e.target.getFlatInteriorPoint(), e.target.getArea());
      partLine = new ol.geom.LineString([coordinates[this.coordinates_length - 2], coordinates[this.coordinates_length - 1]]);
      this.calDistance(this.lastPartLineOverlay, partLine.getFlatMidpoint(), partLine.getLength());
    }
  }


  //Calculates the length of a segment and position the overlay at the midpoint of it.
  calDistance = (overlay, overlayPosition, distance) => {
    if (parseInt(distance) == 0) {
      overlay.setPosition([0, 0]);
    }
    else {
      overlay.setPosition(overlayPosition);
      if (distance >= 1000) {
        overlay.element.innerHTML = (distance / 1000).toFixed(2) + ' km';
      }
      else {
        overlay.element.innerHTML = distance.toFixed(2) + ' m';
      }
    }
  }


  //Calculates the area of Polygon and position the overlay at the center of polygon
  calArea = (overlay, overlayPosition, area) => {
    if (parseInt(area) == 0) {
      overlay.setPosition([0, 0]);
    }
    else {
      overlay.setPosition(overlayPosition);
      if (area >= 10000) {
        overlay.element.innerHTML = Math.round((area / 1000000) * 100) / 100 + ' km<sup>2<sup>';
      }
      else {
        overlay.element.innerHTML = Math.round(area * 100) / 100 + ' m<sup>2<sup>';
      }
    }
  }

}


var format = 'image/png';
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

let map = new OLMap('map', 10).map;
let vector_layer = new VectorLayer('Temp Layer', map).layer
map.addLayer(vector_layer);

var value;
/**
 * Create an overlay to anchor the popup to the map.
 */
var overlay = new ol.Overlay( /** @type {olx.OverlayOptions} */({
  element: container,
  autoPan: true,
  autoPanAnimation: {
    duration: 250
  }
}));
closer.onclick = function () {
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
  console.log(" Vao ham nay");

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
  handleOnCheck('atm', layer_atm);
}
// CHợ 
function oncheckmarkets() {
  handleOnCheck('markets', layer_markets);
}


function initialize_map() {

  //*


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
        LAYERS: 'roadway_functional_classification',
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
        LAYERS: 'atm_banking',
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
  styleFunction = function (feature) {
    return styles[feature.getGeometry().getType()];
  };
  var stylePoint = new ol.style.Style({
    image: new ol.style.Icon({
      anchor: [0.5, 0.5],
      anchorXUnits: "fraction",
      anchorYUnits: "fraction",
      src: "http://localhost/pj/Yellow_dot.svg"
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
          success: function (result, status, erro) {

            if (result == 'null')
              alert("không tìm thấy đối tượng");
            else
              console.log(result);
              var listCityName = result.split('keysplit');
              listCityName.pop();
              console.log(listCityName);

          },
          error: function (req, status, error) {
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
    console.log(result);
    show(document.querySelectorAll('.infomation'))
    $("#infomation_box").html(result);

  }

  map.on('singleclick', function (evt) {
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
        success: function (result, status, erro) {
          displayObjInfo(result, evt.coordinate);
        },
        error: function (req, status, error) {
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
        success: function (result, status, erro) {
          highLightObj(result);
        },
        error: function (req, status, error) {
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
        success: function (result, status, erro) {
          displayObjInfo(result, evt.coordinate);
        },
        error: function (req, status, error) {
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
        success: function (result, status, erro) {
          highLightObj(result);
        },
        error: function (req, status, error) {
          alert(req + " " + status + " " + error);
        }
      });
    }
    if (value == "station") { // trường học
      vectorLayer.setStyle(stylePoint);
      $.ajax({
        type: "POST",
        url: "CMR_pgsqlAPI.php",
        data: {
          functionname: 'getInfoStationToAjax',
          paPoint: myPoint
        },
        success: function (result, status, erro) {
          displayObjInfo(result, evt.coordinate);
        },
        error: function (req, status, error) {
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
        success: function (result, status, erro) {
          highLightObj(result);
        },
        error: function (req, status, error) {
          alert(req + " " + status + " " + error);
        }
      });
    }
    /// bankingggggggggggggggg
    if (value == "bank") {
      vectorLayer.setStyle(stylePoint);
      $.ajax({
        type: "POST",
        url: "CMR_pgsqlAPI.php",
        data: {
          functionname: 'getInfoBankToAjax',
          paPoint: myPoint
        },
        success: function (result, status, erro) {
          displayObjInfo(result, evt.coordinate);
        },
        error: function (req, status, error) {
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
        success: function (result, status, erro) {
          highLightObj(result);
        },
        error: function (req, status, error) {
          alert(req + " " + status + " " + error);
        }
      });
    }
    /// ATMMMMMMMMMMMMMMMMMM
    if (value == "atm") {
      vectorLayer.setStyle(stylePoint);
      $.ajax({
        type: "POST",
        url: "CMR_pgsqlAPI.php",
        data: {
          functionname: 'getInfoATMToAjax',
          paPoint: myPoint
        },
        success: function (result, status, erro) {
          displayObjInfo(result, evt.coordinate);
        },
        error: function (req, status, error) {
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
        success: function (result, status, erro) {
          highLightObj(result);
        },
        error: function (req, status, error) {
          alert(req + " " + status + " " + error);
        }
      });
    }
    /// CHợ 
    if (value == "markets") {
      vectorLayer.setStyle(stylePoint);
      $.ajax({
        type: "POST",
        url: "CMR_pgsqlAPI.php",
        data: {
          functionname: 'getInfoMarketsToAjax',
          paPoint: myPoint
        },
        success: function (result, status, erro) {
          displayObjInfo(result, evt.coordinate);
        },
        error: function (req, status, error) {
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
        success: function (result, status, erro) {
          highLightObj(result);
        },
        error: function (req, status, error) {
          alert(req + " " + status + " " + error);
        }
      });
    }
    //// bệnh viện
    if (value == "hospitals") {
      vectorLayer.setStyle(stylePoint);
      $.ajax({
        type: "POST",
        url: "CMR_pgsqlAPI.php",
        data: {
          functionname: 'getInfoHospitalsToAjax',
          paPoint: myPoint
        },
        success: function (result, status, erro) {
          displayObjInfo(result, evt.coordinate);
        },
        error: function (req, status, error) {
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
        success: function (result, status, erro) {
          highLightObj(result);
        },
        error: function (req, status, error) {
          alert(req + " " + status + " " + error);
        }
      });
    }

  });
};



//Add Interaction to map depending on your selection
let draw = null;
let btnClick = (e) => {  
  removeInteractions();
  let geomType = e.srcElement.attributes.geomtype.nodeValue;
  //Create interaction
  draw = new Draw(geomType, map, vector_layer);
}


//Remove map interactions except default interactions
let removeInteractions = () => {  
  map.getInteractions().getArray().forEach((interaction, i) => {
    if(i > 8) {
      map.removeInteraction(interaction);
    }
  });
}


//Clear vector features and overlays and remove any interaction
let clear = () => {
  removeInteractions();
  map.getOverlays().clear();
  vector_layer.getSource().clear();
}

//Bind methods to click events of buttons
let distanceMeasure = document.getElementById('btn1');
distanceMeasure.onclick = btnClick;

let areaMeasure = document.getElementById('btn2');
areaMeasure.onclick = btnClick;

let clearGraphics = document.getElementById('btn3');
clearGraphics.onclick = clear;

function hide (elements) {
  elements = elements.length ? elements : [elements];
  for (var index = 0; index < elements.length; index++) {
    elements[index].style.display = 'none';
  }
}

function show (elements) {
  elements = elements.length ? elements : [elements];
  for (var index = 0; index < elements.length; index++) {
    elements[index].style.display = 'block';
  }
}

document.getElementById("close_infomation").onclick = function() { hide(document.querySelectorAll('.infomation'));
}
