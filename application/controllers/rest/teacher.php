<?php

require_once 'application/libraries/Format.php';
require_once 'application/libraries/REST_Controller.php';

class teacher extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('teacher_model', 'teacher');
    }

    public function get_teacher_get() {
        $api_key = $this->rest->key;
        if ($this->get('username') == "") {
            $this->response(array("Error" => "No username provided"), 404);
        } else {
            $result = $this->teacher->get_teacher($this->get('username'), $api_key);
            if ($result == -1) {
                $this->response(array("Error" => "No teacher found"), 404);
            } else {
                $this->response($result, 200);
            }
        }
    }

    public function get_teachers_get() {
        $api_key = $this->rest->key;
        $result = $this->teacher->get_teachers($api_key);

        if ($result == "-1") {
            $this->response(array("Error" => "No teachers found"), 404);
        } else {
            $this->response($result, 200);
        }
    }

    public function add_teacher_post() {
        $api_key = $this->rest->key;

        $teacher_data['api_key'] = $api_key;
        $teacher_data['email'] = $this->post('email');
        $teacher_data['username'] = $this->post('username');
        $teacher_data['password'] = $this->post('password');
        
        if ($this->student->save_teacher($teacher_data)) {
            $this->response(array("Success" => "Teacher Added"), 200);
        } else {
            $this->response(array("Error" => "Teacher Couldn't saved"));
        }
    }

}
