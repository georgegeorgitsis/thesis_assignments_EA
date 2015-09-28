<?php

/**
 * Created by PhpStorm.
 * User: topgan1
 * Date: 6/1/15
 * Time: 7:42 PM
 */
class thesis_model extends CI_Model {

    public function __construct() {
        $dbconnect = $this->load->database();
        parent::__construct();
    }

    public function get_thesis_by_teacher($teacher, $api_key) {
        $qry = $this->db->select('thesis.title, thesis.description, user_accounts.uacc_username, user_accounts.uacc_email')
                ->from('thesis')
                ->join('user_accounts', 'user_accounts.uacc_id=thesis.teacher_id')
                ->where('user_accounts.uacc_username', $teacher)
                ->get();

        $result = $qry->result_array();

        return $result;
    }

    public function get_all_thesis($api_key) {
        $qry = $this->db->select('thesis.title, thesis.description, thesis.date_created, user_accounts.uacc_username, user_accounts.uacc_email')
                ->from('thesis')
                ->join('user_accounts', 'user_accounts.uacc_id = thesis.teacher_id')
                ->join('departments', 'departments.id=user_accounts.department_id')
                ->join('keys', 'departments.key=keys.id')
                ->where('keys.key', $api_key)
                ->get();

        $result = $qry->result_array();

        return $result;
    }

    public function save_thesis($thesis, $lessons, $api_key) {

        $qry = $this->db->select('*')
                ->from('department')
                ->join('keys', 'keys.id=department.key', 'keys.key=' . $api_key)
                ->get();

        $result = $qry->row_array();

        $thesis['department_id'] = $result['id'];

        $this->db->insert("thesis", $thesis);
        $thesis_id = $this->db->insert_id();

        foreach ($lessons as $each_lesson) {
            $assigned_lessons['thesis_id'] = $thesis_id;
            $assigned_lessons['course_id'] = $each_lesson;

            $this->db->insert("assigned_lessons_to_thesis", $assigned_lessons);
        }

        return TRUE;
    }

}
