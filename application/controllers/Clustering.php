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
            $bindParams = [$order_start, $order_end];
        }

        $sql = "
        SELECT 
            label,
            COUNT(*) AS jumlah_no_faktur
        FROM (
            SELECT 
                pc.prov_name AS label,
                sdd.no_faktur
            FROM 
                acc_shopee_detail_details sdd
            JOIN 
                acc_shopee_detail sd ON sdd.no_faktur = sd.no_faktur
            JOIN 
                postal_code pc ON sdd.pos_code = pc.pos_code
            $whereClause
            GROUP BY 
                pc.prov_name, sdd.no_faktur
        ) AS subquery
        GROUP BY label
        ORDER BY jumlah_no_faktur DESC
    ";

        $query = $this->db->query($sql, $bindParams);

        $data = [
            'title' => 'Clustering',
            'clustering_data' => $query->result()
        ];

        $this->load->view('theme/v_head', $data);
        $this->load->view('Clustering/v_clustering', $data);
    }

    public function province()
    {
        $order_start = $this->input->get('order_start');
        $order_end = $this->input->get('order_end');
        $prov_name = $this->input->get('prov_id');

        $bindParams = [$prov_name];
        $whereClause = 'WHERE pc.prov_name = ?';
        $dateFilter = '';

        if (!empty($order_start) && !empty($order_end)) {
            $dateFilter = "AND sd.order_date BETWEEN ? AND ?";
            array_push($bindParams, $order_start, $order_end);
        }

        $sql = "
            SELECT 
                pc.city_id,
                pc.city_name AS label,
                COUNT(DISTINCT sd.no_faktur) AS jumlah_no_faktur
            FROM 
                postal_code pc
            LEFT JOIN acc_shopee_detail_details sdd ON sdd.pos_code = pc.pos_code
            LEFT JOIN acc_shopee_detail sd ON sdd.no_faktur = sd.no_faktur
            $whereClause
            $dateFilter
            GROUP BY 
                pc.city_id, pc.city_name
            ORDER BY 
                jumlah_no_faktur DESC
        ";

        $query = $this->db->query($sql, $bindParams);

        $data = [
            'title' => 'Clustering',
            'clustering_data' => $query->result(),
            'filter_mode' => 'city',
            'prov_name' => $prov_name
        ];

        $this->load->view('theme/v_head', $data);
        $this->load->view('Clustering/v_clustering', $data);
    }

    public function district()
    {
        $order_start = $this->input->get('order_start');
        $order_end = $this->input->get('order_end');
        $city_id = $this->input->get('city_id');

        $bindParams = [$city_id];

        $dateFilter = '';
        if (!empty($order_start) && !empty($order_end)) {
            $dateFilter = "AND sd.order_date BETWEEN ? AND ?";
            array_push($bindParams, $order_start, $order_end);
        }

        $sql = "
        SELECT 
            d.district_name AS label,
            COUNT(DISTINCT sd.no_faktur) AS jumlah_no_faktur
        FROM 
            district d
        LEFT JOIN postal_code pc ON pc.dis_id = d.iddistrict
        LEFT JOIN acc_shopee_detail_details sdd ON sdd.pos_code = pc.pos_code
        LEFT JOIN acc_shopee_detail sd ON sdd.no_faktur = sd.no_faktur
        WHERE d.idcity = ?
        $dateFilter
        GROUP BY d.district_name
        ORDER BY jumlah_no_faktur DESC
    ";

        $query = $this->db->query($sql, $bindParams);

        $data = [
            'title' => 'Clustering',
            'clustering_data' => $query->result(),
            'filter_mode' => 'district',
            'city_id' => $city_id
        ];

        $this->load->view('theme/v_head', $data);
        $this->load->view('Clustering/v_clustering', $data);
    }

    public function export_excel()
    {
        require_once(APPPATH . '../vendor/autoload.php');

        $order_start = $this->input->get('order_start');
        $order_end = $this->input->get('order_end');

        $whereClause = '';
        $bindParams = [];

        if (!empty($order_start) && !empty($order_end)) {
            $whereClause = "WHERE sd.order_date BETWEEN ? AND ?";
            $bindParams = [$order_start, $order_end];
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

        $query = $this->db->query($sql, $bindParams);
        $data = $query->result();

        $spreadsheet = new Spreadsheet();
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

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
