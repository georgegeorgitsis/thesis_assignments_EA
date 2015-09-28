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

    public function get_student($username, $api_key) {
        $qry = $this->db->select('user_accounts.uacc_username as username, user_accounts.uacc_email as email, user_accounts.bathmos_proodou')
                ->from('user_accounts')
                ->where('uacc_username', $username)
                ->join('departments', 'departments.id=user_accounts.department_id')
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

    public function get_students($api_key) {
        $qry = $this->db->select('user_accounts.uacc_username as username, user_accounts.uacc_email as email, user_accounts.bathmos_proodou,departments.name')
                ->from('user_accounts')
                ->join('departments', 'departments.id=user_accounts.department_id')
                ->join('keys', 'departments.key=keys.id')
                ->where('keys.key', $api_key)
                ->where('user_accounts.uacc_group_fk', 3)
                ->get();

        $result = $qry->result_array();

        if (!empty($result)) {
            return $result;
        } else {
            return -1;
        }
    }

    public function save_student($student_data) {
        $qry = $this->db->select('departments.id')
                ->from('departments')
                ->join('keys', 'keys.id=departments.key', 'keys.key=' . $student_data['api_key'])
                ->get();

        $result = $qry->row_array();
        $department_id = $result['id'];

        $user_data = array(
            "department_id" => $department_id,
            "bathmos_proodou" => $student_data['bathmos_proodou']
        );

        $this->flexi_auth->insert_user($student_data['email'], $student_data['username'], $student_data['password'], 3, TRUE);
    }

    public function add_grade($grade) {
        $this->db->insert("grades", $grade);
        return TRUE;
    }

    public function add_declaration($declaration) {
        $this->db->insert("declarations", $declaration);
        return TRUE;
    }

    public function delete_student($username) {
        $qry = $this->db->where('uacc_username', $username)->delete('uacc_accounts');
        if ($this->db->affected_rows() > 0)
            return true;
        return false;
    }

    public function edit_student($student_data) {
        if ($this->db->where('uacc_username', $student_data['uacc_username'])->update('user_accounts', $student_data))
            return true;
        return false;
    }

}
