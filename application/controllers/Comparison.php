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
        // Get all filter parameters
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

        // Shopee totals
        $grand_total_invoice = 0;
        $grand_total_payment = 0;
        $grand_total_invoice_non_retur = 0;
        $grand_total_payment_non_retur = 0;
        $grand_total_invoice_retur = 0;
        $grand_total_payment_retur = 0;
        $difference_count = 0;
        $difference_count_non_retur = 0;
        $exceed_ratio_count = 0;
        $exceed_ratio_count_non_retur = 0;
        $mismatch_count = 0;
        $retur_count = 0;

        // TikTok totals
        $grand_total_invoice_tiktok = 0;
        $grand_total_payment_tiktok = 0;
        $grand_total_invoice_non_retur_tiktok = 0;
        $grand_total_payment_non_retur_tiktok = 0;
        $grand_total_invoice_retur_tiktok = 0;
        $grand_total_payment_retur_tiktok = 0;
        $difference_count_tiktok = 0;
        $difference_count_non_retur_tiktok = 0;
        $exceed_ratio_count_tiktok = 0;
        $exceed_ratio_count_non_retur_tiktok = 0;
        $mismatch_count_tiktok = 0;
        $retur_count_tiktok = 0;

        if (($start_date && $end_date) || ($order_start && $order_end)) {
            // Filter conditions
            $filterShopee = [];
            $filterTiktok = [];

            // Apply marketplace filter
            if ($marketplace_filter === 'Shopee') {
                $filterTiktok[] = "1 = 0"; // Exclude TikTok results
            } elseif ($marketplace_filter === 'TikTok') {
                $filterShopee[] = "1 = 0"; // Exclude Shopee results
            }

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

            // Combined Shopee + TikTok query
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
                'Shopee' AS source
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
                'TikTok' AS source
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

                // Get total price_bottom
                if ($row->source == 'Shopee') {
                    $sku_list = $this->db
                        ->select('sku')
                        ->from('acc_shopee_detail_details')
                        ->where('no_faktur', $row->no_faktur)
                        ->get()
                        ->result();
                } else {
                    $sku_list = $this->db
                        ->select('sku')
                        ->from('acc_tiktok_detail_details')
                        ->where('no_faktur', $row->no_faktur)
                        ->get()
                        ->result();
                }

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
                            // TikTok calculations
                            $grand_total_invoice_tiktok += $shopee;
                            $grand_total_payment_tiktok += $accurate;

                            if (!$is_retur) {
                                $grand_total_invoice_non_retur_tiktok += $shopee;
                                $grand_total_payment_non_retur_tiktok += $accurate;
                            } else {
                                $grand_total_invoice_retur_tiktok += $shopee;
                                $grand_total_payment_retur_tiktok += $accurate;
                            }

                            if ($shopee != $accurate) {
                                $difference_count_tiktok++;
                                if (!$is_retur) {
                                    $difference_count_non_retur_tiktok++;
                                }
                            }

                            if ($accurate > 0 && $shopee > 0) {
                                $ratio = (($shopee - $accurate) / $accurate) * 100;

                                if ($ratio_status === 'lebih' && $ratio <= $ratio_limit) continue;
                                if ($ratio > $ratio_limit) {
                                    $exceed_ratio_count_tiktok++;
                                    if (!$is_retur) {
                                        $exceed_ratio_count_non_retur_tiktok++;
                                    }
                                }
                            }

                            if (($row->accurate_total_faktur ?? 0) != ($row->shopee_total_faktur ?? 0) ||
                                ($row->accurate_discount ?? 0) != ($row->shopee_discount ?? 0) ||
                                ($row->accurate_payment ?? 0) != ($row->shopee_payment ?? 0)
                            ) {
                                $mismatch_count_tiktok++;
                            }

                            if ($row->shopee_refund < 0) {
                                $retur_count_tiktok++;
                            }
                        } else {
                            // Shopee calculations
                            $grand_total_invoice += $shopee;
                            $grand_total_payment += $accurate;

                            if (!$is_retur) {
                                $grand_total_invoice_non_retur += $shopee;
                                $grand_total_payment_non_retur += $accurate;
                            } else {
                                $grand_total_invoice_retur += $shopee;
                                $grand_total_payment_retur += $accurate;
                            }

                            if ($shopee != $accurate) {
                                $difference_count++;
                                if (!$is_retur) {
                                    $difference_count_non_retur++;
                                }
                            }

                            if ($accurate > 0 && $shopee > 0) {
                                $ratio = (($shopee - $accurate) / $accurate) * 100;

                                if ($ratio_status === 'lebih' && $ratio <= $ratio_limit) continue;
                                if ($ratio > $ratio_limit) {
                                    $exceed_ratio_count++;
                                    if (!$is_retur) {
                                        $exceed_ratio_count_non_retur++;
                                    }
                                }
                            }

                            if (($row->accurate_total_faktur ?? 0) != ($row->shopee_total_faktur ?? 0) ||
                                ($row->accurate_discount ?? 0) != ($row->shopee_discount ?? 0) ||
                                ($row->accurate_payment ?? 0) != ($row->shopee_payment ?? 0)
                            ) {
                                $mismatch_count++;
                            }

                            if ($row->shopee_refund < 0) {
                                $retur_count++;
                            }
                        }

                        $data_comparison[] = $row;
                        $seen_faktur[] = $row->no_faktur;
                    }
                }
            }
        }

        $additional_revenue = 0;
        if ($order_start && $order_end) {
            $this->db->select_sum('additional_revenue');
            $this->db->where('start_date >=', $order_start);
            $this->db->where('end_date <=', $order_end);
            $additional_data = $this->db->get('acc_shopee_additional')->row();
            $additional_revenue = $additional_data->additional_revenue ?? 0;
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
            'grand_total_invoice_after_retur' => $grand_total_invoice - $grand_total_invoice_retur - $grand_total_invoice_tiktok - $grand_total_invoice_retur_tiktok,
            'grand_total_payment_after_retur' => $grand_total_payment - $grand_total_invoice_retur - $grand_total_payment_tiktok - $grand_total_invoice_retur_tiktok,
            'difference_count' => $difference_count + $difference_count_tiktok,
            'difference_count_non_retur' => $difference_count_non_retur + $difference_count_non_retur_tiktok,
            'exceed_ratio_count' => $exceed_ratio_count + $exceed_ratio_count_tiktok,
            'exceed_ratio_count_non_retur' => $exceed_ratio_count_non_retur + $exceed_ratio_count_non_retur_tiktok,
            'mismatch_count' => $mismatch_count + $mismatch_count_tiktok,
            'retur_count' => $retur_count + $retur_count_tiktok,

            // Other data
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
        // Ambil data detail faktur
        $this->db->select('
        MAX(asd.total_faktur) AS shopee_total_faktur,
        MAX(asd.discount) AS shopee_discount,x
        MAX(asd.payment) AS shopee_payment,
        MAX(asd.refund) AS shopee_refund,
        MAX(aad.total_faktur) AS accurate_total_faktur,
        MAX(aad.discount) AS accurate_discount,
        MAX(aad.payment) AS accurate_payment
    ');
        $this->db->from('acc_shopee_detail asd');
        $this->db->join('acc_accurate_detail aad', 'aad.no_faktur = asd.no_faktur', 'left');
        $this->db->where('asd.no_faktur', $no_faktur);
        $detail = $this->db->get()->row();

        if (!$detail) {
            echo '<div class="text-danger">Data tidak ditemukan.</div>';
            return;
        }

        // Ambil detail produk Shopee
        $this->db->distinct();
        $this->db->select('no_faktur, sku, name_product, price_after_discount');
        $this->db->where('no_faktur', $no_faktur);
        $acc_shopee_detail_details = $this->db->get('acc_shopee_detail_details')->result();

        // Ambil harga bottom per SKU dari tabel acc_shopee_bottom
        $harga_bottom_map = [];
        if (!empty($acc_shopee_detail_details)) {
            $sku_list = array_column($acc_shopee_detail_details, 'sku');
            $this->db->select('sku, price_bottom');
            $this->db->where_in('sku', $sku_list);
            $bottoms = $this->db->get('acc_shopee_bottom')->result();
            foreach ($bottoms as $b) {
                $harga_bottom_map[$b->sku] = $b->price_bottom;
            }
        }

        // Tampilkan data ringkasan
        echo '
    <h5>Perbandingan Data - ' . $no_faktur . '</h5>
    <table class="table table-bordered mb-4">
        <thead><tr><th></th><th>Shopee</th><th>Accurate</th></tr></thead>
        <tr><th>Total Faktur</th><td>' . number_format($detail->shopee_total_faktur) . '</td><td>' . number_format($detail->accurate_total_faktur) . '</td></tr>
        <tr><th>Discount</th><td>' . number_format($detail->shopee_discount) . '</td><td>' . number_format($detail->accurate_discount) . '</td></tr>
        <tr><th>Pembayaran</th><td>' . number_format($detail->shopee_payment) . '</td><td>' . number_format($detail->accurate_payment) . '</td></tr>
        <tr><th>Refund</th><td>' . number_format($detail->shopee_refund) . '</td><td>0</td></tr>
    </table>';

        // Tampilkan detail SKU dan harga
        echo '
    <h5>Detail Produk (Shopee & Bottom)</h5>
    <table class="table table-striped table-bordered">
        <thead><tr><th>SKU</th><th>Nama Produk</th><th>Harga Invoice</th><th>Harga Bottom</th></tr></thead>
        <tbody>';

        foreach ($acc_shopee_detail_details as $item) {
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

        $order_start = $this->input->get('order_start');
        $order_end = $this->input->get('order_end');
        $status_filter = $this->input->get('status');
        $ratio_limit = (float) ($this->input->get('ratio') ?? 0);
        $ratio_status = $this->input->get('ratio_status');
        $matching_status = $this->input->get('matching_status');
        $type_status = $this->input->get('type_status');

        $data_comparison = [];

        if ($order_start && $order_end) {
            $this->db->select('
            asd.no_faktur,
            asd.order_date AS shopee_order_date,
            asd.pay_date AS shopee_pay_date,
            asd.total_faktur AS shopee_total_faktur,
            asd.discount AS shopee_discount,
            asd.payment AS shopee_payment,
            asd.refund AS shopee_refund,
            asd.note AS note,
            asd.is_check AS is_check,
            asd.status_dir AS status_dir,
            aad.pay_date AS accurate_pay_date,
            aad.total_faktur AS accurate_total_faktur,
            aad.discount AS accurate_discount,
            aad.payment AS accurate_payment
        ');
            $this->db->from('acc_shopee_detail asd');
            $this->db->join('acc_accurate_detail aad', 'aad.no_faktur = asd.no_faktur', 'left');
            $this->db->where('asd.order_date >=', $order_start);
            $this->db->where('asd.order_date <=', $order_end);

            if ($type_status == 'retur') {
                $this->db->where('asd.refund <', 0);
            } elseif ($type_status == 'pembayaran') {
                $this->db->where('asd.refund >', 0);
            }

            $this->db->order_by('asd.no_faktur', 'asc');
            $results = $this->db->get()->result();
            $seen_faktur = [];

            foreach ($results as $row) {
                if (in_array($row->no_faktur, $seen_faktur)) continue;

                $sku_list = $this->db->select('sku')
                    ->from('acc_shopee_detail_details')
                    ->where('no_faktur', $row->no_faktur)
                    ->get()->result();
                $skus = array_column($sku_list, 'sku');

                $total_price_bottom = 0;
                if (!empty($skus)) {
                    $this->db->select_sum('price_bottom', 'total_price_bottom');
                    $this->db->where_in('sku', $skus);
                    $result = $this->db->get('acc_shopee_bottom')->row();
                    $total_price_bottom = $result->total_price_bottom ?? 0;
                }
                $row->total_price_bottom = $total_price_bottom;

                $is_sudah_bayar = !empty($row->accurate_pay_date);
                $is_match = (
                    ($row->accurate_total_faktur ?? 0) == ($row->shopee_total_faktur ?? 0) &&
                    ($row->accurate_discount ?? 0) == ($row->shopee_discount ?? 0) &&
                    ($row->accurate_payment ?? 0) == ($row->shopee_payment ?? 0)
                );

                $shopee = (float) ($row->shopee_total_faktur ?? 0);
                $accurate = (float) ($row->accurate_payment ?? 0);

                $pass_status_filter = empty($status_filter) ||
                    ($status_filter == 'Sudah Bayar' && $is_sudah_bayar) ||
                    ($status_filter == 'Belum Bayar' && !$is_sudah_bayar);

                $pass_matching_filter = empty($matching_status) ||
                    ($matching_status == 'match' && $is_match) ||
                    ($matching_status == 'mismatch' && !$is_match);

                $pass_ratio_filter = true;
                if ($accurate > 0 && $shopee > 0) {
                    $ratio = (($shopee - $accurate) / $accurate) * 100;
                    if ($ratio_status === 'lebih' && $ratio <= $ratio_limit) {
                        $pass_ratio_filter = false;
                    }
                }

                if ($pass_status_filter && $pass_matching_filter && $pass_ratio_filter) {
                    $data_comparison[] = $row;
                    $seen_faktur[] = $row->no_faktur;
                }
            }
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Comparison Report");

        // Header with company info and date range
        $sheet->mergeCells('A1:O1');
        $sheet->mergeCells('A2:O2');
        $sheet->mergeCells('A3:O3');

        $sheet->setCellValue('A1', 'Astahomeware');
        $sheet->setCellValue('A2', 'Comparison Report');
        $sheet->setCellValue('A3', 'Periode: ' . ($order_start ?? '-') . ' s.d. ' . ($order_end ?? '-'));

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
            'B' => 'Nomor Faktur',
            'C' => 'Tanggal Pesanan (Shopee)',
            'D' => 'Tanggal Pembayaran (Shopee)',
            'E' => 'Total Faktur (Shopee)',
            'F' => 'Discount (Shopee)',
            'G' => 'Payment (Shopee)',
            'H' => 'Refund (Shopee)',
            'I' => 'Tanggal Pembayaran (ACC)',
            'J' => 'Total Faktur (ACC)',
            'K' => 'Discount (ACC)',
            'L' => 'Payment (ACC)',
            'M' => 'Selisih Ratio',
            'N' => 'Type Faktur',
            'O' => 'Status Matching',
            'P' => 'Status Terbayar (ACC)',
            'Q' => 'Invoice vs Bottom',
            'R' => 'Keterangan',
            'S' => 'Status Check',
            'T' => 'Status Dir'
        ];

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '4', $header);
            $col++;
        }

        // Data rows
        $rowNumber = 5;
        foreach ($data_comparison as $row) {
            $accurate_payment = (float) ($row->accurate_payment ?? 0);
            $shopee_total = (float) ($row->shopee_total_faktur ?? 0);
            $ratio_diference = ($accurate_payment == 0) ? 0 : (($shopee_total - $accurate_payment) / $accurate_payment) * 100;

            $sheet->setCellValue("A$rowNumber", $rowNumber - 4);
            $sheet->setCellValue("B$rowNumber", $row->no_faktur);
            $sheet->setCellValue("C$rowNumber", $row->shopee_order_date ?? '-');
            $sheet->setCellValue("D$rowNumber", $row->shopee_pay_date ?? '-');
            $sheet->setCellValue("E$rowNumber", $shopee_total);
            $sheet->setCellValue("F$rowNumber", $row->shopee_discount ?? 0);
            $sheet->setCellValue("G$rowNumber", $row->shopee_payment ?? 0);
            $sheet->setCellValue("H$rowNumber", $row->shopee_refund ?? 0);
            $sheet->setCellValue("I$rowNumber", $row->accurate_pay_date ?? '-');
            $sheet->setCellValue("J$rowNumber", $row->accurate_total_faktur ?? 0);
            $sheet->setCellValue("K$rowNumber", $row->accurate_discount ?? 0);
            $sheet->setCellValue("L$rowNumber", $accurate_payment);
            $sheet->setCellValue("M$rowNumber", round($ratio_diference, 2) . '%');
            $sheet->setCellValue("N$rowNumber", ($row->shopee_refund ?? 0) < 0 ? 'Retur' : 'Pembayaran');

            $match = (($row->accurate_total_faktur ?? 0) != ($row->shopee_total_faktur ?? 0) ||
                ($row->accurate_discount ?? 0) != ($row->shopee_discount ?? 0) ||
                ($row->accurate_payment ?? 0) != ($row->shopee_payment ?? 0)) ? 'Mismatch' : 'Match';
            $sheet->setCellValue("O$rowNumber", $match);

            $payment_status = !empty($row->accurate_payment) ? 'Sudah Bayar' : 'Belum Bayar';
            $sheet->setCellValue("P$rowNumber", $payment_status);

            $invoice_vs_bottom = ($row->total_price_bottom ?? 0) > $shopee_total ? '< Bottom' : 'Invoice >';
            $sheet->setCellValue("Q$rowNumber", $invoice_vs_bottom);
            $sheet->setCellValue("R$rowNumber", $row->note ?? '');
            $sheet->setCellValue("S$rowNumber", $row->is_check ? 'Yes' : 'No');
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
        $numericColumns = ['E', 'F', 'G', 'H', 'J', 'K', 'L'];
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

        $this->db->where('no_faktur', $no_faktur);
        $result = $this->db->update('acc_shopee_detail', ['note' => $note]);

        if ($result) {
            // Ambil data terbaru untuk dikembalikan
            $updated_data = $this->db->get_where('acc_shopee_detail', ['no_faktur' => $no_faktur])->row();
            echo json_encode([
                'success' => true,
                'no_faktur' => $no_faktur,
                'note' => $updated_data->note
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan keterangan']);
        }
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

        $this->db->where('no_faktur', $no_faktur);
        $updated = $this->db->update('acc_shopee_detail', ['is_check' => 1]);

        if ($updated) {
            echo json_encode([
                'success' => true,
                'message' => 'Status checking berhasil diperbarui',
                'no_faktur' => $no_faktur
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal memperbarui status checking']);
        }
    }

    public function update_checking_batch()
    {
        $faktur_list = $this->input->post('faktur_list');

        if (empty($faktur_list)) {
            echo json_encode(['success' => false, 'message' => 'Data faktur kosong']);
            return;
        }

        $this->db->where_in('no_faktur', $faktur_list);
        $updated = $this->db->update('acc_shopee_detail', ['is_check' => 1]);

        echo json_encode([
            'success' => $updated,
            'message' => $updated ? 'Status checking berhasil diperbarui' : 'Gagal memperbarui status'
        ]);
    }
}
