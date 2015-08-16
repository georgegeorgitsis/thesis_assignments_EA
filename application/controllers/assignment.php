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


        if ($this->form_validation->run() == FALSE) {
            $this->load->template('dptmanager/assignment_get_data_view', $this->view_data);
        } else {
            $this->get_declarations($this->user_data[0]->department_id);
        }
    }

    public function get_declarations($dpt_id = 8) {
        $this->load->model('new_helpers_model', 'helper');
        $this->load->model('new_student_model', 'student');
        $this->load->model('new_thesis_model', 'thesis');
        $this->load->model('new_declarations_model', 'declarations');

        $population_number = $this->input->post('population_number');
        if ($population_number % 2 != 0) {
            $population_number++;
        }

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
        $varitites = $this->helper->get_varitites($this->input->post());

        //fere sto array ton declarations, to assesment, mazi me ola ta fs
        $filled_declarations_with_fs = $this->helper->fill_declarations_with_fs($filled_declarations_before_fs, $all_values, $varitites);

        //fere mou ola ta apodekta genes
        $acceptable_genes = $this->helper->get_acceptable_genes($filled_declarations_with_fs);

        //fere se ena array ola ta values apo students, thesis, priority gia na ftiaksw random pramata
        $single_values = $this->helper->get_single_values_of_genes($acceptable_genes);

        sort($single_values['student_id']);

        //ftiakse mou to prwto individual me tis dilwseis me priority 1
        //$population[0] = $this->helper->create_first_individual($filled_declarations_with_fs);
        //ftiakse to 1o population me random genes. to 1o individual to exoume apo prin
        $population = $this->helper->create_first_population($population_number, $single_values, $acceptable_genes);

        $population = $this->helper->get_population_fitness($population, $acceptable_genes);

        //dinw se kathe individual fitness
        //$population = $this->helper->get_fitness_per_individual($population, $acceptable_genes);
        //merge to 1o population mazi me to 1o individual
        //$population = array_merge($population, $individuals);

        $acceptable_genes_number = 0;
        $collisions_number = 1;
        $turns = 0;
        $total_fitness_before = 0;
        $total_fitness_after = 1;

        while (($collisions_number != 0)) {

            if ($turns != 0) {
                $total_fitness_before = $total_fitness_after;
            }
            $sum_chances = $this->helper->get_sum_single_chances($population);

            $population = $this->helper->roullete_selection($population, $population_number, $sum_chances, $acceptable_genes);

            $population = $this->helper->get_population_fitness($population, $acceptable_genes);

            $best_individual = $this->helper->best_individual($population);
            //$acceptable_genes_number = $best_individual['fitness']['acceptable_genes'];
            $collisions_number = $best_individual['fitness']['collisions'];
            $total_fitness_after = $best_individual['fitness']['total_fitness'];

            $turns++;
        }

        $this->view_data['solution'] = $best_individual;
        $this->load->template('dptmanager/assignment_view', $this->view_data);
        
        //var_dump($best_individual);

        echo "acceptable: " . $this->helper->check_acceptable_genes_per_individual($best_individual, $acceptable_genes);
        echo "<br/>";
        echo "collisions: " . $collisions_number;
    }

}
