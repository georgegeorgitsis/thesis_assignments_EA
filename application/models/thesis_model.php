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

    public function get_assigned_courses_to_thesis($thesis_title) {
        $qry = $this->db->select('courses.name, courses.course_code')
                ->from('courses')
                ->join('assigned_courses_to_thesis', 'assigned_courses_to_thesis.course_id = courses.id')
                ->join('thesis', 'thesis.id=assigned_courses_to_thesis.thesis_id')
                ->where('thesis.title', $thesis_title)
                ->get();
        $result = $qry->result_array();

        return $result;
    }

    public function save_thesis($thesis, $lessons, $api_key) {

        $qry = $this->db->select('user_accounts.uacc_id')
                ->from('user_accounts')
                ->where('uacc_username', $thesis['teacher'])
                ->get();
        $result = $qry->row_array();

        $thesis['teacher_id'] = $result['uacc_id'];

        unset($thesis['teacher']);

        $this->db->insert("thesis", $thesis);
        $thesis_id = $this->db->insert_id();

        foreach ($lessons as $each_lesson) {
            $qry = $this->db->select('courses.id')
                    ->from('courses')
                    ->where('course_code', $each_lesson)
                    ->get();
            $result = $qry->row_array();

            $assigned_lessons['thesis_id'] = $thesis_id;
            $assigned_lessons['course_id'] = $result['id'];

            $this->db->insert("assigned_courses_to_thesis", $assigned_lessons);
        }

        return TRUE;
    }

    public function get_thesis_for_student($department_id) {
        $qry = $this->db->select('*')
                ->from('thesis')
                ->join('user_accounts', 'user_accounts.uacc_id = thesis.teacher_id')
                ->where('user_accounts.department_id =' . $department_id)
                ->order_by('thesis.title')
                ->get();
        $result = $qry->result_array();
        return $result;
    }

    public function get_assigned_courses($department_id) {
        $qry = $this->db->select('*')
                ->from('assigned_courses_to_thesis as ac, courses as c')
                ->where('ac.course_id = c.id')
                ->get();
        $result = $qry->result_array();
        return $result;
    }

    public function edit_thesis($thesis) {

        $thesis_title = $thesis['title'];
        unset($thesis['title']);
        
        $this->db->where('thesis.title', $thesis_title);
        $this->db->update('thesis', $thesis);

        return true;
    }
    
    public function delete_thesis($thesis_title) {
        $this->db->delete('thesis', array('title' => $thesis_title));
        return true;
    }

}
