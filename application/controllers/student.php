<?php

/**
 *   dmegeorge
 *   ==================
 *   Student Controller
 *   CI Controller 2.1
 *   Date : 8/1/15
 *   Created by: PhpStorm
 */
class Student extends MY_Controller
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
        redirect('student/thesis_declaration');
    }

    public function thesis_declaration()
    {
        /*
         * Θα τραβάει από τα priority του department settings το πόσα declaration μπορεί να κάνει
         * Αν έβαλε ο dpt manage priority 5, ο student θα βάζει τα thesis με σειρά προτίμησης 1-5
         */


        $crud = new grocery_CRUD();
        $crud->set_table('declarations');
        $crud->set_subject('Declaration');
        $crud->fields('thesis_id', 'priority');
        $crud->display_as('priority','Priority (1-10)');
        $crud->set_relation('thesis_id','thesis','title');

        $crud->unset_columns('teacher_id','student_id');
        $crud->where('student_id', $this->user_data[0]->uacc_id); //get only his thesis
        $crud->callback_insert(array($this, 'custom_student_insert_decl_callback'));

        $output = $crud->render();
        $this->view_data['output'] = $output;
        $this->load->template('student/student_view', $this->view_data);
    }

    public function custom_student_insert_decl_callback($post_array)
    {
        $post_array['student_id'] = $this->user_data[0]->uacc_id;
        $post_array['date_created'] = date('Y-m-d H:i:s');

        return $this->db->insert('declarations', $post_array);
    }
}

?>