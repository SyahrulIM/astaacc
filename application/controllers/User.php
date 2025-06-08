<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
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
        $title = 'User';
        $role = $this->db->get('role')->result();
        $user = $this->db->where('user.status', 1)->join('role', 'role.idrole=user.idrole')->get('user')->result();
        $data = [
            'title' => $title,
            'role' => $role,
            'user' => $user,
        ];

        $this->load->view('theme/v_head', $data);
        $this->load->view('User/v_user');
    }

    public function addUser()
    {
        $this->load->library('upload');

        $namaLengkap = $this->input->post('inputNamaLengkap');
        $username = $this->input->post('inputUsername');
        $email = $this->input->post('inputEmail');
        $password = $this->input->post('inputPassword');
        $idrole = $this->input->post('inputRole');

        // Cek apakah username sudah dipakai
        $cekUsername = $this->db->get_where('user', ['username' => $username])->row();
        if ($cekUsername) {
            $this->session->set_flashdata('error', 'Username sudah dipakai, silakan gunakan yang lain.');
            redirect('user');
            return;
        }

        $foto = '';
        if (!empty($_FILES['inputFoto']['name'])) {
            $config['upload_path'] = './assets/image/user/';
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['file_name'] = 'foto_' . time();

            $this->upload->initialize($config);
            if ($this->upload->do_upload('inputFoto')) {
                $foto = $this->upload->data('file_name');
            } else {
                $this->session->set_flashdata('error', $this->upload->display_errors());
                redirect('user');
                return;
            }
        }

        $data = [
            'full_name' => $namaLengkap,
            'username' => $username,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'foto' => $foto,
            'idrole' => $idrole,
            'created_by' => $this->session->userdata('username'),
            'created_date' => date("Y-m-d H:i:s"),
            'updated_by' => $this->session->userdata('username'),
            'updated_date' => date("Y-m-d H:i:s")
        ];

        $this->db->insert('user', $data);
        $this->session->set_flashdata('success', 'User berhasil ditambahkan.');
        redirect('user');
    }

    public function editUser()
    {
        $this->load->library('upload');

        $namaLengkap = $this->input->post('editNamaLengkap');
        $username = $this->input->post('editUsername');
        $email = $this->input->post('editEmail');
        $password = $this->input->post('editPassword');
        $idrole = $this->input->post('editRole');

        // Ambil data user lama
        $oldUser = $this->db->get_where('user', ['username' => $username])->row();
        if (!$oldUser) {
            $this->session->set_flashdata('error', 'User tidak ditemukan.');
            redirect('user');
            return;
        }

        // Validasi jika username diubah, tidak boleh sama dengan user lain
        $userId = $oldUser->iduser;
        $cekUsername = $this->db
            ->where('username', $username)
            ->where('iduser !=', $userId)
            ->get('user')
            ->row();
        if ($cekUsername) {
            $this->session->set_flashdata('error', 'Username sudah dipakai oleh user lain.');
            redirect('user');
            return;
        }

        // Foto
        $foto = $oldUser->foto;
        if (!empty($_FILES['editFoto']['name'])) {
            $config['upload_path'] = './assets/image/user/';
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['file_name'] = 'foto_' . time();

            $this->upload->initialize($config);
            if ($this->upload->do_upload('editFoto')) {
                if ($foto && file_exists('./assets/image/user/' . $foto)) {
                    unlink('./assets/image/user/' . $foto);
                }
                $foto = $this->upload->data('file_name');
            } else {
                $this->session->set_flashdata('error', $this->upload->display_errors());
                redirect('user');
                return;
            }
        }

        // Password: hanya diubah jika ada input baru
        $hashedPassword = $oldUser->password;
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        }

        $data = [
            'full_name' => $namaLengkap,
            'username' => $username, // disimpan kembali, bisa jadi diubah
            'email' => $email,
            'password' => $hashedPassword,
            'foto' => $foto,
            'idrole' => $idrole,
            'updated_by' => $this->session->userdata('username'),
            'updated_date' => date("Y-m-d H:i:s")
        ];

        $this->db->where('iduser', $userId);
        $this->db->update('user', $data);

        $this->session->set_flashdata('success', 'User berhasil diupdate.');
        redirect('user');
    }

    public function deleteUser()
    {
        $iduser = $this->input->get('iduser');
    
        if ($iduser != 4) {
            $this->db->where('iduser', $iduser)->set('status', 0)->update('user');
        }
    
        redirect('user');
    }    
}
