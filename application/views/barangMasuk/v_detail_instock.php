            <!-- Page content-->
            <div class="container-fluid">
                <div class="row mt-4">
                    <div class="col">
                        <h1>Detail Barang Masuk - <?php echo $instock_code ?></h1>
                        <h4>Nomer : <?php echo $no_manual ?></h4>
                    </div>
                    <div class="col">
                        <div class="d-flex justify-content-end mt-auto">
                            <a href="<?php echo base_url('barangmasuk/exportExcel?instock_code=' . $instock_code); ?>">
                                <button type="button" class="btn btn-success">
                                    <i class="fa-solid fa-print"></i> Print Excel
                                </button>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <table id="tableproduct" class="display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>SKU</th>
                                    <th>Nama Produk</th>
                                    <th>Gudang</th>
                                    <th>Jumlah</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($detailInStock as $diskey => $disvalue) { ?>
                                <tr>
                                    <td><?php echo $diskey + 1; ?></td>
                                    <td><?php echo $disvalue->sku; ?></td>
                                    <td><?php echo $disvalue->nama_produk; ?></td>
                                    <td><?php echo $disvalue->nama_gudang; ?></td>
                                    <td><?php echo $disvalue->jumlah; ?></td>
                                    <td><?php echo $disvalue->keterangan; ?></td>
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