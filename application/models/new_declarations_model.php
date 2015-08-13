<?php

/**
 *   dmegeorge
 *   ===============
 *   new_declarations_model Model
 *   CI Model 2.1
 *   Date : 8/8/15
 *   Created by: PhpStorm
 */
class new_declarations_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all_declarations($dpt_id)
    {
        $qry = $this->db->select('declarations.student_id,declarations.thesis_id,declarations.priority')
            ->from('declarations')
            ->join('user_accounts', 'user_accounts.uacc_id=declarations.student_id')
            ->where('user_accounts.department_id', $dpt_id)
            ->get();

        $res = $qry->result_array();

        return $res;
    }
}

?>