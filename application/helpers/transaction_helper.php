<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('number_pending_verification')) {
    function number_pending_verification()
    {
        $CI = &get_instance(); // Dapatkan instance CI
        $query = "
	SELECT 
		'INSTOCK' AS tipe,
		i.instock_code AS kode_transaksi,
		i.tgl_terima AS tanggal,
		i.jam_terima AS jam,
		i.kategori,
		i.user,
		g.nama_gudang,
		i.status_verification
	FROM instock i
	LEFT JOIN gudang g ON g.idgudang = i.idgudang
	WHERE i.status_verification = 0

	UNION ALL

	SELECT 
		'OUTSTOCK' AS tipe,
		o.outstock_code AS kode_transaksi,
		o.tgl_keluar AS tanggal,
		o.jam_keluar AS jam,
		o.kategori,
		o.user,
		g.nama_gudang,
		o.status_verification
	FROM outstock o
	LEFT JOIN gudang g ON g.idgudang = o.idgudang
	WHERE o.status_verification = 0

	ORDER BY tanggal DESC, jam DESC
";
        return $CI->db->query($query)->num_rows();
    }
}
