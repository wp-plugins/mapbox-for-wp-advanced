<?php

include_once plugin_dir_path( __FILE__ ).'/default.php';

class Mapboxadv_Shorcut
{
    protected $sbuilder =null;
    
    public function __construct()
    {
        $this->sbuilder = new Mapbox_Builder();
        
        add_shortcode('mapboxadv_map', array($this, 'map_html'));
    }

    public function map_html($atts, $content)
    {
        
        //wp_register_style('mapboxadvcss', plugins_url('mapboxadv.css', __FILE__) );
        //wp_enqueue_style('mapboxadvcss');
      
        $atts = shortcode_atts(array(
            'mapid'=> '',
            'mapsuffix'=> '',
            'mapboxid' => '',
            'mapwidth'=> '',
            'mapheight'=> '',
            'mapstaticmap' => 'default',
            'mappadding' => '',
            'mappaddingtlx' => '',
            'mappaddingtly' => '',
            'mappaddingbrx' => '',
            'mappaddingbry' => '',
            'mapzoomcontrol' => 'default',
            'mapzoomcontrolposition' => 'default',
            'mapcenterlat' => '',
            'mapcenterlng' => '',
            'mapzoom' => '', 
            'mapminzoom' => '', 
            'mapmaxzoom' => '',      
            'mapmaxboundsswlat' => '',
            'mapmaxboundsswlng' => '',
            'mapmaxboundsnelat' => '',
            'mapmaxboundsnelng' => '',  
            'mapfullscreencontrol' => 'default',
            'mapfullscreencontrolposition' => 'default',   
            'mapsharecontrol' => 'default',
            'mapsharecontrolposition' => 'default', 
            'mapgeocodercontrol' => 'default',
            'mapgeocodercontrolposition' => 'default', 
            'mapgeocodercontrolautoc' => 'default',
            'mapgeocoderradiuskm' => '',
            'maplegendposition' => 'default',
            'maplegendbackgroundcolor' => 'default',
            'maplayerscontrolposition' => 'default',
            'mapuselocation' => 'default', 
            'mapgeolocatemaxradiuskm' => '',
            'mapcenteronlocation' => 'default',   
            'mapcirclearoundlocation' => 'default',   
            'mapmaxradiuskm' => '',     
            'mapfittomarkers' => 'default',
            'maplayersids' => '',
            'maplayersurls' => '',
            'mapwmslayers' => '',
            'mapwmslayersdefault' => '',
            //'mapwmslayersopacity' => '',
            //'maplayerscode' => get_option('mapbox_default_maplayerscode',''), 
            'mapfeaturesids' => '',
            'mapfeaturesurls' => '',
            'mapfeaturesurlstimeout' => '',
            'mapfeaturescode' => '',
            'maplegendcontent' => '',
            'maplegendcontentcss' => '',
            'maptitle' => '',
            'mapdescription' => ''
            
            ), $atts);
        
        $this->sbuilder->init($atts);
        
        $html = array();
        $html[] = $content;
        $html[] = $this->sbuilder->gethtml();

        $htmlstr = implode('', $html);
        return $htmlstr;
    }
}
?>