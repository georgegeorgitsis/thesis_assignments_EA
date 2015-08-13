<?php

/**
 * Created by PhpStorm.
 * User: topgan1
 * Date: 8/8/15
 * Time: 7:48 PM
 */
class Population
{
    public $people=array();

    public function __construct($population_size, $filled_declarations)
    {
        if (!isset($populationSize) || $populationSize == 0) {
            die("Must specify a populationsize > 0");
        }

        $qry = $this->ci->db->distinct()->select('student_id')
                                    ->from('declarations')
                                    ->get();
        $res = $qry->result_array();
        $total_student_count = count($res);

        $i =0;
        $genes = array();
        foreach ($filled_declarations as $filled_declaration)
        {
            $genes[$i]['student_id'] = $filled_declaration['student_id'];
            $genes[$i]['thesis_id'] = $filled_declaration['thesis_id'];
            $genes[$i]['assessment'] = $filled_declaration['assessment'];
        }


        for ($i = 0; $i < $populationSize; $i++) {
            $this->people[$i] = new individual($total_student_count, $genes);  //instantiate a new object

        }
    }
}