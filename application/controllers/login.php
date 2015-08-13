<?php

/**
 * Description of login
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   CategoryName
 * @package    Expression package is undefined on line 15, column 18 in Templates/Scripting/ci-controller.php.
 * @author     Sotiris Ganouris <ganouris@gmail.com>
 * @copyright  2014.09.28 Sotiris Ganouris <ganouris@gmail.com>
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    SVN: $Id$
 * @link       http://about.me/sotirisganouris
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Login extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->auth = new stdClass;
        $this->load->library('flexi_auth');
    }

    public function index()
    {
//$this->flexi_auth->insert_user('super@qq.gr', 'super', 'test123' , array());
        $this->login();
    }

    public function login()
    {
        $this->view_data['page_title'] = 'Login';
        $this->form_validation->set_rules('username', 'Username', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() === false) {
            $this->load->view('login_view', $this->view_data);
        } else {
            $identity = $this->input->post('username', true);
            $password = $this->input->post('password', true);
            $remember_me = ($this->input->post('rememberme') == 1) ? true : false;

            if ($this->flexi_auth->login($identity, $password, $remember_me)) {
                $this->session->set_flashdata('message', $this->flexi_auth->get_messages());
                switch ($this->flexi_auth->get_user_group_id()) {
                    case 1:
                        //superadmin
                        redirect('superadmin/index');
                        break;
                    case 2:
                        redirect('teacher/index');
                        break;
                    case 3:
                        redirect('student/index');
                        break;
                    case 4:
                        redirect('dptmanager/index');
                        break;
                }

            } else {
                //failed login
                $this->view_data['error'] = $this->flexi_auth->get_messages();
                $this->session->set_flashdata('error', $this->flexi_auth->get_messages());
                $this->load->view('login_view', $this->view_data);
            }
        }
    }

    public function logout()
    {
        $this->flexi_auth->logout(true);
        redirect('login');
    }

}

/* End of file login.php */
/* Location: ./controllers/login.php */