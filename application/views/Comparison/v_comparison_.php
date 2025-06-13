<!-- Page content-->
<div class="container-fluid">
    <div class="row">
        <div class="col">
            <h1 class="mt-4"><?= $title ?></h1>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card mt-4">
        <div class="card-header">
            <strong>Filter</strong>
        </div>
        <div class="card-body">
            <form method="get" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Tanggal Pembayaran (Start)</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" value="<?= $this->input->get('start_date') ?>">
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">Tanggal Pembayaran (End)</label>
                    <input type="date" id="end_date" name="end_date" class="form-control" value="<?= $this->input->get('end_date') ?>">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div>
                        <button type="submit" class="btn btn-primary me-2">Filter</button>
                        <a href="<?= base_url('comparison') ?>" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

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

    <!-- Data Table Section -->
    <div class="row mt-4">
        <div class="col">
            <table class="display" id="dataTable" width="100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nomor Faktur</th>
                        <th>Tanggal Pesanan</th>
                        <th>Tanggal Pembayaran</th>
                        <th>Total Accurate</th>
                        <th>Total Shopee</th>
                        <th>Diskon Accurate</th>
                        <th>Diskon Shopee</th>
                        <th>Pembayaran Accurate</th>
                        <th>Pembayaran Shopee</th>
                        <th>Status Matching</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($data_comparison as $row) : ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $row->no_faktur ?></td>
                            <td>
                                <?php
                                $faktur = $row->no_faktur;
                                $tanggal_pesanan = '-';
                                if (preg_match('/^\d{6}/', $faktur)) {
                                    $tgl = substr($faktur, 4, 2);
                                    $bln = substr($faktur, 2, 2);
                                    $thn = '20' . substr($faktur, 0, 2); // Misal 24 jadi 2024
                                    $tanggal_pesanan = "$thn-$bln-$tgl";
                                }
                                echo $tanggal_pesanan;
                                ?>
                            </td>
                            <td><?= $row->shopee_pay_date ?? '-' ?></td>
                            <td><?= number_format($row->accurate_total_faktur ?? 0) ?></td>
                            <td><?= number_format($row->shopee_total_faktur ?? 0) ?></td>
                            <td><?= number_format($row->accurate_discount ?? 0) ?></td>
                            <td><?= number_format($row->shopee_discount ?? 0) ?></td>
                            <td><?= number_format($row->accurate_payment ?? 0) ?></td>
                            <td><?= number_format($row->shopee_payment ?? 0) ?></td>
                            <td>
                                <?php
                                if (($row->accurate_total_faktur ?? 0) != ($row->shopee_total_faktur ?? 0) ||
                                    ($row->accurate_discount ?? 0) != ($row->shopee_discount ?? 0) ||
                                    ($row->accurate_payment ?? 0) != ($row->shopee_payment ?? 0)
                                ) {
                                    echo '<span class="badge bg-danger">Mismatch</span>';
                                } else {
                                    echo '<span class="badge bg-success">Match</span>';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- JavaScript Libraries -->
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
        new DataTable('#dataTable', {
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