<?php

/**
 *   dmegeorge
 *   ===============
 *   new_helpers_model Model
 *   CI Model 2.1
 *   Date : 8/8/15
 *   Created by: PhpStorm
 */
class new_helpers_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->model('new_student_model', 'student');
        $this->load->model('new_thesis_model', 'thesis');
    }

    public function get_priority($student_id, $thesis_id) {
        $qry = $this->db->select('priority')
                ->from('declarations')
                ->where('student_id', $student_id)
                ->where('thesis_id', $thesis_id)
                ->get();
        $res = $qry->row_array();

        return $res['priority'];
    }

    public function feature_scaling($min, $max, $value, $rev = false) {
        if ($max == $min) {
            return 1;
        }

        if (!$rev) {
            $x = ($value - $min) / ($max - $min);
        } else {
            $x = ($value - $max) / ($min - $max);
        }

        return abs($x);
    }

    public function get_varitites($post) {
        $varitites = array(
            'mo_sxolis' => intval($post['mo_sxolis']) / 100,
            'mo_assigned_courses' => intval($post['mo_assigned_courses']) / 100,
            'date_added' => intval($post['date_added']) / 100,
            'bathmos_proodou' => intval($post['bathmos_proodou']) / 100,
            'priority' => intval($post['priority']) / 100,
            'terminate' => intval($post['mo_sxolis']) + intval($post['mo_assigned_courses']) + intval($post['date_added']) + intval($post['bathmos_proodou']) + intval($post['priority'])
        );

        return $varitites;
    }

    public function fill_declarations_with_data_before_fs($declarations) {
        $i = 0;
        foreach ($declarations as $declaration) {
            $date_added = $this->student->get_date_added($declaration['student_id']);
            $bathmos_proodou = $this->student->get_bathmos_proodou($declaration['student_id']);
            $mo_sxolis = $this->student->get_mo_sxolis($declaration['student_id']);
            $mo_assigned_courses = $this->thesis->get_mo_assigned_courses_for_student($declaration['student_id'], $declaration['thesis_id']);
            $priority = $declaration['priority'];

            if ($mo_sxolis !== 0 || $mo_assigned_courses !== 0) {
                $filled_declarations[$i]['student_id'] = $declaration['student_id'];
                $filled_declarations[$i]['thesis_id'] = $declaration['thesis_id'];
                $filled_declarations[$i]['priority'] = $priority;
                $filled_declarations[$i]['date_added'] = strtotime($date_added);
                $filled_declarations[$i]['bathmos_proodou'] = $bathmos_proodou;
                $filled_declarations[$i]['mo_sxolis'] = $mo_sxolis;
                $filled_declarations[$i]['mo_assigned_courses'] = $mo_assigned_courses;
            }
            $i++;
        }

        return $filled_declarations;
    }

    public function get_all_values($filled_declarations) {
        $all_values['student_id'] = array();
        $all_values['thesis_id'] = array();
        $all_values['priority'] = array();
        $all_values['date_added'] = array();
        $all_values['bathmos_proodou'] = array();
        $all_values['mo_sxolis'] = array();
        $all_values['mo_assigned_courses'] = array();

        foreach ($filled_declarations as $filled_declaration) {
            if (!in_array($filled_declaration['student_id'], $all_values['student_id'], true)) {
                array_push($all_values['student_id'], $filled_declaration['student_id']);
            }
            if (!in_array($filled_declaration['thesis_id'], $all_values['thesis_id'], true)) {
                array_push($all_values['thesis_id'], $filled_declaration['thesis_id']);
            }
            if (!in_array($filled_declaration['priority'], $all_values['priority'], true)) {
                array_push($all_values['priority'], $filled_declaration['priority']);
            }
            if (!in_array($filled_declaration['date_added'], $all_values['date_added'], true)) {
                array_push($all_values['date_added'], $filled_declaration['date_added']);
            }
            if (!in_array($filled_declaration['bathmos_proodou'], $all_values['bathmos_proodou'], true)) {
                array_push($all_values['bathmos_proodou'], $filled_declaration['bathmos_proodou']);
            }
            if (!in_array($filled_declaration['mo_sxolis'], $all_values['mo_sxolis'], true)) {
                array_push($all_values['mo_sxolis'], $filled_declaration['mo_sxolis']);
            }
            if (!in_array($filled_declaration['mo_assigned_courses'], $all_values['mo_assigned_courses'], true)) {
                array_push($all_values['mo_assigned_courses'], $filled_declaration['mo_assigned_courses']);
            }
        }

        return $all_values;
    }

    public function fill_declarations_with_fs($filled_declarations, $all_values, $varitites) {
        $i = 0;
        foreach ($filled_declarations as $filled_declaration) {
            $filled_declarations[$i]['priority_fs'] = $this->helper->feature_scaling(min($all_values['priority']), max($all_values['priority']), $filled_declaration['priority'], true);
            $filled_declarations[$i]['date_added_fs'] = $this->helper->feature_scaling(min($all_values['date_added']), max($all_values['date_added']), $filled_declaration['date_added']);
            $filled_declarations[$i]['bathmos_proodou_fs'] = $this->helper->feature_scaling(min($all_values['bathmos_proodou']), max($all_values['bathmos_proodou']), $filled_declaration['bathmos_proodou']);
            $filled_declarations[$i]['mo_sxolis_fs'] = $this->helper->feature_scaling(min($all_values['mo_sxolis']), max($all_values['mo_sxolis']), $filled_declaration['mo_sxolis']);
            $filled_declarations[$i]['mo_assigned_courses_fs'] = $this->helper->feature_scaling(min($all_values['mo_assigned_courses']), max($all_values['mo_assigned_courses']), $filled_declaration['mo_assigned_courses']);

            $filled_declarations[$i]['priority_fs_epi_varititas'] = $filled_declarations[$i]['priority_fs'] * $varitites['priority'] * 3;
            $filled_declarations[$i]['date_added_fs_epi_varititas'] = $filled_declarations[$i]['date_added_fs'] * $varitites['date_added'];
            $filled_declarations[$i]['bathmos_proodou_fs_epi_varititas'] = $filled_declarations[$i]['bathmos_proodou_fs'] * $varitites['bathmos_proodou'];
            $filled_declarations[$i]['mo_sxolis_fs_epi_varititas'] = $filled_declarations[$i]['mo_sxolis_fs'] * $varitites['mo_sxolis'];
            $filled_declarations[$i]['mo_assigned_courses_fs_epi_varititas'] = $filled_declarations[$i]['mo_assigned_courses_fs'] * $varitites['mo_assigned_courses'];

            $filled_declarations[$i]['assessment'] = ($filled_declarations[$i]['priority_fs_epi_varititas'] + $filled_declarations[$i]['date_added_fs_epi_varititas'] + $filled_declarations[$i]['bathmos_proodou_fs_epi_varititas'] + $filled_declarations[$i]['mo_sxolis_fs_epi_varititas'] + $filled_declarations[$i]['mo_assigned_courses_fs_epi_varititas']);

            $i++;
        }

        return $filled_declarations;
    }

    public function get_acceptable_genes($declarations) {
        $acceptable_genes = array();

        $i = 0;
        foreach ($declarations as $declaration) {
            $acceptable_genes[$i]['student_id'] = $declaration['student_id'];
            $acceptable_genes[$i]['thesis_id'] = $declaration['thesis_id'];
            $acceptable_genes[$i]['priority'] = $declaration['priority'];
            $acceptable_genes[$i]['assessment'] = $declaration['assessment'];

            $i++;
        }

        return $acceptable_genes;
    }

    public function check_collisions_per_individual($individual) {
        $collisions_found = 0;

        $temp_array = $individual;
        $temp_array = array_values($temp_array);

        $genes_count = count($temp_array) - 1;

        for ($i = 0; $i < $genes_count; $i++) {
            $thesis_id = $temp_array[$i];

            for ($j = $i; $j < $genes_count; $j++) {
                if ($i != $j) {
                    if ($temp_array[$j] == $thesis_id) {
                        $collisions_found++;
                    }
                }
            }
        }

        return $collisions_found;
    }

    public function check_acceptable_genes_per_individual($individual, $acceptable_genes) {
        $acceptable_genes_count = 0;

        $individual_cells_count = count($individual) - 1;
        $genes_count = count($acceptable_genes);

        foreach ($individual as $key => $val) {
            if ($key == "fitness") {
                break;
            }
            foreach ($acceptable_genes as $acceptable_gene) {
                if ($acceptable_gene['student_id'] == $key && $acceptable_gene['thesis_id'] == $val) {
                    $acceptable_genes_count++;
                }
            }
        }

        return $acceptable_genes_count;
    }

    public function get_single_values_of_genes($acceptable_genes) {
        $all_single_values['student_id'] = array();
        $all_single_values['thesis_id'] = array();
        $all_single_values['priority'] = array();

        $genes_count = count($acceptable_genes);

        for ($i = 0; $i < $genes_count; $i++) {
            if (!in_array($acceptable_genes[$i]['student_id'], $all_single_values['student_id'], true)) {
                array_push($all_single_values['student_id'], $acceptable_genes[$i]['student_id']);
            }
            if (!in_array($acceptable_genes[$i]['thesis_id'], $all_single_values['thesis_id'], true)) {
                array_push($all_single_values['thesis_id'], $acceptable_genes[$i]['thesis_id']);
            }
            if (!in_array($acceptable_genes[$i]['priority'], $all_single_values['priority'], true)) {
                array_push($all_single_values['priority'], $acceptable_genes[$i]['priority']);
            }
        }

        return $all_single_values;
    }

    public function get_sum_assesment_per_individual($individual, $acceptable_genes) {
        $individual_cells_count = count($individual) - 1;
        $sum = 0;

        foreach ($individual as $key => $gene) {
            foreach ($acceptable_genes as $acceptable_gene) {
                if ($acceptable_gene['student_id'] == $key && $acceptable_gene['thesis_id'] == $gene) {
                    $sum += $acceptable_gene['assessment'];
                }
            }
        }

        return $sum;
    }

    public function get_sum_single_chances($population = array()) {
        $single_chances = array();

        foreach ($population as $individual) {
            array_push($single_chances, $individual['fitness']['chances']);
        }

        return $single_chances;
    }

    public function create_first_population($population_number, $single_values) {
        $population = array();

        for ($i = 0; $i < $population_number; $i++) {
            foreach ($single_values['student_id'] as $each_student) {
                $r = array_rand($single_values['thesis_id']);
                $population[$i][$each_student] = $single_values['thesis_id'][$r];
            }
        }

        return $population;
    }

    public function get_population_fitness($population, $acceptable_genes) {
        $students_count = count($population[0]);
        $population_count = count($population);

        for ($i = 0; $i < $population_count; $i++) {
            $population[$i]['fitness'] = array();
            $population[$i]['fitness']['acceptable_genes'] = $this->check_acceptable_genes_per_individual($population[$i], $acceptable_genes);
            $population[$i]['fitness']['fs_acceptable_genes'] = $this->helper->feature_scaling(0, $students_count, $population[$i]['fitness']['acceptable_genes']);

            if ($population[$i]['fitness']['fs_acceptable_genes'] == 1) {
                $population[$i]['fitness']['collisions'] = $this->check_collisions_per_individual($population[$i]);
                $population[$i]['fitness']['fs_collisions'] = $this->helper->feature_scaling(0, $students_count, $population[$i]['fitness']['collisions'], true);
            } else {
                $population[$i]['fitness']['collisions'] = $students_count;
                $population[$i]['fitness']['fs_collisions'] = 0;
            }

            if ($population[$i]['fitness']['collisions'] == 0) {
                $population[$i]['fitness']['sum_assesment'] = $this->get_sum_assesment_per_individual($population[$i], $acceptable_genes);
            } else {
                $population[$i]['fitness']['sum_assesment'] = 0;
            }
        }

        $min_sum_assesment = $population[0]['fitness']['sum_assesment'];
        $max_sum_assesment = $population[0]['fitness']['sum_assesment'];
        $sum_population_assesments = 0;

        for ($i = 0; $i < $population_count; $i++) {
            if ($population[$i]['fitness']['sum_assesment'] < $min_sum_assesment) {
                $min_sum_assesment = $population[$i]['fitness']['sum_assesment'];
            }
            if ($population[$i]['fitness']['sum_assesment'] > $max_sum_assesment) {
                $max_sum_assesment = $population[$i]['fitness']['sum_assesment'];
            }
            $sum_population_assesments += $population[$i]['fitness']['sum_assesment'];
        }

        for ($i = 0; $i < $population_count; $i++) {
            if ($population[$i]['fitness']['collisions'] == 0) {
                $population[$i]['fitness']['fs_sum_assesment'] = $this->helper->feature_scaling($min_sum_assesment, $max_sum_assesment, $population[$i]['fitness']['sum_assesment']);
            } else {
                $population[$i]['fitness']['fs_sum_assesment'] = 0;
            }

            $population[$i]['fitness']['total_fitness'] = $population[$i]['fitness']['fs_acceptable_genes'] + $population[$i]['fitness']['fs_collisions'] + $population[$i]['fitness']['fs_sum_assesment'];
        }

        $sum_fitness = 0;
        for ($i = 0; $i < $population_count; $i++) {
            $sum_fitness += $population[$i]['fitness']['total_fitness'];
        }

        for ($i = 0; $i < $population_count; $i++) {
            if ($sum_population_assesments == 0) {
                $sum_population_assesments = 0.1;
            }
            $population[$i]['fitness']['chances'] = (float) number_format($population[$i]['fitness']['total_fitness'] / $sum_population_assesments, 6) * 1000000;
        }

        return $population;
    }

    public function roullete_selection($population = array(), $population_count, $sum_chances, $acceptable_genes) {
        $acceptable_genes_count = count($acceptable_genes);
        $genes_count = count($population[0]) - 1;
        $sum_chances = array_sum($sum_chances);
        $random_number = rand(0, $sum_chances);

        $ch_before = 0;
        $ch_after = 0;

        for ($i = 0; $i < $population_count; $i++) {
            $random_number = rand(0, $sum_chances);

            $ch_before = 0;
            $ch_after = 0;
            $choosen_cell = 0;

            foreach ($population as $individual) {
                $ch_before = $ch_after;
                $ch_after += $individual['fitness']['chances'];

                if (($random_number >= $ch_before) && $random_number <= $ch_after) {
                    $population_for_crossover[$i] = $population[$choosen_cell];
                }

                $choosen_cell++;
            }
        }

        var_dump($population_for_crossover);

        $new_population = $this->crossover($population_for_crossover, $acceptable_genes);

        return $new_population;
    }

    public function crossover($population, $acceptable_genes) {
        $population_count = count($population);
        $genes_count = count($population[0]) - 1;

        //mutation
        for ($m = 0; $m < 1000; $m++) {
            $mutation[$m] = $m;
        }

        for ($i = 0; $i < $population_count; $i++) {

            unset($population[$i]['fitness']);

            if ($i % 2 != 0) {
                for ($j = 0; $j < count($population[$i]); $j++) {

                    $rand = rand(0, 1);

                    if ($rand == 0) {
                        
                    }

                    $mutation_random = $mutation[array_rand($mutation)];

                    if ($mutation_random == 0) {
                        for ($g = 0; $g < count($acceptable_genes); $g++) {
                            $random_thesis[$g] = $acceptable_genes[$g]['thesis_id'];

                            $mutation_thesis_1 = $random_thesis[array_rand($random_thesis)];
                            $mutation_thesis_2 = $random_thesis[array_rand($random_thesis)];

                            $temp_child = $good_child[$j];
                            $good_child[$j]['thesis_id'] = $bad_child[$j]['thesis_id'];
                            $bad_child[$j]['thesis_id'] = $temp_child['thesis_id'];
                        }
                    }
                }

                $new_population[$i - 1] = $good_child;
                $new_population[$i] = $bad_child;
            }
        }

        return $new_population;
    }

    public function best_individual($population) {
        $population_count = count($population);
        $best_individual = $population[0];

        for ($i = 1; $i < $population_count; $i++) {
            if ($population[$i]['fitness']['collisions'] < $best_individual['fitness']['collisions']) {
                $best_individual = $population[$i];
            }
        }

        return $best_individual;
    }

}
