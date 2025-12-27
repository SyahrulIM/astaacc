<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('error', 'Eeettss gak boleh nakal, Login dulu ya kak hehe.');
            redirect('auth');
        }
        $this->load->database();
    }

    public function index()
    {
        // Get today's date
        $today = date('Y-m-d');

        // Shopee stats
        $shopee_count = $this->db->count_all('acc_shopee_detail');
        $shopee_today = $this->db->where('DATE(created_date)', $today)->count_all_results('acc_shopee_detail');

        // Accurate stats
        $accurate_count = 0;
        $accurate_today = 0;
        if ($this->db->table_exists('acc_accurate_detail')) {
            $accurate_count = $this->db->count_all('acc_accurate_detail');
            $accurate_today = $this->db->where('DATE(created_date)', $today)->count_all_results('acc_accurate_detail');
        }

        // TikTok stats
        $tiktok_count = 0;
        $tiktok_today = 0;
        if ($this->db->table_exists('acc_tiktok_detail')) {
            $tiktok_count = $this->db->count_all('acc_tiktok_detail');
            $tiktok_today = $this->db->where('DATE(created_date)', $today)->count_all_results('acc_tiktok_detail');
        }

        // Lazada stats
        $lazada_count = 0;
        $lazada_today = 0;
        if ($this->db->table_exists('acc_lazada_detail')) {
            $lazada_count = $this->db->count_all('acc_lazada_detail');
            $lazada_today = $this->db->where('DATE(created_date)', $today)->count_all_results('acc_lazada_detail');
        }

        // Calculate totals
        $total_count = $shopee_count + $accurate_count + $tiktok_count + $lazada_count;
        $total_today = $shopee_today + $accurate_today + $tiktok_today + $lazada_today;

        $data = [
            'title' => 'Dashboard',
            'shopee_count' => $shopee_count,
            'shopee_today' => $shopee_today,
            'accurate_count' => $accurate_count,
            'accurate_today' => $accurate_today,
            'tiktok_count' => $tiktok_count,
            'tiktok_today' => $tiktok_today,
            'lazada_count' => $lazada_count,
            'lazada_today' => $lazada_today,
            'total_count' => $total_count,
            'total_today' => $total_today,
        ];

        $this->load->view('theme/v_head', $data);
        $this->load->view('Dashboard/v_dashboard');
    }

    // AJAX function for real-time updates
    public function get_real_time_stats()
    {
        $today = date('Y-m-d');

        // Shopee stats
        $shopee_count = $this->db->count_all('acc_shopee_detail');
        $shopee_today = $this->db->where('DATE(created_date)', $today)->count_all_results('acc_shopee_detail');

        // Accurate stats
        $accurate_count = 0;
        $accurate_today = 0;
        if ($this->db->table_exists('acc_accurate_detail')) {
            $accurate_count = $this->db->count_all('acc_accurate_detail');
            $accurate_today = $this->db->where('DATE(created_date)', $today)->count_all_results('acc_accurate_detail');
        }

        // TikTok stats
        $tiktok_count = 0;
        $tiktok_today = 0;
        if ($this->db->table_exists('acc_tiktok_detail')) {
            $tiktok_count = $this->db->count_all('acc_tiktok_detail');
            $tiktok_today = $this->db->where('DATE(created_date)', $today)->count_all_results('acc_tiktok_detail');
        }

        // Lazada stats
        $lazada_count = 0;
        $lazada_today = 0;
        if ($this->db->table_exists('acc_lazada_detail')) {
            $lazada_count = $this->db->count_all('acc_lazada_detail');
            $lazada_today = $this->db->where('DATE(created_date)', $today)->count_all_results('acc_lazada_detail');
        }

        echo json_encode([
            'success' => true,
            'shopee_count' => $shopee_count,
            'shopee_today' => $shopee_today,
            'accurate_count' => $accurate_count,
            'accurate_today' => $accurate_today,
            'tiktok_count' => $tiktok_count,
            'tiktok_today' => $tiktok_today,
            'lazada_count' => $lazada_count,
            'lazada_today' => $lazada_today,
            'updated_at' => date('H:i:s')
        ]);
    }
}
