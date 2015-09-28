<?php

require_once 'application/libraries/Format.php';
require_once 'application/libraries/REST_Controller.php';

class thesis extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('thesis_model', 'thesis');
    }

    public function get_thesis_by_teacher_get() {
        $api_key = $this->rest->key;
        $teacher = $this->get('teacher');
        if ($teacher == "") {
            $this->response(array("Error" => "Wrong/Empty data"));
            return;
        } else {
            $this->response($this->thesis->get_thesis_by_teacher($teacher, $api_key), 200);
        }
    }

    public function get_all_thesis_get() {
        $api_key = $this->rest->key;
        $this->response($this->thesis->get_all_thesis($api_key));
    }

    public function add_thesis_post() {
        $api_key = $this->rest->key;

        $thesis_data['teacher'] = $this->post('teacher');
        $thesis_data['title'] = $this->post('title');
        $thesis_data['description'] = $this->post('description');

        foreach ($thesis_data as $data) {
            if ($data == "") {
                $this->response(array("Error" => "Missing arguments"), 404);
            }
        }

        $lessons_assigned = $this->post('lessons');
        $lessons = explode(",", $lessons_assigned);

        if (empty($lessons_assigned)) {
            $this->response(array("Error" => "Missing lessons"), 404);
            return;
        }

        if ($this->thesis->save_thesis($thesis_data, $lessons, $api_key)) {
            $this->response(array("Success" => "Thesis Added"), 200);
        } else {
            $this->response(array("Error" => "Thesis couldn't saved"), 404);
        }
    }

}
