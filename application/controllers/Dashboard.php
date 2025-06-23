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
    }

    public function index()
    {
        $title = 'Dashboard';

        // Start Acc Detail Shopee
        $this->db->select('
            acc_shopee_detail.no_faktur,
            MAX(acc_shopee_detail.pay_date) AS pay_date,
            MAX(acc_shopee_detail.total_faktur) AS total_faktur,
            MAX(acc_shopee_detail.pay) AS pay,
            MAX(acc_shopee_detail.discount) AS discount,
            MAX(acc_shopee_detail.payment) AS payment,
            MAX(acc_shopee_detail.order_date) AS order_date
        ');
        $this->db->from('acc_shopee_detail');
        $this->db->join('acc_shopee', 'acc_shopee.idacc_shopee = acc_shopee_detail.idacc_shopee');
        $this->db->join('user', 'user.iduser = acc_shopee.iduser');
        $this->db->group_by('acc_shopee_detail.no_faktur');
        $acc_shopee_detail = $this->db->get();
        // End

        // Start Acc Detail Accurate
        $this->db->select('
            acc_accurate_detail.no_faktur,
            MAX(acc_accurate_detail.pay_date) AS pay_date,
            MAX(acc_accurate_detail.total_faktur) AS total_faktur,
            MAX(acc_accurate_detail.pay) AS pay,
            MAX(acc_accurate_detail.discount) AS discount,
            MAX(acc_accurate_detail.payment) AS payment
        ');
        $this->db->from('acc_accurate_detail');
        $this->db->join('acc_accurate', 'acc_accurate.idacc_accurate = acc_accurate_detail.idacc_accurate');
        $this->db->join('user', 'user.iduser = acc_accurate.iduser');
        $this->db->group_by('acc_accurate_detail.no_faktur');
        $acc_accurate_detail = $this->db->get();
        // End

        $data = [
            'title' => $title,
            'acc_shopee_detail' => $acc_shopee_detail->result(),
            'acc_accurate_detail' => $acc_accurate_detail->result()
        ];

        $this->load->view('theme/v_head', $data);
        $this->load->view('Dashboard/v_dashboard');
    }
}
