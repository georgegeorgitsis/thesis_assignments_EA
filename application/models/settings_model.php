<?php

/**
*   dmegeorge
*   ===============
*   settings_model Model
*   CI Model 2.1
*   Date : 8/4/15
*   Created by: PhpStorm
*/

class Settings_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    public function get_settings($dpt_id)
    {
        $qry = $this->db->select('*')
            ->from('settings')
            ->where('department_id', $dpt_id)
            ->get();
        $res = $qry->row_array();

        if (is_array($res))
            return $res;
        return FALSE;

    }
   
}
?>