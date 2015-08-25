<?php

require_once 'application/libraries/Format.php';
require_once 'application/libraries/REST_Controller.php';

class student extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('student_model', 'student');
    }

    public function add_student_post() {
        $api_key = $this->rest->key;
        $student_data['name'] = $this->post('name');
        $student_data['surname'] = $this->post('surname');
        $student_data['am'] = $this->post('am');
        $student_data['email'] = $this->post('email');

        foreach ($student_data as $each_data) {
            if ($each_data == "") {
                $this->response(array("Error" => "Wrong/Empty data"));
                return;
            }
        }
        if ($this->student->save_student($student_data, $api_key)) {
            $this->response(array("Success" => "Student Added"), 200);
        } else {
            $this->response(array("Error" => "Student Couldn't saved"));
        }
    }

    public function get_student_get() {
        $api_key = $this->rest->key;
        $student['username'] = $this->get('username');

        $result = $this->student->get_single_student($student['username'], $api_key);
        return $this->response($result);
    }

    public function get_students_get() {
        $api_key = $this->rest->key;
        $this->response($this->student->get_students($api_key));
    }

    public function add_student_grade_post() {
        $grade['student'] = $this->post('student');
        $grade['lesson'] = $this->post('lesson');
        $grade['grade'] = $this->post('grade');

        foreach ($grade as $each_data) {
            if ($each_data == "") {
                $this->response(array("Error" => "Wrong/Empty data"));
                return;
            }
        }

        if ($this->student->add_grade($grade)) {
            $this->response(array("Success" => "Grade Added"), 200);
        } else {
            $this->response(array("Error" => "Grade Couldn't saved"));
        }
    }
    
    public function add_declaration_post() {
        $declaration['student'] = $this->post('student');
        $declaration['thesis'] = $this->post('thesis');
        $declaration['priority'] = $this->post('priority');
        
        if ($this->student->add_declaration($declaration)) {
            $this->response(array("Success" => "Declaration added"), 200);
        } else {
            $this->response(array("Error" => "Declaration couldn't saved"));
        }
    }

}
