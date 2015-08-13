<?php

require_once 'application/libraries/Format.php';
require_once 'application/libraries/REST_Controller.php';

class thesis extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('thesis_model', 'thesis');
    }

    public function add_thesis_post() {
        $api_key = $this->rest->key;
        $thesis_data['teacher'] = $this->post('teacher');
        $thesis_data['title'] = $this->post('title');
        $thesis_data['description'] = $this->post('description');
        $lessons_assigned = $this->post('lessons');
        $lessons = explode(",", $lessons_assigned);

        if (empty($lessons_assigned)) {
            $this->response(array("Error" => "Wrong/Empty lessons"));
            return;
        }

        foreach ($thesis_data as $each_data) {
            if ($each_data == "") {
                $this->response(array("Error" => "Wrong/Empty data"));
                return;
            }
        }

        if ($this->thesis->save_thesis($thesis_data, $lessons, $api_key)) {
            $this->response('Status', 'Completed');
        } else {
            $this->response('Status', 'ERROR');
        }
    }

    public function get_thesis_by_id_get() {
        $api_key = $this->rest->key;
        $id = $this->get('id');
        if ($id == "") {
            $this->response(array("Error" => "Wrong/Empty data"));
            return;
        }
        $this->response($this->thesis->get_thesis_by_id($id, $api_key), 200);
    }

    public function get_all_thesis_get() {
        $api_key = $this->rest->key;
        
        $this->response($this->thesis->get_all_thesis($api_key));
    }

}
