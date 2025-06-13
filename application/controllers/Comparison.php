<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Comparison extends CI_Controller
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
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        $order_start = $this->input->get('order_start'); // Tambahan
        $order_end = $this->input->get('order_end');     // Tambahan
        $status_filter = $this->input->get('status');

        $data_comparison = [];
        $grand_total_invoice = 0;
        $grand_total_payment = 0;

        if (($start_date && $end_date) || ($order_start && $order_end)) {
            $this->db->select('
            asd.no_faktur,
            asd.order_date AS shopee_order_date,
            asd.pay_date AS shopee_pay_date,
            asd.total_faktur AS shopee_total_faktur,
            asd.discount AS shopee_discount,
            asd.payment AS shopee_payment,

            aad.pay_date AS accurate_pay_date,
            aad.total_faktur AS accurate_total_faktur,
            aad.discount AS accurate_discount,
            aad.payment AS accurate_payment
        ');
            $this->db->from('acc_shopee_detail asd');
            $this->db->join('acc_accurate_detail aad', 'aad.no_faktur = asd.no_faktur', 'left');

            // Filter pembayaran
            if ($start_date && $end_date) {
                $this->db->where('asd.pay_date >=', $start_date);
                $this->db->where('asd.pay_date <=', $end_date);
            }

            // Filter order date
            if ($order_start && $order_end) {
                $this->db->where('asd.order_date >=', $order_start);
                $this->db->where('asd.order_date <=', $order_end);
            }

            $this->db->order_by('asd.no_faktur', 'asc');

            $results = $this->db->get()->result();
            $seen_faktur = [];
            $difference_count = 0;
            $exceed_ratio_count = 0;

            foreach ($results as $row) {
                if (in_array($row->no_faktur, $seen_faktur)) continue;

                $is_sudah_bayar = !empty($row->accurate_pay_date);
                if (
                    empty($status_filter) ||
                    ($status_filter == 'Sudah Bayar' && $is_sudah_bayar) ||
                    ($status_filter == 'Belum Bayar' && !$is_sudah_bayar)
                ) {
                    $shopee = (float) ($row->shopee_total_faktur ?? 0);
                    $accurate = (float) ($row->accurate_payment ?? 0);

                    // Cek selisih nominal
                    if ($shopee != $accurate) {
                        $difference_count++;
                    }

                    // Hitung rasio selisih jika accurate > 0
                    if ($accurate > 0) {
                        $ratio = (($shopee - $accurate) / $accurate) * 100;
                        $ratio_limit = (float) ($this->input->get('ratio') ?? 0);
                        if ($ratio > $ratio_limit) {
                            $exceed_ratio_count++; // faktur yang rasio-nya melebihi batas
                        }
                    }

                    $grand_total_invoice += $shopee;
                    $grand_total_payment += $accurate;

                    $data_comparison[] = $row;
                    $seen_faktur[] = $row->no_faktur;
                }
            }
        }

        $title = 'Comparison';
        $data = [
            'title' => $title,
            'data_comparison' => $data_comparison,
            'grand_total_invoice' => $grand_total_invoice,
            'grand_total_payment' => $grand_total_payment,
            'difference_count' => $difference_count,
            'exceed_ratio_count' => $exceed_ratio_count, // Tambahkan ini
        ];

        $this->load->view('theme/v_head', $data);
        $this->load->view('comparison/v_comparison');
    }
}
