<?php
/**
 * Created by wpbooking.
 * Developer: nasanji
 * Date: 5/23/2017
 * Version: 1.0
 */

if(!class_exists('Traveler_Landing_Controller')){
    class Traveler_Landing_Controller{

        static $_inst;

        function __construct()
        {
            add_action('wp_enqueue_scripts',array($this,'_add_scripts'));
        }

        static function _add_scripts()
        {
            wp_enqueue_style('landing',get_template_directory_uri().'/demo/css/landing.css');
        }

        static function inst(){
            if(!self::$_inst) self::$_inst = new self();

            return self::$_inst;
        }
    }

    Traveler_Landing_Controller::inst();
}