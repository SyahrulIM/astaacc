<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller
{
	public function __construct()
    {
        parent::__construct();
        // Check if the user is logged in
        if (!$this->session->userdata('logged_in')) {
            // Redirect to login with a message
            $this->session->set_flashdata('error', 'Eeettss gak boleh nakal, Login dulu ya kak hehe.');
            redirect('auth');  // Assuming 'auth' is your login controller
        }
    }

	public function index()
	{
		$title = 'Dashboard';
        $product = $this->db->get('product');
        $data = [
            'title' => $title,
            'product' => $product->result()
        ];

		$this->load->view('theme/v_head', $data);
		$this->load->view('Dashboard/v_dashboard');
	}

}
