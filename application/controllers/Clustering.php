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

        $clustering_data = [];

        if (!empty($order_start) && !empty($order_end)) {
            $this->db->select('pc.prov_id, pc.prov_name AS label, COUNT(DISTINCT sdd.no_faktur) AS jumlah_no_faktur');
            $this->db->from('acc_shopee_detail_details sdd');
            $this->db->join('acc_shopee_detail sd', 'sdd.no_faktur = sd.no_faktur');
            $this->db->join('postal_code pc', 'sdd.pos_code = pc.pos_code');
            $this->db->where('sd.order_date >=', $order_start);
            $this->db->where('sd.order_date <=', $order_end);
            $this->db->group_by(['pc.prov_id', 'pc.prov_name']);
            $this->db->order_by('jumlah_no_faktur', 'DESC');

            $clustering_data = $this->db->get()->result();
        }

        $data = [
            'title' => 'Clustering',
            'clustering_data' => $clustering_data,
            'order_start' => $order_start,
            'order_end' => $order_end
        ];

        $this->load->view('theme/v_head', $data);
        $this->load->view('Clustering/v_clustering', $data);
    }

    public function province()
    {
        $order_start = $this->input->get('order_start');
        $order_end = $this->input->get('order_end');
        $prov_id = $this->input->get('prov_id');
        $prov_name = $this->input->get('prov_name');

        // Escape input
        $escapedProvId = $this->db->escape($prov_id);

        $whereClause = "WHERE pc.prov_id = $escapedProvId";

        if (!empty($order_start) && !empty($order_end)) {
            $escapedStart = $this->db->escape($order_start);
            $escapedEnd = $this->db->escape($order_end);
            $whereClause .= " AND sd.order_date BETWEEN $escapedStart AND $escapedEnd";
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
        GROUP BY
            pc.city_id, pc.city_name
        ORDER BY
            jumlah_no_faktur DESC
    ";

        $query = $this->db->query($sql);

        $data = [
            'title' => 'Clustering',
            'clustering_data' => $query->result(),
            'filter_mode' => 'city',
            'prov_name' => $prov_name,
            'prov_id' => $prov_id,
            'order_start' => $order_start,
            'order_end' => $order_end
        ];

        $this->load->view('theme/v_head', $data);
        $this->load->view('Clustering/v_clustering', $data);
    }

    public function district()
    {
        $order_start = $this->input->get('order_start');
        $order_end = $this->input->get('order_end');
        $city_id = $this->input->get('city_id');
        $city_name = $this->input->get('city_name');

        // Validate
        if (empty($city_id)) {
            $this->session->set_flashdata('error', 'City ID is required to view district data.');
            redirect('clustering');
        }

        // Escape input
        $escapedCityId = $this->db->escape($city_id);
        $whereClause = "WHERE pc.city_id = $escapedCityId";

        if (!empty($order_start) && !empty($order_end)) {
            $escapedStart = $this->db->escape($order_start);
            $escapedEnd = $this->db->escape($order_end);
            $whereClause .= " AND sd.order_date BETWEEN $escapedStart AND $escapedEnd";
        }

        $sql = "
        SELECT 
            label,
            COUNT(*) AS jumlah_no_faktur
        FROM (
            SELECT 
                sd.no_faktur,
                MIN(pc.dis_name) AS label
            FROM 
                postal_code pc
            LEFT JOIN 
                acc_shopee_detail_details sdd ON sdd.pos_code = pc.pos_code
            LEFT JOIN 
                acc_shopee_detail sd ON sdd.no_faktur = sd.no_faktur
            $whereClause
            GROUP BY 
                sd.no_faktur
        ) AS subquery
        GROUP BY 
            label
        ORDER BY 
            jumlah_no_faktur DESC
    ";

        $query = $this->db->query($sql);

        $data = [
            'title' => 'Clustering',
            'clustering_data' => $query->result(),
            'filter_mode' => 'district',
            'city_id' => $city_id,
            'city_name' => $city_name
        ];

        $this->load->view('theme/v_head', $data);
        $this->load->view('Clustering/v_clustering', $data);
    }

    public function export_excel()
    {
        require_once(APPPATH . '../vendor/autoload.php');

        $order_start = $this->input->get('order_start');
        $order_end = $this->input->get('order_end');
        $prov_id = $this->input->get('prov_id');
        $city_id = $this->input->get('city_id');

        $prov_display_name = $prov_id;
        if (!empty($prov_id)) {
            $prov_query = $this->db->query("SELECT prov_name FROM postal_code WHERE prov_id = ? LIMIT 1", [$prov_id])->row();
            if ($prov_query) {
                $prov_display_name = $prov_query->prov_name;
            }
        }

        $city_name = '';
        if (!empty($city_id)) {
            $city_query = $this->db->query("SELECT city_name FROM postal_code WHERE city_id = ? LIMIT 1", [$city_id])->row();
            $city_name = $city_query->city_name ?? '';
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        // ===================== SHEET 1: PROVINSI =====================
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Provinsi');

        // Header
        $sheet1->mergeCells('A1:C1')->setCellValue('A1', 'Asta Homeware');
        $sheet1->mergeCells('A2:C2')->setCellValue('A2', 'Clustering Penjualan Online Wilaya Indoensia');
        $sheet1->mergeCells('A3:C3')->setCellValue('A3', 'Periode: ' . ($order_start ?? '-') . ' s/d ' . ($order_end ?? '-'));
        $sheet1->getStyle('A1:A3')->getFont()->setBold(true);

        $whereClause = '';
        $bindParams = [];
        if (!empty($order_start) && !empty($order_end)) {
            $whereClause = "WHERE sd.order_date BETWEEN ? AND ?";
            $bindParams = [$order_start, $order_end];
        }

        $sqlProv = "
        SELECT
            pc.prov_name AS label,
            COUNT(DISTINCT sdd.no_faktur) AS jumlah_no_faktur
        FROM acc_shopee_detail_details sdd
        JOIN acc_shopee_detail sd ON sdd.no_faktur = sd.no_faktur
        JOIN postal_code pc ON sdd.pos_code = pc.pos_code
        $whereClause
        GROUP BY pc.prov_name
        ORDER BY jumlah_no_faktur DESC
    ";
        $dataProv = $this->db->query($sqlProv, $bindParams)->result();

        $sheet1->setCellValue('A4', 'No');
        $sheet1->setCellValue('B4', 'Provinsi');
        $sheet1->setCellValue('C4', 'Jumlah Faktur');
        $sheet1->getStyle('A4:C4')->getFont()->setBold(true);

        $row = 5;
        $no = 1;
        foreach ($dataProv as $prov) {
            $sheet1->setCellValue("A$row", $no++);
            $sheet1->setCellValue("B$row", $prov->label);
            $sheet1->setCellValue("C$row", $prov->jumlah_no_faktur);
            $row++;
        }

        $lastRow = $row - 1;
        $sheet1->getStyle("A4:C$lastRow")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
        ]);
        $sheet1->getColumnDimension('A')->setWidth(5);
        $sheet1->getColumnDimension('B')->setWidth(30);
        $sheet1->getColumnDimension('C')->setWidth(20);

        // ===================== SHEET 2: KOTA =====================
        $sheet2 = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Kota');
        $spreadsheet->addSheet($sheet2);

        // Header
        $sheet2->mergeCells('A1:D1')->setCellValue('A1', 'Asta Homeware');
        $sheet2->mergeCells('A2:D2')->setCellValue('A2', 'Clustering Penjualan Online Wilaya Indoensia');
        $sheet2->mergeCells('A3:D3')->setCellValue('A3', 'Periode: ' . ($order_start ?? '-') . ' s/d ' . ($order_end ?? '-'));
        $sheet2->getStyle('A1:A3')->getFont()->setBold(true);

        $whereClauseCity = '';
        $bindCity = [];

        if (!empty($prov_display_name)) {
            $whereClauseCity = "WHERE pc.prov_name = ?";
            $bindCity[] = $prov_display_name;

            if (!empty($order_start) && !empty($order_end)) {
                $whereClauseCity .= " AND sd.order_date BETWEEN ? AND ?";
                $bindCity[] = $order_start;
                $bindCity[] = $order_end;
            }
        } elseif (!empty($order_start) && !empty($order_end)) {
            $whereClauseCity = "WHERE sd.order_date BETWEEN ? AND ?";
            $bindCity = [$order_start, $order_end];
        }

        $sqlCity = "
        SELECT
            pc.prov_name AS prov_label,
            pc.city_name AS city_label,
            COUNT(DISTINCT sd.no_faktur) AS jumlah_no_faktur
        FROM postal_code pc
        LEFT JOIN acc_shopee_detail_details sdd ON sdd.pos_code = pc.pos_code
        LEFT JOIN acc_shopee_detail sd ON sdd.no_faktur = sd.no_faktur
        $whereClauseCity
        GROUP BY pc.prov_name, pc.city_id, pc.city_name
        ORDER BY jumlah_no_faktur DESC
    ";
        $dataCity = $this->db->query($sqlCity, $bindCity)->result();

        $sheet2->setCellValue('A4', 'No');
        $sheet2->setCellValue('B4', 'Provinsi');
        $sheet2->setCellValue('C4', 'Nama Kota');
        $sheet2->setCellValue('D4', 'Jumlah Faktur');
        $sheet2->getStyle('A4:D4')->getFont()->setBold(true);

        $row = 5;
        $no = 1;
        foreach ($dataCity as $city) {
            $sheet2->setCellValue("A$row", $no++);
            $sheet2->setCellValue("B$row", $city->prov_label);
            $sheet2->setCellValue("C$row", $city->city_label);
            $sheet2->setCellValue("D$row", $city->jumlah_no_faktur);
            $row++;
        }

        $lastRow = $row - 1;
        $sheet2->getStyle("A4:D$lastRow")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
        ]);
        $sheet2->getColumnDimension('A')->setWidth(5);
        $sheet2->getColumnDimension('B')->setWidth(30);
        $sheet2->getColumnDimension('C')->setWidth(30);
        $sheet2->getColumnDimension('D')->setWidth(20);

        // ===================== SHEET 3: KECAMATAN =====================
        $sheet3 = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Kecamatan');
        $spreadsheet->addSheet($sheet3);

        // Header
        $sheet3->mergeCells('A1:E1')->setCellValue('A1', 'Asta Homeware');
        $sheet3->mergeCells('A2:E2')->setCellValue('A2', 'Clustering Penjualan Online Wilaya Indoensia');
        $sheet3->mergeCells('A3:E3')->setCellValue('A3', 'Periode: ' . ($order_start ?? '-') . ' s/d ' . ($order_end ?? '-'));
        $sheet3->getStyle('A1:A3')->getFont()->setBold(true);

        $whereClauseDist = '';
        $bindDist = [];
        if (!empty($city_id)) {
            $whereClauseDist = "WHERE pc.city_id = ?";
            $bindDist[] = $city_id;

            if (!empty($order_start) && !empty($order_end)) {
                $whereClauseDist .= " AND sd.order_date BETWEEN ? AND ?";
                $bindDist[] = $order_start;
                $bindDist[] = $order_end;
            }
        } elseif (!empty($order_start) && !empty($order_end)) {
            $whereClauseDist = "WHERE sd.order_date BETWEEN ? AND ?";
            $bindDist = [$order_start, $order_end];
        }

        $sqlDist = "
        SELECT
            label,
            city_name,
            prov_name,
            COUNT(*) AS jumlah_no_faktur
        FROM (
            SELECT
                sd.no_faktur,
                MIN(pc.dis_name) AS label,
                MIN(pc.city_name) AS city_name,
                MIN(pc.prov_name) AS prov_name
            FROM postal_code pc
            LEFT JOIN acc_shopee_detail_details sdd ON sdd.pos_code = pc.pos_code
            LEFT JOIN acc_shopee_detail sd ON sdd.no_faktur = sd.no_faktur
            $whereClauseDist
            GROUP BY sd.no_faktur
        ) AS subquery
        GROUP BY label, city_name, prov_name
        ORDER BY jumlah_no_faktur DESC
    ";
        $dataDist = $this->db->query($sqlDist, $bindDist)->result();

        $sheet3->setCellValue('A4', 'No');
        $sheet3->setCellValue('B4', 'Kecamatan');
        $sheet3->setCellValue('C4', 'Kota');
        $sheet3->setCellValue('D4', 'Provinsi');
        $sheet3->setCellValue('E4', 'Jumlah Faktur');
        $sheet3->getStyle('A4:E4')->getFont()->setBold(true);

        $row = 5;
        $no = 1;
        foreach ($dataDist as $dist) {
            $sheet3->setCellValue("A$row", $no++);
            $sheet3->setCellValue("B$row", $dist->label);
            $sheet3->setCellValue("C$row", $dist->city_name);
            $sheet3->setCellValue("D$row", $dist->prov_name);
            $sheet3->setCellValue("E$row", $dist->jumlah_no_faktur);
            $row++;
        }

        $lastRow = $row - 1;
        $sheet3->getStyle("A4:E$lastRow")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
        ]);
        $sheet3->getColumnDimension('A')->setWidth(5);
        $sheet3->getColumnDimension('B')->setWidth(30);
        $sheet3->getColumnDimension('C')->setWidth(25);
        $sheet3->getColumnDimension('D')->setWidth(25);
        $sheet3->getColumnDimension('E')->setWidth(20);

        // ===================== EXPORT =====================
        $spreadsheet->setActiveSheetIndex(0);
        $filename = 'Clustering_Penjualan_Astahomeware_' . date('Ymd_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function get_produk_terpesan()
    {
        $order_start = $this->input->get('order_start');
        $order_end = $this->input->get('order_end');
        $prov_id = $this->input->get('prov_id');
        $city_id = $this->input->get('city_id');

        $this->db->select('sdd.sku');
        $this->db->select('COALESCE(MAX(p.nama_produk), MAX(sdd.name_product)) AS nama_produk', false);
        $this->db->select('COUNT(DISTINCT sdd.no_faktur) AS jumlah_terpesan', false);
        $this->db->from('acc_shopee_detail_details sdd');
        $this->db->join('acc_shopee_detail sd', 'sdd.no_faktur = sd.no_faktur');
        $this->db->join('postal_code pc', 'sdd.pos_code = pc.pos_code');
        $this->db->join('product p', 'sdd.sku = p.sku', 'left');

        if (!empty($order_start) && !empty($order_end)) {
            $this->db->where('sd.order_date >=', $order_start);
            $this->db->where('sd.order_date <=', $order_end);
        }

        if (!empty($prov_id)) {
            $this->db->where('pc.prov_id', $prov_id);
        }

        if (!empty($city_id)) {
            $this->db->where('pc.city_id', $city_id);
        }

        $this->db->group_by('sdd.sku');
        $this->db->order_by('jumlah_terpesan', 'DESC');

        $data = $this->db->get()->result();

        header('Content-Type: application/json');
        echo json_encode($data);
    }
}
