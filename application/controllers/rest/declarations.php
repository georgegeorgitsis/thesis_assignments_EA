<?php

require_once 'application/libraries/Format.php';
require_once 'application/libraries/REST_Controller.php';

class declarations extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('new_declarations_model', 'declarations');
    }

    public function get_declarations_get() {
        $api_key = $this->rest->key;

        $declarations = $this->declarations->rest_get_all_declarations(8);

        $this->response($declarations, 200);
    }

}
