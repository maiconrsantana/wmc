<?php 
if(!class_exists('ST_Inventory_Field')){
	class ST_Inventory_Field{
		public  $url;
        public $dir;

        function __construct(){

            $this->dir = st()->dir('plugins/ot-custom/fields/inventory');
            $this->url = st()->url('plugins/ot-custom/fields/inventory');


            add_action('admin_enqueue_scripts',array($this,'add_scripts'));
        }
        function init(){

            if( !class_exists( 'OT_Loader' ) ) return false;


            add_filter( 'ot_option_types_array', array($this, 'ot_add_custom_option_types'));

        }
        function add_scripts(){
                wp_register_script('bootstrap.min', $this->url . '/js/bootstrap.min.js', array('jquery'), NULL, TRUE);
                wp_register_script('moment.min', get_template_directory_uri() . '/js/moment.js', array('jquery'), NULL, TRUE);
                wp_register_script('prettify', $this->url . '/js/prettify.js', array('moment.min', 'bootstrap.min'), NULL, TRUE);
                wp_register_script('jquery.fn.gantt.min', $this->url . '/js/jquery.fn.gantt.min.js', array('jquery','prettify'), NULL, TRUE);
                wp_register_script('gantt.custom', $this->url . '/js/custom.js', array('jquery.fn.gantt.min'), NULL, TRUE);
                wp_register_style('gantt.css', $this->url . '/css/style.css');
                wp_register_style('gantt.custom.css', $this->url . '/css/custom.css',['gantt.css']);
        }

        function ot_post_select_ajax_unit_types($array, $id){
            return apply_filters( 'inventory', $array, $id );
        }

        function ot_add_custom_option_types( $types ) {
            $types['inventory'] = __('Inventory',ST_TEXTDOMAIN);

            return $types;
        }

        function load_view($view = false, $data = array()){

            if(!$view) $view = 'index';

            $file_name = $this->dir.'/views/'.$view.'.php';

            if(file_exists($file_name)){
                extract($data);

                ob_start();

                include $file_name;

                return @ob_get_clean();
            }
        }
	}

    $inventory = new ST_Inventory_Field();
    $inventory->init();

    if(!function_exists('ot_type_inventory')){
        function ot_type_inventory($args = array()){

            wp_enqueue_script( 'gantt.custom' );
            wp_enqueue_style( 'gantt.custom.css' );

            $inventory = new ST_Inventory_Field();
            $default = array(
                'field_name' => ''
            );
            $args = wp_parse_args($args, $default);

            echo $inventory->load_view(false, $args);
        }
    }    
}
?>