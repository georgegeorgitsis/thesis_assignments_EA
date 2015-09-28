<?php

/**
 * Created by PhpStorm.
 * User: topgan1
 * Date: 6/1/15
 * Time: 7:42 PM
 */
class teacher_model extends CI_Model {

    public function __construct() {
        $dbconnect = $this->load->database();
        parent::__construct();
    }

    public function get_teacher($username, $api_key) {
        $qry = $this->db->select('user_accounts.uacc_username as username, user_accounts.uacc_email as email')
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

    public function get_teachers($api_key) {
        $qry = $this->db->select('user_accounts.uacc_username as username, user_accounts.uacc_email as email, departments.name')
                ->from('user_accounts')
                ->join('departments', 'departments.id=user_accounts.department_id')
                ->join('keys', 'departments.key=keys.id')
                ->where('keys.key', $api_key)
                ->where('user_accounts.uacc_group_fk', 2)
                ->get();

        $result = $qry->result_array();

        if (!empty($result)) {
            return $result;
        } else {
            return -1;
        }
    }

    public function save_teacher($teacher_data) {
        $qry = $this->db->select('departments.id')
                ->from('departments')
                ->join('keys', 'keys.id=departments.key', 'keys.key=' . $teacher_data['api_key'])
                ->get();

        $result = $qry->row_array();
        $department_id = $result['id'];

        $user_data = array(
            "department_id" => $department_id,
        );

        $this->flexi_auth->insert_user($teacher_data['email'], $teacher_data['username'], $teacher_data['password'], 2, TRUE);
    }

}
