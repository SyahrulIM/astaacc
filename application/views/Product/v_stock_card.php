            <!-- Page content-->
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <h1 class="mt-4">Kartu Stok - <?php echo $product->nama_produk ?></h1>
                    </div>
                </div>

                <div class="row">
                    <div class="col" style="text-align: right;">
                        <form method="get" action="<?= base_url('product/stockCard') ?>" class="row g-3 mb-3">
                            <input type="hidden" name="sku" value="<?= $product->sku ?>">
                            <div class="col-auto">
                                <select name="idgudang" class="form-select" onchange="this.form.submit()">
                                    <option value="">Pilih Gudang</option>
                                    <?php foreach ($gudang_list as $gudang) : ?>
                                    <option value="<?= $gudang->idgudang ?>" <?= ($selected_gudang == $gudang->idgudang) ? 'selected' : '' ?>>
                                        <?= $gudang->nama_gudang ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="col" style="text-align: right;">
                        <a href="<?php echo base_url('product/exportPdf?sku=' . $product->sku . '&idgudang=' . $selected_gudang); ?>"><button type="button" class="btn btn-success"><i class="fa-solid fa-print"></i> Print PDF</button></a>
                        <a href="<?php echo base_url('product/exportExcel?sku=' . $product->sku . '&idgudang=' . $selected_gudang); ?>"><button type="button" class="btn btn-success"><i class="fa-solid fa-print"></i> Print Excel</button></a>
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
            </div>
            </div>
            <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
            <!-- 2. DataTables JS -->
            <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
            <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
            <script src="https://cdn.datatables.net/rowreorder/1.5.0/js/dataTables.rowReorder.js"></script>
            <script src="https://cdn.datatables.net/rowreorder/1.5.0/js/rowReorder.dataTables.js"></script>
            <script src="https://cdn.datatables.net/responsive/3.0.4/js/dataTables.responsive.js"></script>
            <script src="https://cdn.datatables.net/responsive/3.0.4/js/responsive.dataTables.js"></script>
            <!-- 3. Bootstrap bundle -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
            <!-- 4. Core theme JS -->
            <script src="<?php echo base_url(); ?>js/scripts.js"></script>

            <!-- Initialize DataTables AFTER all scripts are loaded -->
            <script>
                $(document).ready(function() {
                    new DataTable('#tableproduct', {
                        responsive: true,
                        layout: {
                            bottomEnd: {
                                paging: {
                                    firstLast: false
                                }
                            }
                        }
                    });
                });
            </script>
            </body>

            </html>