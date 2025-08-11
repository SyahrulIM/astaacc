<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class Tiktok_recap extends CI_Controller
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
        $title = 'Tiktok Recap';
        $this->db->select('user.full_name as full_name, acc_tiktok.created_date as created_date, acc_tiktok.idacc_tiktok as idacc_tiktok');
        $this->db->join('user', 'user.iduser = acc_tiktok.iduser');
        $acc_tiktok = $this->db->get('acc_tiktok')->result();

        $this->db->select('
            acc_tiktok_detail.no_faktur,
            MAX(acc_tiktok_detail.pay_date) AS pay_date,
            MAX(acc_tiktok_detail.total_faktur) AS total_faktur,
            MAX(acc_tiktok_detail.pay) AS pay,
            MAX(acc_tiktok_detail.discount) AS discount,
            MAX(acc_tiktok_detail.payment) AS payment,
            MAX(acc_tiktok_detail.order_date) AS order_date,
            MAX(acc_tiktok_detail.refund) AS refund,
        ');
        $this->db->from('acc_tiktok_detail');
        $this->db->join('acc_tiktok', 'acc_tiktok.idacc_tiktok = acc_tiktok_detail.idacc_tiktok');
        $this->db->join('user', 'user.iduser = acc_tiktok.iduser');
        $this->db->group_by('acc_tiktok_detail.no_faktur');
        $acc_tiktok_detail = $this->db->get()->result();

        $data = [
            'title' => $title,
            'acc_tiktok' => $acc_tiktok,
            'acc_tiktok_detail' => $acc_tiktok_detail
        ];

        $this->load->view('theme/v_head', $data);
        $this->load->view('Tiktok_recap/v_tiktok_recap');
    }

    public function createTiktok()
    {
        $type_excel = $this->input->post('typeExcel');

        $this->load->library('upload');
        $file = $_FILES['file']['tmp_name'];

        if (empty($file)) {
            $this->session->set_flashdata('error', 'File tidak ditemukan.');
            redirect('tiktok_recap');
            return;
        }

        require APPPATH . '../vendor/autoload.php';
        $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        if ($extension === 'csv' || $extension === 'txt') {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
            $reader->setDelimiter("\,");
        } else {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        }

        $spreadsheet = $reader->load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestRow();

        if ($type_excel === 'income') {
            $acc_tiktok_data = [
                'iduser' => $this->session->userdata('iduser'),
                'created_by' => $this->session->userdata('username'),
                'created_date' => date('Y-m-d H:i:s'),
                'status' => 1
            ];
            $this->db->insert('acc_tiktok', $acc_tiktok_data);
            $idacc_tiktok = $this->db->insert_id();

            for ($rowIndex = 2; $rowIndex <= $highestRow; $rowIndex++) {
                $noFaktur = $sheet->getCell('A' . $rowIndex)->getValue();
                if (empty($noFaktur)) continue;

                $orderDateRaw = $sheet->getCell('C' . $rowIndex)->getFormattedValue();
                $orderDate = date('Y-m-d', strtotime(str_replace('/', '-', $orderDateRaw)));
                $payDateRaw = $sheet->getCell('D' . $rowIndex)->getFormattedValue();
                $payDate = date('Y-m-d', strtotime(str_replace('/', '-', $payDateRaw)));
                $totalFaktur = $sheet->getCell('H' . $rowIndex)->getValue();
                $payment = $sheet->getCell('F' . $rowIndex)->getValue();
                $discount = $sheet->getCell('N' . $rowIndex)->getValue();
                $refund = $sheet->getCell('AT' . $rowIndex)->getValue();

                $detail = [
                    'idacc_tiktok' => $idacc_tiktok,
                    'no_faktur' => $noFaktur,
                    'order_date' => date('Y-m-d', strtotime($orderDate)),
                    'pay_date' => $payDate ? date('Y-m-d', strtotime($payDate)) : null,
                    'total_faktur' => $totalFaktur,
                    'pay' => $totalFaktur,
                    'payment' => $payment,
                    'discount' => $discount,
                    'refund' => $refund,
                    'is_check' => 0
                ];
                $this->db->insert('acc_tiktok_detail', $detail);
            }

            $this->session->set_flashdata('success', 'Data TikTok Income berhasil diimpor.');
        } else {
            // === TikTok Selesai ===
            $rows = $sheet->toArray(null, true, true, true); // ambil semua data ke array (A, B, C ...)
            foreach ($rows as $i => $row) {
                if ($i < 3 || empty($row['A'])) continue; // skip header / baris kosong

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
            }

            $this->session->set_flashdata('success', 'Data TikTok Order berhasil diimpor.');
        }

        redirect('tiktok_recap');
    }

    public function detail_acc()
    {
        $idacc_tiktok = $this->input->get('idacc_tiktok');
        $this->db->where('acc_tiktok.idacc_tiktok', $idacc_tiktok);
        $this->db->join('acc_tiktok_detail', 'acc_tiktok_detail.idacc_tiktok = acc_tiktok.idacc_tiktok');

        // Filter berdasarkan tanggal
        if ($this->input->get('date_from')) {
            $this->db->where('acc_tiktok_detail.pay_date >=', $this->input->get('date_from'));
        }
        if ($this->input->get('date_to')) {
            $this->db->where('acc_tiktok_detail.pay_date <=', $this->input->get('date_to'));
        }

        // Filter berdasarkan No Faktur
        if ($this->input->get('no_faktur')) {
            $this->db->like('acc_tiktok_detail.no_faktur', $this->input->get('no_faktur'));
        }

        $acc_tiktok_detail = $this->db->get('acc_tiktok')->result();

        $persen_input = $this->input->get('persen');

        $data = [
            'title' => 'tiktok Recap',
            'acc_tiktok_detail' => $acc_tiktok_detail,
            'persen_input' => $persen_input
        ];

        $this->load->view('theme/v_head', $data);
        $this->load->view('tiktok_recap/v_tiktok_recap_detail');
    }

    public function detail_faktur()
    {
        $no_faktur = $this->input->get('no_faktur');

        $this->db->distinct();
        $this->db->select('no_faktur, sku, name_product, price_after_discount');
        $this->db->where('no_faktur', $no_faktur);
        $acc_tiktok_detail_details = $this->db->get('acc_tiktok_detail_details')->result();

        $data = [
            'title' => 'Tiktok Recap',
            'acc_tiktok_detail_details' => $acc_tiktok_detail_details
        ];

        $this->load->view('theme/v_head', $data);
        $this->load->view('Tiktok_recap/v_tiktok_recap_detail_details');
    }
}
