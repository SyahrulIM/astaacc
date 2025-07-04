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

        if (($start_date && $end_date) || ($order_start && $order_end)) {
            $this->db->select('
                asd.no_faktur,
                MAX(asd.order_date) AS shopee_order_date,
                MAX(asd.pay_date) AS shopee_pay_date,
                MAX(asd.total_faktur) AS shopee_total_faktur,
                MAX(asd.discount) AS shopee_discount,
                MAX(asd.payment) AS shopee_payment,
                MAX(asd.refund) AS shopee_refund,
                MAX(asd.status_dir) AS status_dir,

                MAX(aad.pay_date) AS accurate_pay_date,
                MAX(aad.total_faktur) AS accurate_total_faktur,
                MAX(aad.discount) AS accurate_discount,
                MAX(aad.payment) AS accurate_payment
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

            if ($type_status == 'retur') {
                $this->db->where('asd.refund <', 0);  // Only returns (0 or negative)
            } elseif ($type_status == 'pembayaran') {
                $this->db->where('asd.refund >', 0);
            }

            $this->db->order_by('asd.no_faktur', 'asc');
            $this->db->group_by('asd.no_faktur');
            $results = $this->db->get()->result();
            $seen_faktur = [];

            foreach ($results as $row) {
                if (in_array($row->no_faktur, $seen_faktur)) continue;

                // Ambil total price_bottom dari acc_shopee_bottom berdasarkan sku per no_faktur
                $sku_list = $this->db
                    ->select('sku')
                    ->from('acc_shopee_detail_details')
                    ->where('no_faktur', $row->no_faktur)
                    ->get()
                    ->result();

                $skus = array_column($sku_list, 'sku');

                $total_price_bottom = 0;
                if (!empty($skus)) {
                    $this->db->select_sum('price_bottom', 'total_price_bottom');
                    $this->db->where_in('sku', $skus);
                    $result = $this->db->get('acc_shopee_bottom')->row();
                    $total_price_bottom = $result->total_price_bottom ?? 0;
                }

                $row->total_price_bottom = $total_price_bottom;
                // End

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

                        $grand_total_invoice += $shopee;
                        $grand_total_payment += $accurate;

                        if (!$is_retur) {
                            $grand_total_invoice_non_retur += $shopee;
                            $grand_total_payment_non_retur += $accurate;
                        }

                        if ($is_retur) {
                            $grand_total_invoice_retur += $shopee;
                            $grand_total_payment_retur += $accurate;
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
            'grand_total_invoice' => $grand_total_invoice,
            'grand_total_payment' => $grand_total_payment,
            'grand_total_invoice_non_retur' => $grand_total_invoice_non_retur,
            'grand_total_payment_non_retur' => $grand_total_payment_non_retur,
            'grand_total_invoice_retur' => $grand_total_invoice_retur,
            'grand_total_payment_retur' => $grand_total_payment_retur,
            'grand_total_invoice_after_retur' => $grand_total_invoice - $grand_total_invoice_retur,
            'grand_total_payment_after_retur' => $grand_total_payment - $grand_total_invoice_retur,
            'difference_count' => $difference_count,
            'difference_count_non_retur' => $difference_count_non_retur,
            'exceed_ratio_count' => $exceed_ratio_count,
            'exceed_ratio_count_non_retur' => $exceed_ratio_count_non_retur,
            'ratio_status' => $ratio_status,
            'ratio_limit' => $ratio_limit,
            'matching_status' => $matching_status,
            'mismatch_count' => $mismatch_count,
            'retur_count' => $retur_count,
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
        MAX(asd.discount) AS shopee_discount,
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

    public function final_dir()
    {
        $no_faktur = $this->input->get('no_faktur');

        if (!$no_faktur) {
            echo json_encode(['status' => 'error', 'message' => 'Nomor faktur tidak ditemukan.']);
            return;
        }

        $this->db->where('no_faktur', $no_faktur);
        $updated = $this->db->update('acc_shopee_detail', ['status_dir' => 'Allowed']);

        if ($updated) {
            echo json_encode(['status' => 'success', 'message' => 'Status Dir berhasil diset sebagai "Allowed".']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengupdate status.']);
        }
    }

    public function final_dir_batch()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $json = json_decode($this->input->raw_input_stream, true);
        $faktur_list = $json['faktur_list'] ?? [];

        if (empty($faktur_list)) {
            echo json_encode(['success' => false, 'message' => 'Data faktur kosong.']);
            return;
        }

        // Contoh update ke database
        foreach ($faktur_list as $faktur) {
            $this->db->where('no_faktur', $faktur)->update('acc_shopee_detail', ['status_dir' => 'Allowed']);
        }

        echo json_encode(['success' => true]);
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
            MAX(asd.order_date) AS shopee_order_date,
            MAX(asd.pay_date) AS shopee_pay_date,
            MAX(asd.total_faktur) AS shopee_total_faktur,
            MAX(asd.discount) AS shopee_discount,
            MAX(asd.payment) AS shopee_payment,
            MAX(asd.refund) AS shopee_refund,
            MAX(asd.status_dir) AS status_dir,
            MAX(aad.pay_date) AS accurate_pay_date,
            MAX(aad.total_faktur) AS accurate_total_faktur,
            MAX(aad.discount) AS accurate_discount,
            MAX(aad.payment) AS accurate_payment
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
            $this->db->group_by('asd.no_faktur');
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

        // Merge and set header text (A1:M3)
        $sheet->mergeCells('A1:M1');
        $sheet->mergeCells('A2:M2');
        $sheet->mergeCells('A3:M3');

        $sheet->setCellValue('A1', 'Astahomeware');
        $sheet->setCellValue('A2', 'Comparison');
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

        // Set header (row 4)
        $headers = [
            'A4' => 'No',
            'B4' => 'Nomor Faktur',
            'C4' => 'Tanggal Pesanan',
            'D4' => 'Tanggal Pembayaran (ACC)',
            'E4' => 'Nominal Invoice',
            'F4' => 'Nilai Diterima (ACC)',
            'G4' => 'Selisih Ratio',
            'H4' => 'Selisih',
            'I4' => 'Type Faktur',
            'J4' => 'Status Matching',
            'K4' => 'Status Terbayar (ACC)',
            'L4' => 'Invoice vs Bottom',
            'M4' => 'Status Dir',
        ];
        foreach ($headers as $cell => $label) {
            $sheet->setCellValue($cell, $label);
        }

        // Set data mulai dari baris 5
        $rowNumber = 5;
        foreach ($data_comparison as $row) {
            $accurate_payment = (float) ($row->accurate_payment ?? 0);
            $shopee_total = (float) ($row->shopee_total_faktur ?? 0);
            $ratio_diference = ($accurate_payment == 0) ? 0 : (($shopee_total - $accurate_payment) / $accurate_payment) * 100;

            $sheet->setCellValue("A$rowNumber", $rowNumber - 4);
            $sheet->setCellValue("B$rowNumber", $row->no_faktur);
            $sheet->setCellValue("C$rowNumber", $row->shopee_order_date ?? '-');
            $sheet->setCellValue("D$rowNumber", $row->accurate_pay_date ?? '-');
            $sheet->setCellValue("E$rowNumber", $shopee_total);
            $sheet->setCellValue("F$rowNumber", $accurate_payment);
            $sheet->setCellValue("G$rowNumber", round($ratio_diference, 2) . '%');
            $sheet->setCellValue("H$rowNumber", $shopee_total - $accurate_payment);
            $sheet->setCellValue("I$rowNumber", ($row->shopee_refund ?? 0) < 0 ? 'Retur' : 'Pembayaran');

            $match = (($row->accurate_total_faktur ?? 0) != ($row->shopee_total_faktur ?? 0) ||
                ($row->accurate_discount ?? 0) != ($row->shopee_discount ?? 0) ||
                ($row->accurate_payment ?? 0) != ($row->shopee_payment ?? 0)) ? 'Mismatch' : 'Match';
            $sheet->setCellValue("J$rowNumber", $match);

            $payment_status = !empty($row->accurate_payment) ? 'Sudah Bayar' : 'Belum Bayar';
            $sheet->setCellValue("K$rowNumber", $payment_status);

            $invoice_vs_bottom = ($row->total_price_bottom ?? 0) > $shopee_total ? '< Bottom' : 'Invoice >';
            $sheet->setCellValue("L$rowNumber", $invoice_vs_bottom);

            $status_dir = $row->status_dir === 'Allowed'
                ? 'Allowed by Dir'
                : (($ratio_diference > $ratio_limit || ($row->shopee_refund ?? 0) < 0 || ($row->total_price_bottom ?? 0) > $shopee_total)
                    ? 'Unsafe'
                    : 'Safe');
            $sheet->setCellValue("M$rowNumber", $status_dir);

            $rowNumber++;
        }

        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->getStyle('E5:F' . ($rowNumber - 1))
            ->getNumberFormat()
            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->getStyle('H5:H' . ($rowNumber - 1))
            ->getNumberFormat()
            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        $sheet->getStyle('A4:M4')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD9D9D9']]
        ]);

        $filename = 'Comparison_Report_' . date('Ymd_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
