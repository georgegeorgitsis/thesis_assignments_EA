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

    public function save_thesis($thesis, $lessons, $api_key) {

        $qry = $this->db->select('*')
                ->from('department')
                ->join('keys', 'keys.id=department.key', 'keys.key=' . $api_key)
                ->get();

        $result = $qry->row_array();

        $thesis['department'] = $result['id'];

        $this->db->insert("thesis", $thesis);
        $thesis_id = $this->db->insert_id();

        foreach ($lessons as $each_lesson) {
            $assigned_lessons['thesis'] = $thesis_id;
            $assigned_lessons['lesson'] = $each_lesson;

            $this->db->insert("assigned_lessons_to_thesis", $assigned_lessons);
        }

        return TRUE;
    }

    public function get_thesis_by_id($id, $api_key) {
        $qry = $this->db->select('thesis.title, thesis.description, teacher.name, teacher.surname, teacher.email')
                ->from('thesis')
                ->where('thesis.id', $id)
                ->join('teacher', 'teacher.id=thesis.teacher')
                ->join('department', 'department.id=teacher.department')
                ->join('keys', 'keys.id=department.key', 'keys.key=' . $api_key)
                ->get();

        $result = $qry->row_array();

        return $result;
    }

    public function get_all_thesis($api_key) {
        $qry = $this->db->select('thesis.title, thesis.description, teacher.name, teacher.surname, teacher.email')
                ->from('thesis')
                ->join('teacher', 'teacher.id=thesis.teacher')
                ->join('department', 'department.id=teacher.department')
                ->join('keys', 'keys.id=department.key', 'keys.key=' . $api_key)
                ->get();

        $result = $qry->result_array();

        return $result;
    }

}
