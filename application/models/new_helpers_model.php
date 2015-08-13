<?php

/**
 *   dmegeorge
 *   ===============
 *   new_helpers_model Model
 *   CI Model 2.1
 *   Date : 8/8/15
 *   Created by: PhpStorm
 */
class new_helpers_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('new_student_model', 'student');
        $this->load->model('new_thesis_model', 'thesis');
    }

    public function get_priority($student_id, $thesis_id)
    {
        $qry = $this->db->select('priority')
            ->from('declarations')
            ->where('student_id', $student_id)
            ->where('thesis_id', $thesis_id)
            ->get();
        $res = $qry->row_array();

        return $res['priority'];
    }

    public function feature_scaling($min, $max, $value, $rev = false)
    {
        if (!$rev) {
            $x = ($value - $min) / ($max - $min);
        } else {
            $x = ($value - $max) / ($min - $max);
        }

        return abs($x);
    }

    public function get_varitites($post)
    {
        $varitites = array(
            'mo_sxolis' => intval($post['mo_sxolis']) / 100,
            'mo_assigned_courses' => intval($post['mo_assigned_courses']) / 100,
            'date_added' => intval($post['date_added']) / 100,
            'bathmos_proodou' => intval($post['bathmos_proodou']) / 100,
            'priority' => intval($post['priority']) / 100
        );

        return $varitites;
    }

    public function fill_declarations_with_data_before_fs($declarations)
    {
        $i = 0;
        foreach ($declarations as $declaration) {
            $date_added = $this->student->get_date_added($declaration['student_id']);
            $bathmos_proodou = $this->student->get_bathmos_proodou($declaration['student_id']);
            $mo_sxolis = $this->student->get_mo_sxolis($declaration['student_id']);
            $mo_assigned_courses = $this->thesis->get_mo_assigned_courses_for_student($declaration['student_id'],
                $declaration['thesis_id']);
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

    public function get_all_values($filled_declarations)
    {
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

    public function fill_declarations_with_fs($filled_declarations, $all_values, $varitites)
    {
        $i = 0;
        foreach ($filled_declarations as $filled_declaration) {
            $filled_declarations[$i]['priority_fs'] = $this->helper->feature_scaling(min($all_values['priority']),
                max($all_values['priority']), $filled_declaration['priority'], true);
            $filled_declarations[$i]['date_added_fs'] = $this->helper->feature_scaling(min($all_values['date_added']),
                max($all_values['date_added']), $filled_declaration['date_added']);
            $filled_declarations[$i]['bathmos_proodou_fs'] = $this->helper->feature_scaling(min($all_values['bathmos_proodou']),
                max($all_values['bathmos_proodou']), $filled_declaration['bathmos_proodou']);
            $filled_declarations[$i]['mo_sxolis_fs'] = $this->helper->feature_scaling(min($all_values['mo_sxolis']),
                max($all_values['mo_sxolis']), $filled_declaration['mo_sxolis']);
            $filled_declarations[$i]['mo_assigned_courses_fs'] = $this->helper->feature_scaling(min($all_values['mo_assigned_courses']),
                max($all_values['mo_assigned_courses']), $filled_declaration['mo_assigned_courses']);

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

    public function get_acceptable_genes($declarations)
    {
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

    public function create_first_individual($filled_declarations_with_fs)
    {
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

    public function get_single_values_of_genes($acceptable_genes)
    {
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

    public function create_first_population($population_number, $single_values, $acceptable_genes)
    {

        for ($i = 1; $i < $population_number; $i++) {
            for ($j = 0; $j < count($single_values['student_id']); $j++) {
                $random_student_id_key = array_rand($single_values['student_id'], 1);
                $random_thesis_id_key = array_rand($single_values['thesis_id'], 1);
                $random_priority_id_key = array_rand($single_values['priority'], 1);

                $individuals[$i][$j]['student_id'] = $single_values['student_id'][$random_student_id_key];
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
            }
        }

        return $individuals;
    }

    public function get_fitness_per_individual($population, $acceptable_genes)
    {
        $population_count = count($population);
        $i = 0;
        foreach ($population as $individual) {
            $population[$i]['fitness']['collisions'] = $this->check_collisions_per_individual($individual);
            $population[$i]['fitness']['acceptable_genes'] = $this->check_acceptable_genes_per_individual($individual,
                $acceptable_genes);
            $population[$i]['fitness']['sum_assesment'] = $this->get_sum_assesment_per_individual($individual);
            $i++;
        }

        $max_collisions = $population[0]['fitness']['collisions'];
        $min_collisions = $population[0]['fitness']['collisions'];

        $max_acceptable_genes = $population[0]['fitness']['acceptable_genes'];
        $min_acceptable_genes = $population[0]['fitness']['acceptable_genes'];

        $max_sum_assesment = $population[0]['fitness']['sum_assesment'];
        $min_sum_assesment = $population[0]['fitness']['sum_assesment'];

        for ($i = 1; $i < $population_count; $i++) {
            if ($population[$i]['fitness']['collisions'] > $max_collisions) {
                $max_collisions = $population[$i]['fitness']['collisions'];
            }
            if ($population[$i]['fitness']['collisions'] < $min_collisions) {
                $min_collisions = $population[$i]['fitness']['collisions'];
            }
            if ($population[$i]['fitness']['acceptable_genes'] > $max_acceptable_genes) {
                $max_acceptable_genes = $population[$i]['fitness']['acceptable_genes'];
            }
            if ($population[$i]['fitness']['acceptable_genes'] < $min_acceptable_genes) {
                $min_acceptable_genes = $population[$i]['fitness']['acceptable_genes'];
            }
            if ($population[$i]['fitness']['collisions'] > $max_sum_assesment) {
                $max_sum_assesment = $population[$i]['fitness']['collisions'];
            }
            if ($population[$i]['fitness']['sum_assesment'] < $min_sum_assesment) {
                $min_sum_assesment = $population[$i]['fitness']['sum_assesment'];
            }
        }

        $all_individuals_sum = 0;

        $i = 0;
        foreach ($population as $individual) {
            $population[$i]['fitness']['fs_collisions'] = $this->helper->feature_scaling($min_collisions,
                $max_collisions, $population[$i]['fitness']['collisions'], true);
            $population[$i]['fitness']['fs_acceptable_genes'] = $this->helper->feature_scaling($min_acceptable_genes,
                $max_acceptable_genes, $population[$i]['fitness']['acceptable_genes']);
            $population[$i]['fitness']['fs_sum_assesment'] = $this->helper->feature_scaling($min_sum_assesment,
                $max_sum_assesment, $population[$i]['fitness']['sum_assesment']);

            $population[$i]['fitness']['total_fitness'] = $population[$i]['fitness']['fs_collisions'] + $population[$i]['fitness']['fs_acceptable_genes'] + $population[$i]['fitness']['fs_sum_assesment'] * 0.5;

            $all_individuals_sum += $population[$i]['fitness']['total_fitness'];
            $i++;
        }

        $i = 0;
        foreach ($population as $individual) {
            $population[$i]['fitness']['chances'] = round($population[$i]['fitness']['total_fitness'] / $all_individuals_sum * 100000000);
            $i++;
        }

        function sort_by_chances($a, $b)
        {
            return $a['fitness']["chances"] - $b['fitness']["chances"];
        }

        //usort($population, "sort_by_chances");

        return $population;
    }

    public function check_collisions_per_individual($individual = array())
    {
        $individual_cells_count = count($individual);
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

    public function check_acceptable_genes_per_individual($individual = array(), $acceptable_genes)
    {
        $individual_cells_count = count($individual) - 1;
        $genes_count = count($acceptable_genes);
        $acceptable_genes_count = 0;

        for ($i = 0; $i < $individual_cells_count; $i++) {
            for ($k = 0; $k < $genes_count; $k++) {
                if ($individual[$i]['student_id'] == $acceptable_genes[$k]['student_id'] && $individual[$i]['thesis_id'] == $acceptable_genes[$k]['thesis_id'] && $individual[$i]['priority'] == $acceptable_genes[$k]['priority']) {
                    $acceptable_genes_count++;
                }
            }
        }

        return $acceptable_genes_count;
    }

    public function get_sum_assesment_per_individual($individual = array())
    {
        $individual_cells_count = count($individual) - 1;
        $sum = 0;

        for ($i = 0; $i < $individual_cells_count; $i++) {
            $sum += $individual[$i]['assessment'];
        }

        return $sum;
    }

    public function get_sum_single_chances($population = array())
    {
        $single_chances = array();

        foreach ($population as $individual) {
            array_push($single_chances, $individual['fitness']['chances']);
        }

        return $single_chances;
    }

    //roullete!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    public function roullete_selection($population = array(), $population_count, $sum_chances)
    {
        $genes_count = count($population[0]) - 1;
        $new_population_for_selection = array();
        $sum_chances = array_sum($sum_chances);
        $random_number = rand(0, $sum_chances);

        $i = 0;
        $ch_before = 0;
        $ch_after = 0;
        $keys_found = 0;

        for ($i = 0; $i < $population_count; $i++) {
            $random_number = rand(0, $sum_chances);
            //echo "<br/> Random number: " . $random_number . "<br/>";

            $ch_before = 0;
            $ch_after = 0;
            $individual_cell = 0;
            foreach ($population as $individual) {
                $ch_before = $ch_after;
                $ch_after += $individual['fitness']['chances'];

                if (($random_number >= $ch_before) && $random_number <= $ch_after) {
                    //echo "<br/>!!!!! found: " . $individual_cell . "<br/>";
                    $new_population_for_selection[$i] = $population[$individual_cell];
                }

                //echo $individual_cell . " min limit: " . $ch_before . " - max limit: " . $ch_after . "<br/>";
                $individual_cell++;
            }

            /*
            if ($i % 2 != 0) {
                $merge_1 = $new_population_for_selection[$i - 1];
                $merge_2 = $new_population_for_selection[$i];

                for ($i = 0; $i < $genes_count; $i++) {
                    
                }
                var_dump($merge_1);
                var_dump($merge_2);
            }
             * 
             */

            //echo "<br/>Next cell -------------<br/>";
        }

        return $new_population_for_selection;

        //var_dump($new_population_for_selection);
    }

}

/*
 * 
 * 
 * 
  //PAME GIA TO TOURNAMENT SELECTION TO KALO
  //tha ftiaksw ena array me ola ta noumera tou population, diladi apo to 0 mexri to 99 stin periptosi mas me population 100
  //tha dialegw 2 random arithmous apo to array, tous opoious tha tous xrisimopoihsw gia na sigkrinw ta individuals
  //an px mou epistrepsei random to value 3 kai 87 tha psaksw sto population to individuals[3] kai individuals[87] kai tha ta sigkrinw
  //meta tha vgazw apo to array tous arithmous pou xrisimopoihsa

  public function tournament_selection($individuals = array(), $population_count) {
  $new_population = array();

  //prepei to count na einai zigo giati gamietai to simpan re file an meinei enas arithmos kai paw na dialeksw 2 random apo autous. PATER PASTITSIOS
  for ($i = 0; $i < $population_count; $i++) {
  $random_draft[$i] = $i;
  }

  //dialegw 2 tixaious arithmous kai pairnw ta values gia na sigkrinw ta antistoixa individuals sto population kai na valw to kalitero se neo array
  for ($i = 0; $i < ($population_count) / 2; $i++) {
  if (!empty($random_draft)) {
  $random_key_1 = array_rand($random_draft, 1);
  unset($random_draft[$random_key_1]);

  $random_key_2 = array_rand($random_draft, 1);
  unset($random_draft[$random_key_2]);

  $individual_1 = $individuals[$random_key_1];
  $individual_2 = $individuals[$random_key_2];

  //vres to individual to kalo kai valto sto array. to allo poulo
  //edw ginetai ourakotagkika giati kanw polla if eno prepei na sigkrinw enan arithmo mono to fitness
  if ($individual_1['fitness']['acceptable_genes'] >= $individual_2['fitness']['acceptable_genes']) {
  $new_population[$i] = $individual_1;
  } else {
  $new_population[$i] = $individual_2;
  }
  }
  }


  for ($i = 0; $i < $population_count; $i++) {
  $random_draft[$i] = $i;
  }

  //dialegw 2 tixaious arithmous kai pairnw ta values gia na sigkrinw ta antistoixa individuals sto population kai na valw to kalitero se neo array
  for ($i = (($population_count) / 2); $i < ($population_count); $i++) {

  if (!empty($random_draft)) {
  $random_key_1 = array_rand($random_draft, 1);
  unset($random_draft[$random_key_1]);

  $random_key_2 = array_rand($random_draft, 1);
  unset($random_draft[$random_key_2]);

  $individual_1 = $individuals[$random_key_1];
  $individual_2 = $individuals[$random_key_2];

  //vres to individual to kalo kai valto sto array. to allo poulo
  //edw ginetai ourakotagkika giati kanw polla if eno prepei na sigkrinw enan arithmo mono to fitness
  if ($individual_1['fitness']['collisions'] <= $individual_2['fitness']['collisions']) {
  $new_population[$i] = $individual_1;
  } else {
  $new_population[$i] = $individual_2;
  }
  }
  }

  return $new_population;
  }
 * 
 * 
 */