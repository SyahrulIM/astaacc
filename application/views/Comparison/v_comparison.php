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
                    <label for="order_start" class="form-label">Tanggal Pesanan (Start)</label>
                    <input type="date" id="order_start" name="order_start" class="form-control" value="<?= $this->input->get('order_start') ?>">
                </div>
                <div class="col-md-4">
                    <label for="order_end" class="form-label">Tanggal Pesanan (End)</label>
                    <input type="date" id="order_end" name="order_end" class="form-control" value="<?= $this->input->get('order_end') ?>">
                </div>
                <!-- <div class="col-md-4">
                    <label for="start_date" class="form-label">Tanggal Pembayaran (Start)</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" value="<?= $this->input->get('start_date') ?>">
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">Tanggal Pembayaran (End)</label>
                    <input type="date" id="end_date" name="end_date" class="form-control" value="<?= $this->input->get('end_date') ?>">
                </div> -->
                <div class="col-md-4">
                    <label for="ratio" class="form-label">Max Ratio</label>
                    <div class="input-group">
                        <input type="number" id="ratio" name="ratio" class="form-control" value="<?= $this->input->get('ratio') ?>" max="100">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="status" class="form-label">Status Pembayaran</label>
                    <select id="status" name="status" class="form-select">
                        <option value="">Pilih Status Pembayaran</option>
                        <option value="Belum Bayar" <?= $this->input->get('status') === 'Belum Bayar' ? 'selected' : '' ?>>Belum Bayar</option>
                        <option value="Sudah Bayar" <?= $this->input->get('status') === 'Sudah Bayar' ? 'selected' : '' ?>>Sudah Bayar</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div>
                        <button type="submit" class="btn btn-primary me-2">Filter</button>
                        <a href="<?= base_url('comparison') ?>" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>
            <hr>
            <div class="row">
                <div class="col text-center">
                    <h3>Summary</h3>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <h5>Grand total Nominal Invoice</h5>
                    <h5> : <?= number_format($grand_total_invoice) ?></h5>
                </div>
                <div class="col">
                    <h5>Grand total Nilai Diterima</h5>
                    <h5> : <?= number_format($grand_total_payment) ?></h5>
                </div>
                <div class="col">
                    <h5>Selisih Total</h5>
                    <h5> : <?= number_format($grand_total_invoice - $grand_total_payment) ?></h5>
                </div>
                <div class="col">
                    <h5>Jumlah Selisih</h5>
                    <h5> : <?= number_format($grand_total_invoice - $grand_total_payment) ?></h5>
                </div>
                <div class="col">
                    <h5>Ratio Selisih</h5>
                    <h5> :
                        <?php
                        echo $grand_total_payment > 0
                            ? round((($grand_total_invoice - $grand_total_payment) / $grand_total_payment) * 100, 2)
                            : 0;
                        ?>%
                    </h5>
                </div>
            </div>
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
                        <th>Nominal Invoice</th>
                        <th>Nilai Diterima</th>
                        <th>Max Ratio</th>
                        <th>Selisih</th>
                        <th>Status Matching</th>
                        <th>Status Terbayar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $ratio_limit = (float) ($this->input->get('ratio') ?? 0);
                    foreach ($data_comparison as $row) :
                        $shopee = (float) ($row->shopee_total_faktur ?? 0);
                        $accurate = (float) ($row->accurate_payment ?? 0);
                        $max_allowed = $accurate ? (($shopee - $accurate) / $accurate) * 100 : 0;
                        $highlight = $max_allowed > $ratio_limit ? 'style="background-color: #f8d7da;"' : '';
                    ?>
                        <tr <?= $highlight ?>>
                            <td><?= $no++ ?></td> <!-- Tambahkan baris ini untuk kolom "No" -->
                            <td><?= $row->no_faktur ?></td>
                            <td>
                                <?= $row->shopee_order_date ?? '-' ?>
                            </td>
                            <td><?= $row->accurate_pay_date ?? '-' ?></td>
                            <td><?= number_format($row->shopee_total_faktur ?? 0) ?></td>
                            <td><?= number_format($row->accurate_payment ?? 0) ?></td>
                            <td><?= $this->input->get('ratio') ?: 0 ?>%</td>
                            <td><?= number_format($row->shopee_total_faktur - $row->accurate_payment ?? 0) ?></td>
                            <td>
                                <?php
                                if (($row->accurate_total_faktur ?? 0) != ($row->shopee_total_faktur ?? 0) ||
                                    ($row->accurate_discount ?? 0) != ($row->shopee_discount ?? 0) ||
                                    ($row->accurate_payment ?? 0) != ($row->shopee_payment ?? 0)
                                ) {
                                    echo '<span class="badge bg-warning">Mismatch</span>';
                                } else {
                                    echo '<span class="badge bg-success">Match</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <?php if (!empty($row->accurate_payment)) : ?>
                                    <span class="badge bg-success">Sudah Bayar</span>
                                <?php else : ?>
                                    <span class="badge bg-warning">Belum Bayar</span>
                                <?php endif; ?>
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