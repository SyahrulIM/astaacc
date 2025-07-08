<!-- Page content-->
<div class="container-fluid">
    <div class="row">
        <div class="col">
            <h1 class="mt-4">Accurate Pembayaran</h1>
        </div>
    </div>
    <!-- Import Excel -->
    <form action="<?= base_url('accurate_recap/createAccurate') ?>" method="post" enctype="multipart/form-data">
        <div class="card mt-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Import Accurate Recap (xlsx)</strong>
                <a href="<?= base_url('assets/template_accurate/pembayaran_faktur_suryajayamakmur_250613135701.xlsx') ?>" class="btn btn-sm btn-success" download>
                    Download Template Accurate
                </a>
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
            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs mt-4" id="accurateTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="list-tab" data-bs-toggle="tab" data-bs-target="#accurate-list" type="button" role="tab">List</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="all-tab" data-bs-toggle="tab" data-bs-target="#accurate-all" type="button" role="tab">All</button>
                </li>
            </ul>

            <!-- Tabs Content -->
            <div class="tab-content" id="accurateTabsContent">
                <!-- Tab 1: list -->
                <div class="tab-pane fade show active" id="accurate-list" role="tabpanel" aria-labelledby="list-tab">
                    <div class="row mt-3">
                        <div class="col">
                            <table id="tableaccurate" class="display" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>User Penginput</th>
                                        <th>Tanggal Import</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($acc_accurate as $ackey => $acvalue) { ?>
                                        <tr>
                                            <td><?= $ackey + 1 ?></td>
                                            <td><?= $acvalue->full_name ?></td>
                                            <td><?= $acvalue->created_date ?></td>
                                            <td>
                                                <a href="<?php echo base_url('accurate_recap/detail_acc?idacc_accurate=' . $acvalue->idacc_accurate) ?>">
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

                <!-- Tab 2: all -->
                <div class="tab-pane fade" id="accurate-all" role="tabpanel" aria-labelledby="all-tab">
                    <div class="row mt-3">
                        <div class="col">
                            <table id="tableaccurate-all" class="display" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nomer Faktur</th>
                                        <th>Tanggal Pembayaran</th>
                                        <th>Total Faktur</th>
                                        <th>Bayar</th>
                                        <th>Diskon</th>
                                        <th>Pembayaran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Assuming you have a $acc_accurate_detail variable similar to Shopee's
                                    foreach ($acc_accurate_detail as $acdkey => $acdvalue) {
                                    ?>
                                        <tr>
                                            <td><?= $acdkey + 1 ?></td>
                                            <td><?= $acdvalue->no_faktur ?></td>
                                            <td><?= $acdvalue->pay_date ?></td>
                                            <td><?= number_format($acdvalue->total_faktur) ?></td>
                                            <td><?= number_format($acdvalue->pay) ?></td>
                                            <td><?= number_format($acdvalue->discount) ?></td>
                                            <td><?= number_format($acdvalue->payment) ?></td>
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
        new DataTable('#tableaccurate', {
            responsive: true,
            layout: {
                bottomEnd: {
                    paging: {
                        firstLast: false
                    }
                }
            }
        });

        new DataTable('#tableaccurate-all', {
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