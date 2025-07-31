<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class Shopee_recap extends CI_Controller
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
        $title = 'Shopee Recap';
        // Get list import Shopee (header)
        $this->db->select('user.full_name as full_name, acc_shopee.created_date as created_date, acc_shopee.idacc_shopee as idacc_shopee');
        $this->db->join('user', 'user.iduser = acc_shopee.iduser');
        $acc_shopee = $this->db->get('acc_shopee')->result();

        // Get detail Shopee (no_faktur unik saja)
        $this->db->select('
            acc_shopee_detail.no_faktur,
            MAX(acc_shopee_detail.pay_date) AS pay_date,
            MAX(acc_shopee_detail.total_faktur) AS total_faktur,
            MAX(acc_shopee_detail.pay) AS pay,
            MAX(acc_shopee_detail.discount) AS discount,
            MAX(acc_shopee_detail.payment) AS payment,
            MAX(acc_shopee_detail.order_date) AS order_date,
            MAX(acc_shopee_detail.refund) AS refund,
        ');
        $this->db->from('acc_shopee_detail');
        $this->db->join('acc_shopee', 'acc_shopee.idacc_shopee = acc_shopee_detail.idacc_shopee');
        $this->db->join('user', 'user.iduser = acc_shopee.iduser');
        $this->db->group_by('acc_shopee_detail.no_faktur');
        $acc_shopee_detail = $this->db->get()->result();

        $data = [
            'title' => $title,
            'acc_shopee' => $acc_shopee,
            'acc_shopee_detail' => $acc_shopee_detail
        ];

        $this->load->view('theme/v_head', $data);
        $this->load->view('Shopee_recap/v_shopee_recap');
    }

    public function createShopee()
    {
        $type_excel = $this->input->post('typeExcel');

        $this->load->library('upload');
        $file = $_FILES['file']['tmp_name'];

        if (empty($file)) {
            $this->session->set_flashdata('error', 'File tidak ditemukan.');
            redirect('shopee_recap');
            return;
        }

        require APPPATH . '../vendor/autoload.php';
        $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        if ($extension === 'csv' || $extension === 'txt') {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
            $reader->setDelimiter("\t");
        } else {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        }
        $spreadsheet = $reader->load($file);
        $sheet = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        if ($type_excel === 'income') {
            // Simpan header Shopee
            $acc_shopee_data = [
                'iduser' => $this->session->userdata('iduser'),
                'excel_type' => $type_excel,
                'created_by' => $this->session->userdata('username'),
                'created_date' => date('Y-m-d H:i:s'),
                'status' => 1
            ];
            $this->db->insert('acc_shopee', $acc_shopee_data);
            $idacc_shopee = $this->db->insert_id();

            // Get the first sheet
            $sheet = $spreadsheet->getActiveSheet();
            $highestRow = $sheet->getHighestRow();

            // Start from row 2 to skip header
            for ($rowIndex = 2; $rowIndex <= $highestRow; $rowIndex++) {
                $noFaktur = $sheet->getCell('B' . $rowIndex)->getValue(); // No. Pesanan (B)
                if (empty($noFaktur)) continue;

                $orderDate = $sheet->getCell('E' . $rowIndex)->getFormattedValue(); // Waktu Pesanan Dibuat (E)
                $payDate = $sheet->getCell('G' . $rowIndex)->getFormattedValue(); // Tanggal Dana Dilepaskan (G)
                $hargaAsli = floatval(str_replace(['.', ','], '', $sheet->getCell('H' . $rowIndex)->getValue())); // Harga Asli Produk (H)
                $totalDiskon = floatval(str_replace(['.', ','], '', $sheet->getCell('I' . $rowIndex)->getValue())); // Total Diskon Produk (I)
                $payment = floatval(str_replace(['.', ','], '', $sheet->getCell('AB' . $rowIndex)->getValue())); // Total Penghasilan (AB)
                $refund = floatval(str_replace(['.', ','], '', $sheet->getCell('J' . $rowIndex)->getValue())); // Jumlah Pengembalian Dana ke Pembeli (J)

                $total = $hargaAsli + $totalDiskon;

                $detail = [
                    'idacc_shopee' => $idacc_shopee,
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
            }

            $this->session->set_flashdata('success', 'Data Shopee Income berhasil diimport.');
        } else {
            foreach ($sheet as $i => $row) {
                if ($i < 2 || empty($row['A'])) continue;

                $price = floatval(str_replace('.', '', $row['Q']));

                $fullAddress = isset($row['AT']) ? trim($row['AT']) : null;

                preg_match('/(\d{5})(?!.*\d)/', $fullAddress, $matches);
                $posCode = isset($matches[1]) ? $matches[1] : null;

                $acc_shopee_detail_details = [
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
                $this->db->insert('acc_shopee_detail_details', $acc_shopee_detail_details);
            }

            $this->session->set_flashdata('success', 'Data Shopee Detail berhasil diimport.');
        }

        redirect('shopee_recap');
    }

    public function detail_acc()
    {
        $idacc_shopee = $this->input->get('idacc_shopee');
        $this->db->where('acc_shopee.idacc_shopee', $idacc_shopee);
        $this->db->join('acc_shopee_detail', 'acc_shopee_detail.idacc_shopee = acc_shopee.idacc_shopee');

        // Filter berdasarkan tanggal
        if ($this->input->get('date_from')) {
            $this->db->where('acc_shopee_detail.pay_date >=', $this->input->get('date_from'));
        }
        if ($this->input->get('date_to')) {
            $this->db->where('acc_shopee_detail.pay_date <=', $this->input->get('date_to'));
        }

        // Filter berdasarkan No Faktur
        if ($this->input->get('no_faktur')) {
            $this->db->like('acc_shopee_detail.no_faktur', $this->input->get('no_faktur'));
        }

        $acc_shopee_detail = $this->db->get('acc_shopee')->result();

        $persen_input = $this->input->get('persen');

        $data = [
            'title' => 'Shopee Recap',
            'acc_shopee_detail' => $acc_shopee_detail,
            'persen_input' => $persen_input
        ];

        $this->load->view('theme/v_head', $data);
        $this->load->view('shopee_recap/v_shopee_recap_detail');
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
