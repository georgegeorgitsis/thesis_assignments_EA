<?php

/**
 *   dmegeorge
 *   ===============
 *   new_thesis_model Model
 *   CI Model 2.1
 *   Date : 8/8/15
 *   Created by: PhpStorm
 */
class new_thesis_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param $thesis_id
     * @return array
     */
    public function get_assigned_courses($thesis_id)
    {
        $qry = $this->db->select('course_id')
            ->from('assigned_courses_to_thesis')
            ->where('thesis_id', $thesis_id)
            ->get();
        $res = $qry->result_array();

        return $res;
    }

    public function get_mo_assigned_courses_for_student($student_id, $thesis_id)
    {
        $assigned_courses = $this->get_assigned_courses($thesis_id);

        //var_dump($assigned_courses);
        //die('qq');
        $course_mo = array();
        foreach ($assigned_courses as $assigned_course) {
            $qry = $this->db->select('grade')
                ->from('grades')
                ->where('course_id', $assigned_course['course_id'])
                ->where('student_id', $student_id)
                ->get();

            $res1 = $qry->row_array();

            if (!isset($res1['grade']) || $res1['grade'] < 5) {
                $res1['grade'] = 0;
            }


            array_push($course_mo, $res1['grade']);
        }

        return array_sum($course_mo) / count($course_mo);
    }
}

?>