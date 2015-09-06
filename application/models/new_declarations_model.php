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

    public function show_results($solution, $acceptable_genes) {
        $i = 0;
        foreach ($solution as $key => $val) {
            if ($key == "fitness") {
                break;
            }
            $qry = $this->db->select('uacc_username')
                    ->from('user_accounts')
                    ->where('uacc_id', $key)
                    ->get();

            $qry1 = $this->db->select('title')
                    ->from('thesis')
                    ->where('id', $val)
                    ->get();

            $res[$i]['student_id'] = $key;
            $res[$i]['thesis_id'] = $val;
            $res[$i]['student'] = $qry->row_array();
            $res[$i]['thesis'] = $qry1->row_array();

            $found = false;
            foreach ($acceptable_genes as $each_gene) {
                if ($key == $each_gene['student_id'] && $val == $each_gene['thesis_id']) {
                    $res[$i]['priority'] = $each_gene['priority'];
                    $res[$i]['assessment'] = $each_gene['assessment'];

                    $found = true;
                }
            }

            if (!$found) {
                $res[$i]['priority'] = "Not acceptable";
                $res[$i]['assessment'] = "Not acceptable";
            }

            $i++;
        }

        return $res;
    }

}

?>