<?php

/**
 * Description of enabler
 *
 * @author mubasharkk
 */

namespace ComfNet\Fiware;


abstract class Enabler {
    
    protected $root_url;
    
    protected function __setup($name) {
        $this->root_url = plugins_url('/'.$name, __FILE__);
    }    
}
