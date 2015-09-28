<?php

/**
 * Created by PhpStorm.
 * User: topgan1
 * Date: 6/1/15
 * Time: 7:42 PM
 */
class lesson_model extends CI_Model {

    public function __construct() {
        $dbconnect = $this->load->database();
        parent::__construct();
    }

    public function get_lesson($code, $api_key) {
        $qry = $this->db->select('courses.course_code as code, courses.name as name, departments.name as department')
                ->from('courses')
                ->where('courses.course_code', $code)
                ->join('departments', 'departments.id=courses.department_id')
                ->join('keys', 'keys.id=departments.key')
                ->where('keys.key', $api_key)
                ->get();

        $result = $qry->row_array();

        if (!empty($result)) {
            return $result;
        } else {
            return -1;
        }
    }

    public function get_lessons($api_key) {
        $qry = $this->db->select('courses.course_code as code, courses.name as name, departments.name as department')
                ->from('courses')
                ->join('departments', 'departments.id=courses.department_id')
                ->join('keys', 'keys.id=departments.key', 'keys.key=' . $api_key)
                ->get();

        $result = $qry->result_array();

        if (!empty($result)) {
            return $result;
        } else {
            return -1;
        }
    }

    public function save_lesson($lesson, $api_key) {

        $qry = $this->db->select('departments.id')
                ->from('departments')
                ->join('keys', 'keys.id=departments.key', 'keys.key=' . $api_key)
                ->get();

        $result = $qry->row_array();

        $lesson['department_id'] = $result['id'];

        $this->db->insert("courses", $lesson);
        return TRUE;
    }

}
