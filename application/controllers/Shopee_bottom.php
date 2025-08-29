<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class Shopee_bottom extends CI_Controller
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
        $title = 'Shopee Bottom Price';

        // Start acc_shopee_bottom
        $this->db->select('sku, price_bottom');
        $acc_shopee_bottom = $this->db->get('acc_shopee_bottom')->result();
        // End
        $data = [
            'title' => $title,
            'acc_shopee_bottom' => $acc_shopee_bottom
        ];

        $this->load->view('theme/v_head', $data);
        $this->load->view('Shopee_bottom/v_shopee_bottom');
    }

    public function createBottom()
    {
        $this->load->library('upload');
        $file = $_FILES['file']['tmp_name'];

        if (!empty($file)) {
            require APPPATH . '../vendor/autoload.php';

            $reader = new Xlsx();
            $spreadsheet = $reader->load($file);
            $sheet = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

            foreach ($sheet as $i => $row) {
                if ($i < 2 || !$row['B']) continue;

                $acc_shopee_bottom = [
                    'sku' => $row['B'],
                    'price_bottom' => (float) str_replace(',', '', $row['D']),
                    'created_date' => date('Y-m-d H:i:s'),
                    'created_by' => $this->session->userdata('username'),
                    'updated_date' => date('Y-m-d H:i:s'),
                    'updated_by' => $this->session->userdata('username'),
                    'status' => 1
                ];
                $this->db->insert('acc_shopee_bottom', $acc_shopee_bottom);
            }

            $this->session->set_flashdata('success', 'Data Shopee berhasil diimport.');
        } else {
            $this->session->set_flashdata('error', 'File tidak ditemukan.');
        }

        redirect('shopee_bottom');
    }

    public function addBottom()
    {
        // Get the form data
        $sku = $this->input->post('sku');
        $bottom = $this->input->post('bottom');

        // Validate input
        if (empty($sku) || empty($bottom)) {
            $response = [
                'status' => 'error',
                'message' => 'SKU and Bottom Price are required.'
            ];
            echo json_encode($response);
            return;
        }

        // Check if SKU already exists
        $this->db->where('sku', $sku);
        $existing = $this->db->get('acc_shopee_bottom')->row();

        if ($existing) {
            // Update existing record
            $data = [
                'price_bottom' => (float) $bottom,
                'updated_date' => date('Y-m-d H:i:s'),
                'updated_by' => $this->session->userdata('username')
            ];

            $this->db->where('sku', $sku);
            $this->db->update('acc_shopee_bottom', $data);

            $response = [
                'status' => 'success',
                'message' => 'Bottom price updated successfully.'
            ];
        } else {
            // Insert new record
            $data = [
                'sku' => $sku,
                'price_bottom' => (float) $bottom,
                'created_date' => date('Y-m-d H:i:s'),
                'created_by' => $this->session->userdata('username'),
                'updated_date' => date('Y-m-d H:i:s'),
                'updated_by' => $this->session->userdata('username'),
                'status' => 1
            ];

            $this->db->insert('acc_shopee_bottom', $data);

            $response = [
                'status' => 'success',
                'message' => 'Bottom price added successfully.'
            ];
        }

        echo json_encode($response);
    }
}
