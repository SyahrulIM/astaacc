<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class Accurate_recap extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('error', 'Eeettss gak boleh nakal, Login dulu ya kak hehe.');
            redirect('auth');
        }
    }

    public function index()
    {
        $title = 'Accurate Recap';

        // Start Get Pembayaran accurate
        $this->db->select('user.full_name as full_name, acc_accurate.created_date as created_date, acc_accurate.idacc_accurate as idacc_accurate');
        $this->db->join('user', 'user.iduser = acc_accurate.iduser');
        $acc_accurate = $this->db->get('acc_accurate');
        // End

        $data = [
            'title' => $title,
            'acc_accurate' => $acc_accurate->result()
        ];

        $this->load->view('theme/v_head', $data);
        $this->load->view('Accurate_recap/v_accurate_recap');
    }

    public function createAccurate()
    {
        $this->load->library('upload');
        $file = $_FILES['file']['tmp_name'];

        if (!empty($file)) {
            require APPPATH . '../vendor/autoload.php';

            $reader = new Xlsx();
            $spreadsheet = $reader->load($file);
            $sheet = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

            $acc_accurate_data = [
                'iduser' => $this->session->userdata('iduser'),
                'created_by' => $this->session->userdata('username'),
                'created_date' => date('Y-m-d H:i:s'),
                'status' => 1
            ];
            $this->db->insert('acc_accurate', $acc_accurate_data);
            $idacc_accurate = $this->db->insert_id();

            foreach ($sheet as $i => $row) {
                if ($i < 6 || !$row['B']) continue;

                $detail = [
                    'idacc_accurate' => $idacc_accurate,
                    'no_faktur' => $row['B'],
                    'pay_date' => date('Y-m-d', strtotime($row['H'])),
                    'total_faktur' => str_replace(',', '', $row['J']),
                    'pay' => str_replace(',', '', $row['L']),
                    'discount' => str_replace(',', '', $row['N']),
                    'payment' => str_replace(',', '', $row['P'])
                ];
                $this->db->insert('acc_accurate_detail', $detail);
            }

            $this->session->set_flashdata('success', 'Data Accurate berhasil diimport.');
        } else {
            $this->session->set_flashdata('error', 'File tidak ditemukan.');
        }

        redirect('accurate_recap');
    }

    public function detail_acc()
    {
        $idacc_accurate = $this->input->get('idacc_accurate');
        $this->db->where('acc_accurate.idacc_accurate', $idacc_accurate);
        $this->db->join('acc_accurate_detail', 'acc_accurate_detail.idacc_accurate = acc_accurate.idacc_accurate');

        // Filter berdasarkan tanggal
        if ($this->input->get('date_from')) {
            $this->db->where('acc_accurate_detail.pay_date >=', $this->input->get('date_from'));
        }
        if ($this->input->get('date_to')) {
            $this->db->where('acc_accurate_detail.pay_date <=', $this->input->get('date_to'));
        }

        // Filter berdasarkan No Faktur
        if ($this->input->get('no_faktur')) {
            $this->db->like('acc_accurate_detail.no_faktur', $this->input->get('no_faktur'));
        }

        $acc_accurate_detail = $this->db->get('acc_accurate')->result();

        $persen_input = $this->input->get('persen');

        $data = [
            'title' => 'Accurate Recap',
            'acc_accurate_detail' => $acc_accurate_detail,
            'persen_input' => $persen_input
        ];

        $this->load->view('theme/v_head', $data);
        $this->load->view('accurate_recap/v_accurate_recap_detail');
    }
}
