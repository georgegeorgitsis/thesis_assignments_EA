<?php

/**
 * Created by PhpStorm.
 * User: topgan1
 * Date: 8/8/15
 * Time: 7:47 PM
 */
class Individual
{
    var $total_count_of_students;
    var $genes;
    public function __construct($total_count = null, $genes = null)
    {
        if (is_null($total_count) || is_null($genes))
            die('count of students or genes are null');

        $this->total_count_of_students = $total_count;
        $this->genes = $genes;



    }
}