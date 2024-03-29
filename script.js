/*
Create and Render map on div with zoom and center
*/


var mapLat = 17.123123;
var mapLng = 105.125125;

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
//school
var layer_school;
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
//bệnh viện
varchkHospitals = document.getElementById("hospitals")


layerBG = new ol.layer.Tile({
  source: new ol.source.OSM({})
});
var viewMap = new ol.View({
  center: ol.proj.fromLonLat([mapLng, mapLat]),
  zoom: 5
});
map = new ol.Map({
  target: "map",
  layers: [layerBG],
  view: viewMap,
});
let vector_layer = new VectorLayer('Temp Layer', map).layer
map.addLayer(vector_layer);

//add mouse postion
var mousePosition = new ol.control.MousePosition({
  className: 'mousePosition',
  projection: 'EPSG:4326',
  coordinateFormat: function (coordinate) { return ol.coordinate.format(coordinate, '{y} , {x}', 6); }
});
map.addControl(mousePosition);

function handleOnCheck(id, layer) {
  if (document.getElementById(id).checked) {
    value = document.getElementById(id).value;
    map.addLayer(layer)
    vectorLayer = new ol.layer.Vector({});
    map.addLayer(vectorLayer);
  } else {
    map.removeLayer(layer);
    map.removeLayer(vectorLayer);
  }
  console.log(" Vao ham nay");

}

function oncheckstation() {
  handleOnCheck('station', layer_school);
}

function oncheckrails() {
  handleOnCheck('rails', layer_rails);

}

function oncheckarg() {
  handleOnCheck('arg', layerARG_adm1);
}

// bệnh viện
function oncheckhospitals() {
  handleOnCheck('hospitals', layer_hospitals);
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
        LAYERS: 'cite:gadm41_vnm_1',
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
        LAYERS: 'cite:gis_osm_roads_free_1',
      }
    })

  });

  layer_school = new ol.layer.Image({
    source: new ol.source.ImageWMS({
      ratio: 1,
      url: 'http://localhost:8080/geoserver/cite/wms?',
      params: {
        'FORMAT': format,
        'VERSION': '1.1.1',
        STYLES: '',
        LAYERS: 'cite:hotosm_vnm_north_education_facilities_points',
      }
    })
  });

  layer_hospitals = new ol.layer.Image({
    source: new ol.source.ImageWMS({
      ratio: 1,
      url: 'http://localhost:8080/geoserver/cite/wms?',
      params: {
        'FORMAT': format,
        'VERSION': '1.1.1',
        STYLES: '',
        LAYERS: 'cite:hotosm_vnm_north_health_facilities_points',
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
      console.log(ctiy.value);
      vectorLayer.setStyle(styleFunction);
      ctiy.value.length ?
        $.ajax({
          type: "POST",
          url: "fetch.php",
          data: {
            request: 'liveSearch',
            searchTxt: ctiy.value
          },
          dataType: 'json',
          success: function (result, status, erro) {
            console.log(result);
            createRows(result, "City");
            document.getElementById("search_box").style.display = "block";
          },
          error: function (req, status, error) {
            alert(req + " " + status + " " + error);
          }
        }) : alert("Nhập dữ liệu tìm kiếm")
    });
  function createRows(data, layerName) {
    var searchTable = document.createElement('table');
    var rows = searchTable.getElementsByTagName("tr");
    searchTable.remove();
    var i = 0;
    for (var key in data) {
      var data2 = data[key];
      console.log(data2["varname_1"]);
      var tableRow = document.createElement('tr');
      var td1 = document.createElement('td');
      var td2 = document.createElement('td');
      for (var key2 in data2) {
        td2.innerHTML = data2[key2];
        td1.innerHTML = data2["varname_1"];
        td2.style.display = 'none';
      }
      tableRow.appendChild(td1);
      tableRow.appendChild(td2);
      searchTable.appendChild(tableRow);
      i = i + 1;
    }
    for (i = 0; i < rows.length; i++) {
      var currentRow = searchTable.rows[i];
      var createClickHandler = function (row) {
        return function () {
          var cell = row.getElementsByTagName("td")[1];
          var id = cell.innerHTML;
          highLightObj(id);
        };
      };
      currentRow.onclick = createClickHandler(currentRow);
    }

    $("#search_div").html(searchTable);
  }


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


  // live location
  $("#btnCrosshair").on("click", function (event) {
    $("#btnCrosshair").toggleClass("clicked");
    if ($("#btnCrosshair").hasClass("clicked")) {
      startAutolocate();
    } else {
      stopAutolocate();
    }
  });
};

//end onload

//liveLocation
var positionFeature = new ol.Feature();
positionFeature.setStyle(
    new ol.style.Style({
        image: new ol.style.Circle({
            radius: 6,
            fill: new ol.style.Fill({
                color: '#3399CC',
            }),
            stroke: new ol.style.Stroke({
                color: '#fff',
                width: 2,
            }),
        }),
    })
);
var geolocation = new ol.Geolocation({
  trackingOptions: {
      enableHighAccuracy: true,
  },
  tracking: true,
  projection: viewMap.getProjection()
});
var accuracyFeature = new ol.Feature();

var currentPositionLayer = new ol.layer.Vector({
    map: map,
    source: new ol.source.Vector({
        features: [accuracyFeature, positionFeature],
    }),
});
function startAutolocate() {
  var coordinates = geolocation.getPosition();
  positionFeature.setGeometry(coordinates ? new ol.geom.Point(coordinates) : null);
  viewMap.setCenter(coordinates);
  viewMap.setZoom(16);
  accuracyFeature.setGeometry(geolocation.getAccuracyGeometry());
  intervalAutolocate = setInterval(function () {
      var coordinates = geolocation.getPosition();
      var accuracy = geolocation.getAccuracyGeometry()
      positionFeature.setGeometry(coordinates ? new ol.geom.Point(coordinates) : null);
      viewMap.getView().setCenter(coordinates);
      viewMap.setZoom(16);
      accuracyFeature.setGeometry(accuracy);
  }, 10000);
}

function stopAutolocate() {
  clearInterval(intervalAutolocate);
  positionFeature.setGeometry(null);
  accuracyFeature.setGeometry(null);
}

//endlivelocation



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
    if (i > 8) {
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

function hide(elements) {
  elements = elements.length ? elements : [elements];
  for (var index = 0; index < elements.length; index++) {
    elements[index].style.display = 'none';
  }
}

function show(elements) {
  elements = elements.length ? elements : [elements];
  for (var index = 0; index < elements.length; index++) {
    elements[index].style.display = 'block';
  }
}

// document.getElementById("close_infomation").onclick = function () {
// //   hide(document.querySelectorAll('.infomation'));
// }
document.getElementById("close_infomation").onclick = function () {
  hide(document.querySelectorAll('.infomation'));
}
document.getElementById("close_search").onclick = function () {
  hide(document.querySelectorAll('.search_infomation'));
}
