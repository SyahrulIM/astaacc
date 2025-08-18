<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
        $marketplace_filter = $this->input->get('marketplace');
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        $order_start = $this->input->get('order_start');
        $order_end = $this->input->get('order_end');
        $status_filter = $this->input->get('status');
        $ratio_limit = (float) ($this->input->get('ratio') ?? 0);
        $ratio_status = $this->input->get('ratio_status');
        $matching_status = $this->input->get('matching_status');
        $type_status = $this->input->get('type_status');

        $data_comparison = [];

        $grand_total_invoice = $grand_total_payment = 0;
        $grand_total_invoice_non_retur = $grand_total_payment_non_retur = 0;
        $grand_total_invoice_retur = $grand_total_payment_retur = 0;
        $difference_count = $difference_count_non_retur = 0;
        $exceed_ratio_count = $exceed_ratio_count_non_retur = 0;
        $mismatch_count = $retur_count = 0;

        $grand_total_invoice_tiktok = $grand_total_payment_tiktok = 0;
        $grand_total_invoice_non_retur_tiktok = $grand_total_payment_non_retur_tiktok = 0;
        $grand_total_invoice_retur_tiktok = $grand_total_payment_retur_tiktok = 0;
        $difference_count_tiktok = $difference_count_non_retur_tiktok = 0;
        $exceed_ratio_count_tiktok = $exceed_ratio_count_non_retur_tiktok = 0;
        $mismatch_count_tiktok = $retur_count_tiktok = 0;

        if (($start_date && $end_date) || ($order_start && $order_end)) {
            $filterShopee = [];
            $filterTiktok = [];

            if ($marketplace_filter === 'Shopee') {
                $filterTiktok[] = "1 = 0";
            } elseif ($marketplace_filter === 'TikTok') {
                $filterShopee[] = "1 = 0";
            }

            // Date filters
            if ($start_date && $end_date) {
                $filterShopee[] = "asd.pay_date >= '{$start_date}'";
                $filterShopee[] = "asd.pay_date <= '{$end_date}'";
                $filterTiktok[] = "atd.pay_date >= '{$start_date}'";
                $filterTiktok[] = "atd.pay_date <= '{$end_date}'";
            }
            if ($order_start && $order_end) {
                $filterShopee[] = "asd.order_date >= '{$order_start}'";
                $filterShopee[] = "asd.order_date <= '{$order_end}'";
                $filterTiktok[] = "atd.order_date >= '{$order_start}'";
                $filterTiktok[] = "atd.order_date <= '{$order_end}'";
            }

            if ($type_status == 'retur') {
                $filterShopee[] = "asd.refund < 0";
                $filterTiktok[] = "atd.refund < 0";
            } elseif ($type_status == 'pembayaran') {
                $filterShopee[] = "asd.refund > 0";
                $filterTiktok[] = "atd.refund > 0";
            }

            $whereShopee = !empty($filterShopee) ? "WHERE " . implode(" AND ", $filterShopee) : "";
            $whereTiktok = !empty($filterTiktok) ? "WHERE " . implode(" AND ", $filterTiktok) : "";

            $sql = "
                SELECT
                    asd.no_faktur,
                    MAX(asd.order_date) AS shopee_order_date,
                    MAX(asd.pay_date) AS shopee_pay_date,
                    MAX(asd.total_faktur) AS shopee_total_faktur,
                    MAX(asd.discount) AS shopee_discount,
                    MAX(asd.payment) AS shopee_payment,
                    MAX(asd.refund) AS shopee_refund,
                    MAX(asd.note) AS note,
                    MAX(asd.is_check) AS is_check,
                    MAX(asd.status_dir) AS status_dir,
                    MAX(aad.pay_date) AS accurate_pay_date,
                    MAX(aad.total_faktur) AS accurate_total_faktur,
                    MAX(aad.discount) AS accurate_discount,
                    MAX(aad.payment) AS accurate_payment,
                    'shopee' AS source
                FROM acc_shopee_detail asd
                LEFT JOIN acc_accurate_detail aad ON aad.no_faktur = asd.no_faktur
                {$whereShopee}
                GROUP BY asd.no_faktur

                UNION

                SELECT
                    atd.no_faktur,
                    MAX(atd.order_date) AS shopee_order_date,
                    MAX(atd.pay_date) AS shopee_pay_date,
                    MAX(atd.total_faktur) AS shopee_total_faktur,
                    MAX(atd.discount) AS shopee_discount,
                    MAX(atd.payment) AS shopee_payment,
                    MAX(atd.refund) AS shopee_refund,
                    MAX(atd.note) AS note,
                    MAX(atd.is_check) AS is_check,
                    MAX(atd.status_dir) AS status_dir,
                    MAX(aad.pay_date) AS accurate_pay_date,
                    MAX(aad.total_faktur) AS accurate_total_faktur,
                    MAX(aad.discount) AS accurate_discount,
                    MAX(aad.payment) AS accurate_payment,
                    'tiktok' AS source
                FROM acc_tiktok_detail atd
                LEFT JOIN acc_accurate_detail aad ON aad.no_faktur = atd.no_faktur
                {$whereTiktok}
                GROUP BY atd.no_faktur
                ORDER BY no_faktur ASC
            ";

            $results = $this->db->query($sql)->result();
            $seen_faktur = [];

            foreach ($results as $row) {
                if (in_array($row->no_faktur, $seen_faktur)) continue;

                $sku_list = $this->db
                    ->select('sku')
                    ->from($row->source == 'Shopee' ? 'acc_shopee_detail_details' : 'acc_tiktok_detail_details')
                    ->where('no_faktur', $row->no_faktur)
                    ->get()
                    ->result();

                $skus = array_column($sku_list, 'sku');
                $total_price_bottom = 0;
                if (!empty($skus)) {
                    $this->db->select_sum('price_bottom', 'total_price_bottom');
                    $this->db->where_in('sku', $skus);
                    $result_bottom = $this->db->get('acc_shopee_bottom')->row();
                    $total_price_bottom = $result_bottom->total_price_bottom ?? 0;
                }
                $row->total_price_bottom = $total_price_bottom;

                $is_sudah_bayar = !empty($row->accurate_pay_date);
                if (
                    empty($status_filter) ||
                    ($status_filter == 'Sudah Bayar' && $is_sudah_bayar) ||
                    ($status_filter == 'Belum Bayar' && !$is_sudah_bayar)
                ) {

                    $shopee = (float) ($row->shopee_total_faktur ?? 0);
                    $accurate = (float) ($row->accurate_payment ?? 0);
                    $is_retur = ($row->shopee_refund ?? 0) < 0;

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

                        if ($row->source == 'TikTok') {
                            $grand_total_invoice_tiktok += $shopee;
                            $grand_total_payment_tiktok += $accurate;

                            if (!$is_retur) {
                                $grand_total_invoice_non_retur_tiktok += $shopee;
                                $grand_total_payment_non_retur_tiktok += $accurate;
                            } else {
                                $grand_total_invoice_retur_tiktok += $shopee;
                                $grand_total_payment_retur_tiktok += $accurate;
                                $retur_count_tiktok++;
                            }

                            if ($shopee != $accurate) {
                                $difference_count_tiktok++;
                                if (!$is_retur) $difference_count_non_retur_tiktok++;
                            }

                            if ($accurate > 0 && $shopee > 0) {
                                $ratio = (($shopee - $accurate) / $accurate) * 100;
                                if ($ratio_status === 'lebih' && $ratio <= $ratio_limit) continue;
                                if ($ratio > $ratio_limit) {
                                    $exceed_ratio_count_tiktok++;
                                    if (!$is_retur) $exceed_ratio_count_non_retur_tiktok++;
                                }
                            }

                            if (!$is_match) $mismatch_count_tiktok++;
                        } else {
                            $grand_total_invoice += $shopee;
                            $grand_total_payment += $accurate;

                            if (!$is_retur) {
                                $grand_total_invoice_non_retur += $shopee;
                                $grand_total_payment_non_retur += $accurate;
                            } else {
                                $grand_total_invoice_retur += $shopee;
                                $grand_total_payment_retur += $accurate;
                                $retur_count++;
                            }

                            if ($shopee != $accurate) {
                                $difference_count++;
                                if (!$is_retur) $difference_count_non_retur++;
                            }

                            if ($accurate > 0 && $shopee > 0) {
                                $ratio = (($shopee - $accurate) / $accurate) * 100;
                                if ($ratio_status === 'lebih' && $ratio <= $ratio_limit) continue;
                                if ($ratio > $ratio_limit) {
                                    $exceed_ratio_count++;
                                    if (!$is_retur) $exceed_ratio_count_non_retur++;
                                }
                            }

                            if (!$is_match) $mismatch_count++;
                        }

                        $data_comparison[] = $row;
                        $seen_faktur[] = $row->no_faktur;
                    }
                }
            }
        }

        $additional_revenue = 0;
        if ($order_start && $order_end) {
            $additional_revenue = 0;

            if ($marketplace_filter == 'Shopee') {
                // Shopee only
                $this->db->select_sum('additional_revenue');
                $this->db->where('start_date >=', $order_start);
                $this->db->where('end_date <=', $order_end);
                $additional_data = $this->db->get('acc_shopee_additional')->row();
                $additional_revenue = $additional_data->additional_revenue ?? 0;
            } elseif ($marketplace_filter == 'TikTok') {
                // TikTok only
                $this->db->select_sum('additional_revenue');
                $this->db->where('start_date >=', $order_start);
                $this->db->where('end_date <=', $order_end);
                $additional_data = $this->db->get('acc_tiktok_additional')->row();
                $additional_revenue = $additional_data->additional_revenue ?? 0;
            } else {
                // All marketplaces → sum both
                // Shopee
                $this->db->select_sum('additional_revenue');
                $this->db->where('start_date >=', $order_start);
                $this->db->where('end_date <=', $order_end);
                $shopee = $this->db->get('acc_shopee_additional')->row()->additional_revenue ?? 0;

                // TikTok
                $this->db->select_sum('additional_revenue');
                $this->db->where('start_date >=', $order_start);
                $this->db->where('end_date <=', $order_end);
                $tiktok = $this->db->get('acc_tiktok_additional')->row()->additional_revenue ?? 0;

                $additional_revenue = $shopee + $tiktok;
            }
        }

        $data = [
            'title' => 'Comparison',
            'data_comparison' => $data_comparison,
            'marketplace_filter' => $marketplace_filter,

            'grand_total_invoice' => $grand_total_invoice + $grand_total_invoice_tiktok,
            'grand_total_payment' => $grand_total_payment + $grand_total_payment_tiktok,
            'grand_total_invoice_non_retur' => $grand_total_invoice_non_retur + $grand_total_invoice_non_retur_tiktok,
            'grand_total_payment_non_retur' => $grand_total_payment_non_retur + $grand_total_payment_non_retur_tiktok,
            'grand_total_invoice_retur' => $grand_total_invoice_retur + $grand_total_invoice_retur_tiktok,
            'grand_total_payment_retur' => $grand_total_payment_retur + $grand_total_payment_retur_tiktok,
            'grand_total_invoice_after_retur' => ($grand_total_invoice + $grand_total_invoice_tiktok) - ($grand_total_invoice_retur + $grand_total_invoice_retur_tiktok),
            'grand_total_payment_after_retur' => ($grand_total_payment + $grand_total_payment_tiktok) - ($grand_total_payment_retur + $grand_total_payment_retur_tiktok),
            'difference_count' => $difference_count + $difference_count_tiktok,
            'difference_count_non_retur' => $difference_count_non_retur + $difference_count_non_retur_tiktok,
            'exceed_ratio_count' => $exceed_ratio_count + $exceed_ratio_count_tiktok,
            'exceed_ratio_count_non_retur' => $exceed_ratio_count_non_retur + $exceed_ratio_count_non_retur_tiktok,
            'mismatch_count' => $mismatch_count + $mismatch_count_tiktok,
            'retur_count' => $retur_count + $retur_count_tiktok,

            'ratio_status' => $ratio_status,
            'ratio_limit' => $ratio_limit,
            'matching_status' => $matching_status,
            'type_status' => $type_status,
            'additional_revenue' => $additional_revenue,
        ];

        $this->load->view('theme/v_head', $data);
        $this->load->view('Comparison/v_comparison');
    }

    public function detail_ajax($no_faktur)
    {
        // Cek sumber data marketplace
        $source = null;
        $cekShopee = $this->db->get_where('acc_shopee_detail', ['no_faktur' => $no_faktur])->row();
        if ($cekShopee) {
            $source = 'shopee';
        } else {
            $cekTiktok = $this->db->get_where('acc_tiktok_detail', ['no_faktur' => $no_faktur])->row();
            if ($cekTiktok) {
                $source = 'tiktok';
            }
        }

        if (!$source) {
            echo '<div class="text-danger">Data tidak ditemukan di Shopee atau TikTok.</div>';
            return;
        }

        if ($source == 'shopee') {
            // Ambil data detail Shopee
            $this->db->select('
            MAX(sd.total_faktur) AS mp_total_faktur,
            MAX(sd.discount) AS mp_discount,
            MAX(sd.payment) AS mp_payment,
            MAX(sd.refund) AS mp_refund,
            MAX(aad.total_faktur) AS accurate_total_faktur,
            MAX(aad.discount) AS accurate_discount,
            MAX(aad.payment) AS accurate_payment
        ');
            $this->db->from('acc_shopee_detail sd');
            $this->db->join('acc_accurate_detail aad', 'aad.no_faktur = sd.no_faktur', 'left');
            $this->db->where('sd.no_faktur', $no_faktur);
            $detail = $this->db->get()->row();

            // Ambil detail SKU
            $this->db->distinct();
            $this->db->select('no_faktur, sku, name_product, price_after_discount');
            $this->db->where('no_faktur', $no_faktur);
            $sku_details = $this->db->get('acc_shopee_detail_details')->result();

            // Ambil harga bottom
            $harga_bottom_map = [];
            if (!empty($sku_details)) {
                $sku_list = array_column($sku_details, 'sku');
                $this->db->select('sku, price_bottom');
                $this->db->where_in('sku', $sku_list);
                $bottoms = $this->db->get('acc_shopee_bottom')->result();
                foreach ($bottoms as $b) {
                    $harga_bottom_map[$b->sku] = $b->price_bottom;
                }
            }
        } else {
            // Ambil data detail TikTok
            $this->db->select('
            MAX(td.total_faktur) AS mp_total_faktur,
            MAX(td.discount) AS mp_discount,
            MAX(td.payment) AS mp_payment,
            MAX(td.refund) AS mp_refund,
            MAX(aad.total_faktur) AS accurate_total_faktur,
            MAX(aad.discount) AS accurate_discount,
            MAX(aad.payment) AS accurate_payment
        ');
            $this->db->from('acc_tiktok_detail td');
            $this->db->join('acc_accurate_detail aad', 'aad.no_faktur = td.no_faktur', 'left');
            $this->db->where('td.no_faktur', $no_faktur);
            $detail = $this->db->get()->row();

            // Ambil detail SKU
            $this->db->distinct();
            $this->db->select('no_faktur, sku, name_product, price_after_discount');
            $this->db->where('no_faktur', $no_faktur);
            $sku_details = $this->db->get('acc_tiktok_detail_details')->result();

            // Ambil harga bottom
            $harga_bottom_map = [];
            if (!empty($sku_details)) {
                $sku_list = array_column($sku_details, 'sku');
                $this->db->select('sku, price_bottom');
                $this->db->where_in('sku', $sku_list);
                $bottoms = $this->db->get('acc_shopee_bottom')->result();
                foreach ($bottoms as $b) {
                    $harga_bottom_map[$b->sku] = $b->price_bottom;
                }
            }
        }

        // Output Ringkasan
        echo '<h5>Perbandingan Data (' . ucfirst($source) . ') - ' . $no_faktur . '</h5>
    <table class="table table-bordered mb-4">
        <thead><tr><th></th><th>' . ucfirst($source) . '</th><th>Accurate</th></tr></thead>
        <tr><th>Total Faktur</th><td>' . number_format($detail->mp_total_faktur) . '</td><td>' . number_format($detail->accurate_total_faktur) . '</td></tr>
        <tr><th>Discount</th><td>' . number_format($detail->mp_discount) . '</td><td>' . number_format($detail->accurate_discount) . '</td></tr>
        <tr><th>Pembayaran</th><td>' . number_format($detail->mp_payment) . '</td><td>' . number_format($detail->accurate_payment) . '</td></tr>
        <tr><th>Refund</th><td>' . number_format($detail->mp_refund) . '</td><td>0</td></tr>
    </table>';

        // Output SKU
        echo '<h5>Detail Produk (' . ucfirst($source) . ' & Bottom)</h5>
    <table class="table table-striped table-bordered">
        <thead><tr><th>SKU</th><th>Nama Produk</th><th>Harga Invoice</th><th>Harga Bottom</th></tr></thead><tbody>';

        foreach ($sku_details as $item) {
            $bottom = $harga_bottom_map[$item->sku] ?? '-';
            echo '<tr>
            <td>' . htmlspecialchars($item->sku) . '</td>
            <td>' . htmlspecialchars($item->name_product) . '</td>
            <td>' . number_format((float)$item->price_after_discount) . '</td>
            <td>' . (is_numeric($bottom) ? number_format($bottom) : '-') . '</td>
        </tr>';
        }

        echo '</tbody></table>';
    }

    public function final_dir_single()
    {
        $no_faktur = $this->input->post('no_faktur');

        $this->db->where('no_faktur', $no_faktur);
        $updated = $this->db->update('acc_shopee_detail', ['status_dir' => 'Allowed']);

        echo json_encode([
            'success' => $updated,
            'no_faktur' => $no_faktur
        ]);
    }

    public function final_dir_batch()
    {
        $faktur_list = $this->input->post('faktur_list');

        $this->db->where_in('no_faktur', $faktur_list);
        $updated = $this->db->update('acc_shopee_detail', ['status_dir' => 'Allowed']);

        echo json_encode([
            'success' => $updated,
            'processed' => count($faktur_list)
        ]);
    }

    public function export_excel()
    {
        require_once(APPPATH . '../vendor/autoload.php');
        $this->load->library('session');

        $marketplace_filter = $this->input->get('marketplace');
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        $order_start = $this->input->get('order_start');
        $order_end = $this->input->get('order_end');
        $status_filter = $this->input->get('status');
        $ratio_limit = (float) ($this->input->get('ratio') ?? 0);
        $ratio_status = $this->input->get('ratio_status');
        $matching_status = $this->input->get('matching_status');
        $type_status = $this->input->get('type_status');

        $data_comparison = [];

        if (($start_date && $end_date) || ($order_start && $order_end)) {
            $filterShopee = [];
            $filterTiktok = [];

            if ($marketplace_filter === 'Shopee') {
                $filterTiktok[] = "1 = 0";
            } elseif ($marketplace_filter === 'TikTok') {
                $filterShopee[] = "1 = 0";
            }

            // Date filters
            if ($start_date && $end_date) {
                $filterShopee[] = "asd.pay_date >= '{$start_date}'";
                $filterShopee[] = "asd.pay_date <= '{$end_date}'";
                $filterTiktok[] = "atd.pay_date >= '{$start_date}'";
                $filterTiktok[] = "atd.pay_date <= '{$end_date}'";
            }
            if ($order_start && $order_end) {
                $filterShopee[] = "asd.order_date >= '{$order_start}'";
                $filterShopee[] = "asd.order_date <= '{$order_end}'";
                $filterTiktok[] = "atd.order_date >= '{$order_start}'";
                $filterTiktok[] = "atd.order_date <= '{$order_end}'";
            }

            if ($type_status == 'retur') {
                $filterShopee[] = "asd.refund < 0";
                $filterTiktok[] = "atd.refund < 0";
            } elseif ($type_status == 'pembayaran') {
                $filterShopee[] = "asd.refund > 0";
                $filterTiktok[] = "atd.refund > 0";
            }

            $whereShopee = !empty($filterShopee) ? "WHERE " . implode(" AND ", $filterShopee) : "";
            $whereTiktok = !empty($filterTiktok) ? "WHERE " . implode(" AND ", $filterTiktok) : "";

            $sql = "
            SELECT
                asd.no_faktur,
                MAX(asd.order_date) AS shopee_order_date,
                MAX(asd.pay_date) AS shopee_pay_date,
                MAX(asd.total_faktur) AS shopee_total_faktur,
                MAX(asd.discount) AS shopee_discount,
                MAX(asd.payment) AS shopee_payment,
                MAX(asd.refund) AS shopee_refund,
                MAX(asd.note) AS note,
                MAX(asd.is_check) AS is_check,
                MAX(asd.status_dir) AS status_dir,
                MAX(aad.pay_date) AS accurate_pay_date,
                MAX(aad.total_faktur) AS accurate_total_faktur,
                MAX(aad.discount) AS accurate_discount,
                MAX(aad.payment) AS accurate_payment,
                'shopee' AS source
            FROM acc_shopee_detail asd
            LEFT JOIN acc_accurate_detail aad ON aad.no_faktur = asd.no_faktur
            {$whereShopee}
            GROUP BY asd.no_faktur

            UNION

            SELECT
                atd.no_faktur,
                MAX(atd.order_date) AS shopee_order_date,
                MAX(atd.pay_date) AS shopee_pay_date,
                MAX(atd.total_faktur) AS shopee_total_faktur,
                MAX(atd.discount) AS shopee_discount,
                MAX(atd.payment) AS shopee_payment,
                MAX(atd.refund) AS shopee_refund,
                MAX(atd.note) AS note,
                MAX(atd.is_check) AS is_check,
                MAX(atd.status_dir) AS status_dir,
                MAX(aad.pay_date) AS accurate_pay_date,
                MAX(aad.total_faktur) AS accurate_total_faktur,
                MAX(aad.discount) AS accurate_discount,
                MAX(aad.payment) AS accurate_payment,
                'tiktok' AS source
            FROM acc_tiktok_detail atd
            LEFT JOIN acc_accurate_detail aad ON aad.no_faktur = atd.no_faktur
            {$whereTiktok}
            GROUP BY atd.no_faktur
            ORDER BY no_faktur ASC
        ";

            $results = $this->db->query($sql)->result();
            $seen_faktur = [];

            foreach ($results as $row) {
                if (in_array($row->no_faktur, $seen_faktur)) continue;

                $sku_list = $this->db
                    ->select('sku')
                    ->from($row->source == 'Shopee' ? 'acc_shopee_detail_details' : 'acc_tiktok_detail_details')
                    ->where('no_faktur', $row->no_faktur)
                    ->get()
                    ->result();

                $skus = array_column($sku_list, 'sku');
                $total_price_bottom = 0;
                if (!empty($skus)) {
                    $this->db->select_sum('price_bottom', 'total_price_bottom');
                    $this->db->where_in('sku', $skus);
                    $result_bottom = $this->db->get('acc_shopee_bottom')->row();
                    $total_price_bottom = $result_bottom->total_price_bottom ?? 0;
                }
                $row->total_price_bottom = $total_price_bottom;

                $is_sudah_bayar = !empty($row->accurate_pay_date);
                if (
                    empty($status_filter) ||
                    ($status_filter == 'Sudah Bayar' && $is_sudah_bayar) ||
                    ($status_filter == 'Belum Bayar' && !$is_sudah_bayar)
                ) {

                    $shopee = (float) ($row->shopee_total_faktur ?? 0);
                    $accurate = (float) ($row->accurate_payment ?? 0);
                    $is_retur = ($row->shopee_refund ?? 0) < 0;

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
                        if ($accurate > 0 && $shopee > 0) {
                            $ratio = (($shopee - $accurate) / $accurate) * 100;
                            if ($ratio_status === 'lebih' && $ratio <= $ratio_limit) continue;
                        }

                        $data_comparison[] = $row;
                        $seen_faktur[] = $row->no_faktur;
                    }
                }
            }
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Comparison Report");

        // Header with company info and date range
        $sheet->mergeCells('A1:T1');
        $sheet->mergeCells('A2:T2');
        $sheet->mergeCells('A3:T3');

        $sheet->setCellValue('A1', 'Astahomeware');
        $sheet->setCellValue('A2', 'Comparison Report');

        $dateRange = '';
        if ($start_date && $end_date) {
            $dateRange .= 'Payment Date: ' . $start_date . ' - ' . $end_date;
        }
        if ($order_start && $order_end) {
            if ($dateRange) $dateRange .= ' | ';
            $dateRange .= 'Order Date: ' . $order_start . ' - ' . $order_end;
        }
        $sheet->setCellValue('A3', $dateRange ?: 'All Dates');

        $sheet->getStyle('A1:A3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(25);
        $sheet->getRowDimension(2)->setRowHeight(20);
        $sheet->getRowDimension(3)->setRowHeight(20);

        // Column headers
        $headers = [
            'A' => 'No',
            'B' => 'Marketplace',
            'C' => 'Nomor Faktur',
            'D' => 'Tanggal Pesanan',
            'E' => 'Tanggal Pembayaran',
            'F' => 'Total Faktur',
            'G' => 'Discount',
            'H' => 'Payment',
            'I' => 'Refund',
            'J' => 'Tanggal Pembayaran (ACC)',
            'K' => 'Total Faktur (ACC)',
            'L' => 'Discount (ACC)',
            'M' => 'Payment (ACC)',
            'N' => 'Selisih Ratio',
            'O' => 'Type Faktur',
            'P' => 'Status Matching',
            'Q' => 'Status Terbayar (ACC)',
            'R' => 'Invoice vs Bottom',
            'S' => 'Keterangan',
            'T' => 'Status Dir'
        ];

        foreach ($headers as $col => $header) {
            $sheet->setCellValue($col . '4', $header);
        }

        // Data rows
        $rowNumber = 5;
        foreach ($data_comparison as $row) {
            $accurate_payment = (float) ($row->accurate_payment ?? 0);
            $shopee_total = (float) ($row->shopee_total_faktur ?? 0);
            $ratio_diference = ($accurate_payment == 0) ? 0 : (($shopee_total - $accurate_payment) / $accurate_payment) * 100;

            $sheet->setCellValue("A$rowNumber", $rowNumber - 4);
            $sheet->setCellValue("B$rowNumber", $row->source == 'shopee' ? 'Shopee' : 'TikTok');
            $sheet->setCellValue("C$rowNumber", $row->no_faktur);
            $sheet->setCellValue("D$rowNumber", $row->shopee_order_date ?? '-');
            $sheet->setCellValue("E$rowNumber", $row->shopee_pay_date ?? '-');
            $sheet->setCellValue("F$rowNumber", $shopee_total);
            $sheet->setCellValue("G$rowNumber", $row->shopee_discount ?? 0);
            $sheet->setCellValue("H$rowNumber", $row->shopee_payment ?? 0);
            $sheet->setCellValue("I$rowNumber", $row->shopee_refund ?? 0);
            $sheet->setCellValue("J$rowNumber", $row->accurate_pay_date ?? '-');
            $sheet->setCellValue("K$rowNumber", $row->accurate_total_faktur ?? 0);
            $sheet->setCellValue("L$rowNumber", $row->accurate_discount ?? 0);
            $sheet->setCellValue("M$rowNumber", $accurate_payment);
            $sheet->setCellValue("N$rowNumber", round($ratio_diference, 2) . '%');
            $sheet->setCellValue("O$rowNumber", ($row->shopee_refund ?? 0) < 0 ? 'Retur' : 'Pembayaran');

            $match = (($row->accurate_total_faktur ?? 0) == ($row->shopee_total_faktur ?? 0) &&
                ($row->accurate_discount ?? 0) == ($row->shopee_discount ?? 0) &&
                ($row->accurate_payment ?? 0) == ($row->shopee_payment ?? 0)) ? 'Match' : 'Mismatch';
            $sheet->setCellValue("P$rowNumber", $match);

            $payment_status = !empty($row->accurate_payment) ? 'Sudah Bayar' : 'Belum Bayar';
            $sheet->setCellValue("Q$rowNumber", $payment_status);

            $invoice_vs_bottom = ($row->total_price_bottom ?? 0) > $shopee_total ? '< Bottom' : 'Invoice >';
            $sheet->setCellValue("R$rowNumber", $invoice_vs_bottom);
            $sheet->setCellValue("S$rowNumber", $row->note ?? '');

            if ($row->status_dir === 'Allowed') {
                $status_dir = 'Allowed by Dir';
            } elseif (
                $ratio_diference > $ratio_limit ||
                ($row->shopee_refund ?? 0) < 0 ||
                ($row->total_price_bottom ?? 0) > $shopee_total
            ) {
                $status_dir = 'Unsafe';
            } else {
                $status_dir = 'Safe';
            }
            $sheet->setCellValue("T$rowNumber", $status_dir);

            $rowNumber++;
        }

        // Formatting
        foreach (range('A', 'T') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Number formatting for numeric columns
        $numericColumns = ['F', 'G', 'H', 'I', 'K', 'L', 'M'];
        foreach ($numericColumns as $col) {
            $sheet->getStyle($col . '5:' . $col . ($rowNumber - 1))
                ->getNumberFormat()
                ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        }

        // Header styling
        $sheet->getStyle('A4:T4')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD9D9D9']]
        ]);

        // Freeze header row
        $sheet->freezePane('A5');

        $filename = 'Comparison_Report_' . date('Ymd_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function update_note()
    {
        $no_faktur = $this->input->post('no_faktur');
        $note = $this->input->post('note');

        // First try to update in Shopee table
        $this->db->where('no_faktur', $no_faktur);
        $shopee_updated = $this->db->update('acc_shopee_detail', ['note' => $note]);

        if ($this->db->affected_rows() > 0) {
            $updated_data = $this->db->get_where('acc_shopee_detail', ['no_faktur' => $no_faktur])->row();
            echo json_encode([
                'success' => true,
                'no_faktur' => $no_faktur,
                'note' => $updated_data->note,
                'source' => 'Shopee'
            ]);
            return;
        }

        // If not found in Shopee, try TikTok table
        $this->db->where('no_faktur', $no_faktur);
        $tiktok_updated = $this->db->update('acc_tiktok_detail', ['note' => $note]);

        if ($this->db->affected_rows() > 0) {
            $updated_data = $this->db->get_where('acc_tiktok_detail', ['no_faktur' => $no_faktur])->row();
            echo json_encode([
                'success' => true,
                'no_faktur' => $no_faktur,
                'note' => $updated_data->note,
                'source' => 'TikTok'
            ]);
            return;
        }

        // If not found in either table
        echo json_encode([
            'success' => false,
            'message' => 'Faktur tidak ditemukan di database Shopee maupun TikTok'
        ]);
    }

    public function update_checking()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $no_faktur = $this->input->post('no_faktur');

        if (empty($no_faktur)) {
            echo json_encode(['success' => false, 'message' => 'Nomor faktur tidak valid']);
            return;
        }

        // First try to update in Shopee table
        $this->db->where('no_faktur', $no_faktur);
        $shopee_updated = $this->db->update('acc_shopee_detail', ['is_check' => 1]);

        if ($this->db->affected_rows() > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Status checking berhasil diperbarui (Shopee)',
                'no_faktur' => $no_faktur,
                'source' => 'Shopee'
            ]);
            return;
        }

        // If not found in Shopee, try TikTok table
        $this->db->where('no_faktur', $no_faktur);
        $tiktok_updated = $this->db->update('acc_tiktok_detail', ['is_check' => 1]);

        if ($this->db->affected_rows() > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Status checking berhasil diperbarui (TikTok)',
                'no_faktur' => $no_faktur,
                'source' => 'TikTok'
            ]);
            return;
        }

        // If not found in either table
        echo json_encode([
            'success' => false,
            'message' => 'Faktur tidak ditemukan di database Shopee maupun TikTok',
            'no_faktur' => $no_faktur
        ]);
    }

    public function update_checking_batch()
    {
        $faktur_list = $this->input->post('faktur_list');

        if (empty($faktur_list)) {
            echo json_encode(['success' => false, 'message' => 'Data faktur kosong']);
            return;
        }

        // Initialize counters
        $shopee_updated = 0;
        $tiktok_updated = 0;
        $not_found = [];

        // Update Shopee orders
        $this->db->where_in('no_faktur', $faktur_list);
        $this->db->update('acc_shopee_detail', ['is_check' => 1]);
        $shopee_updated = $this->db->affected_rows();

        // Update TikTok orders
        $this->db->where_in('no_faktur', $faktur_list);
        $this->db->update('acc_tiktok_detail', ['is_check' => 1]);
        $tiktok_updated = $this->db->affected_rows();

        // Check which invoices weren't found
        $all_faktur = array_flip($faktur_list);

        // Remove found Shopee invoices
        if ($shopee_updated > 0) {
            $found_shopee = $this->db->select('no_faktur')
                ->from('acc_shopee_detail')
                ->where_in('no_faktur', $faktur_list)
                ->where('is_check', 1)
                ->get()
                ->result_array();

            foreach ($found_shopee as $faktur) {
                unset($all_faktur[$faktur['no_faktur']]);
            }
        }

        // Remove found TikTok invoices
        if ($tiktok_updated > 0) {
            $found_tiktok = $this->db->select('no_faktur')
                ->from('acc_tiktok_detail')
                ->where_in('no_faktur', $faktur_list)
                ->where('is_check', 1)
                ->get()
                ->result_array();

            foreach ($found_tiktok as $faktur) {
                unset($all_faktur[$faktur['no_faktur']]);
            }
        }

        $not_found = array_keys($all_faktur);

        $total_updated = $shopee_updated + $tiktok_updated;

        echo json_encode([
            'success' => $total_updated > 0,
            'message' => sprintf(
                'Berhasil update %d faktur (Shopee: %d, TikTok: %d). %d tidak ditemukan: %s',
                $total_updated,
                $shopee_updated,
                $tiktok_updated,
                count($not_found),
                implode(', ', $not_found)
            ),
            'stats' => [
                'shopee_updated' => $shopee_updated,
                'tiktok_updated' => $tiktok_updated,
                'not_found' => $not_found
            ]
        ]);
    }
}
