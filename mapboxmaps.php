<?php 

//Sources
//http://www.paulund.co.uk/wordpress-tables-using-wp_list_table
//http://wpengineer.com/2426/wp_list_table-a-step-by-step-guide/
//http://www.kvcodes.com/2014/05/wp_list_table-bulk-actions-example/
//http://mac-blog.org.ua/wordpress-custom-database-table-example-full/

//-------------------------------------------------------------
//Maps
//-------------------------------------------------------------
    
//1 : database
    
 /**
 * $custom_table_example_db_version - holds current database version
 * and used on plugin update to sync database tables
 */
global $mapboxadv_db_version;
$mapboxadv_db_version = '1.0'; 
/**
 * register_activation_hook implementation
 *
 * will be called when user activates plugin first time
 * must create needed database tables
 */
function mapboxadv_maps_install()
{
    global $wpdb;
    global $custom_table_example_db_version;

    $table_name = $wpdb->prefix . 'mapboxadv_maps'; // do not forget about tables prefix

    $sql = "CREATE TABLE " . $table_name . " (
      id int(11) NOT NULL AUTO_INCREMENT,
      title tinytext NOT NULL,
      description VARCHAR(255) NOT NULL,
      mapboxid VARCHAR(255) NOT NULL,
      suffix VARCHAR(255) NOT NULL,
      legendcontent TEXT DEFAULT '',
      legendcss TEXT DEFAULT '',
      PRIMARY KEY  (id)
    );";

    // we do not execute sql directly
    // we are calling dbDelta which cant migrate database
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // save current database version for later use (on upgrade)
    add_option('mapboxadv_db_version', $mapboxadv_db_version);

    /**
     * [OPTIONAL] Example of updating to 1.1 version
     *
     * If you develop new version of plugin
     * just increment $custom_table_example_db_version variable
     * and add following block of code
     *
     * must be repeated for each new version
     * in version 1.1 we change email field
     * to contain 200 chars rather 100 in version 1.0
     * and again we are not executing sql
     * we are using dbDelta to migrate table changes
     */
    $installed_ver = get_option('mapboxadv_db_version');
    if ($installed_ver != $mapboxadv_db_version) {
        $sql = "CREATE TABLE " . $table_name . " (
          id int(11) NOT NULL AUTO_INCREMENT,
          title tinytext NOT NULL,
          description VARCHAR(255) NOT NULL,
          mapboxid VARCHAR(255) NOT NULL,
          suffix VARCHAR(255) NOT NULL,
          legendcontent TEXT DEFAULT '',
          legendcss TEXT DEFAULT '',
          PRIMARY KEY  (id)
        );";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        // notice that we are updating option, rather than adding it
        update_option('mapboxadv_db_version', $mapboxadv_db_version);
    }
}

register_activation_hook(__FILE__, 'mapboxadv_maps_install');


/**
 * Trick to update plugin database, see docs
 */
function mapboxadv_maps_update_db_check()
{
    global $mapboxadv_db_version;
    if (get_site_option('mapboxadv_db_version') != $mapboxadv_db_version) {
        mapboxadv_maps_install();
    }
}

add_action('plugins_loaded', 'mapboxadv_maps_update_db_check');
    

//2 : admin panels

function maps_page_handler()
{
    /*if(isset($_GET['action'] ) && $_GET['action'] == 'edit' &&  isset($_GET['layer']) && $_GET['layer']  != null ){  
        $id = $_GET['layer'] ;
        $this->edit_selected_layer($id);  
    } else if(isset($_GET['action'] ) && $_GET['action'] == 'delete' &&  isset($_GET['layer']) && $_GET['layer']  != null ){  
        $id = $_GET['layer'] ;
        $this->delete_selected_layer($id);
    } else { */

        $mapsListTable = new Maps_List_Table();
        $mapsListTable->prepare_items();
    ?>
        <div class="wrap">
            <div id="icon-users" class="icon32"></div>
            <h2>Maps <a href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=mapboxadv-maps-form');  ?>" class="add-new-h2">Add New</a></h2>

                <form id="maps-table" method="GET">
                    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
                    <?php $mapsListTable->display(); ?>
                </form>
        </div>
    <?php
    //}
}
   
function maps_form_page_handler() {

    global $wpdb;
    $table_name = $wpdb->prefix . 'mapboxadv_maps'; // do not forget about tables prefix

    $message = '';
    $notice = '';

    // this is default $item which will be used for new records
    $default = array(
    'id' => 0,
    'title' => '',
    'description' => '',
    'mapboxid' => '',
    'suffix' => '',
    'legendcontent' => '',
    'legendcss' => ''
    );

    // here we are verifying does this request is post back and have correct nonce
    if (wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
        // combine our default item with request params
        $item = shortcode_atts($default, $_REQUEST);
        // validate data, and if all ok save item to database
        // if id is zero insert otherwise update
        $item_valid = maps_validate_layer($item);
        if ($item_valid === true) {
            if ($item['id'] == 0) {
                $result = $wpdb->insert($table_name, $item);
                $item['id'] = $wpdb->insert_id;
                if ($result) {
                    $message = __('Map was successfully saved', 'mapboxadv');
                } else {
                    $notice = __('There was an error while saving maps', 'mapboxadv');
                }
            } else {
                $result = $wpdb->update($table_name, $item, array('id' => $item['id']));
                if ($result) {
                    $message = __('Map was successfully updated', 'mapboxadv');
                } else {
                    $notice = __('There was an error while updating maps', 'mapboxadv');
                }
            }
        } else {
            // if $item_valid not true it contains error message(s)
            $notice = $item_valid;
        }
    }
    else {
        // if this is not post back we load item to edit or give new one to create
        $item = $default;

        if (isset($_REQUEST['id'])) {
            $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $_REQUEST['id']), ARRAY_A);
            if (!$item) {
                $item = $default;
                $notice = __('Map not found', 'mapboxadv');
            }
        }          
    }

    // here we adding our custom meta box
    add_meta_box('maps_form_meta_box', 'Map data', 'maps_form_meta_box_handler', 'map', 'normal', 'default');
    
    ?>
    <div class="wrap">
    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e('Map')?> 
        <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=mapboxadv-maps');?>"><?php _e('back to list', 'mapboxadv')?></a>
    </h2>

        <?php if (!empty($notice)): ?>
        <div id="notice" class="error"><p><?php echo $notice ?></p></div>
        <?php endif;?>
        <?php if (!empty($message)): ?>
        <div id="message" class="updated"><p><?php echo $message ?></p></div>
        <?php endif;?>

        <form id="form" method="POST">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
        <?php /* NOTICE: here we storing id to determine will be item added or updated */ ?>
        <input type="hidden" name="id" value="<?php echo $item['id'] ?>"/>

        <div class="metabox-holder" id="poststuff">
        <div id="post-body">
            <div id="post-body-content">
                <?php /* And here we call our custom meta box */ ?>
                <?php do_meta_boxes('map', 'normal', $item); ?>
                <input type="submit" value="<?php _e('Save')?>" id="submit" class="button-primary" name="submit">
            </div>
        </div>
        </div>
        </form>

    </div>
<?php 
}
    
function maps_form_meta_box_handler($item)
{
    ?>

    <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
    <tbody>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="title"><?php _e('Title', 'mapboxadv')?></label>
        </th>
        <td>
            <input id="title" name="title" type="text" style="width: 95%" value="<?php echo esc_attr($item['title'])?>"
                   size="50" class="code" placeholder="<?php _e('Your title', 'mapboxadv')?>" required>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="description"><?php _e('Description', 'mapboxadv')?></label>
        </th>
        <td>
            <input id="description" name="description" type="text" style="width: 95%" value="<?php echo esc_attr($item['description'])?>"
                   size="50" class="code" placeholder="<?php _e('Your description', 'mapboxadv')?>" >
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="mapboxid"><?php _e('Mapbox id', 'mapboxadv')?></label>
        </th>
        <td>
            <input id="mapboxid" name="mapboxid" type="text" style="width: 95%" value="<?php echo esc_attr($item['mapboxid'])?>"
                   size="50" class="code" placeholder="<?php _e('Your Mapbox ID', 'mapboxadv')?>" required>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="suffix"><?php _e('Suffix', 'mapboxadv')?></label>
        </th>
        <td>
            <input id="suffix" name="suffix" type="text" style="width: 95%" value="<?php echo esc_attr($item['suffix'])?>"
                   size="50" class="code" placeholder="<?php _e('Your suffix', 'mapboxadv')?>" >
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="legendcontent"><?php _e('Legend content', 'mapboxadv')?></label>
        </th>
        <td>    
            <?php $content =$item['legendcontent'];
            $editor_id = 'legendcontent';
            $settings = array('textarea_name' => 'legendcontent');
            wp_editor( stripslashes( $content), $editor_id, $settings );?>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="legendcss"><?php _e('Legend css', 'mapboxadv')?></label>
        </th>
        <td>
            <textarea id="legendcss" name="legendcss" type="text" style="width: 95%" rows="8" cols="20" class="code" placeholder="<?php _e('Your legend css', 'mapboxadv')?>" ><?php echo esc_attr($item['legendcss'])?></textarea>
        </td>
    </tr>
    </tbody>
    </table>
    <?php
}
    
function maps_validate_layer($item)
{
    $messages = array();
    if (empty($item['title'])) $messages[] = __('Title is required', 'mapboxadv');
    if (empty($item['mapboxid'])) $messages[] = __('Mapbox ID is required', 'mapboxadv');

    if (empty($messages)) return true;
    return implode('<br />', $messages);
}

if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Create a new table class that will extend the WP_List_Table
 */
class Maps_List_Table extends WP_List_Table
{
    
    function __construct(){
        global $status, $page;
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'map',     //singular name of the listed records
            'plural'    => 'maps',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
        
    }
    
    public function prepare_items()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'mapboxadv_maps'; // do not forget about tables prefix
        
        $per_page = 5; // constant, how much records will be shown per page
        
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
        
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        $this->process_bulk_action();
        
        // will be used in pagination settings
        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");
             
         // prepare query params, as usual current page, order by and order direction
        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'title';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';

        // [REQUIRED] define $items array
        // notice that last argument is ARRAY_A, so we will retrieve array
        $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);

        // [REQUIRED] configure pagination
        $this->set_pagination_args(array(
            'total_items' => $total_items, // total items defined above
            'per_page' => $per_page, // per page constant defined at top of method
            'total_pages' => ceil($total_items / $per_page) // calculate pages count
        ));
    }
    
    public function get_columns()
    {
        $columns = array(
            'cb'          => '<input type="checkbox" />', //Render a checkbox instead of text
            'id'          => 'ID',
            'title'       => 'Title',
            'description' => 'Description',
            'mapboxid'    => 'Mapbox ID',
            'suffix'      => 'Suffix',
            'legendcontent' => 'Legend content',
            'legendcss'   => 'Legend css'
        );

        return $columns;
    }

    public function get_hidden_columns()
    {
        return array('legendcontent', 'legendcss');
    }
    
     public function get_sortable_columns()
    {
        return array('title' => array('title', false));
    }

    public function column_default( $item, $column_name )
    {
        switch( $column_name ) {
            case 'id':
            case 'title':
            case 'description':
            case 'mapboxid':
            case 'suffix':
            case 'legendcontent':
            case 'legendcss':
                return $item[ $column_name ];

            default:
                return print_r( $item, true ) ;
        }
    }
    
    private function sort_data( $a, $b )
    {
        // Set defaults
        $orderby = 'title';
        $order = 'asc';

        // If orderby is set, use this as the sort column
        if(!empty($_GET['orderby']))
        {
            $orderby = $_GET['orderby'];
        }

        // If order is set use this as the order
        if(!empty($_GET['order']))
        {
            $order = $_GET['order'];
        }

        $result = strnatcmp( $a[$orderby], $b[$orderby] );

        if($order === 'asc')
        {
            return $result;
        }

        return -$result;
    }
    
     function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ $item['id']                //The value of the checkbox should be the record's id
        );
    }
    
    function column_title($item) {
        $actions = array(
            'edit' => sprintf('<a href="?page=mapboxadv-maps-form&id=%s">%s</a>', $item['id'], __('Edit', 'mapboxadv')),
            'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $item['id'], __('Delete', 'mapboxadv')),
        );

        return sprintf('%1$s %2$s', $item['title'], $this->row_actions($actions) );
    }
    
    function get_bulk_actions() {
        $actions = array(
        'delete'    => 'Delete'
        );
        return $actions;
    }
    
     function process_bulk_action() {
        
        //Detect when a bulk action is being triggered...
        if( 'delete'===$this->current_action() ) {
           
            if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);

            if (!empty($ids)) {
                global $wpdb;
                $table_name = $wpdb->prefix . 'mapboxadv_maps'; // do not forget about tables prefix

                $wpdb->query("DELETE FROM $table_name WHERE id IN($ids)");
            }
        }
        }
    }

    
    function no_items() {
        _e( 'No maps found.' );
    }
}
?>