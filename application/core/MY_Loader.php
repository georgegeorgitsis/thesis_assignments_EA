<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class MY_Loader extends CI_Loader {

    public function template($template_name, $vars = array(), $return = FALSE) {
        $content = $this->view('template/header_view', $vars, $return);
        $content .= $this->view($template_name, $vars, $return);
        $content .= $this->view('template/footer_view', $vars, $return);
        if ($return) {
            return $content;
        }
    }

}