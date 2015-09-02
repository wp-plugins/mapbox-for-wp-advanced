<?php 

include_once plugin_dir_path( __FILE__ ).'/mapboxadvhelper.php';

class Mapbox_Builder {
    
    protected $params=null;
    
    public function __construct()
    {   
    }

    public function init_plugin_sources()
    {
        //open source mapbox.js scripts
        wp_register_script('mapboxjs', 'http://api.tiles.mapbox.com/mapbox.js/v2.1.9/mapbox.js', false, '1.3.2');
        wp_enqueue_script('mapboxjs'); 
        wp_register_style('mapboxcss', 'http://api.tiles.mapbox.com/mapbox.js/v2.1.9/mapbox.css' );
        wp_enqueue_style('mapboxcss');
    }
    
    public function init($parameters) {
        
        //general plugin params
        $this->params = $parameters;
        $this->params['mapaccesstoken'] = get_option('mapbox_mapaccesstoken', '');
        $this->params['mapattributioncontrol'] = get_option('mapbox_mapattributioncontrol', true);         
        $this->params['mapcontinuousworld'] = get_option('mapbox_mapcontinuousworld', true);    
        $this->params['mapdisabletouchzoom'] = get_option('mapbox_mapdisabletouchezoom', false);
        $this->params['mapdisabledrag'] = get_option('mapbox_mapdisabledrag', false);  
        $this->params['mapclustermode'] = get_option('mapbox_mapclustermode', 0); 
        $this->params['mapclustercolor'] = get_option('mapbox_mapclustercolor', "#000000");
        $this->params['maplocatehighaccuracy'] = get_option('mapbox_maplocatehighaccuracy', false);
        $this->params['mapdisplaylocation'] = get_option('mapbox_mapdisplaylocation', false);
        $this->params['maplocationmarkersize'] = get_option('mapbox_maplocationmarkersize', 'medium');
        $this->params['maplocationmarkersymbol'] = get_option('mapbox_maplocationmarkersymbol', 'star');
        $this->params['maplocationmarkercolor'] = get_option('mapbox_maplocationmarkercolor', '#000000');
        $this->params['mapcenteronmarkerclick'] = get_option('mapbox_mapcenteronmarkerclick', false);
        $this->params['mapstaticmapformat'] = get_option('mapbox_mapstaticmapformat', 'png'); 
        $this->params['mapstaticmapformatretina'] = get_option('mapbox_mapstaticmapformatretina', false); 

        //check global param to add specific includes
        if ($this->params['mapclustermode']!='none') {
            wp_register_script('markerclusterjs', 'https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-markercluster/v0.4.0/leaflet.markercluster.js');
            wp_enqueue_script('markerclusterjs'); 

            wp_register_style('markerclustercss', 'https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-markercluster/v0.4.0/MarkerCluster.css' );
            wp_enqueue_style('markerclustercss');

            wp_register_style('markerclusterdefcss', 'https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-markercluster/v0.4.0/MarkerCluster.Default.css' );
            wp_enqueue_style('markerclusterdefcss');
        } 

        //get default values from plugin params and cast
        
        //get mapbox id and map suffix from database
        global $wpdb;
        $maps_table_name = $wpdb->prefix . 'mapboxadv_maps'; // do not forget about tables prefix      
        // Call $wpdb->prepare passing the values of the array as separate arguments
        $map = $wpdb->get_row("SELECT * FROM $maps_table_name WHERE id=".$this->params['mapid']);
        $this->params['mapboxid'] = $map->mapboxid;
        //$this->params['mapsuffix'] = $map->suffix;
        $this->params['mapinternid'] = str_replace('.', '-', $this->params['mapboxid']);
        if (strlen($this->params['mapsuffix'])) $this->params['mapinternid'] .= '-'.$this->params['mapsuffix'];

        //get map legend from database 
        $this->params['maplegends']=array();
        if (strlen($map->legendcontent) || strlen($map->legendcss)) {          
            $this->params['maplegends']['base']=array();
            
            $this->params['maplegends']['base']['content']=stripslashes($map->legendcontent); 
            if (strlen($this->params['maplegendcontent'])) $this->params['maplegends']['base']['content']=stripslashes($this->params['maplegendcontent']); 
                
            $this->params['maplegends']['base']['css']=$map->legendcss; 
            if (strlen($this->params['maplegendcontentcss'])) $this->params['maplegends']['base']['css']=stripslashes($this->params['maplegendcontentcss']); 
        }
        //for other legends (from wms layers), see below

        //...
         if ($this->params['mapwidth']=='') 
            $this->params['mapwidth']=get_option('mapbox_default_mapwidth',0);
         if ($this->params['mapheight']=='') 
            $this->params['mapheight']=get_option('mapbox_default_mapheight',0);
        
         if ($this->params['mapcenterlat']=='') 
            $this->params['mapcenterlat']=get_option('mapbox_default_mapcenterlat',0);
         if ($this->params['mapcenterlng']=='') 
            $this->params['mapcenterlng']=get_option('mapbox_default_mapcenterlng',0);
        
         if ($this->params['mapmaxboundsswlat']=='') 
            $this->params['mapmaxboundsswlat']=get_option('mapbox_default_mapmaxboundsswlat',0);
         if ($this->params['mapmaxboundsswlng']=='') 
            $this->params['mapmaxboundsswlng']=get_option('mapbox_default_mapmaxboundsswlng',0);
         if ($this->params['mapmaxboundsnelat']=='') 
            $this->params['mapmaxboundsnelat']=get_option('mapbox_default_mapmaxboundsnelat',0);
         if ($this->params['mapmaxboundsnelng']=='') 
            $this->params['mapmaxboundsnelng']=get_option('mapbox_default_mapmaxboundsnelng',0);

        if ($this->params['mapzoom']=='') 
            $this->params['mapzoom']=get_option('mapbox_default_mapzoom',0);
        if ($this->params['mapminzoom']=='') 
            $this->params['mapminzoom']=get_option('mapbox_default_mapminzoom',0);
        if ($this->params['mapmaxzoom']=='') 
            $this->params['mapmaxzoom']=get_option('mapbox_default_mapmaxzoom',0);

        if ($this->params['mappadding']=='') 
            $this->params['mappadding']=get_option('mapbox_default_mappadding',0);
    
         if ($this->params['mappaddingtlx']=='') 
            $this->params['mappaddingtlx']=get_option('mapbox_default_mappaddingtlx',0);
    
         if ($this->params['mappaddingtly']=='') 
            $this->params['mappaddingtly']=get_option('mapbox_default_mappaddingtly',0);
    
         if ($this->params['mappaddingbrx']=='') 
            $this->params['mappaddingbrx']=get_option('mapbox_default_mappaddingbrx',0);
    
         if ($this->params['mappaddingbry']=='') 
            $this->params['mappaddingbry']=get_option('mapbox_default_mappaddingbry',0);

        if ($this->params['mapstaticmap']=='default') 
            $this->params['mapstaticmap']=get_option('mapbox_default_mapstaticmap',true);
        
        $this->params['mapstaticmapformat']=get_option('mapbox_mapstaticmapformat',true);
        
        $this->params['mapstaticmapformatretina']=get_option('mapbox_mapstaticmapformatretina',true);
        
        $this->params['mapstaticmapretinasuffix']='';
        if ($this->params['mapstaticmapformatretina'])
            $this->params['mapstaticmapretinasuffix']='@2x';

        if ($this->params['mapfittomarkers']=='default') 
            $this->params['mapfittomarkers']=get_option('mapbox_default_mapfittomarkers', false);
     
        if ($this->params['mapzoomcontrol']=='default') 
            $this->params['mapzoomcontrol']=get_option('mapbox_default_mapzoomcontrol', false);
        
        if ($this->params['mapzoomcontrolposition']=='default') 
            $this->params['mapzoomcontrolposition']=get_option('mapbox_default_mapzoomcontrolpostion', 'topleft');
        
        if ($this->params['mapfullscreencontrol']=='default') 
        $this->params['mapfullscreencontrol']=get_option('mapbox_default_mapfullscreencontrol', false);

        if ($this->params['mapfullscreencontrolposition']=='default') 
        $this->params['mapfullscreencontrolposition']=get_option('mapbox_default_mapfullscreencontrolposition', 'topright');
           
        if ($this->params['mapsharecontrol']=='default') 
        $this->params['mapsharecontrol']=get_option('mapbox_default_mapsharecontrol', false);

        if ($this->params['mapsharecontrolposition']=='default') 
        $this->params['mapsharecontrolposition']=get_option('mapbox_default_mapsharecontrolposition', 'topright');
               
        if ($this->params['mapgeocodercontrol']=='default') 
        $this->params['mapgeocodercontrol']=get_option('mapbox_default_mapgeocodercontrol', false);

        if ($this->params['mapgeocodercontrolposition']=='default') 
        $this->params['mapgeocodercontrolposition']=get_option('mapbox_default_mapgeocodercontrolposition', 'topright');

        if ($this->params['mapgeocodercontrolautoc']=='default') 
        $this->params['mapgeocodercontrolautoc']=get_option('mapbox_default_mapgeocodercontrolautoc', false);
        
         if ($this->params['mapgeocoderradiuskm']=='') 
        $this->params['mapgeocoderradiuskm']=get_option('mapbox_default_mapgeocoderradiuskm', false);
           
        if ($this->params['maplegendposition']=='default') 
        $this->params['maplegendposition']=get_option('mapbox_default_maplegendposition', 'bottomright');
        
        if ($this->params['maplegendbackgroundcolor']=='default') 
        $this->params['maplegendbackgroundcolor']=get_option('mapbox_default_maplegendbackgroundcolor', '#FFFFFF');
                   
        if ($this->params['maplayerscontrolposition']=='default') 
        $this->params['maplayerscontrolposition']=get_option('mapbox_default_maplayerscontrolposition', 'topright');

        if (($this->params['mapuselocation']===null) || ($this->params['mapuselocation']=='default'))
        $this->params['mapuselocation']=get_option('mapbox_default_mapuselocation', false);

        if ($this->params['mapcenteronlocation']=='default') 
        $this->params['mapcenteronlocation']=get_option('mapbox_default_mapcenteronlocation', false);
        
        if ($this->params['mapcirclearoundlocation']=='default') 
        $this->params['mapcirclearoundlocation']=get_option('mapbox_default_mapcirclearoundlocation', false);
        
        if ($this->params['mapmaxradiuskm']=='')
        $this->params['mapmaxradiuskm'] = get_option('mapbox_default_mapmaxradiuskm', 0);

        if ($this->params['mapgeolocatemaxradiuskm']=='')
        $this->params['mapgeolocatemaxradiuskm'] = get_option('mapbox_default_mapgeolocatemaxradiuskm', 0);

        //Concatenate with default
        /*if (strlen(get_option('mapbox_default_maplayersids'))){
            if (strlen($this->params['maplayersids'])) $this->params['maplayersids'] .= ';';
            $this->params['maplayersids'] .= get_option('mapbox_default_maplayersids');
        }
        if (strlen(get_option('mapbox_default_maplayersurls'))){
            if (strlen($this->params['maplayersurls'])) $this->params['maplayersurls'] .= ';';
            $this->params['maplayersurls'] .= get_option('mapbox_default_maplayersurls');
        }  
        
        if (strlen(get_option('mapbox_default_maplayersids'))){
            if (strlen($this->params['maplayersids'])) $this->params['maplayersids'] .= ';';
            $this->params['maplayersids'] .= get_option('mapbox_default_maplayersids');
        }  
        
        if ($this->params['mapbox_default_mapwmslayersbaseurl']=='')
            $this->params['mapwmslayersbaseurl'] .= get_option('mapbox_default_mapwmslayersbaseurl');

        if ($this->params['mapwmslayerscontrolposition']=='default') 
        $this->params['mapwmslayerscontrolposition']=get_option('mapbox_default_mapwmslayerscontrolposition', 'topright');

         if ($this->params['mapwmslayersopacity']==''){
            $this->params['mapwmslayersopacity'] .= get_option('mapbox_default_mapwmslayersopacity');
        }
        /*if (strlen(get_option('mapbox_default_maplayerscode'))){
            $this->params['maplayerscode'] .= get_option('mapbox_default_maplayerscode');
        }*/
        
        /* if (strlen(get_option('mapbox_default_mapfeaturesids'))){
            if (strlen($this->params['mapfeaturesids'])) $this->params['mapfeaturesids'] .= ';';
            $this->params['mapfeaturesids'] .= get_option('mapbox_default_mapfeaturesids');
         }
        if (strlen(get_option('mapbox_default_mapfeaturesurls'))){
             if (strlen($this->params['mapfeaturesurls'])) $this->params['mapfeaturesurls'] .= ';';
            $this->params['mapfeaturesurls'] .= get_option('mapbox_default_mapfeaturesurls');
        }
       
        if (strlen(get_option('mapbox_default_mapfeaturescode'))){
            $this->params['mapfeaturescode'] .= get_option('mapbox_default_mapfeaturescode');
            //hack
            //$this->params['mapfeaturescode'] = str_replace('[', '&#91;', $this->params['mapfeaturescode']);
            //$this->params['mapfeaturescode'] = str_replace(']', '&#93;', $this->params['mapfeaturescode']);    
        }
        
        if ($this->params['mapfeaturesurlstimeout']=='') 
         $this->params['mapfeaturesurlstimeout'] = get_option('mapbox_default_mapfeaturesurlstimeout');*/

        //maplayers
        $this->params['maplayersids']=str_replace(';',',',$this->params['maplayersids']);
        $this->params['maplayersurls']=str_replace(';',',',$this->params['maplayersurls']);
        
        //Generate mapwmslayers json struct from ids list and db...
        //global $wpdb;
        $wmslayers_table_name = $wpdb->prefix . 'mapboxadv_wmslayers'; // do not forget about tables prefix
        
        //prepare ids list before
        $this->params['mapwmslayers']=str_replace(';',',',$this->params['mapwmslayers']);
       
        $ids = explode(',',  $this->params['mapwmslayers']); 
        $sql = "SELECT * FROM $wmslayers_table_name WHERE ID IN(".implode(', ', array_fill(0, count($ids), '%d')).")";
        // Call $wpdb->prepare passing the values of the array as separate arguments
        $query = call_user_func_array(array($wpdb, 'prepare'), array_merge(array($sql), $ids));

        $wmslayers = $wpdb->get_results($query, ARRAY_A);

        //other legends (from wms layers)
        foreach ($wmslayers as $key => $wmslayer) {

            $this->params['maplegends']['wmslayer'.$wmslayer['id']]=array();
            $this->params['maplegends']['wmslayer'.$wmslayer['id']]['content']=stripslashes($wmslayer['legendcontent']);
            
            $this->params['maplegends']['wmslayer'.$wmslayer['id']]['css']=$wmslayer['legendcss']; 
            
            //unset these columns (not used in js via json)
            unset($wmslayers[$key]['legendcontent']);
            unset($wmslayers[$key]['legendcss']);
        }
        
        $this->params['mapwmslayers'] = json_encode($wmslayers);
    
        //Adapt parameters to context
        if ($this->params['mapzoom']>$this->params['mapmaxzoom']) $this->params['mapzoom']=$this->params['mapmaxzoom'];
        if (($this->params['mapzoom']>0) && ($this->params['mapzoom']<$this->params['mapminzoom'])) $this->params['mapzoom']=$this->params['mapminzoom'];
        
        if ($this->params['mapstaticmap']) {
            $this->params['mapzoomcontrol'] = false;
            $this->params['mapgeocodercontrol'] = false;  
        }
        
         if ($this->params['mapuselocation']==false) {
            $this->params['mapdisplaylocation']=false;
            $this->params['mapcenteronlocation'] = false;
        }
        
        /*if ($this->params['mapmaxradiuskm']) {
            $this->params['mapfittomarkers'] = true;
            $this->params['mapcenteronlocation'] = true;
        }*/
        
        //advanced mapbox for wp script
        //for debug : min version is not used
        //do not use this on production site!
        wp_enqueue_script('mapboxadvjs',plugins_url('assets/js/mapboxadv-min.js', __FILE__), array('jquery'));
        wp_localize_script( 'mapboxadvjs', 'WPMapbox', $this->params );

        //finally, check params to add specific includes
        if ($this->params['mapfullscreencontrol']=='true') {
            wp_register_script('fullscreenjs', 'https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v0.0.4/Leaflet.fullscreen.min.js');
            wp_enqueue_script('fullscreenjs'); 

            wp_register_style('fullscreencss', 'https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v0.0.4/leaflet.fullscreen.css');
            wp_enqueue_style('fullscreencss'); 
        }
    }

    public function getstyle() {

        //add  all custom legends css (plugin)
        foreach ($this->params["maplegends"] as $key => $legend ) {  
            $css.=$legend['css'];
        }
        
        $css.=' #mapbox-canvas-'.$this->params["mapinternid"].' { 
            width:'.$this->params["mapwidth"].';
            height:'.$this->params["mapheight"].';
        }';
        
        $css.=' #mapbox-canvas-'.$this->params["mapinternid"].' .map-legends, #mapbox-'.$this->params["mapinternid"].' .mapboxadv-legend.staticmap { background-color:'.$this->params['maplegendbackgroundcolor'].';';
        if (strcmp($this->params['maplegendbackgroundcolor'],'transparent')==0) {
            $css.='box-shadow:none';
            $css.='border:none';
        }
        $css.='}';
        
        return Mapboxadv::minify($css);
    }

    public function gethtml() {
   
        $html='<style>';
        $html.=$this->getstyle();
        $html.='</style>';
        
        $html.="<div class='mapbox-map' id='mapbox-".$this->params['mapinternid']."' 
        data-mapid='".$this->params['mapid']."' 
        data-mapsuffix='".$this->params['mapsuffix']."' 
        data-mapinternid='".$this->params['mapinternid']."' 
        data-mapboxid='".$this->params['mapboxid']."'
        data-mapstaticmap='".$this->params['mapstaticmap']."' 
        data-mappadding='".$this->params['mappadding']."'
        data-mappaddingtlx='".$this->params['mappaddingtlx']."' 
        data-mappaddingtly='".$this->params['mappaddingtly']."' 
        data-mappaddingbrx='".$this->params['mappaddingbrx']."' 
        data-mappaddingbry='".$this->params['mappaddingbry']."' 
        data-mapcenterlat='".$this->params['mapcenterlat']."'
        data-mapcenterlng='".$this->params['mapcenterlng']."'  
        data-mapzoomcontrol='".$this->params['mapzoomcontrol']."' 
        data-mapzoomcontrolposition='".$this->params['mapzoomcontrolposition']."' 
        data-mapmaxzoom='".$this->params['mapmaxzoom']."' 
        data-mapminzoom='".$this->params['mapminzoom']."'
        data-mapzoom='".$this->params['mapzoom']."'
        data-mapfullscreencontrol='".$this->params['mapfullscreencontrol']."' 
        data-mapfullscreencontrolposition='".$this->params['mapfullscreencontrolposition']."' 
        data-mapsharecontrol='".$this->params['mapsharecontrol']."' 
        data-mapsharecontrolposition='".$this->params['mapsharecontrolposition']."' 
        data-mapgeocodercontrol='".$this->params['mapgeocodercontrol']."'
        data-mapgeocodercontrolautoc='".$this->params['mapgeocodercontrolautoc']."' 
        data-mapgeocodercontrolposition='".$this->params['mapgeocodercontrolposition']."' 
        data-mapgeocoderradiuskm='".$this->params['mapgeocoderradiuskm']."'
        data-maplegendposition='".$this->params['maplegendposition']."'
        data-maplegendbackgroundcolor='".$this->params['maplegendbackgroundcolor']."'
        data-maplayerscontrolposition='".$this->params['maplayerscontrolposition']."'
        data-mapmaxboundsswlat='".$this->params['mapmaxboundsswlat']."' 
        data-mapmaxboundsswlng='".$this->params['mapmaxboundsswlng']."' 
        data-mapmaxboundsnelat='".$this->params['mapmaxboundsnelat']."' 
        data-mapmaxboundsnelng='".$this->params['mapmaxboundsnelng']."' 
        data-mapmaxradiuskm='".$this->params['mapmaxradiuskm']."'
        data-mapgeolocatemaxradiuskm='".$this->params['mapgeolocatemaxradiuskm']."'       
        data-mapuselocation='".$this->params['mapuselocation']."'
        data-mapcenteronlocation='".$this->params['mapcenteronlocation']."'
        data-mapcirclearoundlocation='".$this->params['mapcirclearoundlocation']."'
        data-mapfittomarkers='".$this->params['mapfittomarkers']."'   
        data-maplayersids='".$this->params['maplayersids']."'
        data-maplayersurls='".$this->params['maplayersurls']."'
        data-mapwmslayers='".$this->params['mapwmslayers']."'
        data-mapwmslayersdefault='".$this->params['mapwmslayersdefault']."'
        data-mapfeaturesids='".$this->params['mapfeaturesids']."'
        data-mapfeaturesurls='".$this->params['mapfeaturesurls']."'
        data-mapfeaturesurlstimeout='".$this->params['mapfeaturesurlstimeout']."'
        data-mapfeaturescode='".$this->params['mapfeaturescode']."'
        >";
        //data-mapwmslayersopacity='".$this->params['mapwmslayersopacity']."'
        //data-maplayerscode='".$this->params['maplayerscode']."'
      
        //prepare legends 
        foreach ($this->params["maplegends"] as $key => $legend ) {        
            if (strlen($legend["content"])) {
                if ($this->params["mapstaticmap"]=='true') $class='staticmap';
                else $class='';
                
                $html.='<div class="mapboxadv-legend-content '.$class.'" id="mapboxadv-legend-content-'.$this->params["mapinternid"].'-'.$key.'" style="display:none;">';
              
                $html.=htmlspecialchars_decode($legend["content"]);	
                $html.='</div>';
            }         
        }

        if (strlen($this->params["maptitle"]) || strlen($this->params["mapdescription"])) {
            $html.='<div class="mapbox-map-panel">';
            $html.='<div class="mapbox-map-title">'.$this->params["maptitle"].'</div>';
            $html.='<div class="mapbox-map-description">'.$this->params["mapdescription"].'</div>';
            $html.='</div>';
        }
        
        $html.='</div>';

        return $html;
    }
}
?>
