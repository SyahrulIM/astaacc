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

        // Header recap (tanpa detail)
        $acc_recap = $this->db->query("
        SELECT 
            user.full_name AS full_name,
            acc_shopee.created_date AS created_date,
            acc_shopee.idacc_shopee AS id_data,
            acc_shopee.excel_type AS type,
            'shopee' AS source
        FROM acc_shopee
        JOIN user ON user.iduser=acc_shopee.iduser

        UNION ALL

        SELECT 
            user.full_name AS full_name,
            acc_tiktok.created_date AS created_date,
            acc_tiktok.idacc_tiktok AS id_data,
            acc_tiktok.excel_type AS type,
            'tiktok' AS source
        FROM acc_tiktok
        JOIN user ON user.iduser=acc_tiktok.iduser

        UNION ALL

        SELECT 
            user.full_name AS full_name,
            acc_accurate.created_date AS created_date,
            acc_accurate.idacc_accurate AS id_data,
            NULL AS type,
            'accurate' AS source
        FROM acc_accurate
        JOIN user ON user.iduser=acc_accurate.iduser

        ORDER BY created_date DESC
    ")->result();

        // Detail recap (no_faktur unik saja)
        $acc_recap_detail = $this->db->query("
        SELECT 
            d.no_faktur,
            MAX(d.pay_date) AS pay_date,
            MAX(d.total_faktur) AS total_faktur,
            MAX(d.pay) AS pay,
            MAX(d.discount) AS discount,
            MAX(d.payment) AS payment,
            MAX(d.order_date) AS order_date,
            MAX(d.refund) AS refund,
            'shopee' AS source
        FROM acc_shopee_detail d
        JOIN acc_shopee s ON s.idacc_shopee=d.idacc_shopee
        JOIN user u ON u.iduser=s.iduser
        GROUP BY d.no_faktur

        UNION ALL

        SELECT 
            d.no_faktur,
            MAX(d.pay_date) AS pay_date,
            MAX(d.total_faktur) AS total_faktur,
            MAX(d.pay) AS pay,
            MAX(d.discount) AS discount,
            MAX(d.payment) AS payment,
            MAX(d.order_date) AS order_date,
            MAX(d.refund) AS refund,
            'tiktok' AS source
        FROM acc_tiktok_detail d
        JOIN acc_tiktok t ON t.idacc_tiktok=d.idacc_tiktok
        JOIN user u ON u.iduser=t.iduser
        GROUP BY d.no_faktur

        UNION ALL

        SELECT 
            d.no_faktur,
            MAX(d.pay_date) AS pay_date,
            MAX(d.total_faktur) AS total_faktur,
            MAX(d.pay) AS pay,
            MAX(d.discount) AS discount,
            MAX(d.payment) AS payment,
            NULL AS order_date,
            NULL AS refund,
            'accurate' AS source
        FROM acc_accurate_detail d
        JOIN acc_accurate a ON a.idacc_accurate=d.idacc_accurate
        JOIN user u ON u.iduser=a.iduser
        GROUP BY d.no_faktur

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
            redirect($marketplace . '_recap');
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

            $header_data = [
                'iduser' => $this->session->userdata('iduser'),
                'created_by' => $this->session->userdata('username'),
                'created_date' => date('Y-m-d H:i:s'),
                'status' => 1,
                'excel_type' => $type_excel
            ];

            switch ($marketplace) {
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
                            redirect('shopee_recap');
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
                                $total = $hargaAsli - $totalDiskon;

                                $detail = [
                                    'idacc_shopee' => $id_header,
                                    'no_faktur' => $noFaktur,
                                    'order_date' => date('Y-m-d', strtotime($orderDate)),
                                    'pay_date' => $payDate ? date('Y-m-d', strtotime($payDate)) : null,
                                    'total_faktur' => $total,
                                    'pay' => $total,
                                    'payment' => $payment,
                                    'discount' => $totalDiskon,
                                    'refund' => $refund,
                                    'is_check' => 0
                                ];
                                $this->db->insert('acc_shopee_detail', $detail);
                                $processedOrders++;
                            }
                        }
                        $this->session->set_flashdata($processedOrders > 0 ? 'success' : 'error', $processedOrders > 0 ? "Data Shopee Income berhasil diimport ($processedOrders orders)." : 'Tidak ada data order yang ditemukan dalam sheet income.');
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
                            $this->db->insert('acc_shopee_detail_details', $detail_order);
                            $processedOrders++;
                        }
                        $this->session->set_flashdata($processedOrders > 0 ? 'success' : 'error', $processedOrders > 0 ? "Data Shopee Order berhasil diimport ($processedOrders orders)." : 'Tidak ada data order yang ditemukan.');
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
                            $refund = $sheet->getCell('AT' . $rowIndex)->getValue();

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
                                'is_check' => 0
                            ];
                            $this->db->insert('acc_tiktok_detail', $detail);
                            $processedOrders++;
                        }
                        $this->session->set_flashdata($processedOrders > 0 ? 'success' : 'error', $processedOrders > 0 ? "Data TikTok Income berhasil diimpor ($processedOrders orders)." : 'Tidak ada data order yang ditemukan.');
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
                            $this->db->insert('acc_tiktok_detail_details', $detail_order);
                            $processedOrders++;
                        }
                        $this->session->set_flashdata($processedOrders > 0 ? 'success' : 'error', $processedOrders > 0 ? "Data TikTok Order berhasil diimpor ($processedOrders orders)." : 'Tidak ada data order yang ditemukan.');
                    }
                    break;

                case 'accurate':
                    // Accurate tanpa type_excel
                    $this->db->insert('acc_accurate', [
                        'iduser' => $this->session->userdata('iduser'),
                        'created_by' => $this->session->userdata('username'),
                        'created_date' => date('Y-m-d H:i:s'),
                        'status' => 1
                    ]);
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
                        $this->db->insert('acc_accurate_detail', $detail);
                        $processedOrders++;
                    }
                    $this->session->set_flashdata($processedOrders > 0 ? 'success' : 'error', $processedOrders > 0 ? "Data Accurate berhasil diimport ($processedOrders orders)." : 'Tidak ada data order yang ditemukan.');
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

        $this->db->distinct();
        $this->db->select('no_faktur, sku, name_product, price_after_discount');
        $this->db->where('no_faktur', $no_faktur);
        $acc_shopee_detail_details = $this->db->get('acc_shopee_detail_details')->result();

        $data = [
            'title' => 'Shopee Recap',
            'acc_shopee_detail_details' => $acc_shopee_detail_details
        ];

        $this->load->view('theme/v_head', $data);
        $this->load->view('shopee_recap/v_shopee_recap_detail_details');
    }
}
