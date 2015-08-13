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

    public function save_lesson($lesson, $api_key) {

        $qry = $this->db->select('*')
                ->from('department')
                ->join('keys', 'keys.id=department.key', 'keys.key=' . $api_key)
                ->get();

        $result = $qry->row_array();

        $lesson['department'] = $result['id'];

        $this->db->insert("lesson", $lesson);
        return TRUE;
    }

    public function get_lessons($api_key) {
        $qry = $this->db->select('lesson.name')
                ->from('lesson')
                ->join('department', 'department.id=lesson.department')
                ->join('keys', 'keys.id=department.key', 'keys.key=' . $api_key)
                ->get();

        $result = $qry->result_array();
        $i = 0;
        foreach ($result as $each_row) {
            $res[$i]['name'] = $each_row['name'];
            $i++;
        }
        return $res;
    }

}
