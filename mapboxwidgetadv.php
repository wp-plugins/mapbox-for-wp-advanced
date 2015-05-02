<?php
/*
Author: Stephane Martin
Author URI: http://www.stephanemartinw.com
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: mapboxadv
*/

include_once plugin_dir_path( __FILE__ ).'/mapboxadvhelper.php';
include_once plugin_dir_path( __FILE__ ).'/default.php';

class Mapboxadv_Widget extends WP_Widget
{  
    protected $wbuilder = null;

    public function __construct()
    {   
        load_plugin_textdomain('mapboxadv', false, basename( dirname( __FILE__ ) ) . '/languages' );

        //widget builder
        $this->wbuilder = new Mapbox_Builder();
     
        parent::__construct('Mapboxadv', 'Mapbox for WP Advanced', array('description' => 'Mapbox for WP Advanced'));
    }

    public function widget($args, $instance)
    {
  
        //wp_register_style('mapboxadvcss', plugins_url('mapboxadv.css', __FILE__) );
        //wp_enqueue_style('mapboxadvcss');

             
        echo apply_filters('widget_title', $instance['title']);
       
        $this->wbuilder->init($instance);
    
        $html = array();
        $html[] = $content;
        $html[] = $this->wbuilder->gethtml();
        
        echo implode('', $html);  
        //$htmlstr = implode('', $html);
        //return $htmlstr;
    }
    
//to do
// update widget
/*function update($new_instance, $old_instance) {
      $instance = $old_instance;
      // Fields
      $instance['title'] = strip_tags($new_instance['title']);
      $instance['text'] = strip_tags($new_instance['text']);
      $instance['textarea'] = strip_tags($new_instance['textarea']);
      $instance['checkbox'] = strip_tags($new_instance['checkbox']);
     return $instance;
}*/
    
    public function form($instance)
    {
        $default_settings = array(
            'title' => 'Mapbox for WP Advanced',
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
            'maplegendposition' =>'default',
            'maplegendcontent' => '',
            'maplegendcontentcss' => '',
            'maplayerscontrolposition' => '',
            'maptitle' => '',
            'mapdescription' => '',
            'mapuselocation' => 'default',
            'mapgeolocatemaxradiuskm' => '',
            'mapcenteronlocation' => 'default',
            'mapcirclearoundlocation' => 'default',
            'mapmaxradiuskm' => '', 
            'mapfittomarkers' =>'default',  
            'maplayersids' =>'',
            'maplayersurls' => '',
            'mapwmslayers' => '',
            'mapwmslayersdefault' => '',
            //'maplayerscode' => '',   
            'mapfeaturesids' =>'',
            'mapfeaturesurls' => '',
            'mapfeaturesurlstimeout' => '',
            'mapfeaturescode' => ''
        );

        $instance = wp_parse_args(
            (array) $instance,
            $default_settings
        );

        //Specific widget params
        $title = $instance['title'];
        
        $mapid = $instance['mapid'];
        $mapsuffix = $instance['mapsuffix'];
        $mapboxid = $instance['mapboxid'];
        $mapwidth = $instance['mapwidth'];
        $mapheight = $instance['mapheight'];  
        $mapstaticmap = $instance['mapstaticmap'];  
        $mappadding = $instance['mappadding'];
        $mappaddingtlx = $instance['mappaddingtlx'];
        $mappaddingtly = $instance['mappaddingtly'];
        $mappaddingbrx = $instance['mappaddingbrx'];
        $mappaddingbry = $instance['mappaddingbry'];
        $mapzoomcontrol = $instance['mapzoomcontrol'];
        $mapzoomcontrolposition = $instance['mapzoomcontrolposition'];
        $mapcenterlat = $instance['mapcenterlat'];
        $mapcenterlng = $instance['mapcenterlng'];
        $mapzoom = $instance['mapzoom'];
        $mapminzoom = $instance['mapminzoom'];
        $mapmaxzoom = $instance['mapmaxzoom'];   
        $mapmaxboundsswlat = $instance['mapmaxboundsswlat'];
        $mapmaxboundsswlng = $instance['mapmaxboundsswlng'];
        $mapmaxboundsnelat = $instance['mapmaxboundsnelat'];
        $mapmaxboundsnelng = $instance['mapmaxboundsnelng'];   
        $mapfullscreencontrol = $instance['mapfullscreencontrol'];
        $mapfullscreencontrolposition = $instance['mapfullscreencontrolposition'];
        $mapsharecontrol = $instance['mapsharecontrol'];
        $mapsharecontrolposition = $instance['mapsharecontrolposition'];
        $mapgeocodercontrol = $instance['mapgeocodercontrol'];
        $mapgeocodercontrolposition = $instance['mapgeocodercontrolposition'];
        $mapgeocodercontrolautoc = $instance['mapgeocodercontrolautoc'];
        $mapgeocoderradiuskm = $instance['mapgeocoderradiuskm'];
        $maplegendposition = $instance['maplegendposition'];
        $maplegendbackgroundcolor = $instance['maplegendbackgroundcolor'];
        $maplegendcontent= $instance['maplegendcontent'];
        $maplegendcontentcss= $instance['maplegendcontentcss'];   
        $maplayerscontrolposition = $instance['maplayerscontrolposition'];
        $maptitle=$instance['maptitle'];
        $mapdescription=$instance['mapdescription'];
        $mapuselocation= $instance['mapuselocation']; 
        $mapgeolocatemaxradiuskm= $instance['mapgeolocatemaxradiuskm'];
        $mapcenteronlocation= $instance['mapcenteronlocation']; 
        $mapcirclearoundlocation= $instance['mapcirclearoundlocation']; 
        $mapmaxradiuskm= $instance['mapmaxradiuskm']; 
        $mapfittomarkers= $instance['mapfittomarkers'];
        
        $maplayersids = $instance['maplayersids'];
        $maplayersurls = $instance['maplayersurls'];
        //$mapwmslayersbaseurl = $instance['mapwmslayersbaseurl'];
        //$mapwmslayersurls = $instance['mapwmslayersurls'];
        $mapwmslayers = $instance['mapwmslayers'];
        $mapwmslayersdefault = $instance['mapwmslayersdefault'];
        //$mapwmslayersopacity = $instance['mapwmslayersopacity'];
        
        //$maplayerscode = $instance['maplayerscode'];
        $mapfeaturesids = $instance['mapfeaturesids'];
        $mapfeaturesurls = $instance['mapfeaturesurls'];
        $mapfeaturesurlstimeout = $instance['mapfeaturesurlstimeout'];
        $mapfeaturescode = $instance['mapfeaturescode'];
        ?>

        <!--title-->
        <style>.spacer {font-weight:bold; margin-top:2em;}</style>
        <span class="spacer"><?php _e( 'General', 'mapboxadv' ); ?></span><hr/>
        <p>
        <label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title','mapboxadv' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo  $title; ?>" />
        </p>
        <!------------------------------
        Map
        ------------------------------->
        <!--map id-->
        <p>
        <label for="<?php echo $this->get_field_name( 'mapid' ); ?>"><?php _e( 'Map Id' ,'mapboxadv'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'mapid' ); ?>" name="<?php echo $this->get_field_name( 'mapid' ); ?>" type="text" value="<?php echo  $mapid; ?>" />
        </p>
        <!--map suffix-->
        <p>
        <label for="<?php echo $this->get_field_name( 'mapid' ); ?>"><?php _e( 'Map suffix','mapboxadv'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'mapsuffix' ); ?>" name="<?php echo $this->get_field_name( 'mapsuffix' ); ?>" type="text" value="<?php echo  $mapsuffix; ?>" />
        </p>
        <!--map width-->
        <p>
        <label for="<?php echo $this->get_field_name( 'mapwidth' ); ?>"><?php _e( 'Map width','mapboxadv'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'mapwidth' ); ?>" name="<?php echo $this->get_field_name( 'mapwidth' ); ?>" type="text" value="<?php echo  $mapwidth; ?>" />
        </p>
        <!--map height-->
        <p>
        <label for="<?php echo $this->get_field_name( 'mapheight' ); ?>"><?php _e( 'Map height','mapboxadv' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'mapheight' ); ?>" name="<?php echo $this->get_field_name( 'mapheight' ); ?>" type="text" value="<?php echo  $mapheight; ?>" />
        </p>
         <!-- map fit to markers -->
        <p>
        <label for="<?php echo $this->get_field_name( 'mapfittomarkers' ); ?>"><?php _e( 'Fit map to markers','mapboxadv' ); ?></label>
        <select id="<?php echo $this->get_field_id( 'mapfittomarkers' ); ?>" name="<?php echo $this->get_field_name( 'mapfittomarkers' ); ?>">
        <option <?php selected($instance['mapfittomarkers'], 'default'); ?> value="default">Default</option>
        <option <?php selected($instance['mapfittomarkers'], 'false'); ?> value="false">No</option>
        <option <?php selected($instance['mapfittomarkers'], 'true'); ?> value="true">Yes</option>
        </select>
            
        </p>
        <!-- map static -->
        <p>
        <label for="<?php echo $this->get_field_name( 'mapstaticmap' ); ?>"><?php _e( 'Static map','mapboxadv' ); ?></label>  
        <select id="<?php echo $this->get_field_id( 'mapstaticmap' ); ?>" name="<?php echo $this->get_field_name( 'mapstaticmap' ); ?>">
        <option <?php selected($instance['mapstaticmap'], 'default'); ?> value="default">Default</option>
        <option <?php selected($instance['mapstaticmap'], 'false'); ?> value="false">No</option>
        <option <?php selected($instance['mapstaticmap'], 'true'); ?> value="true">Yes</option>
        </select>
            
        </p>
         <!------------------------------
        Paddings
        ------------------------------->
        <span class="spacer"><?php _e( 'Paddings', 'mapboxadv' ); ?></span><hr/>
        <!-- map padding -->
        <p>
        <label for="<?php echo $this->get_field_name( 'mappadding' ); ?>"><?php _e( 'Map padding','mapboxadv' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'mappadding' ); ?>" name="<?php echo $this->get_field_name( 'mappadding' ); ?>" type="text" value="<?php echo  $mappadding; ?>" />
        </p>
        <!-- map padding tlx-->
        <p>
        <label for="<?php echo $this->get_field_name( 'mappaddingtlx' ); ?>"><?php _e( 'Map padding (Top Left X)','mapboxadv' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'mappaddingtlx' ); ?>" name="<?php echo $this->get_field_name( 'mappaddingtlx' ); ?>" type="text" value="<?php echo  $mappaddingtlx; ?>" />
        </p>
        <!-- map padding tly-->
        <p>
        <label for="<?php echo $this->get_field_name( 'mappaddingtly' ); ?>"><?php _e( 'Map padding (Top Left Y)','mapboxadv' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'mappaddingtly' ); ?>" name="<?php echo $this->get_field_name( 'mappaddingtly' ); ?>" type="text" value="<?php echo  $mappaddingtly; ?>" />
        </p>
         <!-- map padding brx-->
        <p>
        <label for="<?php echo $this->get_field_name( 'mappaddingbrx' ); ?>"><?php _e( 'Map padding (Bottom right X)','mapboxadv' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'mappaddingbrx' ); ?>" name="<?php echo $this->get_field_name( 'mappaddingbrx' ); ?>" type="text" value="<?php echo  $mappaddingbrx; ?>" />
        </p>
         <!-- map padding bry-->
        <p>
        <label for="<?php echo $this->get_field_name( 'mappaddingbry' ); ?>"><?php _e( 'Map padding (Bottom Right Y)','mapboxadv' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'mappaddingbry' ); ?>" name="<?php echo $this->get_field_name( 'mappaddingbry' ); ?>" type="text" value="<?php echo  $mappaddingbry; ?>" />
        </p>
         <!------------------------------
        Center
        ------------------------------->
        <span class="spacer"><?php _e( 'Center', 'mapboxadv' ); ?></span><hr/>
        <!-- map center lat -->
        <p>
        <label for="<?php echo $this->get_field_name( 'mapcenterlat' ); ?>"><?php _e( 'Map center (Latittude)','mapboxadv' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'mapcenterlat' ); ?>" name="<?php echo $this->get_field_name( 'mapcenterlat' ); ?>" type="text" value="<?php echo  $mapcenterlat; ?>" />
        </p>
        <!-- map center lng-->
        <p>
        <label for="<?php echo $this->get_field_name( 'mapcenterlng' ); ?>"><?php _e( 'Map center (Longitude)','mapboxadv' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'mapcenterlng' ); ?>" name="<?php echo $this->get_field_name( 'mapcenterlng' ); ?>" type="text" value="<?php echo  $mapcenterlng; ?>" />
        </p>
        <!-- map max zoom-->
        <p>
         <!------------------------------
        Zoom
        ------------------------------->
        <span class="spacer"><?php _e( 'Zoom levels', 'mapboxadv' ); ?></span><hr/>
        <!-- map zoom -->
        <p>
        <label for="<?php echo $this->get_field_name( 'mapzoom' ); ?>"><?php _e( 'Map zoom level','mapboxadv' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'mapzoom' ); ?>" name="<?php echo $this->get_field_name( 'mapzoom' ); ?>" type="text" value="<?php echo  $mapzoom; ?>" />
        </p>
        <!-- map min zoom-->
        <p>
        <label for="<?php echo $this->get_field_name( 'mapminzoom' ); ?>"><?php _e( 'Minimal zoom level','mapboxadv' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'mapminzoom' ); ?>" name="<?php echo $this->get_field_name( 'mapminzoom' ); ?>" type="text" value="<?php echo  $mapminzoom; ?>" />
        </p>
        <!-- map max zoom-->
        <p>
        <label for="<?php echo $this->get_field_name( 'mapmaxzoom' ); ?>"><?php _e( 'Maximal zoom level','mapboxadv' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'mapmaxzoom' ); ?>" name="<?php echo $this->get_field_name( 'mapmaxzoom'); ?>" type="text" value="<?php echo  $mapmaxzoom; ?>" />
        </p>
        <span class="spacer"><?php _e( 'Bounds' ); ?></span><hr/>
        <!-- map bounds-->
        <p>
        <label for="<?php echo $this->get_field_name( 'mapmaxboundsswlat' ); ?>"><?php _e( 'Map bounds (SW latitude)','mapboxadv' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'mapmaxboundsswlat' ); ?>" name="<?php echo $this->get_field_name( 'mapmaxboundsswlat' ); ?>" type="text" value="<?php echo  $mapmaxboundsswlat; ?>" />
        </p>
        <!-- map bounds-->
        <p>
        <label for="<?php echo $this->get_field_name( 'mapmaxboundsswlng' ); ?>"><?php _e( 'Map bounds (SW longitude)','mapboxadv' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'mapmaxboundsswlng' ); ?>" name="<?php echo $this->get_field_name( 'mapmaxboundsswlng' ); ?>" type="text" value="<?php echo  $mapmaxboundsswlng; ?>" />
        </p>
        <!-- map bounds-->
        <p>
        <label for="<?php echo $this->get_field_name( 'mapmaxboundsnelat' ); ?>"><?php _e( 'Map bounds (NE latitude)','mapboxadv' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'mapmaxboundsnelat' ); ?>" name="<?php echo $this->get_field_name( 'mapmaxboundsnelat' ); ?>" type="text" value="<?php echo  $mapmaxboundsnelat; ?>" />
        </p>
        <!-- map bounds-->
        <p>
        <label for="<?php echo $this->get_field_name( 'mapmaxboundsnelng' ); ?>"><?php _e( 'Map bounds (NE longitude)','mapboxadv' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'mapmaxboundsnelng' ); ?>" name="<?php echo $this->get_field_name( 'mapmaxboundsnelng' ); ?>" type="text" value="<?php echo  $mapmaxboundsnelng; ?>" />
        </p>
        <!------------------------------
        Controls
        ------------------------------->
        <span class="spacer"><?php _e( 'Controls', 'mapboxadv' ); ?></span><hr/>
        <!-- map zoom control -->
        <p>
        <label for="<?php echo $this->get_field_name( 'mapzoomcontrol' ); ?>"><?php _e( 'Add zoom control','mapboxadv' ); ?></label>          
        <select id="<?php echo $this->get_field_id( 'mapzoomcontrol' ); ?>" name="<?php echo $this->get_field_name( 'mapzoomcontrol' ); ?>">
        <option <?php selected($instance['mapzoomcontrol'], 'default'); ?> value="default"><?php _e('Default', 'mapboxadv');?></option>
        <option <?php selected($instance['mapzoomcontrol'], 'false'); ?> value="false"><?php _e('No', 'mapboxadv');?></option>
        <option <?php selected($instance['mapzoomcontrol'], 'true'); ?> value="true"><?php _e('Yes', 'mapboxadv');?></option>
        </select>
        </p>
        <!-- map zoom position -->
        <p>
        <label for="<?php echo $this->get_field_id( 'mapzoomcontrolposition' ); ?> "><?php _e('Map zoom control position','mapboxadv'); ?></label>
        <select id="<?php echo $this->get_field_id( 'mapzoomcontrolposition' ); ?>" name="<?php echo $this->get_field_name( 'mapzoomcontrolposition' ); ?>">
        <option <?php selected($instance['mapzoomcontrolposition'], 'default'); ?> value="default"><?php _e('Default', 'mapboxadv');?></option>
        <option <?php selected($instance['mapzoomcontrolposition'], 'topleft'); ?> value="topleft"><?php _e('Top left', 'mapboxadv');?></option>
        <option <?php selected($instance['mapzoomcontrolposition'], 'topright'); ?> value="topright"><?php _e('Top right', 'mapboxadv');?></option>
        <option <?php selected($instance['mapzoomcontrolposition'], 'bottomleft'); ?> value="bottomleft"><?php _e('Bottom left', 'mapboxadv');?></option>
        <option <?php selected($instance['mapzoomcontrolposition'], 'bottomright'); ?> value="bottomright"><?php _e('Bottom right', 'mapboxadv');?></option>
        </select>
        </p>
         <!-- map fullscreen control -->
        <p>
        <label for="<?php echo $this->get_field_name( 'mapfullscreencontrol' ); ?>"><?php _e( 'Add fullscreen control','mapboxadv' ); ?></label>          
        <select id="<?php echo $this->get_field_id( 'mapfullscreencontrol' ); ?>" name="<?php echo $this->get_field_name( 'mapfullscreencontrol' ); ?>">
        <option <?php selected($instance['mapfullscreencontrol'], 'default'); ?> value="default"><?php _e('Default', 'mapboxadv');?></option>
        <option <?php selected($instance['mapfullscreencontrol'], 'false'); ?> value="false"><?php _e('No', 'mapboxadv');?></option>
        <option <?php selected($instance['mapfullscreencontrol'], 'true'); ?> value="true"><?php _e('Yes', 'mapboxadv');?></option>
        </select>
        </p>
        <!-- map fullscreen position -->
        <p>
        <label for="<?php echo $this->get_field_id( 'mapfullscreencontrolposition' ); ?> "><?php _e('Fullscreen control position','mapboxadv'); ?></label>
        <select id="<?php echo $this->get_field_id( 'mapfullscreencontrolposition' ); ?>" name="<?php echo $this->get_field_name( 'mapfullscreencontrolposition' ); ?>">
        <option <?php selected($instance['mapfullscreencontrolposition'], 'default'); ?> value="default"><?php _e('Default', 'mapboxadv');?></option>
        <option <?php selected($instance['mapfullscreencontrolposition'], 'topleft'); ?> value="topleft"><?php _e('Top left', 'mapboxadv');?></option>
        <option <?php selected($instance['mapfullscreencontrolposition'], 'topright'); ?> value="topright"><?php _e('Top right', 'mapboxadv');?></option>
        <option <?php selected($instance['mapfullscreencontrolposition'], 'bottomleft'); ?> value="bottomleft"><?php _e('Bottom left', 'mapboxadv');?></option>
        <option <?php selected($instance['mapfullscreencontrolposition'], 'bottomright'); ?> value="bottomright"><?php _e('Bottom right', 'mapboxadv');?></option>
        </select>
        </p>
        <!-- map share control -->
        <p>
        <label for="<?php echo $this->get_field_name( 'mapsharecontrol' ); ?>"><?php _e( 'Add share control','mapboxadv' ); ?></label>          
        <select id="<?php echo $this->get_field_id( 'mapsharecontrol' ); ?>" name="<?php echo $this->get_field_name( 'mapsharecontrol' ); ?>">
        <option <?php selected($instance['mapsharecontrol'], 'default'); ?> value="default"><?php _e('Default', 'mapboxadv');?></option>
        <option <?php selected($instance['mapsharecontrol'], 'false'); ?> value="false"><?php _e('No', 'mapboxadv');?></option>
        <option <?php selected($instance['mapsharecontrol'], 'true'); ?> value="true"><?php _e('Yes', 'mapboxadv');?></option>
        </select>
        </p>
        <!-- map share control position -->
        <p>
        <label for="<?php echo $this->get_field_id( 'mapsharecontrolposition' ); ?> "><?php _e('Share control position','mapboxadv'); ?></label>
        <select id="<?php echo $this->get_field_id( 'mapsharecontrolposition' ); ?>" name="<?php echo $this->get_field_name( 'mapsharecontrolposition' ); ?>">
        <option <?php selected($instance['mapsharecontrolposition'], 'default'); ?> value="default"><?php _e('Default', 'mapboxadv');?></option>
        <option <?php selected($instance['mapsharecontrolposition'], 'topleft'); ?> value="topleft"><?php _e('Top left', 'mapboxadv');?></option>
        <option <?php selected($instance['mapsharecontrolposition'], 'topright'); ?> value="topright"><?php _e('Top right', 'mapboxadv');?></option>
        <option <?php selected($instance['mapsharecontrolposition'], 'bottomleft'); ?> value="bottomleft"><?php _e('Bottom left', 'mapboxadv');?></option>
        <option <?php selected($instance['mapsharecontrolposition'], 'bottomright'); ?> value="bottomright"><?php _e('Bottom right', 'mapboxadv');?></option>
        </select>
        </p>
        <!-- map geocoder control -->
        <p>
        <label for="<?php echo $this->get_field_name( 'mapgeocodercontrol' ); ?>"><?php _e( 'Add geocoder control','mapboxadv' ); ?></label>  
          <select id="<?php echo $this->get_field_id( 'mapgeocodercontrol' ); ?>" name="<?php echo $this->get_field_name( 'mapgeocodercontrol' ); ?>">
        <option <?php selected($instance['mapgeocodercontrol'], 'default'); ?> value="default"><?php _e('Default', 'mapboxadv');?></option>
        <option <?php selected($instance['mapgeocodercontrol'], 'false'); ?> value="false"><?php _e('No', 'mapboxadv');?></option>
        <option <?php selected($instance['mapgeocodercontrol'], 'true'); ?> value="true"><?php _e('Yes', 'mapboxadv');?></option>
        </select>     
        </p>
        <!-- map geocoder control auto complete -->
        <p>
        <label for="<?php echo $this->get_field_name( 'mapgeocodercontrolautoc' ); ?>"><?php _e( 'Geocoder control auto completion','mapboxadv' ); ?></label>  
          <select id="<?php echo $this->get_field_id( 'mapgeocodercontrolautoc' ); ?>" name="<?php echo $this->get_field_name( 'mapgeocodercontrolautoc' ); ?>">
        <option <?php selected($instance['mapgeocodercontrolautoc'], 'default'); ?> value="default"><?php _e('Default', 'mapboxadv');?></option>
        <option <?php selected($instance['mapgeocodercontrolautoc'], 'false'); ?> value="false"><?php _e('No', 'mapboxadv');?></option>
        <option <?php selected($instance['mapgeocodercontrolautoc'], 'true'); ?> value="true"><?php _e('Yes', 'mapboxadv');?></option>
        </select>       
        </p>
        <!-- map geocoder radius km -->
        <p>
        <label for="<?php echo $this->get_field_name( 'mapgeocoderradiuskm' ); ?>"><?php _e( 'Max. radius around search (km)','mapboxadv' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'mapgeocoderradiuskm' ); ?>" name="<?php echo $this->get_field_name( 'mapgeocoderradiuskm' ); ?>" type="text" value="<?php echo  $mapgeocoderradiuskm; ?>" />
        </p>
        <!-- map geocoder control position -->
         <p>
        <label for="<?php echo $this->get_field_id( 'mapgeocodercontrolposition' ); ?> "><?php _e('Map geocoder control position','mapboxadv'); ?></label>
        <select id="<?php echo $this->get_field_id( 'mapgeocodercontrolposition' ); ?>" name="<?php echo $this->get_field_name( 'mapgeocodercontrolposition' ); ?>">
        <option <?php selected($instance['mapgeocodercontrolposition'], 'default'); ?> value="default"><?php _e('Default', 'mapboxadv');?></option>
        <option <?php selected($instance['mapgeocodercontrolposition'], 'topleft'); ?> value="topleft"><?php _e('Top left', 'mapboxadv');?></option>
        <option <?php selected($instance['mapgeocodercontrolposition'], 'topright'); ?> value="topright"><?php _e('Top right', 'mapboxadv');?></option>
        <option <?php selected($instance['mapgeocodercontrolposition'], 'bottomleft'); ?> value="bottomleft"><?php _e('Bottom left', 'mapboxadv');?></option>
        <option <?php selected($instance['mapgeocodercontrolposition'], 'bottomright'); ?> value="bottomright"><?php _e('Bottom right', 'mapboxadv');?></option>
        </select>
        </p>
        <!-- map title -->
        <p>
        <label for="<?php echo $this->get_field_id( 'maptitle' ); ?>"><?php _e( 'Map title','mapboxadv' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('maptitle'); ?>" name="<?php echo $this->get_field_name('maptitle'); ?>" value="<?php echo $maptitle; ?>"/>
        <!-- map description -->
        <p>
        <label for="<?php echo $this->get_field_id( 'mapdescription' ); ?>"><?php _e( 'Map description','mapboxadv' ); ?></label>
        <textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('mapdescription'); ?>" name="<?php echo $this->get_field_name('mapdescription'); ?>"><?php echo $mapdescription; ?></textarea>
         <!-- legend content 
        <p>
        <label for="<?php echo $this->get_field_id( 'maplegendcontent' ); ?>"><?php _e( 'Legend content','mapboxadv'); ?></label>

         <?php $content =$maplegendcontent;
            $editor_id = $this->get_field_id( 'maplegendcontent' );
            $settings = array('textarea_name' => $this->get_field_name('maplegendcontent'));
            wp_editor( stripslashes($content), $editor_id, $settings );?>

        </p>
         <!-- legend css -->
         <p>
        <label for="<?php echo $this->get_field_id( 'maplegendcontentcss' ); ?>"><?php _e( 'Legend content style','mapboxadv'); ?></label>
        <textarea class="widefat" rows="8" cols="20" id="<?php echo $this->get_field_id('maplegendcontentcss'); ?>" name="<?php echo $this->get_field_name('maplegendcontentcss'); ?>"><?php echo $maplegendcontentcss; ?></textarea>
        </p>
         <!-- legend background color -->
         <p>
        <label for="<?php echo $this->get_field_name( 'maplegendbackgroundcolor' ); ?>"><?php _e( 'Legend background color','mapboxadv' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'maplegendbackgroundcolor' ); ?>" name="<?php echo $this->get_field_name( 'maplegendbackgroundcolor' ); ?>" type="text" value="<?php echo  $maplegendbackgroundcolor; ?>" />
        </p>
         <!-- legend position -->
         <p>
        <label for="<?php echo $this->get_field_id( 'maplegendposition' ); ?> "><?php _e('Legend position','mapboxadv'); ?></label>
        <select id="<?php echo $this->get_field_id( 'maplegendposition' ); ?>" name="<?php echo $this->get_field_name( 'maplegendposition' ); ?>">
        <option <?php selected($instance['maplegendposition'], 'default'); ?> value="default"><?php _e('Default', 'mapboxadv');?></option>
        <option <?php selected($instance['maplegendposition'], 'topleft'); ?> value="topleft"><?php _e('Top left', 'mapboxadv');?></option>
        <option <?php selected($instance['maplegendposition'], 'topright'); ?> value="topright"><?php _e('Top right', 'mapboxadv');?></option>
        <option <?php selected($instance['maplegendposition'], 'bottomleft'); ?> value="bottomleft"><?php _e('Bottom left', 'mapboxadv');?></option>
        <option <?php selected($instance['maplegendposition'], 'bottomright'); ?> value="bottomright"><?php _e('Bottom right', 'mapboxadv');?></option>
        </select>
        </p>
         <p>
        <label for="<?php echo $this->get_field_name( 'maplayerscontrolposition' ); ?>"><?php _e( 'Layers control position','mapboxadv' ); ?></label>
        <select id="<?php echo $this->get_field_id( 'maplayerscontrolposition' ); ?>" name="<?php echo $this->get_field_name( 'maplayerscontrolposition' ); ?>">
        <option <?php selected($instance['maplayerscontrolposition'], 'default'); ?> value="default"><?php _e('Default', 'mapboxadv');?></option>
        <option <?php selected($instance['maplayerscontrolposition'], 'topleft'); ?> value="topleft"><?php _e('Top left', 'mapboxadv');?></option>
        <option <?php selected($instance['maplayerscontrolposition'], 'topright'); ?> value="topright"><?php _e('Top right', 'mapboxadv');?></option>
        <option <?php selected($instance['maplayerscontrolposition'], 'bottomleft'); ?> value="bottomleft"><?php _e('Bottom left', 'mapboxadv');?></option>
        <option <?php selected($instance['maplayerscontrolposition'], 'bottomright'); ?> value="bottomright"><?php _e('Bottom right', 'mapboxadv');?></option>
        </select> 
        </p>

<!---------------------------------------------------------------------------------
Premium zone
----------------------------------------------------------------------------------->
        <?php if (mapboxadv_isPremium()) { ?>

        <!------------------------------
        Layers
        ------------------------------->
        <span class="spacer"><?php _e( 'Layers','mapboxadv'); ?></span><hr/>
        <p>
        <label for="<?php echo $this->get_field_name( 'maplayersids' ); ?>"><?php _e( 'Tile layer(s) id(s)','mapboxadv' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'maplayersids' ); ?>" name="<?php echo $this->get_field_name( 'maplayersids' ); ?>" type="text" value="<?php echo  $maplayersids; ?>" />
        </p>
         <p>
        <label for="<?php echo $this->get_field_name( 'maplayersurls' ); ?>"><?php _e( 'Tile layer(s) url(s)','mapboxadv' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'maplayersurls' ); ?>" name="<?php echo $this->get_field_name( 'maplayersurls' ); ?>" type="text" value="<?php echo  $maplayersurls; ?>" />
        </p>
        <p>
        <label for="<?php echo $this->get_field_name( 'mapwmslayers' ); ?>"><?php _e( 'WMS layer(s)','mapboxadv' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('mapwmslayers'); ?>" name="<?php echo $this->get_field_name('mapwmslayers'); ?>" type="text" value="<?php echo $mapwmslayers; ?>" ></input>
        </p>
        <p>
        <label for="<?php echo $this->get_field_name( 'mapwmslayersdefault' ); ?>"><?php _e( 'WMS default layer','mapboxadv' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('mapwmslayersdefault'); ?>" name="<?php echo $this->get_field_name('mapwmslayersdefault'); ?>" type="text" value="<?php echo $mapwmslayersdefault; ?>"></input>
        </p>
       
         <!--<p>
        <label for="<php echo $this->get_field_name( 'maplayerscode' ); ?>"><php _e( 'Layer(s) code' ); ?></label>
        <textarea class="widefat" rows="8" cols="20" id="<php echo $this->get_field_id('maplayerscode'); ?>" name="<php echo $this->get_field_name('maplayerscode'); ?>"><php echo $maplayerscode; ?></textarea>-->
        </p>  

        <!------------------------------
        Features
        ------------------------------->
        <span class="spacer"><?php _e( 'Features', 'mapboxadv'); ?></span><hr/>
        <p>
        <label for="<?php echo $this->get_field_name( 'mapfeaturesids' ); ?>"><?php _e( 'Feature(s) id(s)','mapboxadv' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'mapfeaturesids' ); ?>" name="<?php echo $this->get_field_name( 'mapfeaturesids' ); ?>" type="text" value="<?php echo  $mapfeaturesids; ?>" />
        </p>
         <p>
        <label for="<?php echo $this->get_field_name( 'mapfeaturesurls' ); ?>"><?php _e( 'Feature(s) url(s)','mapboxadv' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'mapfeaturesurls' ); ?>" name="<?php echo $this->get_field_name( 'mapfeaturesurls' ); ?>" type="text" value="<?php echo  $mapfeaturesurls; ?>" />
        </p>
         <p>
        <label for="<?php echo $this->get_field_name( 'mapfeaturesurlstimeout' ); ?>"><?php _e( 'Feature(s) url(s) timeout (ms)','mapboxadv' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'mapfeaturesurlstimeout' ); ?>" name="<?php echo $this->get_field_name( 'mapfeaturesurlstimeout' ); ?>" type="text" value="<?php echo  $mapfeaturesurlstimeout; ?>" />
        </p>
         <p>
        <label for="<?php echo $this->get_field_name( 'mapfeaturescode' ); ?>"><?php _e( 'Feature(s) code','mapboxadv' ); ?></label>
        <textarea class="widefat" rows="8" cols="20" id="<?php echo $this->get_field_id('mapfeaturescode'); ?>" name="<?php echo $this->get_field_name('mapfeaturescode'); ?>"><?php echo $mapfeaturescode; ?></textarea>
        </p>
      
        <!------------------------------
        Geolocation
        ------------------------------->
        <span class="spacer"><?php _e( 'Geolocation', 'mapboxadv' ); ?></span><hr/>
         <!-- geolocate -->
        <p>
        <label for="<?php echo $this->get_field_name( 'mapuselocation' ); ?>"><?php _e( 'Geolocate the visitor ','mapboxadv' ); ?></label>  
        <select id="<?php echo $this->get_field_id( 'mapuselocation' ); ?>" name="<?php echo $this->get_field_name( 'mapuselocation' ); ?>">
        <option <?php selected($instance['mapuselocation'], 'default'); ?> value="default">Default</option>
        <option <?php selected($instance['mapuselocation'], 'false'); ?> value="false">No</option>
        <option <?php selected($instance['mapuselocation'], 'true'); ?> value="true">Yes</option>
        </select>
        </p>
        <!-- map max radius km -->
        <p>
        <label for="<?php echo $this->get_field_name( 'mapgeolocatemaxradiuskm' ); ?>"><?php _e( 'Max. markers distance from geolocation (km)','mapboxadv' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'mapgeolocatemaxradiuskm' ); ?>" name="<?php echo $this->get_field_name( 'mapgeolocatemaxradiuskm' ); ?>" type="text" value="<?php echo  $mapgeolocatemaxradiuskm; ?>" />
        </p>
        <!-- map max radius km -->
        <p>
        <label for="<?php echo $this->get_field_name( 'mapmaxradiuskm' ); ?>"><?php _e( 'Max. radius around geolocation (km)','mapboxadv' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'mapmaxradiuskm' ); ?>" name="<?php echo $this->get_field_name( 'mapmaxradiuskm' ); ?>" type="text" value="<?php echo  $mapmaxradiuskm; ?>" />
        </p>
         <!-- center on click -->
        <p>
        <label for="<?php echo $this->get_field_name( 'mapcenteronlocation' ); ?>"><?php _e( 'Center on gelocation marker' ,'mapboxadv'); ?></label>  
         <select id="<?php echo $this->get_field_id( 'mapcenteronlocation' ); ?>" name="<?php echo $this->get_field_name( 'mapcenteronlocation' ); ?>">
        <option <?php selected($instance['mapcenteronlocation'], 'default'); ?> value="default">Default</option>
        <option <?php selected($instance['mapcenteronlocation'], 'false'); ?> value="false">No</option>
        <option <?php selected($instance['mapcenteronlocation'], 'true'); ?> value="true">Yes</option>
        </select>
        </p>
         <!-- circle around -->
        <p>
        <label for="<?php echo $this->get_field_name( 'mapcirclearoundlocation' ); ?>"><?php _e( 'Circle around gelocation marker','mapboxadv' ); ?></label>  
         <select id="<?php echo $this->get_field_id( 'mapcirclearoundlocation' ); ?>" name="<?php echo $this->get_field_name( 'mapcirclearoundlocation' ); ?>">
        <option <?php selected($instance['mapcirclearoundlocation'], 'default'); ?> value="default">Default</option>
        <option <?php selected($instance['mapcirclearoundlocation'], 'false'); ?> value="false">No</option>
        <option <?php selected($instance['mapcirclearoundlocation'], 'true'); ?> value="true">Yes</option>
        </select>
        </p>

<!---------------------------------------------------------------------------------
End Premium zone
----------------------------------------------------------------------------------->
        <?php }?>
        <?php
    }
}
?>