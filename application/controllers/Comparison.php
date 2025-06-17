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
        $order_start = $this->input->get('order_start');
        $order_end = $this->input->get('order_end');
        $status_filter = $this->input->get('status');
        $ratio_limit = (float) ($this->input->get('ratio') ?? 0);
        $ratio_status = $this->input->get('ratio_status');
        $matching_status = $this->input->get('matching_status');

        $data_comparison = [];
        $grand_total_invoice = 0;
        $grand_total_payment = 0;
        $difference_count = 0;
        $exceed_ratio_count = 0;

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

            if ($start_date && $end_date) {
                $this->db->where('asd.pay_date >=', $start_date);
                $this->db->where('asd.pay_date <=', $end_date);
            }

            if ($order_start && $order_end) {
                $this->db->where('asd.order_date >=', $order_start);
                $this->db->where('asd.order_date <=', $order_end);
            }

            $this->db->order_by('asd.no_faktur', 'asc');
            $results = $this->db->get()->result();
            $seen_faktur = [];

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

                    $is_match = (
                        ($row->accurate_total_faktur ?? 0) == ($row->shopee_total_faktur ?? 0) &&
                        ($row->accurate_discount ?? 0) == ($row->shopee_discount ?? 0) &&
                        ($row->accurate_payment ?? 0) == ($row->shopee_payment ?? 0)
                    );

                    if (
                        empty($matching_status) ||
                        ($matching_status === 'match' && $is_match) ||
                        ($matching_status === 'mismatch' && !$is_match)
                    ) {
                        if ($shopee != $accurate) $difference_count++;

                        if ($accurate > 0 && $shopee > 0) {
                            $ratio = (($shopee - $accurate) / $shopee) * 100;

                            if ($ratio_status === 'lebih' && $ratio <= $ratio_limit) continue;
                            if ($ratio > $ratio_limit) $exceed_ratio_count++;
                        }

                        $grand_total_invoice += $shopee;
                        $grand_total_payment += $accurate;

                        $data_comparison[] = $row;
                        $seen_faktur[] = $row->no_faktur;
                    }
                }
            }
        }

        $data = [
            'title' => 'Comparison',
            'data_comparison' => $data_comparison,
            'grand_total_invoice' => $grand_total_invoice,
            'grand_total_payment' => $grand_total_payment,
            'difference_count' => $difference_count,
            'exceed_ratio_count' => $exceed_ratio_count,
            'ratio_status' => $ratio_status,
            'ratio_limit' => $ratio_limit,
            'matching_status' => $matching_status
        ];

        $this->load->view('theme/v_head', $data);
        $this->load->view('comparison/v_comparison'); // <- ini view utama kamu
    }

    public function detail_ajax($no_faktur)
    {
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
        $this->db->where('asd.no_faktur', $no_faktur);
        $detail = $this->db->get()->row();

        if (!$detail) {
            echo '<div class="text-danger">Data tidak ditemukan.</div>';
            return;
        }

        echo '
        <table class="table table-bordered">
            <thead>
                <th></th>
                <th>Shopee</th>
                <th>Accurate</th>
            </thead>
            <tr><th>Total Faktur</th><td>' . htmlspecialchars($detail->shopee_total_faktur) . '</td><td>' . htmlspecialchars($detail->accurate_total_faktur) . '</td></tr>
            <tr><th>Discount</th><td>' . htmlspecialchars($detail->shopee_discount) . '</td><td>' . htmlspecialchars($detail->accurate_discount) . '</td></tr>
            <tr><th>Pembayaran</th><td>' . htmlspecialchars($detail->shopee_payment) . '</td><td>' . htmlspecialchars($detail->accurate_payment) . '</td></tr>
        </table>';
    }
}