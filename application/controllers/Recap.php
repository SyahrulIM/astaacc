<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class Recap extends CI_Controller
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
        $title = 'Import Payment';

        $acc_recap = $this->db->query("
    SELECT 
        user.full_name AS full_name,
        DATE_FORMAT(acc_shopee.created_date, '%Y-%m-%d %H:%i:%s') AS created_date,
        acc_shopee.idacc_shopee AS id_data,
        acc_shopee.excel_type AS type,
        CASE 
            WHEN acc_shopee.is_kotime = 1 THEN 'shopee_kotime'
            ELSE 'shopee'
        END AS source
    FROM acc_shopee
    JOIN user ON user.iduser = acc_shopee.iduser
    WHERE acc_shopee.created_date IS NOT NULL

    UNION ALL

    SELECT 
        user.full_name AS full_name,
        DATE_FORMAT(acc_tiktok.created_date, '%Y-%m-%d %H:%i:%s') AS created_date,
        acc_tiktok.idacc_tiktok AS id_data,
        acc_tiktok.excel_type AS type,
        CASE 
            WHEN acc_tiktok.is_kotime = 1 THEN 'tiktok_kotime'
            ELSE 'tiktok'
        END AS source
    FROM acc_tiktok
    JOIN user ON user.iduser = acc_tiktok.iduser
    WHERE acc_tiktok.created_date IS NOT NULL

    UNION ALL

    SELECT 
        user.full_name AS full_name,
        DATE_FORMAT(acc_accurate.created_date, '%Y-%m-%d %H:%i:%s') AS created_date,
        acc_accurate.idacc_accurate AS id_data,
        'accurate' AS type,
        'accurate' AS source
    FROM acc_accurate
    JOIN user ON user.iduser = acc_accurate.iduser
    WHERE acc_accurate.created_date IS NOT NULL

    UNION ALL

    SELECT 
        user.full_name AS full_name,
        DATE_FORMAT(acc_lazada.created_date, '%Y-%m-%d %H:%i:%s') AS created_date,
        acc_lazada.idacc_lazada AS id_data,
        acc_lazada.excel_type AS type,
        CASE 
            WHEN acc_lazada.is_kotime = 1 THEN 'lazada_kotime'
            ELSE 'lazada'
        END AS source
    FROM acc_lazada
    JOIN user ON user.iduser = acc_lazada.iduser
    WHERE acc_lazada.created_date IS NOT NULL

    ORDER BY created_date DESC
")->result();

        // Query untuk detail data
        $acc_recap_detail = $this->db->query("
    SELECT 
        d.no_faktur,
        DATE_FORMAT(d.pay_date, '%Y-%m-%d') AS pay_date,
        d.total_faktur,
        d.pay,
        d.discount,
        d.payment,
        DATE_FORMAT(d.order_date, '%Y-%m-%d') AS order_date,
        d.refund,
        d.idacc_shopee_detail AS id_detail,
        CASE 
            WHEN s.is_kotime = 1 THEN 'shopee_kotime'
            ELSE 'shopee'
        END AS source
    FROM acc_shopee_detail d
    JOIN (
        SELECT no_faktur, MAX(idacc_shopee_detail) AS max_id
        FROM acc_shopee_detail
        GROUP BY no_faktur
    ) x ON d.no_faktur = x.no_faktur AND d.idacc_shopee_detail = x.max_id
    JOIN acc_shopee s ON s.idacc_shopee = d.idacc_shopee
    JOIN user u ON u.iduser = s.iduser
    WHERE d.pay_date IS NOT NULL

    UNION ALL

    SELECT 
        d.no_faktur,
        DATE_FORMAT(d.pay_date, '%Y-%m-%d') AS pay_date,
        d.total_faktur,
        d.pay,
        d.discount,
        d.payment,
        DATE_FORMAT(d.order_date, '%Y-%m-%d') AS order_date,
        d.refund,
        d.idacc_tiktok_detail AS id_detail,
        CASE 
            WHEN t.is_kotime = 1 THEN 'tiktok_kotime'
            ELSE 'tiktok'
        END AS source
    FROM acc_tiktok_detail d
    JOIN (
        SELECT no_faktur, MAX(idacc_tiktok_detail) AS max_id
        FROM acc_tiktok_detail
        GROUP BY no_faktur
    ) x ON d.no_faktur = x.no_faktur AND d.idacc_tiktok_detail = x.max_id
    JOIN acc_tiktok t ON t.idacc_tiktok = d.idacc_tiktok
    JOIN user u ON u.iduser = t.iduser
    WHERE d.pay_date IS NOT NULL

    UNION ALL

    SELECT 
        d.no_faktur,
        DATE_FORMAT(d.pay_date, '%Y-%m-%d') AS pay_date,
        d.total_faktur,
        d.pay,
        d.discount,
        d.payment,
        NULL AS order_date,
        NULL AS refund,
        d.idacc_accurate_detail AS id_detail,
        'accurate' AS source
    FROM acc_accurate_detail d
    JOIN (
        SELECT no_faktur, MAX(idacc_accurate_detail) AS max_id
        FROM acc_accurate_detail
        GROUP BY no_faktur
    ) x ON d.no_faktur = x.no_faktur AND d.idacc_accurate_detail = x.max_id
    JOIN acc_accurate a ON a.idacc_accurate = d.idacc_accurate
    JOIN user u ON u.iduser = a.iduser
    WHERE d.pay_date IS NOT NULL

    UNION ALL

    SELECT 
        d.no_faktur,
        DATE_FORMAT(d.pay_date, '%Y-%m-%d') AS pay_date,
        d.total_faktur,
        d.pay,
        d.discount,
        d.payment,
        DATE_FORMAT(d.order_date, '%Y-%m-%d') AS order_date,
        d.refund,
        d.idacc_lazada_detail AS id_detail,
        CASE 
            WHEN l.is_kotime = 1 THEN 'lazada_kotime'
            ELSE 'lazada'
        END AS source
    FROM acc_lazada_detail d
    JOIN (
        SELECT no_faktur, MAX(idacc_lazada_detail) AS max_id
        FROM acc_lazada_detail
        GROUP BY no_faktur
    ) x ON d.no_faktur = x.no_faktur AND d.idacc_lazada_detail = x.max_id
    JOIN acc_lazada l ON l.idacc_lazada = d.idacc_lazada
    JOIN user u ON u.iduser = l.iduser
    WHERE d.pay_date IS NOT NULL

    ORDER BY pay_date DESC
")->result();

        $data = [
            'title' => $title,
            'acc_recap' => $acc_recap,
            'acc_recap_detail' => $acc_recap_detail
        ];

        $this->load->view('theme/v_head', $data);
        $this->load->view('Recap/v_recap');
    }

    public function createRecap()
    {
        $marketplace = $this->input->post('marketplace');
        $type_excel = $this->input->post('typeExcel');
        $file = $_FILES['file']['tmp_name'];

        if (empty($file)) {
            $this->session->set_flashdata('error', 'File tidak ditemukan.');
            redirect('recap');
            return;
        }

        require APPPATH . '../vendor/autoload.php';
        $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

        if ($extension === 'csv' || $extension === 'txt') {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
            $reader->setDelimiter($marketplace === 'shopee' ? "\t" : ",");
        } else {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        }

        try {
            $spreadsheet = $reader->load($file);

            // Determine if it's a kotime version
            $is_kotime = strpos($marketplace, '_kotime') !== false;
            $base_marketplace = $is_kotime ? str_replace('_kotime', '', $marketplace) : $marketplace;

            $header_data = [
                'iduser' => $this->session->userdata('iduser'),
                'created_by' => $this->session->userdata('username'),
                'created_date' => date('Y-m-d H:i:s'),
                'status' => 1,
                'excel_type' => $type_excel,
                'is_kotime' => $is_kotime ? 1 : 0
            ];

            switch ($base_marketplace) {
                case 'shopee':
                    $this->db->insert('acc_shopee', $header_data);
                    $id_header = $this->db->insert_id();

                    if ($type_excel === 'income') {
                        $incomeSheets = [];
                        foreach ($spreadsheet->getSheetNames() as $sheetName) {
                            if (stripos($sheetName, 'income') !== false) {
                                $incomeSheets[] = $sheetName;
                            }
                        }
                        if (empty($incomeSheets)) {
                            $this->session->set_flashdata('error', 'Tidak ditemukan sheet income dalam file.');
                            redirect('recap');
                            return;
                        }

                        $processedOrders = 0;
                        foreach ($incomeSheets as $sheetName) {
                            $sheet = $spreadsheet->getSheetByName($sheetName);
                            $highestRow = $sheet->getHighestRow();
                            for ($rowIndex = 2; $rowIndex <= $highestRow; $rowIndex++) {
                                $noFaktur = $sheet->getCell('B' . $rowIndex)->getValue();
                                if (empty($noFaktur)) continue;
                                $orderDate = $sheet->getCell('E' . $rowIndex)->getFormattedValue();
                                $payDate = $sheet->getCell('G' . $rowIndex)->getFormattedValue();
                                $hargaAsli = floatval(str_replace(['.', ','], '', $sheet->getCell('H' . $rowIndex)->getValue()));
                                $totalDiskon = abs(floatval(str_replace(['.', ','], '', $sheet->getCell('I' . $rowIndex)->getValue())));
                                $payment = floatval(str_replace(['.', ','], '', $sheet->getCell('AB' . $rowIndex)->getValue()));
                                $refund = floatval(str_replace(['.', ','], '', $sheet->getCell('J' . $rowIndex)->getValue()));
                                $income = floatval(str_replace(['.', ','], '', $sheet->getCell('AD' . $rowIndex)->getValue()));
                                $total = $hargaAsli - $totalDiskon;
                                $discount = $total - $income;

                                $detail = [
                                    'idacc_shopee' => $id_header,
                                    'no_faktur' => $noFaktur,
                                    'order_date' => date('Y-m-d', strtotime($orderDate)),
                                    'pay_date' => $payDate ? date('Y-m-d', strtotime($payDate)) : null,
                                    'total_faktur' => $total,
                                    'pay' => $total,
                                    'payment' => $income,
                                    'discount' => $discount,
                                    'refund' => $refund,
                                    'is_check' => 0,
                                    'created_date' => date('Y-m-d H:i:s'),
                                    'created_by' => $this->session->userdata('username'),
                                    'updated_date' => date('Y-m-d H:i:s'),
                                    'updated_by' => $this->session->userdata('username'),
                                    'status' => 1
                                ];

                                // Upsert for acc_shopee_detail
                                $exists = $this->db->get_where('acc_shopee_detail', [
                                    'no_faktur' => $noFaktur,
                                    'idacc_shopee' => $id_header
                                ])->row();

                                if ($exists) {
                                    $this->db->where('no_faktur', $noFaktur)
                                        ->where('idacc_shopee', $id_header)
                                        ->update('acc_shopee_detail', $detail);
                                } else {
                                    $this->db->insert('acc_shopee_detail', $detail);
                                }
                                $processedOrders++;
                            }
                        }
                        $this->session->set_flashdata($processedOrders > 0 ? 'success' : 'error', $processedOrders > 0 ? "Data Shopee " . ($is_kotime ? "Kotime" : "Asta") . " Income berhasil diimport ($processedOrders orders)." : 'Tidak ada data order yang ditemukan dalam sheet income.');
                    } else {
                        $sheet = $spreadsheet->getActiveSheet();
                        $rows = $sheet->toArray(null, true, true, true);
                        $processedOrders = 0;
                        foreach ($rows as $i => $row) {
                            if ($i < 2 || empty($row['A'])) continue;
                            $price = floatval(str_replace('.', '', $row['Q']));
                            $fullAddress = isset($row['AT']) ? trim($row['AT']) : null;
                            preg_match('/(\d{5})(?!.*\d)/', $fullAddress, $matches);
                            $posCode = $matches[1] ?? null;

                            $detail_order = [
                                'no_faktur' => $row['A'],
                                'sku' => $row['N'],
                                'name_product' => $row['M'],
                                'price_after_discount' => $price,
                                'address' => $fullAddress,
                                'pos_code' => $posCode,
                                'created_date' => date('Y-m-d H:i:s'),
                                'created_by' => $this->session->userdata('username'),
                                'updated_date' => date('Y-m-d H:i:s'),
                                'updated_by' => $this->session->userdata('username'),
                                'status' => 1
                            ];

                            // FIXED: Remove idacc_shopee from WHERE clause for acc_shopee_detail_details
                            $exists = $this->db->get_where('acc_shopee_detail_details', [
                                'no_faktur' => $row['A']
                            ])->row();

                            if ($exists) {
                                $this->db->where('no_faktur', $row['A'])
                                    ->update('acc_shopee_detail_details', $detail_order);
                            } else {
                                $this->db->insert('acc_shopee_detail_details', $detail_order);
                            }
                            $processedOrders++;
                        }
                        $this->session->set_flashdata($processedOrders > 0 ? 'success' : 'error', $processedOrders > 0 ? "Data Shopee " . ($is_kotime ? "Kotime" : "Asta") . " Order berhasil diimport ($processedOrders orders)." : 'Tidak ada data order yang ditemukan.');
                    }
                    break;

                case 'tiktok':
                    $this->db->insert('acc_tiktok', $header_data);
                    $id_header = $this->db->insert_id();

                    if ($type_excel === 'income') {
                        $sheet = $spreadsheet->getActiveSheet();
                        $highestRow = $sheet->getHighestRow();
                        $processedOrders = 0;
                        for ($rowIndex = 2; $rowIndex <= $highestRow; $rowIndex++) {
                            $noFaktur = $sheet->getCell('A' . $rowIndex)->getValue();
                            if (empty($noFaktur)) continue;
                            $orderDateRaw = $sheet->getCell('C' . $rowIndex)->getFormattedValue();
                            $orderDate = date('Y-m-d', strtotime(str_replace('/', '-', $orderDateRaw)));
                            $payDateRaw = $sheet->getCell('D' . $rowIndex)->getFormattedValue();
                            $payDate = date('Y-m-d', strtotime(str_replace('/', '-', $payDateRaw)));
                            $totalFaktur = $sheet->getCell('H' . $rowIndex)->getValue();
                            $payment = $sheet->getCell('F' . $rowIndex)->getValue();
                            $discountRaw = $sheet->getCell('N' . $rowIndex)->getValue();
                            $discount = is_numeric($discountRaw) ? abs($discountRaw) : str_replace('-', '', $discountRaw);
                            $refund = $sheet->getCell('K' . $rowIndex)->getValue();

                            $detail = [
                                'idacc_tiktok' => $id_header,
                                'no_faktur' => $noFaktur,
                                'order_date' => $orderDate,
                                'pay_date' => $payDate ? date('Y-m-d', strtotime($payDate)) : null,
                                'total_faktur' => $totalFaktur,
                                'pay' => $totalFaktur,
                                'payment' => $payment,
                                'discount' => $discount,
                                'refund' => $refund,
                                'is_check' => 0,
                                'created_date' => date('Y-m-d H:i:s'),
                                'created_by' => $this->session->userdata('username'),
                                'updated_date' => date('Y-m-d H:i:s'),
                                'updated_by' => $this->session->userdata('username'),
                                'status' => 1
                            ];

                            // Upsert for acc_tiktok_detail
                            $exists = $this->db->get_where('acc_tiktok_detail', [
                                'no_faktur' => $noFaktur,
                                'idacc_tiktok' => $id_header
                            ])->row();

                            if ($exists) {
                                $this->db->where('no_faktur', $noFaktur)
                                    ->where('idacc_tiktok', $id_header)
                                    ->update('acc_tiktok_detail', $detail);
                            } else {
                                $this->db->insert('acc_tiktok_detail', $detail);
                            }
                            $processedOrders++;
                        }
                        $this->session->set_flashdata($processedOrders > 0 ? 'success' : 'error', $processedOrders > 0 ? "Data TikTok " . ($is_kotime ? "Kotime" : "Asta") . " Income berhasil diimpor ($processedOrders orders)." : 'Tidak ada data order yang ditemukan.');
                    } else {
                        $sheet = $spreadsheet->getActiveSheet();
                        $rows = $sheet->toArray(null, true, true, true);
                        $processedOrders = 0;
                        foreach ($rows as $i => $row) {
                            if ($i < 3 || empty($row['A'])) continue;
                            $price = floatval(str_replace('.', '', $row['P']));
                            $fullAddress = trim(($row['AV'] ?? '') . ', ' . ($row['AU'] ?? '') . ', ' . ($row['AT'] ?? ''));
                            $posCode = '';

                            $detail_order = [
                                'no_faktur' => $row['A'],
                                'sku' => $row['G'],
                                'name_product' => $row['H'],
                                'price_after_discount' => $price,
                                'address' => $fullAddress,
                                'pos_code' => $posCode,
                                'created_date' => date('Y-m-d H:i:s'),
                                'created_by' => $this->session->userdata('username'),
                                'updated_date' => date('Y-m-d H:i:s'),
                                'updated_by' => $this->session->userdata('username'),
                                'status' => 1
                            ];

                            // FIXED: Remove idacc_tiktok from WHERE clause for acc_tiktok_detail_details
                            $exists = $this->db->get_where('acc_tiktok_detail_details', [
                                'no_faktur' => $row['A']
                            ])->row();

                            if ($exists) {
                                $this->db->where('no_faktur', $row['A'])
                                    ->update('acc_tiktok_detail_details', $detail_order);
                            } else {
                                $this->db->insert('acc_tiktok_detail_details', $detail_order);
                            }
                            $processedOrders++;
                        }
                        $this->session->set_flashdata($processedOrders > 0 ? 'success' : 'error', $processedOrders > 0 ? "Data TikTok " . ($is_kotime ? "Kotime" : "Asta") . " Order berhasil diimpor ($processedOrders orders)." : 'Tidak ada data order yang ditemukan.');
                    }
                    break;

                case 'accurate':
                    $header_data['is_kotime'] = $is_kotime ? 1 : 0;
                    $this->db->insert('acc_accurate', $header_data);
                    $id_header = $this->db->insert_id();

                    $sheet = $spreadsheet->getActiveSheet();
                    $rows = $sheet->toArray(null, true, true, true);
                    $processedOrders = 0;
                    foreach ($rows as $i => $row) {
                        if ($i < 6 || !$row['B']) continue;
                        $detail = [
                            'idacc_accurate' => $id_header,
                            'no_faktur' => $row['B'],
                            'pay_date' => date('Y-m-d', strtotime($row['H'])),
                            'total_faktur' => str_replace(',', '', $row['J']),
                            'pay' => str_replace(',', '', $row['L']),
                            'discount' => str_replace(',', '', $row['N']),
                            'payment' => str_replace(',', '', $row['P'])
                        ];

                        // Upsert for acc_accurate_detail
                        $exists = $this->db->get_where('acc_accurate_detail', [
                            'no_faktur' => $row['B'],
                            'idacc_accurate' => $id_header
                        ])->row();

                        if ($exists) {
                            $this->db->where('no_faktur', $row['B'])
                                ->where('idacc_accurate', $id_header)
                                ->update('acc_accurate_detail', $detail);
                        } else {
                            $this->db->insert('acc_accurate_detail', $detail);
                        }
                        $processedOrders++;
                    }
                    $this->session->set_flashdata($processedOrders > 0 ? 'success' : 'error', $processedOrders > 0 ? "Data Accurate " . ($is_kotime ? "Kotime" : "Asta") . " berhasil diimport ($processedOrders orders)." : 'Tidak ada data order yang ditemukan.');
                    break;

                case 'lazada':
                    $this->db->insert('acc_lazada', $header_data);
                    $id_header = $this->db->insert_id();

                    $sheet = $spreadsheet->getActiveSheet();
                    $rows = $sheet->toArray(null, true, true, true);
                    $processedOrders = 0;

                    // Create arrays to store order data
                    $orderData = [];

                    foreach ($rows as $i => $row) {
                        if ($i < 2) continue;

                        // Get order number from column K (Nomor Pesanan)
                        $orderNumber = trim($row['K'] ?? '');
                        if (empty($orderNumber)) continue;

                        $feeName = trim($row['D'] ?? '');
                        $amount = floatval(str_replace(['.', ','], '', $row['E'] ?? '0'));

                        // Clean up fee name
                        $feeName = preg_replace('/\s+/', ' ', $feeName);

                        // Initialize order data if not exists
                        if (!isset($orderData[$orderNumber])) {
                            $orderData[$orderNumber] = [
                                'no_faktur' => $orderNumber,
                                'order_date' => !empty($row['J']) ? date('Y-m-d', strtotime($row['J'])) : null,
                                'pay_date' => !empty($row['H']) ? date('Y-m-d', strtotime($row['H'])) : null,
                                'total_faktur' => 0,
                                'total_sum' => 0,
                                'discount_sum' => 0,
                                'refund' => 0,
                                'fee_details' => [],
                                'omset_details' => []
                            ];
                        }

                        // Store fee details
                        $orderData[$orderNumber]['fee_details'][] = [
                            'name' => $feeName,
                            'amount' => $amount
                        ];

                        // Sum all amounts
                        $orderData[$orderNumber]['total_sum'] += $amount;

                        // Check fee type
                        if (strpos($feeName, 'Omset Penjualan') !== false) {
                            // Store each omset entry separately
                            $orderData[$orderNumber]['omset_details'][] = [
                                'amount' => abs($amount),
                                'sku' => trim($row['L'] ?? ''),
                                'product_name' => trim($row['T'] ?? '')
                            ];
                            $orderData[$orderNumber]['total_faktur'] += abs($amount);
                        } elseif (strpos($feeName, 'Diskon') !== false || strpos($feeName, 'Promosi') !== false) {
                            $orderData[$orderNumber]['discount_sum'] += abs($amount);
                        }
                    }

                    // Process each order
                    foreach ($orderData as $orderNumber => $data) {
                        $pay = $data['total_sum'];
                        $total_faktur = $data['total_faktur'];
                        $discount = $data['discount_sum'];

                        $detail = [
                            'idacc_lazada' => $id_header,
                            'no_faktur' => $orderNumber,
                            'order_date' => $data['order_date'],
                            'pay_date' => $data['pay_date'],
                            'total_faktur' => $total_faktur,
                            'pay' => $pay,
                            'discount' => $discount,
                            'payment' => $pay,
                            'refund' => $data['refund'],
                            'is_check' => 0,
                            'created_date' => date('Y-m-d H:i:s'),
                            'created_by' => $this->session->userdata('username'),
                            'updated_date' => date('Y-m-d H:i:s'),
                            'updated_by' => $this->session->userdata('username'),
                            'status' => 1
                        ];

                        // Upsert main order detail - Keep idacc_lazada for acc_lazada_detail
                        $exists = $this->db->get_where('acc_lazada_detail', [
                            'no_faktur' => $orderNumber,
                            'idacc_lazada' => $id_header
                        ])->row();

                        if ($exists) {
                            $this->db->where('no_faktur', $orderNumber)
                                ->where('idacc_lazada', $id_header)
                                ->update('acc_lazada_detail', $detail);
                        } else {
                            $this->db->insert('acc_lazada_detail', $detail);
                        }

                        // Insert order details for each omset entry
                        if (!empty($data['omset_details'])) {
                            foreach ($data['omset_details'] as $omset) {
                                if (!empty($omset['sku']) && !empty($omset['product_name'])) {
                                    $detail_order = [
                                        'no_faktur' => $orderNumber,
                                        'sku' => $omset['sku'],
                                        'name_product' => $omset['product_name'],
                                        'price_after_discount' => $omset['amount'],
                                        'address' => '',
                                        'pos_code' => '',
                                        'created_date' => date('Y-m-d H:i:s'),
                                        'created_by' => $this->session->userdata('username'),
                                        'updated_date' => date('Y-m-d H:i:s'),
                                        'updated_by' => $this->session->userdata('username'),
                                        'status' => 1
                                    ];

                                    // FIXED: Remove idacc_lazada from WHERE clause for acc_lazada_detail_details
                                    $existsDetail = $this->db->get_where('acc_lazada_detail_details', [
                                        'no_faktur' => $orderNumber,
                                        'sku' => $omset['sku']
                                    ])->row();

                                    if ($existsDetail) {
                                        $this->db->where('no_faktur', $orderNumber)
                                            ->where('sku', $omset['sku'])
                                            ->update('acc_lazada_detail_details', $detail_order);
                                    } else {
                                        $this->db->insert('acc_lazada_detail_details', $detail_order);
                                    }
                                }
                            }
                        }

                        $processedOrders++;
                    }

                    $this->session->set_flashdata(
                        $processedOrders > 0 ? 'success' : 'error',
                        $processedOrders > 0 ? "Data Lazada " . ($is_kotime ? "Kotime" : "Asta") . " berhasil diimport ($processedOrders orders)." : 'Tidak ada data order yang ditemukan.'
                    );
                    break;

                default:
                    $this->session->set_flashdata('error', 'Marketplace tidak dikenali.');
                    break;
            }
        } catch (Exception $e) {
            $this->session->set_flashdata('error', 'Error processing file: ' . $e->getMessage());
        }
        redirect('recap');
    }

    public function detail_faktur()
    {
        $no_faktur = $this->input->get('no_faktur');
        $marketplace = $this->input->get('marketplace');

        $this->db->select('no_faktur, sku, name_product, price_after_discount');
        $this->db->where('no_faktur', $no_faktur);

        if ($marketplace === 'tiktok') {
            $acc_detail_details = $this->db->get('acc_tiktok_detail_details')->result();
        } elseif ($marketplace === 'lazada') {
            $acc_detail_details = [];
        } else {
            $acc_detail_details = $this->db->get('acc_shopee_detail_details')->result();
        }

        $data = [
            'title' => ucfirst($marketplace) . ' Recap',
            'acc_detail_detail' => $acc_detail_details
        ];

        $this->load->view('theme/v_head', $data);
        $this->load->view('shopee_recap/v_shopee_recap_detail_details');
    }

    public function detail_payment()
    {
        $idacc_recap = $this->input->get('idacc_recap');
        $marketplace = $this->input->get('marketplace');

        $this->db->select('no_faktur, pay_date, total_faktur, pay, discount, payment');

        if ($marketplace === 'accurate') {
            $this->db->where('idacc_accurate', $idacc_recap);
            $acc_detail = $this->db->get('acc_accurate_detail')->result();
        } elseif ($marketplace === 'tiktok') {
            $this->db->where('idacc_tiktok', $idacc_recap);
            $acc_detail = $this->db->get('acc_tiktok_detail')->result();
        } elseif ($marketplace === 'lazada') {
            $this->db->where('idacc_lazada', $idacc_recap);
            $acc_detail = $this->db->get('acc_lazada_detail')->result();
        } else {
            $this->db->where('idacc_shopee', $idacc_recap);
            $acc_detail = $this->db->get('acc_shopee_detail')->result();
        }

        $data = [
            'title' => ucfirst($marketplace) . ' Recap',
            'acc_detail' => $acc_detail
        ];

        $this->load->view('theme/v_head', $data);
        $this->load->view('shopee_recap/v_shopee_recap_detail');
    }
}
