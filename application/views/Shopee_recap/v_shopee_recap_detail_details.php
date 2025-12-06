            <!-- Page content-->
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <h1 class="mt-4">Detail Faktur - <?php echo $this->input->get('no_faktur') ?></h1>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <table id="tabledetailfaktur" class="display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nomer Faktur</th>
                                    <th>SKU</th>
                                    <th>Nama Produk</th>
                                    <th>Harga Setelah Diskon</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($acc_detail_detail as $asddkey => $asddvalue) { ?>
                                <tr>
                                    <td><?= $asddkey + 1 ?></td>
                                    <td><?= $asddvalue->no_faktur ?></td>
                                    <td><?= $asddvalue->sku ?></td>
                                    <td><?= $asddvalue->name_product ?></td>
                                    <td><?= $asddvalue->price_after_discount ?></td>
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
                    new DataTable('#tabledetailfaktur', {
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