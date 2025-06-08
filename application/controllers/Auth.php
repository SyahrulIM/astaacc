<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller
{

    public function index()
    {
        // Check if user is already logged in
        if ($this->session->userdata('logged_in')) {
            redirect('dashboard');  // Redirect to the dashboard if logged in
        }

        $this->load->view('Auth/v_auth');
    }

	public function login()
	{
		// Set form validation rules
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
		$this->form_validation->set_rules('password', 'Password', 'required');
	
		if ($this->form_validation->run() == FALSE) {
			// If validation fails, reload the login page with validation errors
			$this->load->view('Auth/v_auth');
		} else {
			// Get user input
			$email = $this->input->post('email');
			$password = $this->input->post('password');
	
			// Check user credentials in the database with status = 1
			$query = $this->db->get_where('user', ['email' => $email, 'status' => 1]);
			$user = $query->row();
	
			if ($user && password_verify($password, $user->password)) {
				// Successful login, set session data
				$this->session->set_userdata([
					'logged_in' => TRUE,
					'iduser' => $user->iduser,
					'full_name' => $user->full_name,
					'username' => $user->username,
					'idrole' => $user->idrole,
					'foto' => $user->foto,
				]);
				redirect('dashboard');  // Redirect to the dashboard
			} else {
				// Invalid login or user is inactive (status != 1)
				$this->session->set_flashdata('error', 'Invalid email, password, or inactive account.');
				redirect('auth');
			}
		}
	}	

    public function logout()
    {
        // Destroy the session and redirect to login page
        $this->session->sess_destroy();
        redirect('auth');
    }
}