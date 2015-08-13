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

    public function get_single_student($am, $api_key) {
        $qry = $this->db->select('student.name, student.surname, student.am')
                ->from('student')
                ->where('am', $am)
                ->join('department', 'department.id=teacher.department')
                ->join('keys', 'keys.id=department.key', 'keys.key=' . $api_key)
                ->get();

        $result = $qry->row_array();

        return $result;
    }

    public function get_students($api_key) {
        $qry = $this->db->select('student.name, student.surname, student.am')
                ->from('student')
                ->join('department', 'department.id=student.department')
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

    public function add_grade($grade) {
        $this->db->insert("grades", $grade);
        return TRUE;
    }

    public function add_declaration($declaration) {
        $this->db->insert("declarations", $declaration);
        return TRUE;
    }

}
