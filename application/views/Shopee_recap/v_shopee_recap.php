            <!-- Page content-->
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <h1 class="mt-4">Shopee Pembayaran</h1>
                    </div>
                </div>
                <!-- Import Excel -->
                <form action="<?= base_url('shopee_recap/createShopee') ?>" method="post" enctype="multipart/form-data">
                    <div class="card mt-3">
                        <div class="card-header">
                            <strong>Import Shopee Recap (xlsx)</strong>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="file" class="form-label">Pilih File Excel:</label>
                                <input type="file" class="form-control" name="file" id="file" accept=".xlsx" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Import</button>
                        </div>
                    </div>
                </form>
                <!-- End -->
                <!-- Flash messages -->
                <?php if ($this->session->flashdata('error')) : ?>
                    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                        <?= $this->session->flashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if ($this->session->flashdata('success')) : ?>
                    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                        <?= $this->session->flashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <!-- End -->
                <div class="row">
                    <div class="col">
                        <table id="tableproduct" class="display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>User Penginput</th>
                                    <th>Tanggal Import</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($acc_shopee as $askey => $asvalue){?>
                                <tr>
                                    <td><?= $askey+1 ?></td>
                                    <td><?= $asvalue->full_name ?></td>
                                    <td><?= $asvalue->created_date?></td>
                                    <td><a href="<?php echo base_url('shopee_recap/detail_acc?idacc_shopee=' . $asvalue->idacc_shopee)?>"><button type="button" class="btn btn-success"><i class="fas fa-list"></i> Details</button></a></td>
                                </tr>
                                <?php }?>
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