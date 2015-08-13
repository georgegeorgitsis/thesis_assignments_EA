<?php

/**
 *   dmegeorge
 *   ===============
 *   new_student_model Model
 *   CI Model 2.1
 *   Date : 8/8/15
 *   Created by: PhpStorm
 */
class new_student_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get_date_added($student_id)
    {
        $qry = $this->db->select('uacc_date_added')
            ->from('user_accounts')
            ->where('uacc_id', $student_id)
            ->get();
        $res = $qry->row_array();

        return $res['uacc_date_added'];
    }

    public function get_bathmos_proodou($student_id)
    {
        $qry = $this->db->select('bathmos_proodou')
            ->from('user_accounts')
            ->where('uacc_id', $student_id)
            ->get();
        $res = $qry->row_array();

        return $res['bathmos_proodou'];
    }

    public function get_mo_sxolis($student_id)
    {
        $qry = $this->db->select_avg('grade', 'avg_grades')
            ->from('grades')
            ->where('student_id', $student_id)
            ->get();
        $res = $qry->row_array();

        if (is_null($res['avg_grades']))
            return 0;

        return $res['avg_grades'];
    }

}

?>