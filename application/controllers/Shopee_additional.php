<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class Shopee_additional extends CI_Controller
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
        $acc_shopee_additional = $this->db->get('acc_shopee_additional');

        $data = [
            'title' => 'Shopee Additional',
            'acc_shopee_additional' => $acc_shopee_additional
        ];

        $this->load->view('theme/v_head', $data);
        $this->load->view('Shopee_recap/v_shopee_recap_additional');
    }

    public function createAdditional()
    {
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $additional_revenue = $this->input->post('additional_revenue');

        if (!$month || !$year || !$additional_revenue) {
            $this->session->set_flashdata('error', 'Semua field harus diisi!');
            redirect('shopee_additional');
            return;
        }

        $start_date = date("Y-m-d", strtotime("$year-$month-01"));
        $end_date = date("Y-m-t", strtotime($start_date));

        // Cek apakah data untuk bulan dan tahun tersebut sudah ada
        $existing = $this->db->get_where('acc_shopee_additional', [
            'start_date' => $start_date,
            'end_date' => $end_date
        ])->row();

        $now = date('Y-m-d H:i:s');
        $username = $this->session->userdata('username');

        if ($existing) {
            // Update data
            $this->db->where('idacc_shopee_additional', $existing->idacc_shopee_additional);
            $this->db->update('acc_shopee_additional', [
                'additional_revenue' => $additional_revenue,
                'updated_by' => $username,
                'updated_date' => $now
            ]);
            $this->session->set_flashdata('success', 'Data berhasil diupdate untuk periode tersebut.');
        } else {
            // Insert baru
            $this->db->insert('acc_shopee_additional', [
                'additional_revenue' => $additional_revenue,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'created_by' => $username,
                'created_date' => $now,
                'status' => 1
            ]);
            $this->session->set_flashdata('success', 'Data berhasil ditambahkan.');
        }

        redirect('shopee_additional');
    }
}
