<?php

/**
 *   dmegeorge
 *   ==================
 *   dptmanager Controller
 *   CI Controller 2.1
 *   Date : 8/1/15
 *   Created by: PhpStorm
 */
class Dptmanager extends MY_Controller {

    var $view_data;
    var $user_data;

    public function __construct() {
        parent::__construct();
        $this->user_data = $this->flexi_auth->get_user_by_identity()->result();
        //var_dump($this->user_data[0]);
    }

    public function index() {
        $this->load->template('dptmanager/dptmanager_view', $this->view_data);
    }

    public function student_management() {
        $crud = new grocery_CRUD();
        $crud->set_table('user_accounts');
        $crud->set_subject('Student');
        $crud->set_relation('department_id', 'departments', 'name');
        $crud->fields('uacc_email', 'uacc_username', 'uacc_password', 'uacc_active', 'uacc_suspend', 'bathmos_proodou');
        $crud->unset_columns('uacc_password', 'uacc_ip_address', 'uacc_salt', 'uacc_activation_token', 'uacc_forgotten_password_token', 'uacc_forgotten_password_expire', 'uacc_update_email_token', 'uacc_update_email');
        $crud->where('uacc_group_fk', 3); //get only students
        $crud->where('department_id', $this->user_data[0]->department_id); //get only his department data
        $crud->callback_insert(array($this, 'custom_user_insert_stu_callback'));

        $output = $crud->render();
        $this->view_data['output'] = $output;
        $this->load->template('dptmanager/dptmanager_view', $this->view_data);
    }

    public function teacher_management() {
        $crud = new grocery_CRUD();
        $crud->set_table('user_accounts');
        $crud->set_subject('Teacher');
        $crud->set_relation('department_id', 'departments', 'name');
        $crud->fields('uacc_email', 'uacc_username', 'uacc_password', 'uacc_active', 'uacc_suspend');
        $crud->unset_columns('uacc_password', 'uacc_ip_address', 'uacc_salt', 'uacc_activation_token', 'uacc_forgotten_password_token', 'uacc_forgotten_password_expire', 'uacc_update_email_token', 'uacc_update_email');
        $crud->where('uacc_group_fk', 2); //get only teachers
        $crud->where('department_id', $this->user_data[0]->department_id); //get only his department data
        $crud->callback_insert(array($this, 'custom_user_insert_tea_callback'));

        $output = $crud->render();
        $this->view_data['output'] = $output;
        $this->load->template('dptmanager/dptmanager_view', $this->view_data);
    }

    public function course_management() {
        $crud = new grocery_CRUD();
        $crud->set_table('courses');
        $crud->set_subject('Course');
        $crud->fields('course_code', 'name');
        $crud->unset_columns('department_id', 'id');
        $crud->where('department_id', $this->user_data[0]->department_id); //get only his department data
        $crud->callback_insert(array($this, 'custom_course_insert_callback'));

        $output = $crud->render();
        $this->view_data['output'] = $output;
        $this->load->template('dptmanager/dptmanager_view', $this->view_data);
    }

    public function assign_grades_to_students() {
        $crud = new grocery_CRUD();
        $crud->set_table('grades');
        $crud->set_subject('Grades');
        $crud->fields('course_id', 'student_id', 'grade');
        $crud->unset_columns('department_id', 'id');

        $crud->set_relation('student_id', 'user_accounts', 'uacc_email', array('department_id' => $this->user_data[0]->department_id, 'uacc_group_fk' => 3));
        $crud->set_relation('course_id', 'courses', 'name', array('department_id' => $this->user_data[0]->department_id));
        $crud->fields('student_id', 'course_id', 'grade');

        $crud->where('grades.department_id', $this->user_data[0]->department_id); //get only his department data
        $crud->callback_insert(array($this, 'custom_grade_to_student_insert_callback'));

        $output = $crud->render();
        $this->view_data['output'] = $output;
        $this->load->template('dptmanager/dptmanager_view', $this->view_data);
    }

    public function department_settings() {
        //to department id einai unique ston pinaka settings gia na exoume mia eggrafi settings ana dpt
        $crud = new grocery_CRUD();
        $crud->set_table('settings');
        $crud->set_subject('Setting');

        $crud->set_relation('department_id', 'departments', 'name');
        $crud->where('department_id', $this->user_data[0]->department_id); //get only his department data
        //$crud->callback_update(array($this,'custom_user_update_callback'));
        $output = $crud->render();
        $this->view_data['output'] = $output;
        $this->load->template('dptmanager/dptmanager_view', $this->view_data);
    }

    public function custom_grade_to_student_insert_callback($post_array) {
        $post_array['department_id'] = $this->user_data[0]->department_id;
        return $this->db->insert('grades', $post_array);
    }

    public function custom_course_insert_callback($post_array) {
        $post_array['department_id'] = $this->user_data[0]->department_id;

        return $this->db->insert('courses', $post_array);
    }

    public function custom_user_insert_stu_callback($post) {
        return $this->flexi_auth->insert_user($this->input->post('uacc_email'), $this->input->post('uacc_email'), $this->input->post('uacc_password'), array('department_id' => $this->user_data[0]->department_id, 'uacc_group_fk' => 3), 3, 1);
    }

    public function custom_user_insert_tea_callback($post) {
        return $this->flexi_auth->insert_user($this->input->post('uacc_email'), $this->input->post('uacc_email'), $this->input->post('uacc_password'), array('department_id' => $this->user_data[0]->department_id, 'uacc_group_fk' => 2), 2, 1);
    }

    public function show_declarations() {
        $qry = $this->db->select('ua.uacc_id as student_id, ua.uacc_username as student,t.id as thesis_id, t.title as thesis, d.priority')
                ->from('declarations d, user_accounts ua, thesis t')
                ->where('ua.uacc_id = d.student_id')
                ->where('t.id = d.thesis_id')
                ->order_by('ua.uacc_id')
                ->order_by('d.priority')
                ->get();

        $output = $qry->result_array();

        $this->view_data['output'] = $output;

        $this->load->template('dptmanager/declarations_view', $this->view_data);
    }

}

?>