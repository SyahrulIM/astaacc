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
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <strong>Import Shopee Recap (xlsx)</strong>
                            <div>
                                <a href="<?= base_url('assets/template_excel/Income.sudah dilepas.id.20250101_20250630.xlsx') ?>" class="btn btn-sm btn-success me-2" download>
                                    Download Template Income
                                </a>
                                <a href="<?= base_url('assets/template_excel/Order.completed.20250501_20250531.xlsx') ?>" class="btn btn-sm btn-info" download>
                                    Download Template Order
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="typeExcel" class="form-label">Type Excel</label>
                                <select name="typeExcel" id="typeExcel" class="form-select">
                                    <option value="income">Income</option>
                                    <option value="order">Order</option>
                                </select>
                            </div>
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
                        <!-- Tabs Navigation -->
                        <ul class="nav nav-tabs mt-4" id="listTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="list-tab" data-bs-toggle="tab" data-bs-target="#list" type="button" role="tab">List</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab">All</button>
                            </li>
                        </ul>

                        <!-- Tabs Content -->
                        <div class="tab-content" id="listTabsContent">
                            <!-- Tab 1: list -->
                            <div class="tab-pane fade show active" id="list" role="tabpanel" aria-labelledby="list-tab">
                                <div class="row mt-3">
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
                                                <?php
                                                foreach ($acc_shopee as $askey => $asvalue) {
                                                ?>
                                                    <tr>
                                                        <td><?= $askey + 1 ?></td>
                                                        <td><?= $asvalue->full_name ?></td>
                                                        <td><?= $asvalue->created_date ?></td>
                                                        <td>
                                                            <a href="<?php echo base_url('shopee_recap/detail_acc?idacc_shopee=' . $asvalue->idacc_shopee) ?>">
                                                                <button type="button" class="btn btn-success"><i class="fas fa-list"></i> Details</button>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab 2: all (example filter - you can adjust based on backend or JS) -->
                            <div class="tab-pane fade" id="all" role="tabpanel" aria-labelledby="all-tab">
                                <div class="row mt-3">
                                    <div class="col">
                                        <table id="tableall" class="display" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nomer Faktur</th>
                                                    <th>Tanggal Pesanan</th>
                                                    <th>Tanggal Pembayaran</th>
                                                    <th>Total Faktur</th>
                                                    <th>Bayar</th>
                                                    <th>Diskon</th>
                                                    <th>Refund</th>
                                                    <th>Pembayaran</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                foreach ($acc_shopee_detail as $asdkey => $asdvalue) {
                                                ?>
                                                    <tr>
                                                        <td><?= $asdkey + 1 ?></td>
                                                        <td><?= $asdvalue->no_faktur ?></td>
                                                        <td><?= $asdvalue->order_date ?></td>
                                                        <td><?= $asdvalue->pay_date ?></td>
                                                        <td><?= number_format($asdvalue->total_faktur) ?></td>
                                                        <td><?= number_format($asdvalue->pay) ?></td>
                                                        <td><?= number_format($asdvalue->discount) ?></td>
                                                        <td><?= number_format($asdvalue->refund) ?></td>
                                                        <td><?= number_format($asdvalue->payment) ?></td>
                                                        <td><a href="<?= base_url('shopee_recap/detail_faktur?no_faktur=' . $asdvalue->no_faktur) ?>"><button type="button" class="btn btn-success">Detail Faktur</button></a></td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
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

                $(document).ready(function() {
                    new DataTable('#tableall', {
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