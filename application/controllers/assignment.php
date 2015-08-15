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

    public function __construct()
    {
        parent::__construct();
        $this->user_data = $this->flexi_auth->get_user_by_identity()->result();
        //var_dump($this->user_data[0]);
    }

    public function get_data()
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('mo_sxolis', 'mo_sxolis', 'trim');
        $this->form_validation->set_rules('mo_assigned_courses', 'mo_assigned_courses', 'trim');
        $this->form_validation->set_rules('date_added', 'date_added', 'trim');
        $this->form_validation->set_rules('bathmos_proodou', 'bathmos_proodou', 'trim');
        $this->form_validation->set_rules('priority', 'priority', 'trim');


        if ($this->form_validation->run() == FALSE)
        {
            $this->load->template('dptmanager/assignment_get_data_view', $this->view_data);
        }
        else
        {
            $this->get_declarations($this->user_data[0]->department_id);
        }
    }
    public function get_declarations($dpt_id = 8) {
        $this->load->model('new_helpers_model', 'helper');
        $this->load->model('new_student_model', 'student');
        $this->load->model('new_thesis_model', 'thesis');
        $this->load->model('new_declarations_model', 'declarations');

        $population_number = $this->input->post('population_number');

        $filled_declarations = array();
        $all_priorities = array();
        $all_dates_added = array();
        $all_bathmoi_proodou = array();
        $all_mo_sxolis = array();
        $all_mo_assigned_courses = array();
        $acceptable_genes = array();
        $population = array();

        //fere ola ta declarations apo tin vasi
        $declarations = $this->declarations->get_all_declarations($dpt_id);

        //vale sto array ton declarations, vathmous, mesous orous, etos eisagogis, bathmo proodou ktlp
        $filled_declarations_before_fs = $this->helper->fill_declarations_with_data_before_fs($declarations);

        //fere se array ola ta values ton vathmon, meso orwn, bathmon proodou ktlp
        $all_values = $this->helper->get_all_values($filled_declarations_before_fs);

        //fere tis varitites
        $varitites = $this->helper->get_varitites($this->input->post());

        //fere sto array ton declarations, to assesment, mazi me ola ta fs
        $filled_declarations_with_fs = $this->helper->fill_declarations_with_fs($filled_declarations_before_fs, $all_values, $varitites);

        //fere mou ola ta apodekta genes
        $acceptable_genes = $this->helper->get_acceptable_genes($filled_declarations_with_fs);

        //ftiakse mou to prwto individual me tis dilwseis me prioroty 1
        $population[0] = $this->helper->create_first_individual($filled_declarations_with_fs);

        //fere se ena array ola ta values apo students, thesis, priority gia na ftiaksw random pramata
        $single_values = $this->helper->get_single_values_of_genes($acceptable_genes);

        //ftiakse to 1o population me random genes. to 1o individual to exoume apo prin
        $individuals = $this->helper->create_first_population($population_number, $single_values, $acceptable_genes);

        //merge to 1o population mazi me to 1o individual
        $population = array_merge($population, $individuals);
        
        //dinw se kathe individual fitness
        $population = $this->helper->get_fitness_per_individual($population, $acceptable_genes);
        
        //pairnw to sum apo fitness gia na ipologisw ta chances pou exei to kathe individual
        $sum_chances = $this->helper->get_sum_single_chances($population);
        
        //var_dump($sum_chances);
        
        //to neo population meta apo roullete
        $population = $this->helper->roullete_selection($population, $population_number, $sum_chances);

        //var_dump($population);
        //var_dump($varitites);
        $this->view_data['population'] = $population;
        $this->load->template('dptmanager/assignment_view', $this->view_data);

    }

}
