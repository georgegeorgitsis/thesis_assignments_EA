<?php

/**
 * Created by PhpStorm.
 * User: topgan1
 * Date: 6/1/15
 * Time: 7:42 PM
 */
class student_model extends CI_Model {

    public function __construct() {
        $dbconnect = $this->load->database();
        parent::__construct();
    }

    public function save_student($student, $api_key) {

        $qry = $this->db->select('*')
                ->from('department')
                ->join('keys', 'keys.id=department.key', 'keys.key=' . $api_key)
                ->get();

        $result = $qry->row_array();

        $student['department'] = $result['id'];

        $this->db->insert("student", $student);
        return TRUE;
    }

    public function get_single_student($username, $api_key) {
        $qry = $this->db->select('user_accounts.uacc_username as username, user_accounts.uacc_email as email, user_accounts.bathmos_proodou')
                ->from('user_accounts')
                ->where('uacc_username', $username)
                ->join('departments', 'departments.id=user_accounts.department_id')
                ->join('keys', 'keys.id=departments.key', 'keys.key=' . $api_key)
                ->get();

        $result = $qry->row_array();

        return $result;
    }

    public function get_students($api_key) {
        $qry = $this->db->select('user_accounts.uacc_username as username, user_accounts.uacc_email as email, user_accounts.bathmos_proodou')
                ->from('user_accounts')
                ->join('departments', 'departments.id=user_accounts.department_id')
                ->join('keys', 'keys.id=departments.key', 'keys.key=' . $api_key)
                ->get();

        $result = $qry->result_array();

        return $result;
    }

    public function add_grade($grade) {
        $this->db->insert("grades", $grade);
        return TRUE;
    }

    public function add_declaration($declaration) {
        $this->db->insert("declarations", $declaration);
        return TRUE;
    }

}
