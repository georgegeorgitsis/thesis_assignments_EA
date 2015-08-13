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

    public function save_teacher($teacher, $api_key) {
        $qry = $this->db->select('*')
                ->from('department')
                ->join('keys', 'keys.id=department.key', 'keys.key=' . $api_key)
                ->get();

        $result = $qry->row_array();

        $teacher['department'] = $result['id'];

        $this->db->insert("teacher", $teacher);
        return TRUE;
    }

    public function get_single_teacher($am, $api_key) {
        $qry = $this->db->select('teacher.name, teacher.surname, teacher.am')
                ->from('teacher')
                ->where('am', $am)
                ->join('department', 'department.id=teacher.department')
                ->join('keys', 'keys.id=department.key', 'keys.key=' . $api_key)
                ->get();

        $result = $qry->row_array();

        return $result;
    }

    public function get_teachers($api_key) {
        $qry = $this->db->select('teacher.name, teacher.surname, teacher.am')
                ->from('teacher')
                ->join('department', 'department.id=teacher.department')
                ->join('keys', 'keys.id=department.key', 'keys.key=' . $api_key)
                ->get();

        $result = $qry->result_array();
        $i = 0;
        foreach ($result as $each_row) {
            $res[$i]['name'] = $each_row['name'];
            $res[$i]['surname'] = $each_row['surname'];
            $res[$i]['am'] = $each_row['am'];
            $i++;
        }
        return $res;
    }
    
    public function save_direct_assignment($data, $api_key) {
        $qry = $this->db->select('*')
                ->from('department')
                ->join('keys', 'keys.id=department.key', 'keys.key=' . $api_key)
                ->get();

        $result = $qry->row_array();
        
        $assignment['thesis'] = $data['thesis'];
        $assignment['student'] = $data['student'];
        $assignment['department'] = $result['id'];
        
        $this->db->insert("assignments", $assignment);
        return TRUE;
    }
}
