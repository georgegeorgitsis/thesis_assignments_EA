<?php

/**
 *   dmegeorge
 *   ===============
 *   new_declarations_model Model
 *   CI Model 2.1
 *   Date : 8/8/15
 *   Created by: PhpStorm
 */
class new_declarations_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_all_declarations($dpt_id) {
        $qry = $this->db->select('declarations.student_id,declarations.thesis_id,declarations.priority')
                ->from('declarations')
                ->join('user_accounts', 'user_accounts.uacc_id=declarations.student_id')
                ->where('user_accounts.department_id', $dpt_id)
                ->get();

        $res = $qry->result_array();

        return $res;
    }

    public function show_results($solution) {

        $i = count($solution) - 1;

        for ($k = 0; $k < $i; $k++) {
            $qry = $this->db->select('uacc_username')
                    ->from('user_accounts')
                    ->where('uacc_id', $solution[$k]['student_id'])
                    ->get();

            $qry1 = $this->db->select('title')
                    ->from('thesis')
                    ->where('id', $solution[$k]['thesis_id'])
                    ->get();

            $res[$k]['student_id'] = $solution[$k]['student_id'];
            $res[$k]['thesis_id'] = $solution[$k]['thesis_id'];
            $res[$k]['student'] = $qry->row_array();
            $res[$k]['thesis'] = $qry1->row_array();
            $res[$k]['priority'] = $solution[$k]['priority'];
            $res[$k]['assessment'] = $solution[$k]['assessment'];
        }


        return $res;
    }

}

?>