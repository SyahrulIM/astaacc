<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Stock</title>
</head>

<style>
    #tableproduct, #tableproduct th, #tableproduct td {
        border: 1px solid black;
        border-collapse: collapse;
    }
    #tableproduct th, #tableproduct td {
        padding: 8px;
        text-align: left;
    }
</style>

<body style="width: 18cm;">
    <div class="container">
        <div class="row">
            <div class="col" style="text-align: center;">
                <img src="<?php echo base_url('assets/image/Logo Warna.png') ?>" alt="" style="width: 20%;">
            </div>
        </div>

        <div class="row">
            <div class="col" style="text-align: center;">
                <h1>KARTU STOK</h1>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <h3>SKU: <?php echo $product->sku ?></h3>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <h3>Nama Barang: <?php echo $product->nama_produk ?></h3>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <table id="tableproduct" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kategori</th>
                            <th>Kode Transaksi Stock</th>
                            <th>Tanggal</th>
                            <th>Masuk</th>
                            <th>Keluar</th>
                            <th>Sisa</th>
                            <th>Penginput</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transaction_stock as $tskey => $tsvalue) { ?>
                            <tr>
                                <td><?php echo $tskey + 1; ?></td>
                                <td><?php echo $tsvalue->kategori; ?></td>
                                <td><?php echo $tsvalue->stock_code; ?></td>
                                <td><?php echo $tsvalue->datetime; ?></td>
                                <td><?php echo $tsvalue->instock; ?></td>
                                <td><?php echo $tsvalue->outstock; ?></td>
                                <td><?php echo $tsvalue->sisa; ?></td>
                                <td><?php echo $tsvalue->user; ?></td>
                                <td><?php echo $tsvalue->keterangan; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

<script>
    window.onload = function() {
        window.print();
    };
</script>

</html>