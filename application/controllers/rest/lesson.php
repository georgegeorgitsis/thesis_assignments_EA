<?php

require_once 'application/libraries/Format.php';
require_once 'application/libraries/REST_Controller.php';

class lesson extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('lesson_model', 'lesson');
    }

    public function get_lesson_get() {
        $api_key = $this->rest->key;
        if ($this->get('code') == "") {
            $this->response(array("Error" => "No code provided"), 404);
        } else {
            $result = $this->lesson->get_lesson($this->get('code'), $api_key);
            if ($result == -1) {
                $this->response(array("Error" => "No lesson found"), 404);
            } else {
                $this->response($result, 200);
            }
        }
    }

    public function get_lessons_get() {
        $api_key = $this->rest->key;
        $result = $this->lesson->get_lessons($api_key);

        if ($result == "-1") {
            $this->response(array("Error" => "No lessons found"), 404);
        } else {
            $this->response($result, 200);
        }
    }

    public function add_lesson_post() {
        $api_key = $this->rest->key;
        $lesson_data['name'] = $this->post('name');
        $lesson_data['course_code'] = $this->post('code');

        var_dump($lesson_data);

        foreach ($lesson_data as $each_data) {
            if ($each_data == "") {
                $this->response(array("Error" => "Wrong/Empty data"));
                return;
            }
        }
        if ($this->lesson->save_lesson($lesson_data, $api_key)) {
            $this->response(array("Success" => "Course Added"), 200);
        } else {
            $this->response(array("Error" => "Couldn't save course"));
        }
    }

}
