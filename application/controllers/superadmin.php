<?php

/**
 *   dmegeorge
 *   ==================
 *   Superadmin Controller
 *   CI Controller 2.1
 *   Date : 8/1/15
 *   Created by: PhpStorm
 */
class Superadmin extends MY_Controller
{

    var $view_data;
    var $user_data;

    public function __construct()
    {
        parent::__construct();
        $this->user_data = $this->flexi_auth->get_user_by_identity()->result();

    }

    public function index()
    {
        $this->load->template('superadmin/superadmin_view', $this->view_data);

    }

    public function dpt_management()
    {
        $crud = new grocery_CRUD();
        $crud->set_table('departments');
        $output = $crud->render();
        $this->view_data['output'] = $output;
        $this->load->template('superadmin/superadmin_view', $this->view_data);

    }

    public function dpt_administrator_management()
    {
        $crud = new grocery_CRUD();
        $crud->set_table('user_accounts');
        $crud->set_relation('uacc_group_fk', 'user_groups', 'ugrp_name');
        $crud->set_relation('department_id', 'departments', 'name');
        $crud->fields('uacc_group_fk', 'uacc_email', 'uacc_username', 'uacc_password', 'uacc_active', 'uacc_suspend',
            'department_id');
        $crud->unset_columns('uacc_password', 'uacc_ip_address', 'uacc_salt', 'uacc_activation_token',
            'uacc_forgotten_password_token', 'uacc_forgotten_password_expire', 'uacc_update_email_token',
            'uacc_update_email');
        //$crud->where('uacc_group_fk',4);
        $crud->callback_insert(array($this, 'custom_user_insert_callback'));

        $output = $crud->render();
        $this->view_data['output'] = $output;
        $this->load->template('superadmin/superadmin_view', $this->view_data);

    }

    public function settings()
    {
        $crud = new grocery_CRUD();
        $crud->set_table('settings');
        $crud->set_relation('department_id', 'departments', 'name');
        //$crud->callback_update(array($this,'custom_user_update_callback'));
        $output = $crud->render();
        $this->view_data['output'] = $output;
        $this->load->template('superadmin/superadmin_view', $this->view_data);

    }

    public function custom_user_insert_callback($post)
    {
        return $this->flexi_auth->insert_user($this->input->post('uacc_email'), $this->input->post('uacc_email'),
            $this->input->post('uacc_password'), array('department_id' => $this->input->post('department_id')), $this->input->post('uacc_group_fk'), 1);
    }

    public function custom_user_update_callback($post)
    {
        $this->flexi_auth->update_user(user_id, user_data);
    }
}

?>