<?php

require_once 'application/libraries/Format.php';
require_once 'application/libraries/REST_Controller.php';

class administrator extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('administrator_model', 'administrator');
    }

    public function add_department_post() {
        $department['name'] = $this->post('name');
        $department['key'] = $this->post('key');

        foreach ($department as $each_data) {
            if ($each_data == "") {
                $this->response(array("Error" => "Wrong/Empty data"));
                return;
            }
        }
        if ($this->administrator->add_department($department)) {
            $this->response(array("Success" => "Department added"), 200);
        }
    }

    public function get_department_get() {
        $key = $this->get('key');
        if ($key == "") {
            $this->response(array("Error" => "Wrong/Empty data"));
            return;
        }

        $this->response($this->administrator->get_department($key), 200);
    }

    public function add_department_settings_post() {
        $settings['department_id'] = $this->post('department');
        $settings['teacher_max_thesis'] = $this->post('teacher_max_thesis');
        $settings['student_max_declarations'] = $this->post('student_max_declarations');
        $settings['teacher_add_thesis_after'] = $this->post('teacher_add_thesis_after');
        $settings['teacher_add_thesis_before'] = $this->post('teacher_add_thesis_before');
        $settings['student_declarations_after'] = $this->post('student_declarations_after');
        $settings['student_declarations_before'] = $this->post('student_declarations_before');
        $settings['next_assignment'] = $this->post('next_assignment');
        $settings['max_instant_assignments_per_teacher'] = $this->post('max_instant_assignments_per_teacher');

        foreach ($settings as $each_data) {
            if ($each_data == "") {
                $this->response(array("Error" => "Wrong/Empty data"));
                return;
            }
        }

        if ($this->administrator->add_settings($settings)) {
            $this->response(array("Success" => "Settings added"), 200);
        }
    }

    public function do_assignments_post() {
        $department_key = $this->post('api_key');
        $this->response($this->administrator->do_assignments($department_key), 200);
    }

}
