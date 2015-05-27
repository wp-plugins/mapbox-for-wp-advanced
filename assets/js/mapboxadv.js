var canvaspreffix = 'mapbox-canvas';
var maps={};

jQuery(window).load(function() {

    var pleasegeolocate = false;
    
    L.mapbox.accessToken = WPMapbox.mapaccesstoken;
    L.mapbox.config.FORCE_HTTPS = true;
    L.mapbox.config.HTTPS_URL = 'https://api.tiles.mapbox.com' + '/v4';

    // Iterate through each map to get corresponding params
    jQuery('.mapbox-map').each(function(i) {

        var mapbox = jQuery(this); 
        
        var index =  mapbox.data('mapinternid');
       //index=i;

        //access general params via WPMapbox.name 
        maps[index]={};
        maps[index].accesstoken = WPMapbox.mapaccesstoken; //else via data- : ele.dataset.accesstoken;
        maps[index].mapid = mapbox.data('mapid');
        maps[index].mapboxid = mapbox.data('mapboxid');
        maps[index].mapsuffix = mapbox.data('mapsuffix'); 
        maps[index].mapinternid = convertToSafeId(mapbox.data('mapinternid'));

        //create the map now
        $canvas='<div class="'+canvaspreffix+'" id="'+canvaspreffix+'-'+maps[index].mapinternid+'"></div>';
        mapbox.prepend($canvas);
        maps[index].mapcanvas = mapbox.children('#'+canvaspreffix+'-'+ maps[index].mapinternid); 
       
        maps[index].pixelwidth = maps[index].mapcanvas.outerWidth();
        maps[index].pixelheight = maps[index].mapcanvas.outerHeight();

        maps[index].mapstaticmap = mapbox.data('mapstaticmap');
        maps[index].mapstaticmapformat =  WPMapbox.mapstaticmapformat;
        maps[index].mapstaticmapformatretina =  WPMapbox.mapstaticmapformatretina;
        maps[index].mapstaticmapretinasuffix = WPMapbox.mapstaticmapretinasuffix;

        maps[index].mappadding =  mapbox.data('mappadding');
        maps[index].mappaddingtlx =  mapbox.data('mappaddingtlx');
        maps[index].mappaddingtly = mapbox.data('mappaddingtly');
        maps[index].mappaddingbrx =  mapbox.data('mappaddingbrx');
        maps[index].mappaddingbry =  mapbox.data('mappaddingbry');

        maps[index].mapcontinuousworld = WPMapbox.mapcontinuousworld;
        maps[index].mapattributioncontrol = WPMapbox.mapattributioncontrol;

        maps[index].mapdisabletouchzoom = WPMapbox.mapdisabletouchzoom;
        maps[index].mapdisabledrag = WPMapbox.mapdisabledrag;

        maps[index].mapclustermode = WPMapbox.mapclustermode;
        maps[index].mapclustercolor = WPMapbox.mapclustercolor;

        maps[index].mapcenterlat =  mapbox.data('mapcenterlat');
        maps[index].mapcenterlng =  mapbox.data('mapcenterlng');

        maps[index].mapmaxzoom =  mapbox.data('mapmaxzoom');
        maps[index].mapminzoom =  mapbox.data('mapminzoom');
        maps[index].mapzoom =  mapbox.data('mapzoom');

        maps[index].mapmaxbounds = null;
        maps[index].mapmaxboundsswlat =  mapbox.data('mapmaxboundsswlat');
        maps[index].mapmaxboundsswlng =  mapbox.data('mapmaxboundsswlng');
        maps[index].mapmaxboundsnelat =  mapbox.data('mapmaxboundsnelat');
        maps[index].mapmaxboundsnelng =  mapbox.data('mapmaxboundsnelng');
        if ((maps[index].mapmaxboundsswlat>=-90) && (maps[index].mapmaxboundsswlat<=90) &&
        (maps[index].mapmaxboundsswlng>=-180) && (maps[index].mapmaxboundsswlng<=180) &&
        (maps[index].mapmaxboundsnelat>=-90) && (maps[index].mapmaxboundsnelat<=90) &&
        (maps[index].mapmaxboundsnelng>=-90) && (maps[index].mapmaxboundsnelng<=180) && 
        ((maps[index].mapmaxBoundsswlat!==0)&&(maps[index].mapmaxboundsswlng!==0)&&(maps[index].mapmaxboundsnelat!==0)&&(maps[index].mapmaxboundsnelng!=-0))) {
            maps[index].mapmaxbounds=L.latLngBounds(L.latLng(maps[index].mapmaxboundsswlat, maps[index].mapmaxboundsswlng),L.latLng(maps[index].mapmaxboundsnelat, maps[index].mapmaxboundsnelng));
        }

        maps[index].mapsharecontrol =  mapbox.data('mapsharecontrol');
        maps[index].mapsharecontrolposition =  mapbox.data('mapsharecontrolposition');
        maps[index].mapfullscreencontrol = mapbox.data('mapfullscreencontrol');
        maps[index].mapfullscreencontrolposition = mapbox.data('mapfullscreencontrolposition');
        maps[index].mapzoomcontrol =  mapbox.data('mapzoomcontrol');
        maps[index].mapzoomcontrolposition =  mapbox.data('mapzoomcontrolposition');
        maps[index].mapgeocodercontrol =  mapbox.data('mapgeocodercontrol');
        maps[index].mapgeocodercontrolautoc =  mapbox.data('mapgeocodercontrolautoc');
        maps[index].mapgeocodercontrolposition =  mapbox.data('mapgeocodercontrolposition');
        maps[index].mapgeocoderradiuskm =  mapbox.data('mapgeocoderradiuskm');

        maps[index].maplegendbackgroundcolor =  mapbox.data('maplegendbackgroundcolor');
        maps[index].maplegendposition =  mapbox.data('maplegendposition');
        
        maps[index].maplayerscontrolposition = mapbox.data('maplayerscontrolposition');

        maps[index].mapuselocation = mapbox.data('mapuselocation'); //( mapbox.data('mapuselocation') === '1') || ( mapbox.data('mapuselocation') === 'on');
        if (maps[index].mapuselocation) pleasegeolocate=true;
        
        maps[index].maplocatehighaccuracy = WPMapbox.maplocatehighaccuracy;
        maps[index].mapdisplaylocation = WPMapbox.mapdisplaylocation;
        maps[index].maplocationmarkersize = WPMapbox.maplocationmarkersize;
        maps[index].maplocationmarkersymbol = WPMapbox.maplocationmarkersymbol;
        maps[index].maplocationmarkercolor = WPMapbox.maplocationmarkercolor;
        maps[index].mapcenteronlocation =  mapbox.data('mapcenteronlocation');
        maps[index].mapgeolocatemaxradiuskm =  convertStrToInt(mapbox.data('mapgeolocatemaxradiuskm'));
        maps[index].geolocationisinradius = false;

        maps[index].mapfittomarkers = mapbox.data('mapfittomarkers');
        maps[index].mapmaxradiuskm =  mapbox.data('mapmaxradiuskm');
        
        //maps[index].group = null;
        maps[index].mapmarkersbounds = null;

        maps[index].mapcenteronmarkerclick = WPMapbox.mapcenteronmarkerclick;

        maps[index].maplayersids = mapbox.data('maplayersids');
        maps[index].maplayersurls = mapbox.data('maplayersurls');
        //maps[index].maplayerscode = mapbox.data('maplayerscode');     
        
        maps[index].mapwmslayers = mapbox.data('mapwmslayers');
        maps[index].mapwmslayersdefault = mapbox.data('mapwmslayersdefault');

        maps[index].mapfeaturesids = mapbox.data('mapfeaturesids');
        maps[index].mapfeaturesurls = mapbox.data('mapfeaturesurls');
        maps[index].mapfeaturesurlstimeout = mapbox.data('mapfeaturesurlstimeout');
        maps[index].mapfeaturescode = mapbox.data('mapfeaturescode');

        maps[index].mapcirclearoundlocation = mapbox.data('mapcirclearoundlocation'); 
    });
    
    //First display the default map (to avoid blank)
    prepareMap(); 

    //Then, get geolocation (to be shared for all maps)
    //if (pleasegeolocate) getLocation();
  
});
    
function prepareMap() {  
    jQuery.each(maps, function( index, map ) { 
        //create now the map with options if it's not a static 
        if (!map.mapstaticmap) {
            prepareDynamicMap(map);
        }
        //create a static map
        else 
            prepareStaticMap(map);
    });
}

function prepareDynamicMap(map) {
    
    if (map.mapstaticmap) return; 

    //map options for creation
    var mapoptions={};
    
    //mapoptions.accessToken= map.accesstoken;
    if (!map.mapcontinuousworld) 
        // These options apply to the tile layer in the map.
        mapoptions.tileLayer= {          
        // This option disables loading tiles outside of the world bounds.
        noWrap: true
        };
    
    mapoptions.maxBounds = map.mapmaxbounds;  
    
    if ( map.mapminzoom>0) mapoptions.minZoom= map.mapminzoom ;
    if ( map.mapmaxzoom >0) mapoptions.maxZoom= map.mapmaxzoom ;

    mapoptions.attributioncontrol=map.mapattributioncontrol ; 
    map.lmap = L.mapbox.map(canvaspreffix+'-'+map.mapinternid, map.mapboxid, mapoptions); 
    //L.mapbox.featureLayer(map.mapboxid).on('ready', function(e) {
    
    //additionnal layers 
    addLayers(map);
    
    map.lmap.featureLayer.on('ready', function(e) {

       var id = e.target._geojson.id;
       var internid = id;
       var container = jQuery(e.target._map._container); 
       if (container.length) {
           var containerid=container.attr('id');
           internid=containerid.substr(canvaspreffix.length+1);
       }
       var thismap = maps[internid];
       var thislayer = this; 

        //additionnal features
        addFeatures(thismap);    
        
        //get markers
        thismap.markers = [];
        thismap.lmap.eachLayer(function(marker) { 
            if (marker instanceof L.Marker) {
                //default
                marker.isinbounds =true;
                thismap.markers.push(marker); 
            }
        });
        
        if (thismap.markers.length>0) {
            var markersgroup = new L.featureGroup(thismap.markers);
            thismap.mapmarkersbounds = markersgroup.getBounds(); 
        }	
        
        //clusters
        addClusterMarkers(thismap, e);
       
         //map is ready, geolocate or finalize        
        thismap.geolocationLatLng=null;
        if (map.mapuselocation) getLocation();
        else updateMap(thismap);
          
    });

    //Centering marker on click ?
    if (map.mapcenteronmarkerclick) {
        map.lmap.featureLayer.on('click', function(e) {
            map.lmap.panTo(e.layer.getLatLng());
        });
    }
    
    //info control
    //to do
    //map.lmap.addControl(L.mapbox.infoControl().addInfo('foo'));

    //zoom control
    if ((map.mapzoomcontrol) && (!map.mapstaticmap)) addZoomControl(map);
    
    //full screen control
    if (map.mapfullscreencontrol) addFullscreenControl(map);
    
    //share control 
    if (map.mapsharecontrol) addShareControl(map);
    
    //geocoder control
    if (map.mapgeocodercontrol) addGeocoderControl(map);

    //add base legend control
    var legendcontentcontainer = jQuery("#mapboxadv-legend-content-"+map.mapinternid+"-base");
    if (legendcontentcontainer.length) addLegendControl(map, legendcontentcontainer, 'base');

    // disable drag and zoom handlers
    if (map.mapdisabledrag) {                              
        map.lmap.dragging.disable();                               
        //lmap.tap.disable();                                
    }

    if (map.mapdisabletouchzoom) {       
        map.lmap.touchZoom.disable();
        map.lmap.doubleClickZoom.disable();
        map.lmap.scrollWheelZoom.disable();   
        map.lmap.boxZoom.disable();  
    }  
}

function prepareStaticMap(map) {

    if (!map.mapstaticmap) return; 

    //map is ready, finalize
    updateMap(map);
}

function addClusterMarkers(map, event) {

    if (map.mapclustermode=='none') return false; 
    
    var clusterGroup=null;
    if (map.mapclustermode=='classic') clusterGroup = new L.MarkerClusterGroup();
    else clusterGroup = new L.MarkerClusterGroup({
      // The iconCreateFunction takes the cluster as an argument and returns
      // an icon that represents it. We use L.mapbox.marker.icon in this
      // example, but you could also use L.icon or L.divIcon.
      iconCreateFunction: function(cluster) {
        return L.mapbox.marker.icon({
          // show the number of markers in the cluster on the icon.
          'marker-symbol': cluster.getChildCount(),
          'marker-color': map.mapclustercolor,
        }/*, {accessToken: map.accesstoken}*/);
      }
    });
    
    event.target.eachLayer(function(layer) {
        clusterGroup.addLayer(layer);
        //???
        map.lmap.removeLayer(layer);
    });
    
    map.lmap.addLayer(clusterGroup);
    
    return true;
}

function addShareControl(map) {
    share = new L.mapbox.shareControl(null/*,{accessToken: map.accesstoken}*/);
    if (map.mapsharecontrolposition!='default') share.setPosition(map.mapsharecontrolposition);
    
    share.addTo(map.lmap);
}

function addFullscreenControl(map) {  
    var fullscreencontrol=L.control.fullscreen();
    if (map.mapfullscreencontrolposition!='default') fullscreencontrol.setPosition(map.mapfullscreencontrolposition);
        
    fullscreencontrol.addTo(map.lmap);
}

function addZoomControl(map) {
    if (map.mapzoomcontrolposition!='default')
        new L.Control.Zoom({ position: map.mapzoomcontrolposition/*, 
                        accessToken: map.accesstoken*/}).addTo(map.lmap);
    else 
        new L.Control.Zoom(/*{accessToken: map.accesstoken}*/).addTo(map.lmap);
}

function addGeocoderControl(map) {
    
    var geocoder=null;
    if (map.mapgeocodercontrolposition!='default')
    geocoder = new L.mapbox.geocoderControl('mapbox.places', {
    autocomplete: map.mapgeocodercontrolautoc,
        position: map.mapgeocodercontrolposition,
        /*accessToken: map.accesstoken,*/
    }).addTo(map.lmap);
    else geocoder = new L.mapbox.geocoderControl('mapbox.places', {
        autocomplete: map.mapgeocodercontrolautoc,
        /*accessToken: map.accesstoken,*/
    }).addTo(map.lmap);
   
     geocoder.on('select', function(res) {
         map.mapcenter = new L.LatLng(res.feature.geometry.coordinates[1], res.feature.geometry.coordinates[0]);
         updateMarkersBoundsFromPosition(map, map.mapcenter, map.mapgeocoderradiuskm);
         
         //rem : force to fit if a radius is used ?
         updateMap(map, 'geocoder');    
    });
}

function addLegendControl(map, container, bloc) {
    
    var lclass='';
    if (bloc=='base') lclass="visible";
    
    map.maplegendcontent = '<div class="mapboxadv-legend-bloc mapboxadv-legend-'+bloc+'  '+lclass+'">'+container.html()+'</div>';
    
    //var legendwrapper=null;
    var legendwrapper=jQuery('#mapboxadv-legend-'+map.mapinternid);
    if (legendwrapper.length==0) {

        legendwrapper = jQuery('<div>')
            .attr('id', 'mapboxadv-legend-'+map.mapinternid)
            .addClass('mapboxadv-legend')
            .addClass(map.maplegendposition)
            .html(map.maplegendcontent);

        var legendcontrol=null;
        if (!map.mapstaticmap)  {
            if (map.maplegendposition!='default')
                legendcontrol=new L.mapbox.legendControl( {
                position: map.maplegendposition,
                /*accessToken: map.accesstoken,*/
                }).addTo(map.lmap);
            else 
                legendcontrol=new L.mapbox.legendControl( {
                /*accessToken: map.accesstoken,*/
                }).addTo(map.lmap);
    
            legendcontrol.addLegend(legendwrapper.prop('outerHTML'));
        }
        else {   
            //legend for static map, simulate dynamic map
            legendwrapper
            .addClass('staticmap')
            .attr('style', 'display:block');

            var canvas = jQuery('#'+canvaspreffix+'-'+map.mapinternid);
            //container.attr('style', 'display:block').empty().append(legendwrapper);
            canvas.append(legendwrapper);
        }
    }
    //add html content to existing base legend
    else {
        legendwrapper.append(map.maplegendcontent); 
    }
}


/*function addLocationMarker_geojson(map, position) {
    //GeoJSOn marker
    L.mapbox.featureLayer({
    // this feature is in the GeoJSON format: see geojson.org
    // for the full specification
    type: 'Feature',
    geometry: {
    type: 'Point',
    // coordinates here are in longitude, latitude order because
    // x, y is the standard for GeoJSON and many formats
    coordinates: [
    position.latlng.lng,
    position.latlng.lat 
    ]
    },
    properties: {
    title: 'Peregrine Espresso',
    description: '1718 14th St NW, Washington, DC',
    // one can customize markers by adding simplestyle properties
    // https://www.mapbox.com/guides/an-open-platform/#simplestyle
    'marker-size': 'large',
    'marker-color': '#BE9A6B',
    'marker-symbol': 'park' //from maki
    }
    }, {accessToken: map.accesstoken}).addTo(map.lmap);
}*/

/*function addLocationMarker_icon(map, position) {
    //icon marker
    var locationIcon = L.icon({
        iconUrl: 'my-icon.png',
        iconRetinaUrl: 'my-icon@2x.png',
        iconSize: [32, 32],
        iconAnchor: [22, 94],
        popupAnchor: [-3, -76],
        shadowUrl: 'my-icon-shadow.png',
        shadowRetinaUrl: 'my-icon-shadow@2x.png',
        shadowSize: [68, 95],
        shadowAnchor: [22, 94]
    });
    L.marker([position.latlng.lat,position.latlng.lng ], {icon: locationIcon}).addTo(map.lmap);                      
}*/

function addLocationMarker_mapboxicon (map) {
    if (map.geolocationLatLng) {

        var locationIcon= L.mapbox.marker.icon({
            'marker-size': map.maplocationmarkersize,
            'marker-symbol': map.maplocationmarkersymbol, 
            'marker-color':  map.maplocationmarkercolor,
        }/*, {accessToken: map.accesstoken}*/);
        L.marker([map.geolocationLatLng.lat,map.geolocationLatLng.lng ], {icon: locationIcon}).addTo(map.lmap);
        
        if (map.mapcirclearoundlocation) {
            var radius = map.mapmaxradiuskm*1000;
            
            circlejson={};
            circlejson.type='Circle';
            circlejson.coordinates=[map.geolocationLatLng.lat, map.geolocationLatLng.lng];
            circlejson.radius=radius;
            circlejson.properties={
                stroke:true, 
                color:map.maplocationmarkercolor, 
                fill:map.maplocationmarkercolor, 
                weight:5};
            addCircle(map, circlejson);
        }
    }
}

function hasForceToFit(map, mode) {
    var hasto=false;
    switch(mode) {
        case 'geocoder':
            if (map.mapgeocoderradiuskm>0) hasto=true;
            break;
            
        default:
            if ((map.mapmaxradiuskm>0) && (map.geolocationLatLng) && (map.geolocationisinradius)) 
                hasto=true;
            break;
    }
    
    return hasto;
}
 
function updateMap(map, mode) {
    
    if (typeof(mode) === 'undefined') mode='normal';

    //display a geoloc marker ?
    //if (map.mapuselocation) getLocation();
    //if ((!map.mapstaticmap) && (map.mapdisplaylocation)) addLocationMarker_mapboxicon(map);
    
    var forcetofit = hasForceToFit(map, mode);

    //zoom or bounds	
    if ((!map.mapfittomarkers) && (!forcetofit)) { 
      
        //Defaut map center
        //map.mapcenter = map.lmap.getCenter();
        
        //or use settings
        if ((map.mapcenterlat!=0) && (map.mapcenterlng!=0)) {
            map.mapcenter= new L.LatLng(map.mapcenterlat, map.mapcenterlng);
        }
        //or center on geolocation ?      
        if ((map.geolocationLatLng) && (map.geolocationisinradius) && (map.mapcenteronlocation)) {
            map.mapcenter = map.geolocationLatLng;
        }
        
        zoomAndCenterMap(map);
    }
    else {
        //here we dont need zoom and center values
        fitMapToBounds(map, map.mapmarkersbounds);
    }
    
    //hack 
    if (map.mapstaticmap) {
        //add legend control for static map now
        var legendcontentcontainer = jQuery("#mapboxadv-legend-content-"+map.mapinternid+"-base");
        if (legendcontentcontainer.length) addLegendControl(map, legendcontentcontainer, 'base');
    }
}

function buildStaticUrl(map, forcetofit) {
    
    if (typeof(forcetofit)==="undefined") forcetofit=false;
    
    //on static maps, no zoom auto is available ? check new version !
    var staticurl='';
    var overlays='';   
    var param='';   

    zoom = map.mapzoom;
    if (map.mapzoom==0) zoom=map.mapminzoom;

    if (forcetofit) param='auto';
    else if ((map.mapcenterlat!=0) && (map.mapcenterlng!=0)) {
        //map.mapcenter= new L.LatLng(map.mapcenterlat, map.mapcenterlng);
        param = map.mapcenter.lng+','+map.mapcenter.lat+','+zoom;
    }
    
    overlays = prepareStaticMapOverlays(map);

    staticurl='http://api.tiles.mapbox.com/v4/'+map.mapboxid;
    if (overlays.length) staticurl+='/'+overlays;
    if (param.length) staticurl+='/'+param;
    staticurl+='/'+map.pixelwidth+'x'+map.pixelheight+map.mapstaticmapretinasuffix+'.'+map.mapstaticmapformat;
    staticurl+='?access_token='+map.accesstoken;
    
    return staticurl;
}
   
function zoomAndCenterMap(map) {
    
    if (map.mapstaticmap) {
            var staticurl = buildStaticUrl(map);
            jQuery("#"+canvaspreffix+"-"+map.mapinternid).empty().append("<img class='mapbox-static' id='mapbox-static"+map.mapinternid+"' src='"+staticurl+"'/>");
    }
    else { 
        if (map.mapzoom>0) map.lmap.setView(map.mapcenter, map.mapzoom);
        else map.lmap.setView(map.mapcenter);
        
        //force refresh
        map.lmap.invalidateSize(true);
    }
}
   
function fitMapToBounds (map, bounds)
{    
    if (map.mapstaticmap) {
        var staticurl = buildStaticUrl(map, true);
        jQuery("#"+canvaspreffix+"-"+map.mapinternid).empty().append("<img class='mapbox-static' id='mapbox-static"+map.mapinternid+"' src='"+staticurl+"'/>");
    
    }
    else {
        if ((typeof(bounds)!=='undefined') && (bounds!==null) && (bounds.isValid())) {
            
            //Center is set by the fit bounds effect....
            //then fit...
            //Convert % to pixels
            var padding = map.mappadding;  
            if (jQuery.type(padding)=='string' && padding.indexOf('%')!=-1) padding = map.pixelwidth*(convertStrToInt(padding)/100); 
            else padding = convertStrToInt(padding);
            var paddingtlx = padding;
            var paddingtly = padding;
            var paddingbrx = padding;
            var paddingbry = padding;

            var paddingtlpoint = null;		
            if (map.paddingtlx!==0) {
                //Convert % to pixels
                paddingtlx = map.mappaddingtlx;
                if (jQuery.type(paddingtlx)=='string' &&  paddingtlx.indexOf('%')!=-1)
                    paddingtlx = map.pixelwidth*(parseInt(paddingtlx)/100); 	
                else paddingtlx = convertStrToInt(paddingtlx);
            }

            if (map.paddingtly!==0) {
                paddingtly = map.mappaddingtly;
                if (jQuery.type(paddingtly)=='string' && paddingtly.indexOf('%')!=-1) {
                        paddingtly = map.pixelwidth*(parseInt(paddingtly)/100); 
                }
                else paddingtly = convertStrToInt(paddingtly);
            }

            if (paddingtlx!==0 || paddingtly!==0) paddingtlpoint = new L.Point(paddingtlx, paddingtly);

            var paddingbrpoint = null;			
            if (map.paddingbrx!==0) {
                //Convert % to pixels
                paddingbrx = map.mappaddingbrx;		
                if (jQuery.type(paddingbrx)=='string' &&  paddingbrx.indexOf('%')!=-1)
                    paddingbrx = map.pixelwidth*(parseInt(paddingbrx)/100); 
                else paddingbrx = convertStrToInt(paddingbrx);
            }

            if (map.paddingbry!==0) {
                paddingbry = map.mappaddingbry;
                if (jQuery.type(paddingbry)=='string' &&  paddingbry.indexOf('%')!=-1) 
                    paddingbry = map.pixelwidth*(parseInt(paddingbry)/100); 
                else paddingbry = convertStrToInt(paddingbry);
            }

            if (paddingbrx!==0 || paddingbry!==0) paddingbrpoint = new L.Point(paddingbrx, paddingbry);

            if (paddingtlpoint || paddingbrpoint) {
                map.lmap.fitBounds(bounds, {paddingTopLeft:paddingtlpoint, paddingBottomRight:paddingbrpoint});
            }
            else {
                map.lmap.fitBounds(bounds);
            }
  
            //force refresh
            map.lmap.invalidateSize(true);
        }   
    }
}

function updateMarkersBoundsFromPosition(map, position, radius, expand) {

    //if (map.markers.length==0) return false;   
    
    if ((typeof position ==='undefined') || (position==null)) return false; 
    if (typeof radius ==='undefined') radius=0; 
    if (typeof expand ==='undefined') expand=true; 
    
    //Eliminate now from bound all markers that are too far away from the given position
    if (radius>0) {     
        for (i=map.markers.length-1;i>=0; i--) {
            if (map.markers[i].getLatLng().distanceTo(position)>radius*1000) {
                //map.markers.splice(i,1); 
                map.markers[i].isinbounds = false;
            }
            else map.markers[i].isinbounds = true;
        }
        
        //Recalculate bounds 
        var markersinbounds=[];
        jQuery.each(map.markers, function( index, marker ) { 
            if (marker.isinbounds) markersinbounds.push(marker);
        });
        if (markersinbounds.length>0) {

            var markersgroup = L.featureGroup(markersinbounds);
            map.mapmarkersbounds = markersgroup.getBounds(); 
        }
        //No more markers left
        else {
            //map.group = null;
            map.mapmarkersbounds = null;		   	    		
        }
    }          
    
    if (map.mapmarkersbounds!==null) {
        map.mapmarkersbounds.extend(position);
         minimize=true;
    }
    //no markers in bounds
    else {
        map.mapmarkersbounds = new L.LatLngBounds(position, position);
        //force to false to avoid strong zoom on map center
        minimize=false;
    }
    
    if (expand) { 
        if (radius>0) {
            //Define max bounds for our map 
            map.mapmarkersbounds = new L.LatLngBounds(
                     dest(position.lat, position.lng,
                     45, radius));

            for (a=135; a<=315;a=a+45) {
            map.mapmarkersbounds.extend(
                    dest(position.lat,position.lng,
                    a, radius));
            }
        }
    }
}

//tools
function convertToJSON(str) {
    str=str.replace(/&#91;/g, '[');
    str=str.replace(/&#93;/g, ']');
    return str;
}

function convertStrToInt(str) {
    //to avoid NaN
    return (parseInt(str) || 0);
 }

function convertStrToFloat(str) {
    //to avoid NaN
    return (parseFloat(str) || 0);
 }
    
function convertStrToBool(str) {
    return ((str === "true") || (str === "1")) ;
}

function convertToSafeId(str) {
    return str.replace(' ', '-').trim();
}

//Thanks to herostwist on http://jsfiddle.net/herostwist/DnSHY/2/
if (typeof(Number.prototype.toRad) === "undefined") {
      Number.prototype.toRad = function() {
        return this * Math.PI / 180;
      }
}

if (typeof(Number.prototype.toDeg) === "undefined") {
      Number.prototype.toDeg = function() {
        return this * 180 / Math.PI;
      }
}

function dest (lat,lng, brng, dist) {
    this._radius = 6371;
    dist = typeof(dist) == 'number' ? dist : typeof(dist) == 'string' && dist.trim() != '' ? +dist : NaN;
    dist = dist / this._radius;
    brng = brng.toRad();  
    var lat1 = lat.toRad(),
        lon1 = lng.toRad();
    var lat2 = Math.asin(Math.sin(lat1) * Math.cos(dist) +
        Math.cos(lat1) * Math.sin(dist) *
        Math.cos(brng));
    var lon2 = lon1 + Math.atan2(Math.sin(brng) * Math.sin(dist) *
        Math.cos(lat1), Math.cos(dist) -
        Math.sin(lat1) * Math.sin(lat2));
    lon2 = (lon2 + 3 * Math.PI) % (2 * Math.PI) - Math.PI;

    return new L.LatLng(lat2.toDeg(),lon2.toDeg());
}

//Thanks to ???
function getBoundsZoomLevel(map, bounds) {
    var WORLD_DIM = { height: 256, width: 256 };
    var ZOOM_MAX = 21;

    function latRad(lat) {
        var sin = Math.sin(lat * Math.PI / 180);
        var radX2 = Math.log((1 + sin) / (1 - sin)) / 2;
        return Math.max(Math.min(radX2, Math.PI), -Math.PI) / 2;
    }

    function zoom(mapPx, worldPx, fraction) {
        return Math.floor(Math.log(mapPx / worldPx / fraction) / Math.LN2);
    }

    var ne = bounds.getNorthEast();
    var sw = bounds.getSouthWest();

    var latFraction = (latRad(ne.lat) - latRad(sw.lat)) / Math.PI;

    var lngDiff = ne.lng - sw.lng;
    var lngFraction = ((lngDiff < 0) ? (lngDiff + 360) : lngDiff) / 360;

    var latZoom = zoom(map.pixelheight, WORLD_DIM.height, latFraction);
    var lngZoom = zoom(map.pixelwidth, WORLD_DIM.width, lngFraction);

    return Math.min(latZoom, lngZoom, ZOOM_MAX);
}function getLocation() {}
function prepareStaticMapOverlays(map){return '';}
function addLayers(map) {}
function addWMSLayers(map) {}
function addCircle(map, circle) {}
function addCircles(map) {}
function addFeatures(map) {}
function addJSONToFeatureLayer(map, json) {}