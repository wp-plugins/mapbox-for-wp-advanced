<?php
/*
Plugin Name: Mapbox for WP Advanced
Plugin URI: http://mapbox-for-wordpress.stephanemartinw.com
Description:
Version: 1.0.0
Author: Stephane Martin
Author URI: http://www.stephanemartinw.com
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: mapboxadv
*/

include_once plugin_dir_path( __FILE__ ).'/mapboxadvhelper.php';
include_once plugin_dir_path( __FILE__ ).'/default.php';

include_once plugin_dir_path( __FILE__ ).'/mapboxwidgetadv.php';
include_once plugin_dir_path( __FILE__ ).'/mapboxmaps.php';

if (mapboxadv_isPremium()) {
    include_once plugin_dir_path( __FILE__ ).'/mapboxwmslayers.php';
}

class Mapboxadv
{
    public static function minify($css) {
        /* remove comments */
        $css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
        /* remove tabs, spaces, newlines, etc. */
        $css = str_replace( array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $css);
        return trim( $css );
    }

    public function __construct()
    {
        load_plugin_textdomain('mapboxadv', false, basename( dirname( __FILE__ ) ) . '/languages' );

        add_action('widgets_init', function(){register_widget('Mapboxadv_Widget');});
        add_action('admin_menu', array($this, 'add_admin_menu'),20);

        add_action('admin_init', array($this, 'register_settings'));

        //test meta boxes
        //add_action( 'add_meta_boxes', 'dynamic_add_custom_box' );
        include_once plugin_dir_path( __FILE__ ).'shortcut.php';
        new Mapboxadv_Shorcut();
    }

    public function init_plugin_sources()
    {
         //general css
        //can be completed below by inline css
        wp_register_style('mapboxadvcss', plugins_url('/assets/css/mapboxadv-min.css', __FILE__) );
        wp_enqueue_style('mapboxadvcss');

        //legend
        $defaultmaplegendcontentcss=get_option('mapbox_default_maplegendcontentcss', '');
        wp_add_inline_style( 'mapboxadvcss', Mapboxadv::minify($defaultmaplegendcontentcss));
    }


    public function add_admin_menu()
    {
        add_menu_page(__('Mapbox for WP Advanced','mapboxadv'), __('Mapbox for WP Advanced','mapboxadv'), 'manage_options', 'mapboxadv', array($this, 'menu_general_html') );
        add_submenu_page('mapboxadv', __('Mapbox for WP Advanced','mapboxadv'), __('General','mapboxadv'), 'manage_options', 'mapboxadv', array($this, 'menu_general_html') );
        add_submenu_page('mapboxadv', __('Mapbox for WP Advanced','mapboxadv'), __('Maps','mapboxadv'),  'activate_plugins', 'mapboxadv-maps', 'maps_page_handler');
        add_submenu_page('mapboxadv-maps', __('Add new','mapboxadv'), __('Add new','mapboxadv'), 'activate_plugins', 'mapboxadv-maps-form', 'maps_form_page_handler');

        //add_submenu_page('mapboxadv', __('Mapbox for WP Advanced'), __('Map'), 'manage_options', 'mapboxadv-map', array($this, 'menu_map_html') );
         //add_submenu_page('mapboxadv', __('Mapbox for WP Advanced'), __('Static map'), 'manage_options', 'mapboxadv-staticmap', array($this, 'menu_staticmap_html') );
        add_submenu_page('mapboxadv', __('Mapbox for WP Advanced','mapboxadv'), __('Controls','mapboxadv'), 'manage_options', 'mapboxadv-controls', array($this, 'menu_controls_html') );

        if (mapboxadv_isPremium()) {

          add_submenu_page('mapboxadv', __('Mapbox for WP Advanced','mapboxadv'), __('Geolocation','mapboxadv'), 'manage_options', 'mapboxadv-geolocation', array($this, 'menu_geolocation_html') );

        add_submenu_page('mapboxadv', __('Mapbox for WP Advanced','mapboxadv'), __('Layers','mapboxadv'), 'manage_options', 'mapboxadv-layers', array($this, 'menu_layers_html') );

        add_submenu_page('mapboxadv', __('Mapbox for WP Advanced','mapboxadv'), __('WMS Layers','mapboxadv'),  'activate_plugins', 'mapboxadv-wmslayers', 'wmslayers_page_handler');
        add_submenu_page('mapboxadv-wmslayers', __('Add new','mapboxadv'), __('Add new','mapboxadv'), 'activate_plugins', 'mapboxadv-wmslayers-form', 'wmslayers_form_page_handler');


        add_submenu_page('mapboxadv', __('Mapbox for WP Advanced','mapboxadv'), __('Features','mapboxadv'), 'manage_options', 'mapboxadv-features', array($this, 'menu_features_html') );

        }
    }

    public function menu_general_html()
    {
        echo '<h1>'.get_admin_page_title().'</h1>';?>

        <form method="post" action="options.php">
            <?php settings_fields('mapbox_general_settings') ?>
            <?php do_settings_sections('mapbox_general_settings') ?>
            <?php submit_button(); ?>
        </form>
    <?php }

    /*public function menu_map_html()
    {
        echo '<h1>'.get_admin_page_title().'</h1>';?>

        <form method="post" action="options.php">
            <?php settings_fields('mapbox_general_settings') ?>
            <?php do_settings_sections('mapbox_general_settings') ?>
            <?php submit_button(); ?>
        </form>
    <?php }*/

    /*public function menu_staticmap_html()
    {
        echo '<h1>'.get_admin_page_title().'</h1>';?>

        <form method="post" action="options.php">
            <?php settings_fields('mapbox_staticmap_settings') ?>
            <?php do_settings_sections('mapbox_staticmap_settings') ?>
            <?php submit_button(); ?>
        </form>
    <?php }*/

    public function menu_controls_html()
    {
        echo '<h1>'.get_admin_page_title().'</h1>';?>

        <form method="post" action="options.php">
            <?php settings_fields('mapbox_controls_settings') ?>
            <?php do_settings_sections('mapbox_controls_settings') ?>
            <?php submit_button(); ?>
        </form>
    <?php }

    public function menu_layers_html()
    {
        echo '<h1>'.get_admin_page_title().'</h1>';?>

        <form method="post" action="options.php">
            <?php settings_fields('mapbox_layers_settings') ?>
            <?php do_settings_sections('mapbox_layers_settings') ?>
            <?php submit_button(); ?>
        </form>
    <?php }

    public function menu_features_html()
    {
        echo '<h1>'.get_admin_page_title().'</h1>';?>

        <form method="post" action="options.php">
            <?php settings_fields('mapbox_features_settings') ?>
            <?php do_settings_sections('mapbox_features_settings') ?>
            <?php submit_button(); ?>
        </form>
    <?php }

     public function menu_geolocation_html()
    {
        echo '<h1>'.get_admin_page_title().'</h1>';?>

        <form method="post" action="options.php">
            <?php settings_fields('mapbox_geolocation_settings') ?>
            <?php do_settings_sections('mapbox_geolocation_settings') ?>
            <?php submit_button(); ?>
        </form>
    <?php }

    public function register_settings()
    {
        //General
        register_setting('mapbox_general_settings', 'mapbox_mapaccesstoken');
        register_setting('mapbox_general_settings', 'mapbox_mapcontinuousworld');
        register_setting('mapbox_general_settings', 'mapbox_mapdisabletouchzoom');
        register_setting('mapbox_general_settings', 'mapbox_mapdisabledrag');
        register_setting('mapbox_general_settings', 'mapbox_mapcenteronmarkerclick');
        register_setting('mapbox_general_settings', 'mapbox_mapclustermode');
        register_setting('mapbox_general_settings', 'mapbox_mapclustercolor');
        //register_setting('mapbox_general_settings', 'mapbox_default_mapid');
        //register_setting('mapbox_general_settings', 'mapbox_default_mapsuffix');
        register_setting('mapbox_general_settings', 'mapbox_default_mapwidth');
        register_setting('mapbox_general_settings', 'mapbox_default_mapheight');
        register_setting('mapbox_general_settings', 'mapbox_default_mappadding');
        register_setting('mapbox_general_settings', 'mapbox_default_mappaddingtlx');
        register_setting('mapbox_general_settings', 'mapbox_default_mappaddingtly');
        register_setting('mapbox_general_settings', 'mapbox_default_mappaddingbrx');
        register_setting('mapbox_general_settings', 'mapbox_default_mappaddingbry');
        register_setting('mapbox_general_settings', 'mapbox_default_mapminzoom');
        register_setting('mapbox_general_settings', 'mapbox_default_mapmaxzoom');
        register_setting('mapbox_general_settings', 'mapbox_default_mapzoom');
        register_setting('mapbox_general_settings', 'mapbox_default_mapmaxboundsswlat');
        register_setting('mapbox_general_settings', 'mapbox_default_mapmaxboundsswlng');
        register_setting('mapbox_general_settings', 'mapbox_default_mapmaxboundsnelat');
        register_setting('mapbox_general_settings', 'mapbox_default_mapmaxboundsnelng');
        register_setting('mapbox_general_settings', 'mapbox_default_mapcenterlat');
        register_setting('mapbox_general_settings', 'mapbox_default_mapcenterlng');
        register_setting('mapbox_general_settings', 'mapbox_default_mapstaticmap');
        register_setting('mapbox_general_settings', 'mapbox_mapstaticmapformat');
        register_setting('mapbox_general_settings', 'mapbox_mapstaticmapformatretina');
        register_setting('mapbox_general_settings', 'mapbox_default_mapfittomarkers');

        //Controls
        register_setting('mapbox_controls_settings', 'mapbox_default_mapzoomcontrol');
        register_setting('mapbox_controls_settings', 'mapbox_default_mapzoomcontrolposition');
        register_setting('mapbox_controls_settings', 'mapbox_default_mapfullscreencontrol');
        register_setting('mapbox_controls_settings', 'mapbox_default_mapfullscreencontrolposition');
        register_setting('mapbox_controls_settings', 'mapbox_default_mapsharecontrol');
        register_setting('mapbox_controls_settings', 'mapbox_default_mapsharecontrolposition');
        register_setting('mapbox_controls_settings', 'mapbox_default_mapgeocodercontrol');
        register_setting('mapbox_controls_settings', 'mapbox_default_mapgeocodercontrolposition');
        register_setting('mapbox_controls_settings', 'mapbox_default_mapgeocodercontrolautoc');
        register_setting('mapbox_controls_settings', 'mapbox_default_mapgeocoderradiuskm');
        register_setting('mapbox_controls_settings', 'mapbox_default_maplegendposition');
        register_setting('mapbox_controls_settings', 'mapbox_default_maplegendbackgroundcolor');
        register_setting('mapbox_controls_settings', 'mapbox_default_maplegendcontentcss');

        register_setting('mapbox_controls_settings', 'mapbox_default_maplayerscontrolposition');

        register_setting('mapbox_controls_settings', 'mapbox_mapattributioncontrol');

        //Layers
        register_setting('mapbox_layers_settings', 'mapbox_default_maplayersids');
        register_setting('mapbox_layers_settings', 'mapbox_default_maplayersurls');
        //register_setting('mapbox_layers_settings', 'mapbox_default_mapwmslayers');
        //register_setting('mapbox_layers_settings', 'mapbox_default_mapwmslayerscontrolposition');
        //register_setting('mapbox_layers_settings', 'mapbox_default_mapwmslayersopacity');
        //register_setting('mapbox_layers_settings', 'mapbox_default_maplayerscode');

        //Features
        register_setting('mapbox_features_settings', 'mapbox_default_mapfeaturesids');
        register_setting('mapbox_features_settings', 'mapbox_default_mapfeaturesurls');
        register_setting('mapbox_features_settings', 'mapbox_default_mapfeaturesurlstimeout');
        register_setting('mapbox_features_settings', 'mapbox_default_mapfeaturescode');

        //Geolocation
        register_setting('mapbox_geolocation_settings', 'mapbox_default_mapuselocation');
        register_setting('mapbox_geolocation_settings', 'mapbox_default_mapgeolocatemaxradiuskm');
        register_setting('mapbox_geolocation_settings', 'mapbox_default_mapmaxradiuskm');
        register_setting('mapbox_geolocation_settings', 'mapbox_maplocatehighaccuracy');
        register_setting('mapbox_geolocation_settings', 'mapbox_mapdisplaylocation');
        register_setting('mapbox_geolocation_settings', 'mapbox_maplocationmarkersize');
        register_setting('mapbox_geolocation_settings', 'mapbox_maplocationmarkersymbol');
        register_setting('mapbox_geolocation_settings', 'mapbox_maplocationmarkercolor');

        register_setting('mapbox_geolocation_settings', 'mapbox_default_mapcenteronlocation');
        register_setting('mapbox_geolocation_settings', 'mapbox_default_mapcirclearoundlocation');

        //General
        add_settings_section('mapbox_general_section', __('General','mapboxadv'), array($this, 'section_general_html'), 'mapbox_general_settings');
        add_settings_field('mapbox_mapaccesstoken', __('Acces Token','mapboxadv'), array($this, 'general_accesstoken_html'), 'mapbox_general_settings', 'mapbox_general_section');
        add_settings_field('mapbox_mapcontinuousworld', __('Continuous world','mapboxadv'), array($this, 'general_continuousworld_html'), 'mapbox_general_settings', 'mapbox_general_section');
        add_settings_field('mapbox_mapdisabletouchzoom', __('Disable touch zoom','mapboxadv'), array($this, 'general_disabletouchzoom_html'), 'mapbox_general_settings', 'mapbox_general_section');
        add_settings_field('mapbox_mapdisabledrag', __('Disable drag','mapboxadv'), array($this, 'general_disabledrag_html'), 'mapbox_general_settings', 'mapbox_general_section');
         add_settings_field('mapbox_mapcenteronmarkerclick', __('Center on marker click','mapboxadv'), array($this, 'general_centeronmarkerclick_html'), 'mapbox_general_settings', 'mapbox_general_section');
        add_settings_field('mapbox_mapclustermode', __('Clusters mode','mapboxadv'), array($this, 'general_clustermode_html'), 'mapbox_general_settings', 'mapbox_general_section');
        add_settings_field('mapbox_mapclustercolor', __('Clusters color','mapboxadv'), array($this, 'general_clustercolor_html'), 'mapbox_general_settings', 'mapbox_general_section');

        //add_settings_section('mapbox_general_section', __('Map','mapboxadv'), array($this, 'section_map_html'), 'mapbox_general_settings');

        //add_settings_field('mapbox_default_mapid', __('Map ID','mapboxadv'), array($this, 'general_id_html'), 'mapbox_general_settings', 'mapbox_general_section');
         //add_settings_field('mapbox_default_suffix', __('Map suffix','mapboxadv'), array($this, 'general_suffix_html'), 'mapbox_general_settings', 'mapbox_general_section');

        add_settings_field('mapbox_default_mapstaticmap', __( 'Static map','mapboxadv' ), array($this, 'general_staticmap_html'), 'mapbox_general_settings', 'mapbox_general_section');
        //add_settings_section('mapbox_staticmap_section', __('Static map','mapboxadv'), array($this, 'section_staticmap_html'), 'mapbox_staticmap_settings');
        add_settings_field('mapbox_mapstaticmapformat', __('Static map format','mapboxadv'), array($this, 'general_staticmapformat_html'), 'mapbox_general_settings', 'mapbox_general_section');
        add_settings_field('mapbox_mapstaticmapformatretina', __('Static map for Retina','mapboxadv'), array($this, 'general_staticmapformatretina_html'), 'mapbox_general_settings', 'mapbox_general_section');


        add_settings_field('mapbox_default_mapwidth', __('Map width','mapboxadv'), array($this, 'general_width_html'), 'mapbox_general_settings', 'mapbox_general_section');
        add_settings_field('mapbox_default_mapheight', __('Map height','mapboxadv'), array($this, 'general_height_html'), 'mapbox_general_settings', 'mapbox_general_section');
         add_settings_field('mapbox_default_mappadding', __('Map padding','mapboxadv'), array($this, 'general_padding_html'), 'mapbox_general_settings', 'mapbox_general_section');
         add_settings_field('mapbox_default_mappaddingtlx', __('Map padding (Top left X)','mapboxadv'), array($this, 'general_paddingtlx_html'), 'mapbox_general_settings', 'mapbox_general_section');
         add_settings_field('mapbox_default_mappaddingtly', __('Map padding (Top left Y)','mapboxadv'), array($this, 'general_paddingtly_html'), 'mapbox_general_settings', 'mapbox_general_section');
         add_settings_field('mapbox_default_mappaddingbrx', __('Map padding (Bottom right X)','mapboxadv'), array($this, 'general_paddingbrx_html'), 'mapbox_general_settings', 'mapbox_general_section');
         add_settings_field('mapbox_default_mappaddingbry', __('Map padding (Bottom right Y)','mapboxadv'), array($this, 'general_paddingbry_html'), 'mapbox_general_settings', 'mapbox_general_section');
         add_settings_field('mapbox_default_mapcenterlat', __('Map center (Latitude)','mapboxadv'), array($this, 'general_centerlat_html'), 'mapbox_general_settings', 'mapbox_general_section');
         add_settings_field('mapbox_default_mapcenterlng', __('Map center (Longitude)','mapboxadv'), array($this, 'general_centerlng_html'), 'mapbox_general_settings', 'mapbox_general_section');
        add_settings_field('mapbox_default_mapzoom', __('Map zoom level','mapboxadv'), array($this, 'general_zoom_html'), 'mapbox_general_settings', 'mapbox_general_section');
        add_settings_field('mapbox_default_mapminzoom', __('Map min. zoom level','mapboxadv'), array($this, 'general_minzoom_html'), 'mapbox_general_settings', 'mapbox_general_section');
        add_settings_field('mapbox_default_mapmaxzoom', __('Map max. zoom level','mapboxadv'), array($this, 'general_maxzoom_html'), 'mapbox_general_settings', 'mapbox_general_section');
        add_settings_field('mapbox_default_mapmaxboundsswlat', __('Map bounds (SW Latitude)','mapboxadv'), array($this, 'general_maxboundsswlat_html'), 'mapbox_general_settings', 'mapbox_general_section');
        add_settings_field('mapbox_default_mapmaxboundsswlng', __('Map bounds (SW Longitude)','mapboxadv'), array($this, 'general_maxboundsswlng_html'), 'mapbox_general_settings', 'mapbox_general_section');
        add_settings_field('mapbox_default_mapmaxboundsnelat', __('Map bounds (NE Latitude)','mapboxadv'), array($this, 'general_maxboundsnelat_html'), 'mapbox_general_settings', 'mapbox_general_section');
        add_settings_field('mapbox_default_mapmaxboundsnelng', __('Map bounds (NE Longitude)','mapboxadv'), array($this, 'general_maxboundsnelng_html'), 'mapbox_general_settings', 'mapbox_general_section');
        add_settings_field('mapbox_default_mapfittomarkers', __('Fit map to markers','mapboxadv'), array($this, 'general_fittomarkers_html'), 'mapbox_general_settings', 'mapbox_general_section');

        //Controls
        add_settings_section('mapbox_controls_section', __('Controls','mapboxadv'), array($this, 'section_controls_html'), 'mapbox_controls_settings');
        add_settings_field('mapbox_default_mapzoomcontrol', __('Zoom control','mapboxadv'), array($this, 'controls_zoomcontrol_html'), 'mapbox_controls_settings', 'mapbox_controls_section');
        add_settings_field('mapbox_default_mapzoomcontrolposition', __('Zoom control position','mapboxadv'), array($this, 'controls_zoomcontrolposition_html'), 'mapbox_controls_settings', 'mapbox_controls_section');
         add_settings_field('mapbox_default_mapfullscreencontrol', __('Fullscreen control','mapboxadv'), array($this, 'controls_fullscreencontrol_html'), 'mapbox_controls_settings', 'mapbox_controls_section');
        add_settings_field('mapbox_default_mapfullscreencontrolposition', __('Fullscreeen control position','mapboxadv'), array($this, 'controls_fullscreencontrolposition_html'), 'mapbox_controls_settings', 'mapbox_controls_section');
         add_settings_field('mapbox_default_mapsharecontrol', __('Share control','mapboxadv'), array($this, 'controls_sharecontrol_html'), 'mapbox_controls_settings', 'mapbox_controls_section');
        add_settings_field('mapbox_default_mapsharecontrolposition', __('Share control position','mapboxadv'), array($this, 'controls_sharecontrolposition_html'), 'mapbox_controls_settings', 'mapbox_controls_section');
        add_settings_field('mapbox_default_mapgeocodercontrol', __('Geocoder control','mapboxadv'), array($this, 'controls_geocodercontrol_html'), 'mapbox_controls_settings', 'mapbox_controls_section');
        add_settings_field('mapbox_default_mapgeocodercontrolposition', __('Geocoder control position','mapboxadv'), array($this, 'controls_geocodercontrolposition_html'), 'mapbox_controls_settings', 'mapbox_controls_section');
        add_settings_field('mapbox_default_mapgeocodercontrolautoc', __('Geocoder control auto complete','mapboxadv'), array($this, 'controls_geocodercontrolautoc_html'), 'mapbox_controls_settings', 'mapbox_controls_section');
        add_settings_field('mapbox_default_mapgeocoderradiuskm', __('Geocoder control radius (km)','mapboxadv'), array($this, 'controls_geocoderradiuskm_html'), 'mapbox_controls_settings', 'mapbox_controls_section');
         add_settings_field('mapbox_default_maplegendposition', __('Legend position','mapboxadv'), array($this, 'controls_legendposition_html'), 'mapbox_controls_settings', 'mapbox_controls_section');
          add_settings_field('mapbox_default_maplegendbackgroundcolor', __('Legend background color','mapboxadv'), array($this, 'controls_legendbackgroundcolor_html'), 'mapbox_controls_settings', 'mapbox_controls_section');
         add_settings_field('mapbox_default_maplegendcontentcss', __('Legend default css','mapboxadv'), array($this, 'controls_lengendcontentcss_html'), 'mapbox_controls_settings', 'mapbox_controls_section');

        add_settings_field('mapbox_default_maplayerscontrolposition', __('Layers(s) control position','mapboxadv'), array($this, 'controls_layerscontrolposition_html'), 'mapbox_controls_settings', 'mapbox_controls_section');

        add_settings_field('mapbox_mapattributioncontrol', __('Atribution control','mapboxadv'), array($this, 'controls_attributioncontrol_html'), 'mapbox_controls_settings', 'mapbox_controls_section');

         //Layers
        add_settings_section('mapbox_layers_section', __('Layers','mapboxadv'), array($this, 'section_layers_html'), 'mapbox_layers_settings');
        add_settings_field('mapbox_default_maplayersids', __('Tile layer(s) id(s)','mapboxadv'), array($this, 'layers_layersids_html'), 'mapbox_layers_settings', 'mapbox_layers_section');
        add_settings_field('mapbox_default_maplayersurls', __('Tile layer(s) url(s)','mapboxadv'), array($this, 'layers_layersurls_html'), 'mapbox_layers_settings', 'mapbox_layers_section');
         /*add_settings_field('mapbox_default_mapwmslayers', __('WMS layers(s)','mapboxadv'), array($this, 'layers_wmslayers_html'), 'mapbox_layers_settings', 'mapbox_layers_section');
         add_settings_field('mapbox_default_mapwmslayerscontrolposition', __('WMS layers(s) control position','mapboxadv'), array($this, 'layers_wmslayerscontrolposition_html'), 'mapbox_layers_settings', 'mapbox_layers_section');
         add_settings_field('mapbox_default_mapwmslayersopacity', __('WMS layers(s) opacity','mapboxadv'), array($this, 'layers_wmslayersopacity_html'), 'mapbox_layers_settings', 'mapbox_layers_section');  */

         //add_settings_field('mapbox_default_maplayerscode', 'Layer(s) tileJSON code', array($this, 'layers_layerscode_html'), 'mapbox_layers_settings', 'mapbox_layers_section');

         //Features
        add_settings_section('mapbox_features_section', __('Features','mapboxadv'), array($this, 'section_features_html'), 'mapbox_features_settings');
        add_settings_field('mapbox_default_mapfeaturesids', __('Feature(s) id(s)','mapboxadv'), array($this, 'features_featuresids_html'), 'mapbox_features_settings', 'mapbox_features_section');
        add_settings_field('mapbox_default_mapfeaturesurls', __('Feature(s) url(s)','mapboxadv'), array($this, 'features_featuresurls_html'), 'mapbox_features_settings', 'mapbox_features_section');
        add_settings_field('mapbox_default_mapfeaturesurlstimeout', __('Feature(s) url(s) timeout (ms)','mapboxadv'), array($this, 'features_featuresurlstimeout_html'), 'mapbox_features_settings', 'mapbox_features_section');
         add_settings_field('mapbox_default_mapfeaturescode', __('Feature(s) geoJSON code','mapboxadv'), array($this, 'features_featurescode_html'), 'mapbox_features_settings', 'mapbox_features_section');

        //Geolocation
        add_settings_section('mapbox_geolocation_section', __('Geolocation','mapboxadv'), array($this, 'section_geolocation_html'), 'mapbox_geolocation_settings');
        add_settings_field('mapbox_default_mapuselocation', __('Use geolocation','mapboxadv'), array($this, 'geolocation_uselocation_html'), 'mapbox_geolocation_settings', 'mapbox_geolocation_section');
        add_settings_field('mapbox_maplocatehighaccuracy', __('Geolocation high accuracy','mapboxadv'), array($this, 'geolocation_locatehighaccuracy_html'), 'mapbox_geolocation_settings', 'mapbox_geolocation_section');
        add_settings_field('mapbox_mapdisplaylocation', __('Display geolocation marker','mapboxadv'), array($this, 'geolocation_displaylocation_html'), 'mapbox_geolocation_settings', 'mapbox_geolocation_section');
        add_settings_field('mapbox_maplocationmarkersymbol', __('Geolocation marker symbol','mapboxadv'), array($this, 'geolocation_locationmarkersymbol_html'), 'mapbox_geolocation_settings', 'mapbox_geolocation_section');
        add_settings_field('mapbox_maplocationmarkersize', __('Geolocation marker size','mapboxadv'), array($this, 'geolocation_locationmarkersize_html'), 'mapbox_geolocation_settings', 'mapbox_geolocation_section');
        add_settings_field('mapbox_maplocationmarkercolor', __('Geolocation marker color','mapboxadv'), array($this, 'geolocation_locationmarkercolor_html'), 'mapbox_geolocation_settings', 'mapbox_geolocation_section');
        add_settings_field('mapbox_default_mapgeolocatemaxradiuskm', __('Max. markers distance from geolocation (km)','mapboxadv'), array($this, 'geolocation_geolocatemaxradiuskm_html'), 'mapbox_geolocation_settings', 'mapbox_geolocation_section');
        add_settings_field('mapbox_default_mapmaxradiuskm', __('Max. radius around geolocation (km)','mapboxadv'), array($this, 'geolocation_maxradiuskm_html'), 'mapbox_geolocation_settings', 'mapbox_geolocation_section');
        add_settings_field('mapbox_default_mapcenteronlocation', __('Center map on geolocation','mapboxadv'), array($this, 'geolocation_centeronlocation_html'), 'mapbox_geolocation_settings', 'mapbox_geolocation_section');
         add_settings_field('mapbox_default_mapcirclearoundlocation', __('Circle around geolocation','mapboxadv'), array($this, 'geolocation_circlearoundlocation_html'), 'mapbox_geolocation_settings', 'mapbox_geolocation_section');
    }

    //General fields
    public function section_general_html()
    {
        echo 'General';
    }

    public function general_accesstoken_html()
    { ?><input type="text" id="mapbox_mapaccesstoken" name="mapbox_mapaccesstoken" value="<?php echo get_option('mapbox_mapaccesstoken','')?>"/><?php }
    public function general_continuousworld_html()
    { ?><input type="checkbox" id="mapbox_mapcontinuousworld" name="mapbox_mapcontinuousworld" value="1" <?php echo checked( get_option('mapbox_mapcontinuousworld',true), true);?>/><?php }
     public function general_disabletouchzoom_html()
    { ?><input type="checkbox" id="mapbox_mapdisabletouchzoom" name="mapbox_mapdisabletouchzoom" value="1" <?php echo checked( get_option('mapbox_mapdisabletouchzoom',false), true);?>/><?php }
     public function general_disabledrag_html()
    { ?><input type="checkbox" id="mapbox_mapdisabledrag" name="mapbox_mapdisabledrag" value="1" <?php echo checked( get_option('mapbox_mapdisabledrag',false), true);?>/><?php }


    //Map fields
    /*public function section_map_html()
    {
        echo 'Map default settings';
    }*/

    /*public function general_id_html()
    { ?><input type="text" id="mapbox_default_mapid" name="mapbox_default_mapid" value="<?php echo get_option('mapbox_default_mapid','')?>"/><?php }
    public function general_suffix_html()
    { ?><input type="text" id="mapbox_default_mapsuffix" name="mapbox_default_mapsuffix" value="<?php echo get_option('mapbox_default_mapsuffix','')?>"/><?php }*/
    public function general_staticmap_html()
    { ?><input type="checkbox" id="mapbox_mapstaticmap" name="mapbox_default_mapstaticmap" value="1" <?php echo checked( get_option('mapbox_default_mapstaticmap',false), true);?>/><?php }
     public function general_width_html()
    { ?><input type="text" id="mapbox_default_mapwidth" name="mapbox_default_mapwidth" value="<?php echo get_option('mapbox_default_mapwidth','100%')?>"/><?php }
     public function general_height_html()
    { ?><input type="text" id="mapbox_default_mapheight" name="mapbox_default_mapheight" value="<?php echo get_option('mapbox_default_mapheight','350px')?>"/><?php }
     public function general_padding_html()
    { ?><input type="text" id="mapbox_default_mappadding" name="mapbox_default_mappadding" value="<?php echo get_option('mapbox_default_mappadding','')?>"/><?php }
     public function general_paddingtlx_html()
    { ?><input type="text" id="mapbox_default_mappaddingtlx" name="mapbox_default_mappaddingtlx" value="<?php echo get_option('mapbox_default_mappaddingtlx','')?>"/><?php }
     public function general_paddingtly_html()
    { ?><input type="text" id="mapbox_default_mappaddingtly" name="mapbox_default_mappaddingtly" value="<?php echo get_option('mapbox_default_mappaddingtly','')?>"/><?php }
     public function general_paddingbrx_html()
    { ?><input type="text" id="mapbox_default_mappaddingbrx" name="mapbox_default_mappaddingbrx" value="<?php echo get_option('mapbox_default_mappaddingbrx','')?>"/><?php }
     public function general_paddingbry_html()
    { ?><input type="text" id="mapbox_default_mappaddingbry" name="mapbox_default_mappaddingbry" value="<?php echo get_option('mapbox_default_mappaddingbry','')?>"/><?php }
     public function general_zoom_html()
    { ?><input type="text" id="mapbox_default_mapzoom" name="mapbox_default_mapzoom" value="<?php echo get_option('mapbox_default_mapzoom',0)?>"/><?php }
     public function general_minzoom_html()
    { ?><input type="text" id="mapbox_default_mapminzoom" name="mapbox_default_mapminzoom" value="<?php echo get_option('mapbox_default_mapminzoom',1)?>"/><?php }
     public function general_maxzoom_html()
    { ?><input type="text" id="mapbox_default_mapmaxzoom" name="mapbox_default_mapmaxzoom" value="<?php echo get_option('mapbox_default_mapmaxzoom',16)?>"/><?php }
    public function general_maxboundsswlat_html()
    { ?><input type="text" id="mapbox_default_mapmaxboundsswlat" name="mapbox_default_mapmaxboundsswlat" value="<?php echo get_option('mapbox_default_mapmaxboundsswlat',0)?>"/><?php }
      public function general_maxboundsswlng_html()
    { ?><input type="text" id="mapbox_default_mapmaxboundsswlng" name="mapbox_default_mapmaxboundsswlng" value="<?php echo get_option('mapbox_default_mapmaxboundsswlng',0)?>"/><?php }
      public function general_maxboundsnelat_html()
    { ?><input type="text" id="mapbox_default_mapmaxboundsnelat" name="mapbox_default_mapmaxboundsnelat" value="<?php echo get_option('mapbox_default_mapmaxboundsnelat',0)?>"/><?php }
      public function general_maxboundsnelng_html()
    { ?><input type="text" id="mapbox_default_mapmaxboundsnelng" name="mapbox_default_mapmaxboundsnelng" value="<?php echo get_option('mapbox_default_mapmaxboundsnelng',0)?>"/><?php }
     public function general_centerlat_html()
    { ?><input type="text" id="mapbox_default_mapcenterlat" name="mapbox_default_mapcenterlat" value="<?php echo get_option('mapbox_default_mapcenterlat',0)?>"/><?php }
     public function general_centerlng_html()
    { ?><input type="text" id="mapbox_default_mapcenterlng" name="mapbox_default_mapcenterlng" value="<?php echo get_option('mapbox_default_mapcenterlng',0)?>"/><?php }
    public function general_fittomarkers_html()
    { ?><input type="checkbox" id="mapbox_default_mapfittomarkers" name="mapbox_default_mapfittomarkers" value="1" <?php echo checked( get_option('mapbox_default_mapfittomarkers',false), true);?>/><?php }
     public function general_centeronmarkerclick_html()
    { ?><input type="checkbox" id="mapbox_mapcenteronmarkerclick" name="mapbox_mapcenteronmarkerclick" value="1" <?php echo checked( get_option('mapbox_mapcenteronmarkerclick',false), true);?>/><?php }
    //Static map fields
    /*public function section_staticmap_html()
    {
        echo 'Static map default settings';
    }*/
    public function general_staticmapformat_html()
    { ?> <select id="mapbox_mapstaticmapformat" name="mapbox_mapstaticmapformat">
         <option <?php selected(get_option('mapbox_mapstaticmapformat','png'), 'png'); ?> value="png">PNG</option>
        <option <?php selected(get_option('mapbox_mapstaticmapformat','png'), 'png32'); ?> value="png32">32 color indexed PNG</option>
        <option <?php selected(get_option('mapbox_mapstaticmapformat','png'), 'png64'); ?> value="png64">64 color indexed PNG</option>
         <option <?php selected(get_option('mapbox_mapstaticmapformat','png'), 'png128'); ?> value="png68">128 color indexed PNG</option>
        <option <?php selected(get_option('mapbox_mapstaticmapformat','png'), 'png256'); ?> value="png256">256 color indexed PNG</option>
         <option <?php selected(get_option('mapbox_mapstaticmapformat','png'), 'jpg70'); ?> value="jpg70">70% quality JPG</option>
         <option <?php selected(get_option('mapbox_mapstaticmapformat','png'), 'jpg80'); ?> value="jpg80">80% quality JPG</option>
         <option <?php selected(get_option('mapbox_mapstaticmapformat','png'), 'jpg90'); ?> value="jpg90">90% quality JPG</option>
    </select><?php }
     public function general_staticmapformatretina_html()
    { ?><input type="checkbox" id="mapbox_mapstaticmapformatretina" name="mapbox_mapstaticmapformatretina" value="1" <?php echo checked( get_option('mapbox_mapstaticmapformatretina',false), true);?>/><?php }

    public function general_clustermode_html()
    { ?> <select id="mapbox_mapclustermode" name="mapbox_mapclustermode">
        <option <?php selected(get_option('mapbox_mapclustermode','none'), 'none'); ?> value="none"><?php _e('None','mapboxadv');?></option>
        <option <?php selected(get_option('mapbox_mapclustermode','none'), 'classic'); ?> value="classic"><?php _e('Classic','mapboxadv')?></option>
        <option <?php selected(get_option('mapbox_mapclustermode','none'), 'custom'); ?> value="custom"><?php _e('Custom','mapboxadv')?></option>
    </select><?php }
    public function general_clustercolor_html()
    { ?><input type="text" id="mapbox_mapclustercolor" name="mapbox_mapclustercolor" value="<?php echo get_option('mapbox_mapclustercolor', '#000000');?>"/><?php }



    //Controls fields
    public function section_controls_html()
    {
        echo 'Controls default settings';
    }

    public function controls_zoomcontrol_html()
    { ?><input type="checkbox" id="mapbox_default_mapzoomcontrol" name="mapbox_default_mapzoomcontrol" value="1" <?php echo checked( get_option('mapbox_default_mapzoomcontrol',false), true);?>/><?php }
    public function controls_zoomcontrolposition_html()
    { ?> <select id="mapbox_default_mapzoomcontrolposition" name="mapbox_default_mapzoomcontrolposition">
         <option <?php selected(get_option('mapbox_default_mapzoomcontrolposition','topleft'), 'topleft'); ?> value="topleft"><?php _e('Top left','mapboxadv');?></option>
        <option <?php selected(get_option('mapbox_default_mapzoomcontrolposition','topleft'), 'topright'); ?> value="topright"><?php _e('Top right','mapboxadv');?></option>
        <option <?php selected(get_option('mapbox_default_mapzoomcontrolposition','topleft'), 'bottomleft'); ?> value="bottomleft"><?php _e('Bottom left','mapboxadv');?></option>
         <option <?php selected(get_option('mapbox_default_mapzoomcontrolposition','topleft'), 'bottomright'); ?> value="bottomright"><?php _e('Bottom right','mapboxadv');?></option>
    </select><?php }
     public function controls_fullscreencontrol_html()
    { ?><input type="checkbox" id="mapbox_default_mapfullscreencontrol" name="mapbox_default_mapfullscreencontrol" value="1" <?php echo checked( get_option('mapbox_default_mapfullscreencontrol',false), true);?>/><?php }
    public function controls_fullscreencontrolposition_html()
    { ?> <select id="mapbox_default_mapfullscreencontrolposition" name="mapbox_default_mapfullscreencontrolposition">
         <option <?php selected(get_option('mapbox_default_mapfullscreencontrolposition','topleft'), 'topleft'); ?> value="topleft"><?php _e('Top left','mapboxadv');?></option>
        <option <?php selected(get_option('mapbox_default_mapfullscreencontrolposition','topleft'), 'topright'); ?> value="topright"><?php _e('Top right','mapboxadv');?></option>
        <option <?php selected(get_option('mapbox_default_mapfullscreencontrolposition','topleft'), 'bottomleft'); ?> value="bottomleft"><?php _e('Bottom left','mapboxadv');?></option>
         <option <?php selected(get_option('mapbox_default_mapfullscreencontrolposition','topleft'), 'bottomright'); ?> value="bottomright"><?php _e('Bottom right','mapboxadv');?></option>
    </select><?php }
     public function controls_sharecontrol_html()
    { ?><input type="checkbox" id="mapbox_default_mapsharecontrol" name="mapbox_default_mapsharecontrol" value="1" <?php echo checked( get_option('mapbox_default_mapsharecontrol',false), true);?>/><?php }
    public function controls_sharecontrolposition_html()
    { ?> <select id="mapbox_default_mapsharecontrolposition" name="mapbox_default_mapsharecontrolposition">
         <option <?php selected(get_option('mapbox_default_mapsharecontrolposition','topleft'), 'topleft'); ?> value="topleft"><?php _e('Top left','mapboxadv');?></option>
        <option <?php selected(get_option('mapbox_default_mapsharecontrolposition','topleft'), 'topright'); ?> value="topright"><?php _e('Top right','mapboxadv');?></option>
        <option <?php selected(get_option('mapbox_default_mapsharecontrolposition','topleft'), 'bottomleft'); ?> value="bottomleft"><?php _e('Bottom left','mapboxadv');?></option>
         <option <?php selected(get_option('mapbox_default_mapsharecontrolposition','topleft'), 'bottomright'); ?> value="bottomright"><?php _e('Bottom right','mapboxadv');?></option>
    </select><?php }
     public function controls_geocodercontrol_html()
    { ?><input type="checkbox" id="mapbox_default_mapgeocodercontrol" name="mapbox_default_mapgeocodercontrol" value="1" <?php echo checked( get_option('mapbox_default_mapgeocodercontrol',false), true);?>/><?php }
    public function controls_geocodercontrolposition_html()
    { ?> <select id="mapbox_default_mapgeocodercontrolposition" name="mapbox_default_mapgeocodercontrolposition">
         <option <?php selected(get_option('mapbox_default_mapgeocodercontrolposition','topleft'), 'topleft'); ?> value="topleft"><?php _e('Top left','mapboxadv');?></option>
        <option <?php selected(get_option('mapbox_default_mapgeocodercontrolposition','topleft'), 'topright'); ?> value="topright"><?php _e('Top right','mapboxadv');?></option>
        <option <?php selected(get_option('mapbox_default_mapgeocodercontrolposition','topleft'), 'bottomleft'); ?> value="bottomleft"><?php _e('Bottom left','mapboxadv');?></option>
         <option <?php selected(get_option('mapbox_default_mapgeocodercontrolposition','topleft'), 'bottomright'); ?> value="bottomright"><?php _e('Bottom right','mapboxadv');?></option>
    </select><?php }
      public function controls_geocodercontrolautoc_html()
    { ?><input type="checkbox" id="mapbox_default_mapgeocodercontrolautoc" name="mapbox_default_mapgeocodercontrolautoc" value="1" <?php echo checked( get_option('mapbox_default_mapgeocodercontrolautoc',false), true);?>/><?php }
     public function controls_geocoderradiuskm_html()
    { ?><input type="text" id="mapbox_default_mapmaxradiuskm" name="mapbox_default_mapgeocoderradiuskm" value="<?php echo get_option('mapbox_default_mapgeocoderradiuskm', 0)?>"/><?php }
    //legend
    public function controls_legendposition_html()
    { ?> <select id="mapbox_default_maplegendposition" name="mapbox_default_maplegendposition">
         <option <?php selected(get_option('mapbox_default_maplegendposition','bottomright'), 'topleft'); ?> value="topleft"><?php _e('Top left','mapboxadv');?></option>
        <option <?php selected(get_option('mapbox_default_maplegendposition','bottomright'), 'topright'); ?> value="topright"><?php _e('Top right','mapboxadv');?></option>
        <option <?php selected(get_option('mapbox_default_maplegendposition','bottomright'), 'bottomleft'); ?> value="bottomleft"><?php _e('Bottom left','mapboxadv');?></option>
         <option <?php selected(get_option('mapbox_default_maplegendposition','bottomright'), 'bottomright'); ?> value="bottomright"><?php _e('Bottom right','mapboxadv');?></option>
    </select><?php }
    public function controls_legendbackgroundcolor_html()
    { ?><input type="text" id="mapbox_default_maplegendbackgroundcolor" name="mapbox_default_maplegendbackgroundcolor" value="<?php echo get_option('mapbox_default_maplegendbackgroundcolor','#FFFFFF')?>"/><?php }
     public function controls_lengendcontentcss_html()
    { ?><textarea  id="mapbox_default_maplegendcontentcss" name="mapbox_default_maplegendcontentcss" rows="8" cols="20" ><?php echo get_option('mapbox_default_maplegendcontentcss','')?></textarea><?php }

    public function controls_layerscontrolposition_html()
    { ?><select id="mapbox_default_maplayerscontrolposition" name="mapbox_default_maplayerscontrolposition">
         <option <?php selected(get_option('mapbox_default_maplayerscontrolposition','bottomright'), 'topleft'); ?> value="topleft"><?php _e('Top left','mapboxadv');?></option>
        <option <?php selected(get_option('mapbox_default_maplayerscontrolposition','bottomright'), 'topright'); ?> value="topright"><?php _e('Top right','mapboxadv');?></option>
        <option <?php selected(get_option('mapbox_default_maplayerscontrolposition','bottomright'), 'bottomleft'); ?> value="bottomleft"><?php _e('Bottom left','mapboxadv');?></option>
         <option <?php selected(get_option('mapbox_default_maplayerscontrolposition','bottomright'), 'bottomright'); ?> value="bottomright"><?php _e('Bottom right','mapboxadv');?></option>
    </select><?php }

    public function controls_attributioncontrol_html()
    { ?><input type="checkbox" id="mapbox_mapattributioncontrol" name="mapbox_mapattributioncontrol" value="1" <?php echo checked( get_option('mapbox_mapattributioncontrol',true), true);?>/><?php }


    //Layers fields
    public function section_layers_html()
    {
        echo 'Additionnal layers default settings';
    }

    public function layers_layersids_html()
    { ?><input type="text" id="mapbox_default_maplayersids" name="mapbox_default_maplayersids" value="<?php echo get_option('mapbox_default_maplayersids','')?>"/><?php }
     public function layers_layersurls_html()
    { ?><input type="text" id="mapbox_default_maplayersurls" name="mapbox_default_maplayersurls" value="<?php echo get_option('mapbox_default_maplayersurls','')?>"/><?php }
     public function layers_layerscode_html()
    { ?><textarea  id="mapbox_default_maplayerscode" name="mapbox_default_maplayerscode" rows="8" cols="20" ><?php echo get_option('mapbox_default_maplayerscode','')?></textarea><?php }
     //wms
     /*public function layers_wmslayers_html()
     { ?><textarea  type="text" id="mapbox_default_mapwmslayers" name="mapbox_default_mapwmslayers" rows="8" cols="20"><?php echo get_option('mapbox_default_mapwmslayers','')?></textarea><?php }
    public function layers_wmslayerscontrolposition_html()
    { ?><select id="mapbox_default_mapwmslayerscontrolposition" name="mapbox_default_mapwmslayerscontrolposition">
         <option <?php selected(get_option('mapbox_default_mapwmslayerscontrolposition','bottomright'), 'topleft'); ?> value="topleft"><?php _e('Top left','mapboxadv');?></option>
        <option <?php selected(get_option('mapbox_default_mapwmslayerscontrolposition','bottomright'), 'topright'); ?> value="topright"><?php _e('Top right','mapboxadv');?></option>
        <option <?php selected(get_option('mapbox_default_mapwmslayerscontrolposition','bottomright'), 'bottomleft'); ?> value="bottomleft"><?php _e('Bottom left','mapboxadv');?></option>
         <option <?php selected(get_option('mapbox_default_mapwmslayerscontrolposition','bottomright'), 'bottomright'); ?> value="bottomright"><?php _e('Bottom right','mapboxadv');?></option>
    </select><?php }

    public function layers_wmslayersopacity_html()
    { ?><input type="text" id="mapbox_default_mapwmslayersopacity" name="mapbox_default_mapwmslayersopacity" value="<?php echo get_option('mapbox_default_mapwmslayersopacity','')?>"/><?php }*/

     //Features fields
    public function section_features_html()
    {
        echo 'Additionnal features default settings';
    }

    public function features_featuresids_html()
    { ?><input type="text" id="mapbox_default_mapfeaturesids" name="mapbox_default_mapfeaturesids" value="<?php echo get_option('mapbox_default_featuresids','')?>"/><?php }
     public function features_featuresurls_html()
    { ?><input type="text" id="mapbox_default_mapfeaturesurls" name="mapbox_default_mapfeaturesurls" value="<?php echo get_option('mapbox_default_featuresurls','')?>"/><?php }
    public function features_featuresurlstimeout_html()
    { ?><input type="text" id="mapbox_default_mapfeaturesurlstimeout" name="mapbox_default_mapfeaturesurlstimeout" value="<?php echo get_option('mapbox_default_featuresurlstimeout','')?>"/><?php }
     public function features_featurescode_html()
    { ?><textarea  id="mapbox_default_mapfeaturescode" name="mapbox_default_mapfeaturescode" rows="8" cols="20" ><?php echo get_option('mapbox_default_mapfeaturescode','')?></textarea><?php }

    //Geolocation fields
    public function section_geolocation_html()
    {
        echo 'Geolocation default settings';
    }

    public function geolocation_uselocation_html()
    { ?><input type="checkbox" id="mapbox_default_mapuselocation" name="mapbox_default_mapuselocation" value="1" <?php echo checked( get_option('mapbox_default_mapuselocation',false), true);?>/><?php }
    public function geolocation_geolocatemaxradiuskm_html()
    { ?><input type="text" id="mapbox_default_mapgeolocatemaxradiuskm" name="mapbox_default_mapgeolocatemaxradiuskm" value="<?php echo get_option('mapbox_default_mapgeolocatemaxradiuskm', 0);?>"/><?php }
    public function geolocation_maxradiuskm_html()
    { ?><input type="text" id="mapbox_default_mapmaxradiuskm" name="mapbox_default_mapmaxradiuskm" value="<?php echo get_option('mapbox_default_mapmaxradiuskm',0)?>"/><?php }
     public function geolocation_locatehighaccuracy_html()
    { ?><input type="checkbox" id="mapbox_maplocatehighaccuracy" name="mapbox_maplocatehighaccuracy" value="1" <?php echo checked( get_option('mapbox_maplocatehighaccuracy',true), true);?>/><?php }
     public function geolocation_displaylocation_html()
    { ?><input type="checkbox" id="mapbox_mapdisplaylocation" name="mapbox_mapdisplaylocation" value="1" <?php echo checked( get_option('mapbox_mapdisplaylocation',false), true);?>/><?php }
    public function geolocation_locationmarkersymbol_html()
    { ?><input type="text" id="mapbox_maplocationmarkersymbol" name="mapbox_maplocationmarkersymbol" value="<?php echo get_option('mapbox_maplocationmarkersymbol','star');?>"/><?php }
        public function geolocation_locationmarkersize_html()
    { ?><input type="text" id="mapbox_maplocationmarkersize" name="mapbox_maplocationmarkersize" value="<?php echo get_option('mapbox_maplocationmarkersize','medium');?>"/><?php }
        public function geolocation_locationmarkercolor_html()
    { ?><input type="text" id="mapbox_maplocationmarkercolor" name="mapbox_maplocationmarkercolor" value="<?php echo get_option('mapbox_maplocationmarkercolor','#000000');?>"/><?php }
     public function geolocation_centeronlocation_html()
    { ?><input type="checkbox" id="mapbox_default_mapcenteronlocation" name="mapbox_default_mapcenteronlocation" value="1" <?php echo checked( get_option('mapbox_default_mapcenteronlocation',false), true);?>/><?php }
    public function geolocation_circlearoundlocation_html()
    { ?><input type="checkbox" id="mapbox_default_mapcirclearoundlocation" name="mapbox_default_mapcirclearoundlocation" value="1" <?php echo checked( get_option('mapbox_default_mapcirclearoundlocation',false), true);?>/><?php }
}

new Mapboxadv();

?>
