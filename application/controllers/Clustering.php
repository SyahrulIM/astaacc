<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Clustering extends CI_Controller
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
        $order_start = $this->input->get('order_start');
        $order_end = $this->input->get('order_end');

        $whereClause = '';
        $bindParams = [];

        if (!empty($order_start) && !empty($order_end)) {
            $whereClause = "WHERE sd.order_date BETWEEN ? AND ?";
            $bindParams[] = $order_start;
            $bindParams[] = $order_end;
        }

        $sql = "
            SELECT 
                pc.prov_name AS province_name,
                COUNT(DISTINCT sdd.no_faktur) AS jumlah_no_faktur
            FROM 
                acc_shopee_detail_details sdd
            JOIN 
                acc_shopee_detail sd ON sdd.no_faktur = sd.no_faktur
            JOIN 
                postal_code pc ON sdd.pos_code = pc.pos_code
            $whereClause
            GROUP BY 
                pc.prov_name
            ORDER BY 
                jumlah_no_faktur DESC
        ";

        $query = !empty($bindParams)
            ? $this->db->query($sql, $bindParams)
            : $this->db->query($sql);

        $data = [
            'title' => 'Clustering',
            'clustering_data' => $query->result()
        ];

        $this->load->view('theme/v_head', $data);
        $this->load->view('Clustering/v_clustering', $data);
    }

public function export_excel()
{
    require_once(APPPATH . '../vendor/autoload.php');

    $order_start = $this->input->get('order_start');
    $order_end = $this->input->get('order_end');

    $where_date = '';
    if ($order_start && $order_end) {
        $where_date = "WHERE asd.order_date BETWEEN '{$order_start}' AND '{$order_end}'";
    }

    $query = $this->db->query("
        SELECT 
            pc.prov_name AS province_name,
            COUNT(DISTINCT asdd.no_faktur) AS jumlah_no_faktur
        FROM 
            acc_shopee_detail_details asdd
        JOIN 
            acc_shopee_detail asd ON asd.no_faktur = asdd.no_faktur
        JOIN 
            postal_code pc ON asdd.pos_code = pc.pos_code
        {$where_date}
        GROUP BY 
            pc.prov_name
        ORDER BY 
            jumlah_no_faktur DESC
    ");

    $data = $query->result();

    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Header
    $sheet->setCellValue('A1', 'No');
    $sheet->setCellValue('B1', 'Provinsi');
    $sheet->setCellValue('C1', 'Jumlah Faktur');

    // Data
    $row = 2;
    $no = 1;
    foreach ($data as $d) {
        $sheet->setCellValue('A' . $row, $no++);
        $sheet->setCellValue('B' . $row, $d->province_name);
        $sheet->setCellValue('C' . $row, $d->jumlah_no_faktur);
        $row++;
    }

    // Output
    $filename = 'Clustering_Faktur_' . date('Ymd_His') . '.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"{$filename}\"");
    header('Cache-Control: max-age=0');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}
}
