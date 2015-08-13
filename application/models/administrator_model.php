<?php

class administrator_model extends CI_Model {

    public function __construct() {
        $dbconnect = $this->load->database();
        parent::__construct();
    }

    public function add_department($department) {
        $keys['key'] = $department['key'];
        $keys['level'] = 2;
        $keys['ignore_limits'] = 0;
        $keys['date_created'] = date("Y-m-d");

        $this->db->insert("keys", $keys);
        $key_id = $this->db->insert_id();

        $department_data['name'] = $department['name'];
        $department_data['key'] = $key_id;
        $this->db->insert("departments", $department_data);

        return TRUE;
    }

    public function get_department($key) {
        $qry = $this->db->select('departments.id, departments.name, keys.date_created')
                ->from('keys')
                ->where('keys.key', $key)
                ->join('departments', 'keys.id=departments.key')
                ->get();

        $result = $qry->row_array();
        return $result;
    }

    public function add_settings($settings) {
        $this->db->insert("settings", $settings);
        return TRUE;
    }

    public function do_assignments($department_key) {
        $department = $this->get_department($department_key);
        $deparment_id = $department['id'];

        //fere mou oles tis ptixiakes district
        $qry = $this->db->select('distinct(thesis_id)')
                ->from('declarations')
                ->join('user_accounts', 'user_accounts.uacc_id=declarations.student_id')
                ->join('departments', 'departments.id=user_accounts.department_id', 'departments.id=' . $deparment_id)
                ->order_by('declarations.thesis_id')
                ->get();
        $result = $qry->result_array();

        foreach ($result as $each_row) {
            //gia kathe ptixiaki
            $thesis = $each_row['thesis_id'];

            //fere mou olous tous mathites pou exoun dilosei tin ptixiaki kai den exoun parei ptixiaki h i ptixiaki den exei dilothei apo kanenan
            $qry_string_students = 'SELECT declarations.thesis_id, declarations.student_id, declarations.priority FROM declarations, user_accounts, departments'
                    . ' WHERE user_account.uacc_id = declarations.student_id AND departments.id = user_accounts.department_id AND departments.id=' . $deparment_id
                    . ' AND declarations.student_id NOT IN (SELECT student_id FROM assignments WHERE department=' . $deparment_id . ')'
                    . ' AND declarations.thesis NOT IN (SELECT thesis FROM assignments WHERE department=' . $deparment_id . ')'
                    . ' AND declarations.thesis=' . $thesis
                    . ' ORDER BY declarations.priority';

            $qry_students = $this->db->query($qry_string_students);
            $result_students = $qry_students->result_array();

            $i = 0;
            foreach ($result_students as $each_student_thesis) {
                if ($i == 0) {
                    //kane best choise ton 1o mathiti
                    $best_choise_student = $each_student_thesis['student'];
                    $best_choise_priority = $each_student_thesis['priority'];

                    //vres ton meso oro sta proapaitoumena
                    $qry_avg_thesis_lessons_best_student_string = "SELECT AVG(grades.grade) as avg FROM grades, thesis, assigned_lessons_to_thesis, lesson "
                            . "WHERE grades.lesson=lesson.id AND lesson.id=assigned_lessons_to_thesis.lesson AND assigned_lessons_to_thesis.thesis=$thesis AND grades.student=$best_choise_student GROUP BY grades.student";
                    $qry_avg_thesis_lessons_best_student = $this->db->query($qry_avg_thesis_lessons_best_student_string);
                    $qry_avg_thesis_lessons_best_student_result = $qry_avg_thesis_lessons_best_student->row_array();
                    $best_choise_sum_assigned_lessons = $qry_avg_thesis_lessons_best_student_result['avg'];
                } else {
                    //gia olous tous ipoloipous kane sigkriseis

                    if ($each_student_thesis['priority'] < $best_choise_priority) {
                        //an exei kalitero priority kanton best student kai vres mesous orous gia auton
                        $best_choise_student = $each_student_thesis['student'];
                        $best_choise_priority = $each_student_thesis['priority'];

                        //vres ton meso oro sta proapaitoumena
                        $qry_avg_thesis_lessons_best_student_string = "SELECT AVG(grades.grade) as avg FROM grades, thesis, assigned_lessons_to_thesis, lesson "
                                . "WHERE grades.lesson=lesson.id AND lesson.id=assigned_lessons_to_thesis.lesson AND assigned_lessons_to_thesis.thesis=$thesis AND grades.student=$best_choise_student GROUP BY grades.student";
                        $qry_avg_thesis_lessons_best_student = $this->db->query($qry_avg_thesis_lessons_best_student_string);
                        $qry_avg_thesis_lessons_best_student_result = $qry_avg_thesis_lessons_best_student->row_array();
                        $best_choise_sum_assigned_lessons = $qry_avg_thesis_lessons_best_student_result['avg'];
                    }

                    if ($each_student_thesis['priority'] == $best_choise_priority) {
                        //an exoun idia proteraiotita o antipalos me ton best choise
                        $opponent_student = $each_student_thesis['student'];

                        //vres ton meso oro tou antipalou                        
                        $qry_avg_thesis_lessons_best_student_string = "SELECT AVG(grades.grade) as avg FROM grades, thesis, assigned_lessons_to_thesis, lesson "
                                . "WHERE grades.lesson=lesson.id AND lesson.id=assigned_lessons_to_thesis.lesson AND assigned_lessons_to_thesis.thesis=$thesis AND grades.student=$best_choise_student GROUP BY grades.student";
                        $qry_avg_thesis_lessons_best_student = $this->db->query($qry_avg_thesis_lessons_best_student_string);
                        $qry_avg_thesis_lessons_best_student_result = $qry_avg_thesis_lessons_best_student->row_array();
                        $opponent_sum_assigned_lessons = $qry_avg_thesis_lessons_best_student_result['avg'];

                        //an o antipalos exei kalitero meso oso sta proapaitoumena kanton best choise
                        if ($opponent_sum_assigned_lessons > $best_choise_sum_assigned_lessons) {
                            $best_choise_student = $opponent_student;
                            $best_choise_priority = $each_student_thesis['priority'];
                        }
                    }
                }

                $i++;
            }
            
            //dose ston best choise tin ptixiaki
            $assignment['thesis'] = $thesis;
            $assignment['student'] = $best_choise_student;
            $assignment['department'] = $deparment_id;
            $this->db->insert("assignments", $assignment);
        }
    }

}
