<?php

require_once 'application/libraries/Format.php';
require_once 'application/libraries/REST_Controller.php';

class student extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('student_model', 'student');
    }

    public function get_student_get() {
        $api_key = $this->rest->key;
        if ($this->get('username') == "") {
            $this->response(array("Error" => "No username provided"), 404);
        } else {
            $result = $this->student->get_student($this->get('username'), $api_key);
            if ($result == -1) {
                $this->response(array("Error" => "No student found"), 404);
            } else {
                $this->response($result, 200);
            }
        }
    }

    public function get_students_get() {
        $api_key = $this->rest->key;
        $result = $this->student->get_students($api_key);

        if ($result == "-1") {
            $this->response(array("Error" => "No students found"), 404);
        } else {
            $this->response($result, 200);
        }
    }

    public function add_student_post() {
        $api_key = $this->rest->key;

        $student_data['api_key'] = $api_key;
        $student_data['email'] = $this->post('email');
        $student_data['username'] = $this->post('username');
        $student_data['password'] = $this->post('password');
        $student_data['bathmos_proodou'] = $this->post('bathmos_proodou');

        if ($this->student->save_student($student_data)) {
            $this->response(array("Success" => "Student Added"), 200);
        } else {
            $this->response(array("Error" => "Student Couldn't saved"), 404);
        }
    }

    public function edit_student_put() {
        $api_key = $this->rest->key;

        $student_data['uacc_username'] = $this->put('username');
        $student_data['bathmos_proodou'] = $this->put('bathmos_proodou');

        var_dump($student_data);

        foreach ($student_data as $data) {
            if ($data == "") {
                $this->response(array("Error" => "Missing arguments"), 404);
            }
        }

        if ($this->student->edit_student($student_data)) {
            $this->response(array("Success" => "Student saved"), 200);
        } else {
            $this->response(array("Error" => "Student Couldn't saved"), 404);
        }
        var_dump($student_data);
    }

    public function add_student_grade_post() {
        $grade['student'] = $this->post('student');
        $grade['lesson'] = $this->post('lesson');
        $grade['grade'] = $this->post('grade');

        foreach ($grade as $each_data) {
            if ($each_data == "") {
                $this->response(array("Error" => "Wrong/Empty data", 404));
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

    public function delete_student_delete() {
        $api_key = $this->rest->key;
        if ($this->get('username') == "") {
            $this->response(array("Error" => "No username provided"), 406);
        } else {
            $result = $this->student->delete_student($this->get('username'));
            if ($result) {
                $this->response($result, 200);
            } else {
                $this->response(array("Error" => "Could not delete student"), 410);
            }
        }
    }

}
