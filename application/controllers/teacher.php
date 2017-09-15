<?php

/**
 *   dmegeorge
 *   ==================
 *   teacher Controller
 *   CI Controller 2.1
 *   Date : 8/1/15
 *   Created by: PhpStorm
 */
class Teacher extends MY_Controller {

    var $view_data;
    var $user_data;
    var $settings;

    public function __construct() {
        parent::__construct();
        $this->user_data = $this->flexi_auth->get_user_by_identity()->result();
        $this->load->model('settings_model');
        $this->settings = $this->settings_model->get_settings($this->user_data[0]->department_id);
    }

    public function index() {
        redirect('teacher/thesis_management');
    }

    public function thesis_management() {
        $cur_date = date('Y-m-d');
       // if ($cur_date < $this->settings['teacher_add_thesis_before'] || $cur_date > $this->settings['teacher_add_thesis_after']) {
            //$this->session->set_flashdata('error', 'Date error');
            //$this->load->template('teacher/teacher_view', $this->view_data);
        //} else {

            $crud = new grocery_CRUD();
            $crud->set_table('thesis');
            $crud->set_subject('Thesis');
            $crud->fields('title', 'description');
            $crud->unset_columns('teacher_id');
            $crud->where('teacher_id', $this->user_data[0]->uacc_id); //get only his thesis
            $crud->callback_insert(array($this, 'custom_teacher_insert_thesis_callback'));

            $output = $crud->render();
            $this->view_data['output'] = $output;
            $this->load->template('teacher/teacher_view', $this->view_data);
        //}
    }

    public function courses_per_thesis_management() {
        $crud = new grocery_CRUD();
        $crud->set_table('assigned_courses_to_thesis');
        $crud->set_subject('Assigned Course to Thesis');
        $crud->set_relation('thesis_id', 'thesis', 'title');
        $crud->set_relation('course_id', 'courses', 'name'); // *******
        $crud->unset_columns('teacher_id');

        $crud->fields('thesis_id', 'course_id');

        //$crud->where('assigned_courses_to_thesis.teacher_id', $this->user_data[0]->uacc_id); //get only his thesis
        $crud->callback_insert(array($this, 'custom_teacher_insert_assign_callback'));

        $output = $crud->render();
        $this->view_data['output'] = $output;
        $this->load->template('teacher/teacher_view', $this->view_data);
    }

    public function custom_teacher_insert_assign_callback($post_array) {
        $post_array['teacher_id'] = $this->user_data[0]->uacc_id;

        return $this->db->insert('assigned_courses_to_thesis', $post_array);
    }

    public function custom_teacher_insert_thesis_callback($post_array) {
        $qry = $this->db->select('id')->from('thesis')->where('teacher_id', $this->user_data[0]->uacc_id)->get();
        $res = $qry->row_array();

        $count_teacher_thesis = $qry->num_rows();
        //if ($this->settings['teacher_max_thesis'] >= $count_teacher_thesis) {
            //$this->session->set_flashdata('error', 'ERROR OCCURED : Max Thesis per teacher is ' . $count_teacher_thesis);
            //$this->load->template('message_view', $this->view_data);
            //return FALSE;
       // } else {
            $post_array['teacher_id'] = $this->user_data[0]->uacc_id;
            $post_array['date_created'] = date('Y-m-d H:i:s');

            return $this->db->insert('thesis', $post_array);
       // }
    }

}

?>