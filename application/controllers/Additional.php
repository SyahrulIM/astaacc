<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Additional extends CI_Controller
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
        $acc_additional = $this->db->query("
        SELECT
            acc_shopee_additional.idacc_shopee_additional AS id,
            acc_shopee_additional.additional_revenue AS additional_revenue,
            acc_shopee_additional.start_date AS start_date,
            acc_shopee_additional.end_date AS end_date,
            'shopee' as source
        FROM
            acc_shopee_additional
        UNION ALL
        SELECT
            acc_tiktok_additional.idacc_tiktok_additional AS id,
            acc_tiktok_additional.additional_revenue AS additional_revenue,
            acc_tiktok_additional.start_date AS start_date,
            acc_tiktok_additional.end_date AS end_date,
            'tiktok' as source
        FROM
            acc_tiktok_additional;
        ");

        $data = [
            'title' => 'Additional',
            'acc_additional' => $acc_additional
        ];

        $this->load->view('theme/v_head', $data);
        $this->load->view('Additional/v_additional');
    }

    public function createAdditional()
    {
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $additional_revenue = $this->input->post('additional_revenue');
        $marketplace = $this->input->post('marketplace');
        if (!$additional_revenue) {
            $additional_revenue = 0;
        }

        if ($marketplace == 'shopee') {
            if ($additional_revenue == '') {
                $additional_revenue = 0;
            }
            if (!$month || !$year || !$additional_revenue) {
                $this->session->set_flashdata('error', 'Semua field harus diisi!');
                redirect('additional');
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
        } else {
            if (!$month || !$year || !$additional_revenue) {
                $this->session->set_flashdata('error', 'Semua field harus diisi!');
                redirect('additional');
                return;
            }

            $start_date = date("Y-m-d", strtotime("$year-$month-01"));
            $end_date = date("Y-m-t", strtotime($start_date));

            // Cek apakah data untuk bulan dan tahun tersebut sudah ada
            $existing = $this->db->get_where('acc_tiktok_additional', [
                'start_date' => $start_date,
                'end_date' => $end_date
            ])->row();

            $now = date('Y-m-d H:i:s');
            $username = $this->session->userdata('username');

            if ($existing) {
                // Update data
                $this->db->where('idacc_tiktok_additional', $existing->idacc_tiktok_additional);
                $this->db->update('acc_tiktok_additional', [
                    'additional_revenue' => $additional_revenue,
                    'updated_by' => $username,
                    'updated_date' => $now
                ]);
                $this->session->set_flashdata('success', 'Data berhasil diupdate untuk periode tersebut.');
            } else {
                // Insert baru
                $this->db->insert('acc_tiktok_additional', [
                    'additional_revenue' => $additional_revenue,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'created_by' => $username,
                    'created_date' => $now,
                    'status' => 1
                ]);
                $this->session->set_flashdata('success', 'Data berhasil ditambahkan.');
            }
        }
        redirect('additional');
    }
}
