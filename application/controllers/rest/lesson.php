<?php

require_once 'application/libraries/Format.php';
require_once 'application/libraries/REST_Controller.php';

class lesson extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('lesson_model', 'lesson');
    }

    public function add_lesson_post() {
        $api_key = $this->rest->key;
        $lesson_data['name'] = $this->post('name');

        foreach ($lesson_data as $each_data) {
            if ($each_data == "") {
                $this->response(array("Error" => "Wrong/Empty data"));
                return;
            }
        }
        if ($this->lesson->save_lesson($lesson_data, $api_key)) {
            $this->response(array("Success" => "Lesson Added"), 200);
        }
    }

    public function get_lessons_get() {
        $api_key = $this->rest->key;
        $this->response($this->lesson->get_lessons($api_key));
    }

}
