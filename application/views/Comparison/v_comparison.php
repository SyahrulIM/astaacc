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
                    <input type="date" id="order_start" name="order_start" class="form-control" value="<?= $this->input->get('order_start') ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="order_end" class="form-label">Tanggal Pesanan (End)</label>
                    <input type="date" id="order_end" name="order_end" class="form-control" value="<?= $this->input->get('order_end') ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="ratio" class="form-label">Max Ratio</label>
                    <div class="input-group">
                        <input type="number" id="ratio" name="ratio" class="form-control" value="<?= $this->input->get('ratio') ?>" max="100" required>
                        <span class="input-group-text">%</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="status" class="form-label">Status Pembayaran</label>
                    <select id="status" name="status" class="form-select">
                        <option value="">Semua</option>
                        <option value="Belum Bayar" <?= $this->input->get('status') === 'Belum Bayar' ? 'selected' : '' ?>>Belum Bayar</option>
                        <option value="Sudah Bayar" <?= $this->input->get('status') === 'Sudah Bayar' ? 'selected' : '' ?>>Sudah Bayar</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="ratio_status">Status Ratio</label>
                    <select name="ratio_status" class="form-select">
                        <option value="">Semua</option>
                        <option value="lebih" <?= ($ratio_status === 'lebih' ? 'selected' : '') ?>>Lebih dari Max Ratio</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="type_status">Type Pembayaran</label>
                    <select name="type_status" class="form-select">
                        <option value="">Semua</option>
                        <option value="pembayaran" <?= ($type_status === 'pembayaran' ? 'selected' : '') ?>>Pembayaran</option>
                        <option value="retur" <?= ($type_status === 'retur' ? 'selected' : '') ?>>Retur</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="matching_status" class="form-label">Status Matching</label>
                    <select id="matching_status" name="matching_status" class="form-select">
                        <option value="">Semua</option>
                        <option value="match" <?= ($this->input->get('matching_status') === 'match') ? 'selected' : '' ?>>Match</option>
                        <option value="mismatch" <?= ($this->input->get('matching_status') === 'mismatch') ? 'selected' : '' ?>>Mismatch</option>
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
                    <h5>Ratio Selisih</h5>
                    <h5> :
                        <?php
                        echo $grand_total_payment > 0
                            ? round((($grand_total_invoice - $grand_total_payment) / $grand_total_payment) * 100, 2)
                            : 0;
                        ?>%
                    </h5>
                </div>
                <div class="col">
                    <h5>Jumlah Faktur Melebihi Max Ratio</h5>
                    <h5> : <?= number_format($exceed_ratio_count) ?></h5>
                </div>
                <div class="col">
                    <h5>Jumlah Faktur Mismatch</h5>
                    <h5> : <?= number_format($mismatch_count) ?></h5>
                </div>
                <div class="col">
                    <h5>Jumlah Faktur Retur</h5>
                    <h5> : <?= number_format($retur_count) ?></h5>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col text-center mb-4">
                    <h3>Summary</h3>
                    <font size='4'>Setelah Dikurangi Retur</font>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <h5>Grand total Nominal Invoice</h5>
                    <h5> : <?= number_format($grand_total_invoice_after_retur) ?></h5>
                </div>
                <div class="col">
                    <h5>Grand total Nilai Diterima</h5>
                    <h5> : <?= number_format($grand_total_payment_after_retur) ?></h5>
                </div>
                <div class="col">
                    <h5>Selisih Total</h5>
                    <h5> : <?= number_format($grand_total_invoice_after_retur - $grand_total_payment_after_retur) ?></h5>
                </div>
                <div class="col">
                    <h5>Ratio Selisih</h5>
                    <h5> :
                        <?php
                        echo $grand_total_payment_after_retur > 0
                            ? round((($grand_total_invoice_after_retur - $grand_total_payment_after_retur) / $grand_total_payment_after_retur) * 100, 2)
                            : 0;
                        ?>%
                    </h5>
                </div>
                <div class="col">
                    <h5>Jumlah Faktur Melebihi Max Ratio</h5>
                    <h5> : <?= number_format($exceed_ratio_count_non_retur) ?></h5>
                </div>
                <div class="col">
                    <h5>Jumlah Faktur Mismatch</h5>
                    <h5> : <?= number_format($mismatch_count); ?> </h5>
                </div>
                <div class="col">
                    <h5>Jumlah Faktur Retur</h5>
                    <h5> : <?= number_format($retur_count) ?></h5>
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
    <!-- End -->

    <!-- Modal Bootstrap -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail Faktur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="detailContent">
                    Loading...
                </div>
            </div>
        </div>
    </div>
    <!-- End -->

    <!-- Modal Bootstrap -->
    <div class="modal fade" id="priceModal" tabindex="-1" aria-labelledby="priceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="priceModalLabel">Detail Faktur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="priceContent">
                    Loading...
                </div>
            </div>
        </div>
    </div>
    <!-- End -->

    <!-- Data Table Section -->
    <div class="row mt-4">
        <div class="col">
            <table class="display" id="dataTable" width="100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nomor Faktur</th>
                        <th>Tanggal Pesanan</th>
                        <th>
                            Tanggal Pembayaran<br>
                            <font size="2">ACC</font>
                        </th>
                        <th>Nominal Invoice</th>
                        <th>
                            Nilai Diterima<br>
                            <font size="2">ACC</font>
                        </th>
                        <th>Max Ratio</th>
                        <th>Selisih Ratio</th>
                        <th>Selisih</th>
                        <th>Refund</th>
                        <th>
                            Type Faktur<br>
                            <font size="2">MP</font>
                        </th>
                        <th>Status Matching</th>
                        <th>
                            Status Terbayar<br>
                            <font size="2">ACC</font>
                        </th>
                        <th>
                            Invoice vs Bottom
                        </th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $ratio_limit = (float) ($this->input->get('ratio') ?? 0);
                    foreach ($data_comparison as $row) :
                        if ($row->accurate_payment == 0) {
                            $ratio_diference = 0;
                        } else {
                            $ratio_diference = (($row->shopee_total_faktur - $row->accurate_payment) / $row->accurate_payment) * 100;
                        }

                        $highlight = ($ratio_diference > $ratio_limit ||
                            ($row->shopee_refund ?? 0) < 0 ||
                            ($row->total_price_bottom ?? 0) > ($row->shopee_total_faktur ?? 0)
                        ) ? 'style="background-color: #f8d7da;"' : '';
                    ?>
                        <tr <?= $highlight ?>>
                            <td><?= $no++ ?></td>
                            <td><?= $row->no_faktur ?></td>
                            <td>
                                <?= $row->shopee_order_date ?? '-' ?>
                            </td>
                            <td><?= $row->accurate_pay_date ?? '-' ?></td>
                            <td><?= number_format($row->shopee_total_faktur ?? 0) ?></td>
                            <td><?= number_format($row->accurate_payment ?? 0) ?></td>
                            <td><?= $this->input->get('ratio') ?: 0 ?>%</td>
                            <td class="dt-type-numeric"><?php
                                                        if ($row->accurate_payment == 0) {
                                                            echo '0%';
                                                        } else {
                                                            echo number_format((($row->shopee_total_faktur - $row->accurate_payment) / $row->accurate_payment) * 100) . '%';
                                                        }
                                                        ?></td>
                            <td><?= number_format($row->shopee_total_faktur - $row->accurate_payment ?? 0) ?></td>
                            <td><?= number_format($row->shopee_refund) ?></td>
                            <td>
                                <?= ($row->shopee_refund ?? 0) < 0 ? '<span class="badge bg-warning">Retur</span>' : '<span class="badge bg-success">Pembayaran</span>' ?>
                            </td>
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
                            <td>
                                <?php if ($row->total_price_bottom > $row->shopee_total_faktur) { ?>
                                    <span class="badge bg-warning">< Bottom</span>
                                <?php } else { ?>
                                    <span class="badge bg-success">
                                        Invoice ></span>
                                        <?php } ?>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-success detail-btn" data-faktur="<?= $row->no_faktur ?>">Detail</button>
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
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/rowreorder/1.5.0/js/dataTables.rowReorder.js"></script>
<script src="https://cdn.datatables.net/rowreorder/1.5.0/js/rowReorder.dataTables.js"></script>
<script src="https://cdn.datatables.net/responsive/3.0.4/js/dataTables.responsive.js"></script>
<script src="https://cdn.datatables.net/responsive/3.0.4/js/responsive.dataTables.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo base_url(); ?>js/scripts.js"></script>

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
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.detail-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const faktur = this.getAttribute('data-faktur');
                fetch('<?= base_url('comparison/detail_ajax/') ?>' + faktur)
                    .then(response => response.text())
                    .then(html => {
                        document.getElementById('detailContent').innerHTML = html;
                        new bootstrap.Modal(document.getElementById('detailModal')).show();
                    });
            });
        });
    });
</script>