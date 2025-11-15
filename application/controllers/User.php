<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;

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
        $handphone = $this->input->post('inputHandphone');
        $is_whatsapp = $this->input->post('inputWhatsapp');

        $cekUsername = $this->db->get_where('user', ['username' => $username])->row();
        if ($cekUsername && $cekUsername->status == 1) {
            $this->session->set_flashdata('error', 'Username sudah dipakai oleh user aktif, silakan gunakan yang lain.');
            redirect('user');
            return;
        }

        $cekEmail = $this->db->get_where('user', ['email' => $email])->row();
        if ($cekEmail && $cekEmail->status == 1) {
            $this->session->set_flashdata('error', 'Email sudah dipakai oleh user aktif, silakan gunakan yang lain.');
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
            'handphone' => $handphone,
            'is_whatsapp' => $is_whatsapp,
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
        $handphone = $this->input->post('editHandphone');
        $is_whatsapp = $this->input->post('inputWhatsapp');

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
            ->where('status', 1) // hanya cek ke user yang aktif
            ->get('user')
            ->row();
        if ($cekUsername) {
            $this->session->set_flashdata('error', 'Username sudah dipakai oleh user aktif lain.');
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
            'handphone' => $handphone,
            'is_whatsapp' => $is_whatsapp,
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

    public function verifyUser()
    {
        $iduser = $this->input->get('iduser');

        if ($iduser) {
            $user = $this->db->get_where('user', ['iduser' => $iduser])->row();

            if ($user) {
                $this->db->where('iduser', $iduser);
                $this->db->update('user', ['is_verify' => 1]);

                $target = $user->handphone;

                if (!empty($target)) {
                    $token = 'EyuhsmTqzeKaDknoxdxt';
                    $message = 'Selamat, ' . $user->full_name . '! 🎉 Kamu sudah diverifikasi dan sekarang bisa akses Asta People.';

                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => 'https://api.fonnte.com/send',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_POST => true,
                        CURLOPT_POSTFIELDS => array(
                            'target' => $target,
                            'message' => $message,
                            'countryCode' => '62',
                        ),
                        CURLOPT_HTTPHEADER => array(
                            'Authorization: ' . $token
                        ),
                    ));

                    curl_exec($curl);
                    curl_close($curl);
                }
            }
        }

        redirect('user');
    }

    public function exportExcel()
    {
        // Ambil data dari database
        $users = $this->db->get('user')->result();

        // Buat object spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header kolom
        $sheet->setCellValue('A1', 'ID User');
        $sheet->setCellValue('B1', 'Nama');
        $sheet->setCellValue('C1', 'Email');
        $sheet->setCellValue('D1', 'Status');

        // Isi data
        $row = 2;
        foreach ($users as $u) {
            $sheet->setCellValue('A' . $row, $u->iduser);
            $sheet->setCellValue('B' . $row, $u->nama);
            $sheet->setCellValue('C' . $row, $u->email);
            $sheet->setCellValue('D' . $row, $u->is_verify ? 'Verified' : 'Unverified');
            $row++;
        }

        // Buat writer
        $writer = new Xlsx($spreadsheet);

        // Set header untuk download file
        $filename = 'data_user_' . date('Ymd_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Output ke browser
        $writer->save('php://output');
        exit;
    }

    public function exportUserPdf()
    {
        $iduser = $this->input->get('iduser');

        // Get specific user data
        $user = $this->db->where('user.iduser', $iduser)
            ->join('role', 'role.idrole=user.idrole')
            ->get('user')
            ->row();

        if (!$user) {
            show_error('User tidak ditemukan.');
        }

        $data = [
            'title' => 'Data Pengguna',
            'user' => $user,
        ];

        $this->load->view('User/v_user_pdf', $data);
    }

    public function change_foto()
    {
        $this->load->library('upload');
        $iduser = $this->session->userdata('iduser');

        // Ambil data user lama
        $oldUser = $this->db->get_where('user', ['iduser' => $iduser])->row();
        if (!$oldUser) {
            $this->session->set_flashdata('error', 'User tidak ditemukan.');
            redirect($_SERVER['HTTP_REFERER']);
            return;
        }

        // Cek upload
        if (!empty($_FILES['foto']['name'])) {
            $config['upload_path'] = './assets/image/user/';
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['file_name'] = 'foto_' . time();

            $this->upload->initialize($config);

            if ($this->upload->do_upload('foto')) {
                // Hapus foto lama kalau ada
                if ($oldUser->foto && file_exists('./assets/image/user/' . $oldUser->foto)) {
                    unlink('./assets/image/user/' . $oldUser->foto);
                }

                $newFoto = $this->upload->data('file_name');

                // Update ke DB
                $this->db->where('iduser', $iduser)->update('user', [
                    'foto' => $newFoto,
                    'updated_by' => $this->session->userdata('username'),
                    'updated_date' => date("Y-m-d H:i:s")
                ]);

                // Update session biar langsung ke-refresh
                $this->session->set_userdata('foto', $newFoto);

                $this->session->set_flashdata('success', 'Foto berhasil diganti.');
            } else {
                $this->session->set_flashdata('error', $this->upload->display_errors());
            }
        } else {
            $this->session->set_flashdata('error', 'Silakan pilih foto terlebih dahulu.');
        }

        redirect($_SERVER['HTTP_REFERER']);
    }

    public function changeUsername()
    {
        $data = [
            'username' => $this->input->post('inputUsername')
        ];
        $this->db->where('iduser', $this->session->userdata('iduser'));
        $this->db->update('user', $data);
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function changePassword()
    {
        if (!empty($this->input->post('inputPassword'))) {
            $hashedPassword = password_hash($this->input->post('inputPassword'), PASSWORD_DEFAULT);
        }
        $data = [
            'password' => $hashedPassword
        ];
        $this->db->where('iduser', $this->session->userdata('iduser'));
        $this->db->update('user', $data);
        redirect($_SERVER['HTTP_REFERER']);
    }
}
