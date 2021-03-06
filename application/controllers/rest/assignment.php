<?php

/**
 *   dmegeorge
 *   ==================
 *   Test Controller
 *   CI Controller 2.1
 *   Date : 8/8/15
 *   Created by: PhpStorm
 */
class Assignment extends MY_Controller {

    var $view_data;
    var $user_data;

    public function __construct() {
        ini_set('max_execution_time', 60);
        parent::__construct();
        $this->user_data = $this->flexi_auth->get_user_by_identity()->result();
    }

    public function get_data() {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('mo_sxolis', 'mo_sxolis', 'trim');
        $this->form_validation->set_rules('mo_assigned_courses', 'mo_assigned_courses', 'trim');
        $this->form_validation->set_rules('date_added', 'date_added', 'trim');
        $this->form_validation->set_rules('bathmos_proodou', 'bathmos_proodou', 'trim');
        $this->form_validation->set_rules('priority', 'priority', 'trim');

        $this->load->template('dptmanager/assignment_get_data_view', $this->view_data);

        if ($this->form_validation->run() == FALSE) {
            
        } else {
            $this->get_declarations($this->user_data[0]->department_id);
        }
    }

    public function get_declarations() {
        $dpt_id = $this->get('department_id');

        $this->load->model('new_helpers_model', 'helper');
        $this->load->model('new_student_model', 'student');
        $this->load->model('new_thesis_model', 'thesis');
        $this->load->model('new_declarations_model', 'declarations');

        $population_number = $this->input->post('population_number');
        if ($population_number % 2 != 0) {
            $population_number++;
        }

        $time_start = microtime(true);

        //fere ola ta declarations apo tin vasi
        $declarations = $this->declarations->get_all_declarations($dpt_id);
        $declarations = array_values($declarations);

        //vale sto array ton declarations, vathmous, mesous orous, etos eisagogis, bathmo proodou ktlp
        $filled_declarations_before_fs = $this->helper->fill_declarations_with_data_before_fs($declarations);
        $filled_declarations_before_fs = array_values($filled_declarations_before_fs);

        //fere se array ola ta values ton vathmon, meso orwn, bathmon proodou ktlp
        $all_values = $this->helper->get_all_values($filled_declarations_before_fs);

        //posoi foitites prepei na paroun ptixiaki
        $students_to_get_thesis_number = count($all_values['student_id']);

        //fere tis varitites
        $varitites = array(
            'mo_sxolis' => intval($this->get['mo_sxolis']) / 100,
            'mo_assigned_courses' => intval($this->get['mo_assigned_courses']) / 100,
            'date_added' => intval($this->get['date_added']) / 100,
            'bathmos_proodou' => intval($this->get['bathmos_proodou']) / 100,
            'priority' => intval($this->get['priority']) / 100,
            'terminate' => intval($this->get['mo_sxolis']) + intval($this->get['mo_assigned_courses']) + intval($this->get['date_added']) + intval($this->get['bathmos_proodou']) + intval($this->get['priority'])
        );

        //fere sto array ton declarations, to assesment, mazi me ola ta fs
        $filled_declarations_with_fs = $this->helper->fill_declarations_with_fs($filled_declarations_before_fs, $all_values, $varitites);

        //fere mou ola ta apodekta genes
        $acceptable_genes = $this->helper->get_acceptable_genes($filled_declarations_with_fs);

        //fere se ena array ola ta values apo students, thesis, priority gia na ftiaksw random pramata
        $single_values = $this->helper->get_single_values_of_genes($acceptable_genes);

        //ftiakse to 1o population me random genes. to 1o individual to exoume apo prin
        $population = $this->helper->create_first_population($population_number, $single_values, $acceptable_genes);

        $population = $this->helper->get_population_fitness($population, $acceptable_genes);

        $diff = 0.01;
        $turns = 0;
        $fitness_prev = 0;
        $fitness_curr = 0;
        $break_point = 0;

        while ($break_point != 500) {

            $sum_chances = $this->helper->get_sum_single_chances($population);

            $population = $this->helper->roullete_selection($population, $population_number, $sum_chances, $acceptable_genes, $all_values);

            $population = $this->helper->crossover($population, $acceptable_genes, $all_values);

            $population = $this->helper->get_population_fitness($population, $acceptable_genes);

            $best_individual = $this->helper->best_individual($population, $students_to_get_thesis_number);

            $fitness_curr = $best_individual['fitness']['total_fitness'];

            if (abs($fitness_prev - $fitness_curr) < $diff) {
                $break_point ++;
                if ($best_individual['fitness']['collisions'] != 0) {
                    $break_point = 0;
                }
            } else {
                $break_point = 0;
            }

            if ($best_individual['fitness']['collisions'] == 0 && $best_individual['fitness']['acceptable_genes'] == $students_to_get_thesis_number) {
                //echo "found";
                //break;
            }
            
            if($turns == 500) {
                //break;
            }
            
            $fitness_prev = $fitness_curr;
            $turns++;
        }

        $time_end = microtime(true);
        $execution_time = $time_end - $time_start;

        $solution = $this->declarations->show_results($best_individual, $acceptable_genes);

        $first_choises = 0;
        foreach ($best_individual as $key => $val) {
            if ($key == "fitness") {
                break;
            } else {
                foreach ($acceptable_genes as $each_gene) {
                    if ($each_gene['student_id'] == $key && $each_gene['thesis_id'] == $val && $each_gene['priority'] == 1) {
                        $first_choises++;
                    }
                }
            }
        }

        $general_results['acceptable_genes'] = $this->helper->check_acceptable_genes_per_individual($best_individual, $acceptable_genes);
        $general_results['collisions'] = $best_individual['fitness']['collisions'];
        $general_results['total_fitness'] = $best_individual['fitness']['total_fitness'];
        $general_results['turns'] = $turns;
        $general_results['execution_time'] = $execution_time;
        $general_results['first_choises'] = $first_choises;

        $this->view_data['solution'] = $solution;
        $this->view_data['general_results'] = $general_results;

        $this->response($this->view_data, 200);
    }

}
