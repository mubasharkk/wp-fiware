<?php

/**
 * Description of poi
 *
 * @author mubasharkk
 */

namespace ComfNet\Fiware\Enablers;


class POI extends \ComfNet\Fiware\Enabler {
    
    
    const NAME = 'poi';
    
    function __construct() {
        $this->__setup(self::NAME);
    }
    
    function parse_short_code($attrs){
        switch($attrs['type']){
            case 'map':
                $center = !empty($attrs['center']) ? $center : NULL;
                include_once dirname(__FILE__).'/views/map.php';
                break;
        }
    }
    
    function _init_backend_scripts_styles(){
        
    }
    
    function _init_frontend_scripts_styles(){
        wp_enqueue_script('fw-poi-gmap', "//maps.googleapis.com/maps/api/js");
        wp_enqueue_script('fw-poi-funcs', $this->root_url.'/js/functions.js');
//        wp_enqueue_script('fw-poi-gmap', $this->root_url.'/js/data.js');
    }
}

