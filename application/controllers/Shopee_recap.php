<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class Shopee_recap extends CI_Controller
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
        $title = 'Shopee Recap';
        $product = $this->db->get('product');

        // Start Get Pembayaran Shopee
        $this->db->select('user.full_name as full_name, acc_shopee.created_date as created_date, acc_shopee.idacc_shopee as idacc_shopee');
        $this->db->join('user', 'user.iduser = acc_shopee.iduser');
        $acc_shopee = $this->db->get('acc_shopee');
        // End

        $data = [
            'title' => $title,
            'acc_shopee' => $acc_shopee->result()
        ];

        $this->load->view('theme/v_head', $data);
        $this->load->view('Shopee_recap/v_shopee_recap');
    }

    public function createShopee()
    {
        $this->load->library('upload');
        $file = $_FILES['file']['tmp_name'];

        if (!empty($file)) {
            require APPPATH . '../vendor/autoload.php';

            $reader = new Xlsx();
            $spreadsheet = $reader->load($file);
            $sheet = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

            $acc_shopee_data = [
                'iduser' => $this->session->userdata('iduser'),
                'created_by' => $this->session->userdata('username'),
                'created_date' => date('Y-m-d H:i:s'),
                'status' => 1
            ];
            $this->db->insert('acc_shopee', $acc_shopee_data);
            $idacc_shopee = $this->db->insert_id();

            foreach ($sheet as $i => $row) {
                if ($i < 6 || !$row['B']) continue;

                $detail = [
                    'idacc_shopee' => $idacc_shopee,
                    'no_faktur' => $row['B'],
                    'pay_date' => date('Y-m-d', strtotime($row['H'])),
                    'total_faktur' => str_replace(',', '', $row['J']),
                    'pay' => str_replace(',', '', $row['L']),
                    'discount' => str_replace(',', '', $row['N']),
                    'payment' => str_replace(',', '', $row['P'])
                ];
                $this->db->insert('acc_shopee_detail', $detail);
            }

            $this->session->set_flashdata('success', 'Data Shopee berhasil diimport.');
        } else {
            $this->session->set_flashdata('error', 'File tidak ditemukan.');
        }

        redirect('shopee_recap');
    }

    public function detail_acc()
    {
        $idacc_shopee = $this->input->get('idacc_shopee');
        $this->db->where('acc_shopee.idacc_shopee', $idacc_shopee);
        $this->db->join('acc_shopee_detail', 'acc_shopee_detail.idacc_shopee = acc_shopee.idacc_shopee');

        // Filter berdasarkan tanggal
        if ($this->input->get('date_from')) {
            $this->db->where('acc_shopee_detail.pay_date >=', $this->input->get('date_from'));
        }
        if ($this->input->get('date_to')) {
            $this->db->where('acc_shopee_detail.pay_date <=', $this->input->get('date_to'));
        }

        // Filter berdasarkan No Faktur
        if ($this->input->get('no_faktur')) {
            $this->db->like('acc_shopee_detail.no_faktur', $this->input->get('no_faktur'));
        }

        $acc_shopee_detail = $this->db->get('acc_shopee')->result();

        $persen_input = $this->input->get('persen');

        $data = [
            'title' => 'Shopee Recap',
            'acc_shopee_detail' => $acc_shopee_detail,
            'persen_input' => $persen_input
        ];

        $this->load->view('theme/v_head', $data);
        $this->load->view('shopee_recap/v_shopee_recap_detail');
    }
}
