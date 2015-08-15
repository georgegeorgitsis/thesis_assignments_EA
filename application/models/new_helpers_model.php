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
            'priority' => intval($post['priority']) / 100
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

            $filled_declarations[$i]['priority_fs_epi_varititas'] = $filled_declarations[$i]['priority_fs'] * $varitites['priority'];
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

    public function check_collisions_per_individual($individual = array()) {
        $individual_cells_count = count($individual) - 1;
        $collisions_found = 0;

        for ($i = 0; $i < $individual_cells_count; $i++) {
            $thesis_id = $individual[$i]['thesis_id'];

            for ($j = $i; $j < $individual_cells_count; $j++) {
                if ($i != $j) {
                    if ($individual[$j]['thesis_id'] == $thesis_id) {
                        $collisions_found++;
                    }
                }
            }
        }

        return $collisions_found;
    }

    public function check_acceptable_genes_per_individual($individual = array(), $acceptable_genes) {
        $individual_cells_count = count($individual) - 1;
        $genes_count = count($acceptable_genes);
        $acceptable_genes_count = 0;

        for ($i = 0; $i < $individual_cells_count; $i++) {
            if ($individual[$i]['assessment'] != 0) {
                $acceptable_genes_count++;
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

    public function create_first_population($population_number, $single_values, $acceptable_genes) {
        for ($i = 0; $i < $population_number; $i++) {
            $j = 0;
            foreach ($single_values['student_id'] as $each_student) {
                $random_thesis_id_key = array_rand($single_values['thesis_id'], 1);
                $random_priority_id_key = array_rand($single_values['priority'], 1);

                $individuals[$i][$j]['student_id'] = $each_student;
                $individuals[$i][$j]['thesis_id'] = $single_values['thesis_id'][$random_thesis_id_key];
                $individuals[$i][$j]['priority'] = $single_values['priority'][$random_priority_id_key];

                //des an exei kala genes kai dose to assesment. allios dose 0
                for ($k = 0; $k < count($single_values['student_id']); $k++) {
                    $individuals[$i][$j]['assessment'] = 0;
                    if ($individuals[$i][$j]['student_id'] == $acceptable_genes[$k]['student_id'] && $individuals[$i][$j]['thesis_id'] == $acceptable_genes[$k]['thesis_id'] && $individuals[$i][$j]['priority'] == $acceptable_genes[$k]['priority']) {
                        $individuals[$i][$j]['assessment'] = $acceptable_genes[$k]['assessment'];
                        break;
                    }
                }
                $j++;
            }

            $individuals[$i] = $this->get_individual_fitness_info($individuals[$i], $acceptable_genes);
        }

        return $individuals;
    }

    public function get_individual_fitness_info($individual, $acceptable_genes) {
        $students_count = count($individual) - 1;

        $individual['fitness']['acceptable_genes'] = $this->check_acceptable_genes_per_individual($individual, $acceptable_genes);
        $individual['fitness']['fs_acceptable_genes'] = $this->helper->feature_scaling(0, $students_count, $individual['fitness']['acceptable_genes']);
        $individual['fitness']['sum_assesment'] = $this->get_sum_assesment_per_individual($individual);

        if ($individual['fitness']['fs_acceptable_genes'] == 1) {
            $individual['fitness']['collisions'] = $this->check_collisions_per_individual($individual);
        } else {
            $individual['fitness']['collisions'] = 0;
        }

        $individual['fitness']['fs_collisions'] = $this->helper->feature_scaling(0, $students_count, $individual['fitness']['collisions']);
        $individual['fitness']['total_fitness'] = $individual['fitness']['fs_acceptable_genes'] + $individual['fitness']['fs_collisions'];

        return $individual;
    }

    public function get_individual_sum_fitness($population) {
        $population_count = count($population);

        $max_sum_assesment = $population[0]['fitness']['sum_assesment'];
        $min_sum_assesment = $population[0]['fitness']['sum_assesment'];
        $sum_population_assesments = 0;
        for ($i = 1; $i < $population_count; $i++) {
            if ($population[$i]['fitness']['sum_assesment'] < $min_sum_assesment) {
                $min_sum_assesment = $population[$i]['fitness']['sum_assesment'];
            }
            if ($population[$i]['fitness']['sum_assesment'] > $max_sum_assesment) {
                $max_sum_assesment = $population[$i]['fitness']['sum_assesment'];
            }
            $sum_population_assesments += $population[$i]['fitness']['sum_assesment'];
        }

        for ($i = 0; $i < $population_count; $i++) {
            if ($min_sum_assesment == $max_sum_assesment) {
                $population[$i]['fitness']['fs_sum_assesment'] = 0;
            } else {
                $population[$i]['fitness']['fs_sum_assesment'] = $this->helper->feature_scaling($min_sum_assesment, $max_sum_assesment, $population[$i]['fitness']['sum_assesment']);
            }
            $population[$i]['fitness']['total_fitness'] += $population[$i]['fitness']['fs_sum_assesment'];
            $population[$i]['fitness']['chances'] = round($population[$i]['fitness']['total_fitness'] / $sum_population_assesments * 100000);
        }

        return $population;
    }

    /*
      public function get_fitness_per_individual($population, $acceptable_genes) {
      $population_count = count($population);
      $students_count = count($population[0]) - 1;

      $i = 0;
      foreach ($population as $individual) {
      $population[$i]['fitness']['collisions'] = $this->check_collisions_per_individual($individual);
      $population[$i]['fitness']['acceptable_genes'] = $this->check_acceptable_genes_per_individual($individual, $acceptable_genes);
      $population[$i]['fitness']['sum_assesment'] = $this->get_sum_assesment_per_individual($individual);
      $i++;
      }

      $max_sum_assesment = $population[0]['fitness']['sum_assesment'];
      $min_sum_assesment = $population[0]['fitness']['sum_assesment'];

      for ($i = 0; $i < $population_count; $i++) {
      if ($population[$i]['fitness']['sum_assesment'] > $max_sum_assesment) {
      $max_sum_assesment = $population[$i]['fitness']['sum_assesment'];
      }
      if ($population[$i]['fitness']['sum_assesment'] < $min_sum_assesment) {
      $min_sum_assesment = $population[$i]['fitness']['sum_assesment'];
      }
      }

      $all_individuals_sum = 0;

      $i = 0;
      $min_collisions = 0;
      $max_collisions = $students_count;
      $min_acceptable_genes = 0;
      $max_acceptable_genes = $students_count;

      foreach ($population as $individual) {
      $population[$i]['fitness']['fs_collisions'] = $this->helper->feature_scaling(0, $students_count, $population[$i]['fitness']['collisions'], true);
      $population[$i]['fitness']['fs_acceptable_genes'] = $this->helper->feature_scaling(0, $students_count, $population[$i]['fitness']['acceptable_genes']);

      if ($min_sum_assesment == $max_sum_assesment) {
      $population[$i]['fitness']['fs_sum_assesment'] = 0;
      } else {
      $population[$i]['fitness']['fs_sum_assesment'] = $this->helper->feature_scaling($min_sum_assesment, $max_sum_assesment, $population[$i]['fitness']['sum_assesment']);
      }

      if ($population[$i]['fitness']['fs_acceptable_genes'] != 1) {
      $population[$i]['fitness']['total_fitness'] = $population[$i]['fitness']['fs_acceptable_genes'];
      } else {
      $population[$i]['fitness']['total_fitness'] = $population[$i]['fitness']['fs_acceptable_genes'];

      if ($population[$i]['fitness']['fs_collisions'] != 1) {
      $population[$i]['fitness']['total_fitness'] = $population[$i]['fitness']['fs_acceptable_genes'] + $population[$i]['fitness']['fs_collisions'];
      } else {
      $population[$i]['fitness']['total_fitness'] = $population[$i]['fitness']['fs_acceptable_genes'] + $population[$i]['fitness']['fs_collisions'] + $population[$i]['fitness']['fs_sum_assesment'] * 3;
      }

      }

      $all_individuals_sum += $population[$i]['fitness']['total_fitness'];
      $i++;
      }


      $i = 0;
      foreach ($population as $individual) {
      $population[$i]['fitness']['chances'] = round($population[$i]['fitness']['total_fitness'] / $all_individuals_sum * 100000);
      $i++;
      }

      return $population;
      }
     * 
     */

    public function get_population_fitness($population = array()) {
        $sum_population_fitness = 0;
        foreach ($population as $individual) {
            $sum_population_fitness += $individual['fitness']['total_fitness'];
        }

        return $sum_population_fitness;
    }

    public function get_sum_assesment_per_individual($individual = array()) {
        $individual_cells_count = count($individual) - 1;
        $sum = 0;

        for ($i = 0; $i < $individual_cells_count; $i++) {
            $sum += $individual[$i]['assessment'];
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

//roullete!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
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
                    $new_population[$i] = $population[$choosen_cell];
                }

                $choosen_cell++;
            }

            //crossover
            if ($i % 2 != 0) {
                $mutation_random = rand(0, 1000);

                $individual_1 = $new_population[$i - 1];
                $individual_2 = $new_population[$i];

                for ($k = 0; $k < $genes_count; $k++) {
                    for ($acc = 0; $acc < $acceptable_genes_count; $acc++) {
                        if (($individual_1[$k]['student_id'] == $acceptable_genes[$acc]['student_id']) && ($individual_1[$k]['thesis_id'] == $acceptable_genes[$acc]['thesis_id'])) {
                            if (($individual_2[$k]['student_id'] == $acceptable_genes[$acc]['student_id']) && ($individual_2[$k]['thesis_id'] == $acceptable_genes[$acc]['thesis_id'])) {
                                if ($individual_1[$k]['assessment'] > $individual_2[$k]['assessment']) {
                                    $good_individual[$k] = $individual_1[$k];
                                    $bad_individual[$k] = $individual_2[$k];
                                } else {
                                    $good_individual[$k] = $individual_2[$k];
                                    $bad_individual[$k] = $individual_1[$k];
                                    if ($mutation_random == 0) {
                                        $acceptable_genes_random_1 = rand(0, $acceptable_genes_count - 1);
                                        $acceptable_genes_random_2 = rand(0, $acceptable_genes_count - 1);

                                        $good_individual[$k]['thesis_id'] = $acceptable_genes[$acceptable_genes_random_1]['thesis_id'];
                                        $bad_individual[$k]['thesis_id'] = $acceptable_genes[$acceptable_genes_random_2]['thesis_id'];
                                    }
                                }
                            }
                        } else {
                            if ($individual_1[$k]['assessment'] > $individual_2[$k]['assessment']) {
                                $good_individual[$k] = $individual_1[$k];
                                $bad_individual[$k] = $individual_2[$k];
                            } else {
                                $good_individual[$k] = $individual_2[$k];
                                $bad_individual[$k] = $individual_1[$k];
                            }
                        }
                    }
                }

                $new_population[$i - 1] = $good_individual;
                $new_population[$i] = $bad_individual;

                $new_population[$i - 1] = $this->get_individual_fitness_info($new_population[$i - 1], $acceptable_genes);
                $new_population[$i] = $this->get_individual_fitness_info($new_population[$i], $acceptable_genes);
            }
        }
        
        var_dump($new_population);
        die();
        
        return $new_population;
    }

    public function crossover($selected_individuals, $sum_chances, $acceptable_genes) {
        $genes_count = count($selected_individuals[0]) - 1;
        $i = 0;

        foreach ($selected_individuals as $individual) {
            if ($i % 2 != 0) {
                $individual_1 = $selected_individuals[$i - 1];
                $individual_2 = $selected_individuals[$i];

                for ($k = 0; $k < $genes_count; $k++) {
                    if ($individual_1[$k]['assessment'] > $individual_2[$k]['assessment']) {
                        $good_individual[$k] = $individual_1[$k];
                        $bad_individual[$k] = $individual_2[$k];
                    } else {
                        $good_individual[$k] = $individual_2[$k];
                        $bad_individual[$k] = $individual_1[$k];
                    }
                }

                $new_population[$i - 1] = $good_individual;
                $new_population[$i] = $bad_individual;
            }
            $i++;
        }

        return $new_population;
    }

    public function best_individual($population) {
        $population_count = count($population);
        $best_individual = $population[0];

        for ($i = 1; $i < $population_count; $i++) {
            if ($population[$i]['fitness']['acceptable_genes'] > $best_individual['fitness']['acceptable_genes']) {
                $best_individual = $population[$i];
            }
        }

        return $best_individual;
    }

}

/*
      public function create_first_individual($filled_declarations_with_fs) {
      $individual = array();

      $i = 0;
      foreach ($filled_declarations_with_fs as $declaration) {
      if ($declaration['priority'] == 1) {

      $individual[$i]['student_id'] = $declaration['student_id'];
      $individual[$i]['thesis_id'] = $declaration['thesis_id'];
      $individual[$i]['priority'] = $declaration['priority'];
      $individual[$i]['assessment'] = $declaration['assessment'];

      $i++;
      }
      }

      return $individual;
      }
     * 
     */