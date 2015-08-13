<?php

require_once 'application/libraries/Format.php';
require_once 'application/libraries/REST_Controller.php';

class teacher extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('teacher_model', 'teacher');
    }

    public function add_teacher_post() {
        $api_key = $this->rest->key;
        $teacher_data['name'] = $this->post('name');
        $teacher_data['surname'] = $this->post('surname');
        $teacher_data['am'] = $this->post('am');
        $teacher_data['email'] = $this->post('email');

        foreach ($teacher_data as $each_data) {
            if ($each_data == "") {
                $this->response(array("Error" => "Wrong/Empty data"));
                return;
            }
        }
        if ($this->teacher->save_teacher($teacher_data, $api_key)) {
            $this->response(array("Success" => "Teacher Added"), 200);
        }
    }

    public function get_teacher_get() {
        $api_key = $this->rest->key;
        $teacher['am'] = $this->get('am');

        $result = $this->teacher->get_single_teacher($teacher['am'], $api_key);
        $this->response($result);
    }

    public function get_teachers_get() {
        $api_key = $this->rest->key;
        $this->response($this->teacher->get_teachers($api_key));
    }

    public function direct_assignment_post() {
        $api_key = $this->rest->key;
        $data['thesis'] = $this->post('thesis');
        $data['student'] = $this->post('student');

        if ($this->teacher->save_direct_assignment($data, $api_key)) {
            $this->response(array("Success" => "Direct assignment added"), 200);
        }else {
            $this->response(array("Error" => "Couldn't add direct assignment"));
        }
    }

}
