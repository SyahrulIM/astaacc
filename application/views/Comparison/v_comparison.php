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
                <!-- Marketplace Filter -->
                <div class="col-md-3">
                    <label for="marketplace" class="form-label">Marketplace</label>
                    <select id="marketplace" name="marketplace" class="form-select">
                        <option value="">Semua</option>
                        <option value="shopee" <?= ($marketplace_filter === 'shopee' ? 'selected' : '') ?>>Shopee Asta</option>
                        <option value="shopee_kotime" <?= ($marketplace_filter === 'shopee_kotime' ? 'selected' : '') ?>>Shopee Kotime</option>
                        <option value="tiktok" <?= ($marketplace_filter === 'tiktok' ? 'selected' : '') ?>>TikTok Asta</option>
                        <option value="tiktok_kotime" <?= ($marketplace_filter === 'tiktok_kotime' ? 'selected' : '') ?>>TikTok Kotime</option>
                        <option value="lazada" <?= ($marketplace_filter === 'lazada' ? 'selected' : '') ?>>Lazada Asta</option>
                        <option value="lazada_kotime" <?= ($marketplace_filter === 'lazada_kotime' ? 'selected' : '') ?>>Lazada Kotime</option>
                        <option value="blibli" <?= ($marketplace_filter === 'blibli' ? 'selected' : '') ?>>Blibli</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="order_start" class="form-label">Tanggal Pesanan (Start)</label>
                    <input type="date" id="order_start" name="order_start" class="form-control" value="<?= $this->input->get('order_start') ?>" required>
                </div>
                <div class="col-md-3">
                    <label for="order_end" class="form-label">Tanggal Pesanan (End)</label>
                    <input type="date" id="order_end" name="order_end" class="form-control" value="<?= $this->input->get('order_end') ?>" required>
                </div>
                <div class="col-md-3">
                    <label for="ratio" class="form-label">Max Ratio</label>
                    <div class="input-group">
                        <input type="number" id="ratio" name="ratio" class="form-control" value="<?= $this->input->get('ratio') ?>" max="100" required>
                        <span class="input-group-text">%</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status Pembayaran</label>
                    <select id="status" name="status" class="form-select">
                        <option value="">Semua</option>
                        <option value="Belum Bayar" <?= $this->input->get('status') === 'Belum Bayar' ? 'selected' : '' ?>>Belum Bayar</option>
                        <option value="Sudah Bayar" <?= $this->input->get('status') === 'Sudah Bayar' ? 'selected' : '' ?>>Sudah Bayar</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="ratio_status">Status Ratio</label>
                    <select name="ratio_status" class="form-select">
                        <option value="">Semua</option>
                        <option value="lebih" <?= ($ratio_status === 'lebih' ? 'selected' : '') ?>>Lebih dari Max Ratio</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="type_status">Type Pembayaran</label>
                    <select name="type_status" class="form-select">
                        <option value="">Semua</option>
                        <option value="pembayaran" <?= ($type_status === 'pembayaran' ? 'selected' : '') ?>>Pembayaran</option>
                        <option value="retur" <?= ($type_status === 'retur' ? 'selected' : '') ?>>Retur</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="matching_status" class="form-label">Status Matching</label>
                    <select id="matching_status" name="matching_status" class="form-select">
                        <option value="">Semua</option>
                        <option value="match" <?= ($this->input->get('matching_status') === 'match') ? 'selected' : '' ?>>Match</option>
                        <option value="mismatch" <?= ($this->input->get('matching_status') === 'mismatch') ? 'selected' : '' ?>>Mismatch</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <div>
                        <button type="submit" class="btn btn-primary me-2">Filter</button>
                        <a href="<?= base_url('comparison') ?>" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>
            <hr>
            <div class="row">
                <div class="row">
                    <div class="col text-center">
                        <h3>Summary - Marketplace
                            <?php
                            if (empty($marketplace_filter)) {
                                echo 'Semua';
                            } else {
                                // Konversi format marketplace_filter ke tampilan yang lebih user-friendly
                                if (strpos($marketplace_filter, '_kotime') !== false) {
                                    $base = str_replace('_kotime', '', $marketplace_filter);
                                    echo ucfirst($base) . ' Kotime';
                                } elseif ($marketplace_filter === 'blibli') {
                                    echo 'Blibli';
                                } else {
                                    echo ucfirst($marketplace_filter) . ' Asta';
                                }
                            }
                            ?>
                        </h3>
                    </div>
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
                        echo $grand_total_invoice > 0
                            ? round((($grand_total_invoice - $grand_total_payment) / $grand_total_invoice) * 100, 2)
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
                <div class="col">
                    <h5>Additional Revenue</h5>
                    <h5> : <?= number_format($additional_revenue, 0, ',', '.') ?></h5>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col text-center mb-4">
                    <h3>Summary - Marketplace
                        <?php
                        if (empty($marketplace_filter)) {
                            echo 'Semua';
                        } else {
                            // Konversi format marketplace_filter ke tampilan yang lebih user-friendly
                            if (strpos($marketplace_filter, '_kotime') !== false) {
                                $base = str_replace('_kotime', '', $marketplace_filter);
                                echo ucfirst($base) . ' Kotime';
                            } elseif ($marketplace_filter === 'blibli') {
                                echo 'Blibli';
                            } else {
                                echo ucfirst($marketplace_filter) . ' Asta';
                            }
                        }
                        ?>
                    </h3>
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
                        echo $grand_total_invoice_after_retur > 0
                            ? round((($grand_total_invoice_after_retur - $grand_total_payment_after_retur) / $grand_total_invoice_after_retur) * 100, 2)
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
                <div class="col">
                    <h5>Additional Revenue</h5>
                    <h5> : <?= number_format($additional_revenue, 0, ',', '.') ?></h5>
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

    <!-- Modal Edit Keterangan -->
    <div class="modal fade" id="editNoteModal" tabindex="-1" aria-labelledby="editNoteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editNoteForm" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editNoteModalLabel">Edit Keterangan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="no_faktur" id="editNoFaktur">
                        <div class="mb-3">
                            <label for="editNoteText" class="form-label">Keterangan</label>
                            <textarea class="form-control" name="note" id="editNoteText" rows="4"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End -->

    <!-- Modal Konfirmasi Checking -->
    <div class="modal fade" id="confirmCheckModal" tabindex="-1" aria-labelledby="confirmCheckModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmCheckModalLabel">Konfirmasi Checking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menandai faktur ini sudah dicek?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="confirmCheckBtn">Ya, Tandai Sudah Dicek</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End -->

    <!-- Data Table Section -->
    <div class="row mt-4">
        <div class="col text-end">
            <div class="mb-3">
                <a href="<?= base_url('comparison/export_excel?' . http_build_query($this->input->get())) ?>" class="btn btn-success me-2"><i class="fa-solid fa-print"></i> Export Excel</a>
                <button id="finalDirSelected" class="btn btn-primary">
                    <i class="fas fa-check-circle"></i> Final Dir Select
                </button>
                <button id="multiCheckBtn" class="btn btn-info me-2">
                    <i class="fas fa-check-circle"></i> Tercheck Select
                </button>
            </div>
        </div>
        <div class="col-12">
            <table class="display" id="dataTable" width="100%">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAll"></th>
                        <th>No</th>
                        <th>Nomor Faktur</th>
                        <th>Marketplace</th>
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
                            Payment vs Bottom
                        </th>
                        <th>Keterangan</th>
                        <th>Status Check</th>
                        <th>
                            Status Dir
                        </th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $ratio_limit = (float) ($this->input->get('ratio') ?? 0);
                    foreach ($data_comparison as $row) :
                        // Hitung Selisih Ratio
                        if (!$row->accurate_payment) {
                            $ratio_diference = 0; // tidak ada transaksi
                        } else {
                            $ratio_diference = (($row->shopee_total_faktur - $row->accurate_payment) / $row->shopee_total_faktur) * 100;
                        }

                        // Tentukan highlight
                        if (
                            $ratio_diference > $ratio_limit
                            && $row->status_dir !== 'Allowed'
                            && $row->shopee_total_faktur != $row->accurate_payment
                        ) {
                            $highlight = 'style="background-color: #f8d7da;"';
                        } else {
                            $highlight = '';
                        }
                        ?>
                    <tr <?= $highlight ?>>
                        <td><input type="checkbox" class="select-row" value="<?= $row->no_faktur ?>"></td>
                        <td><?= $no++ ?></td>
                        <td><?= $row->no_faktur ?></td>
                        <td>
                            <?php
                                if (strpos($row->source, 'tiktok') !== false) {
                                    $is_kotime = strpos($row->source, '_kotime') !== false;
                                    ?>
                            <img src="https://cdn.brandfetch.io/idoruRsDhk/theme/dark/symbol.svg?c=1bxid64Mup7aczewSAYMX&t=1668515567929" alt="Tiktok Logo" style="height:20px; vertical-align:middle; margin-right:5px;">
                            Tiktok <?php echo $is_kotime ? 'Kotime' : 'Asta'; ?>
                            <?php } else if (strpos($row->source, 'shopee') !== false) {
                                    $is_kotime = strpos($row->source, '_kotime') !== false;
                                    ?>
                            <img src="https://cdn.brandfetch.io/idgVhUUiaD/w/500/h/500/theme/dark/icon.jpeg?c=1bxid64Mup7aczewSAYMX&t=1750904105236" alt="Shopee Logo" style="height:20px; vertical-align:middle; margin-right:5px;">
                            Shopee <?php echo $is_kotime ? 'Kotime' : 'Asta'; ?>
                            <?php } else if (strpos($row->source, 'lazada') !== false) {
                                    $is_kotime = strpos($row->source, '_kotime') !== false;
                                    ?>
                            <img src="https://cdn.brandfetch.io/idEvFu7hHv/w/400/h/400/theme/dark/icon.jpeg?c=1bxid64Mup7aczewSAYMX&t=1757586763652" alt="Lazada Logo" style="height:20px; vertical-align:middle; margin-right:5px;">
                            Lazada <?php echo $is_kotime ? 'Kotime' : 'Asta'; ?>
                            <?php } else if ($row->source == 'blibli') { ?>
                            <img src="https://cdn.brandfetch.io/idNm9i5M80/w/400/h/400/theme/dark/icon.jpeg?c=1bxid64Mup7aczewSAYMX&t=1757586526763" alt="Blibli Logo" style="height:20px; vertical-align:middle; margin-right:5px;">
                            Blibli
                            <?php } ?>
                        </td>
                        <td><?= $row->shopee_order_date ?? '-' ?></td>
                        <td><?= $row->accurate_pay_date ?? '-' ?></td>
                        <td><?= number_format($row->shopee_total_faktur ?? 0) ?></td>
                        <td><?= number_format($row->accurate_payment ?? 0) ?></td>
                        <td><?= $this->input->get('ratio') ?: 0 ?>%</td>
                        <td class="dt-type-numeric">
                            <?php
                                if (!$row->accurate_payment) {
                                    echo '0%';
                                } else {
                                    echo number_format((($row->shopee_total_faktur - $row->accurate_payment) / $row->shopee_total_faktur) * 100, 2) . '%';
                                }
                                ?>
                        </td>
                        <td><?= number_format(($row->shopee_total_faktur ?? 0) - ($row->accurate_payment ?? 0)) ?></td>
                        <td>
                            <?= ($row->shopee_refund ?? 0) < 0 ? '<span class="badge bg-warning">Retur</span>' : '<span class="badge bg-success">Pembayaran</span>' ?>
                        </td>
                        <td>
                            <?php
                                if (($row->accurate_total_faktur ?? 0) != ($row->shopee_total_faktur ?? 0)) {
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
                            <?php if ($row->total_price_bottom > $row->accurate_payment) { ?>
                            <span class="badge bg-warning">
                                < Bottom</span> <?php } else { ?> <span class="badge bg-success">Payment >
                            </span>
                            <?php } ?>
                        </td>
                        <?php if (isset($row->note)) { ?>
                        <td><?= $row->note; ?></td>
                        <?php } else { ?>
                        <td>-</td>
                        <?php } ?>
                        <?php if ($row->is_check == 1) { ?>
                        <td><span class="badge bg-success">Sudah</span></td>
                        <?php } else if ($row->is_check == 0 && empty($highlight)) { ?>
                        <td><span class="badge bg-success">Safe</span></td>
                        <?php } else if ($row->is_check == 0) { ?>
                        <td><span class="badge bg-warning">Belum</span></td>
                        <?php } ?>
                        <td>
                            <?php
                                if ($row->status_dir === 'Allowed') {
                                    echo '<span class="badge bg-success">Allowed by Dir</span>';
                                } elseif ($highlight) {
                                    echo '<span class="badge bg-warning">Unsafe</span>';
                                } else {
                                    echo '<span class="badge bg-success">Safe</span>';
                                }
                                ?>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-success detail-btn" data-faktur="<?= $row->no_faktur ?>"><i class="fa-solid fa-list"></i> Detail</button>
                            <button type="button" class="btn btn-sm btn-info btn-edit-note" data-faktur="<?= $row->no_faktur ?>" data-note="<?= htmlspecialchars($row->note ?? '') ?>"><i class="fa-solid fa-file-pen"></i> Edit Keterangan</button>
                            <?php if ($highlight) { ?>
                            <a href="#" class="btn btn-sm btn-primary btn-final-dir" data-faktur="<?= $row->no_faktur ?>" onclick="return confirm('Yakin ingin set status Allowed untuk faktur ini?')">
                                <i class="fa-solid fa-check-double"></i> Final Dir
                            </a>
                            <?php } ?>
                            <?php if ($row->is_check == 0) { ?>
                            <button class="btn btn-xs btn-info btn-checking" data-faktur="<?= $row->no_faktur ?>">
                                <i class="fa-solid fa-check"></i> Tandai Checking
                            </button>
                            <?php } ?>
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
            responsive: false,
            scrollX: true,
            layout: {
                bottomEnd: {
                    paging: {
                        firstLast: false
                    }
                }
            },
            columnDefs: [{
                targets: 0,
                orderable: false
            }]
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

    // Toast function for notifications
    function showToast(message, type = 'success') {
        const toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';

        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');

        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;

        toastContainer.appendChild(toast);
        document.body.appendChild(toastContainer);

        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();

        toast.addEventListener('hidden.bs.toast', function() {
            document.body.removeChild(toastContainer);
        });
    }

    // Start Final Dir
    $(document).on('click', '.btn-final-dir', function(e) {
        e.preventDefault();
        const faktur = $(this).data('faktur');
        const row = $(this).closest('tr'); // Dapatkan elemen tr

        if (!confirm('Yakin set status Allowed untuk faktur ini?')) return;

        $.ajax({
            url: '<?= base_url("comparison/final_dir_single") ?>',
            type: 'POST',
            data: {
                no_faktur: faktur
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // 1. Hapus highlight dari row
                    row.css('background-color', ''); // Hapus style inline

                    // 2. Update status column
                    row.find('td:eq(17)').html('<span class="badge bg-success">Allowed by Dir</span>');

                    // 3. Hapus tombol Final Dir
                    row.find('.btn-final-dir').remove();

                    // 4. Update status dir di kolom status
                    row.find('td:eq(17)').html('<span class="badge bg-success">Allowed by Dir</span>');

                    showToast('Status Dir berhasil diupdate', 'success');
                }
            }
        });
    });
    // End

    // Start Multiple Select
    document.getElementById('selectAll').addEventListener('change', function() {
        const isChecked = this.checked;
        document.querySelectorAll('.select-row').forEach(cb => cb.checked = isChecked);
    });

    // Final Dir Multi Select
    $('#finalDirSelected').on('click', function() {
        const selected = $('.select-row:checked').map(function() {
            return $(this).val();
        }).get();

        if (selected.length === 0) {
            showToast('Pilih minimal satu faktur', 'warning');
            return;
        }

        $.ajax({
            url: '<?= base_url("comparison/final_dir_batch") ?>',
            type: 'POST',
            data: {
                faktur_list: selected
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    selected.forEach(faktur => {
                        const row = $(`input[value="${faktur}"]`).closest('tr');

                        // 1. Hapus highlight
                        row.css('background-color', '');

                        // 2. Update status column
                        row.find('td:eq(17)').html('<span class="badge bg-success">Allowed by Dir</span>');

                        // 3. Hapus tombol Final Dir
                        row.find('.btn-final-dir').remove();

                        // 4. Update status dir di kolom status
                        row.find('td:eq(17)').html('<span class="badge bg-success">Allowed by Dir</span>');
                    });
                    showToast(`${selected.length} faktur berhasil diupdate`, 'success');
                }
            }
        });
    });
    // End

    // Start Modal Edit Keterangan (Without Page Refresh)
    $(document).ready(function() {
        // Show modal dan isi datanya
        $(document).on('click', '.btn-edit-note', function() {
            const faktur = $(this).data('faktur');
            const note = $(this).data('note');

            $('#editNoFaktur').val(faktur);
            $('#editNoteText').val(note);

            const modal = new bootstrap.Modal(document.getElementById('editNoteModal'));
            modal.show();
        });

        // Submit form
        $('#editNoteForm').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: "<?= base_url('comparison/update_note') ?>",
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Update the note in the table row without refresh
                        const faktur = $('#editNoFaktur').val();
                        const newNote = $('#editNoteText').val();

                        // Find all rows with this invoice number and update their note cells
                        $(`[data-faktur="${faktur}"]`).each(function() {
                            // Update the note in the table cell
                            const row = $(this).closest('tr');
                            row.find('td:eq(15)').text(newNote || '-'); // Assuming note is in 15th column (0-based index 14)

                            // Update the data-note attribute on the edit button
                            row.find('.btn-edit-note').data('note', newNote);
                        });

                        // Close the modal
                        bootstrap.Modal.getInstance(document.getElementById('editNoteModal')).hide();

                        // Show success message
                        showToast('Keterangan berhasil diperbarui', 'success');
                    } else {
                        showToast('Gagal: ' + response.message, 'error');
                    }
                },
                error: function() {
                    showToast('Terjadi kesalahan saat menyimpan keterangan', 'error');
                }
            });
        });
    });
    // End

    // Start Tandai Checking
    // Handle checking button click
    $(document).on('click', '.btn-checking', function() {
        const faktur = $(this).data('faktur');
        const modal = new bootstrap.Modal(document.getElementById('confirmCheckModal'));

        $('#confirmCheckModal').data('faktur', faktur);
        modal.show();
    });

    // Handle confirmation button click
    $('#confirmCheckBtn').on('click', function() {
        const faktur = $('#confirmCheckModal').data('faktur');
        const modal = bootstrap.Modal.getInstance(document.getElementById('confirmCheckModal'));

        $.ajax({
            url: '<?= base_url("comparison/update_checking") ?>',
            type: 'POST',
            data: {
                no_faktur: faktur
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Update status check column (change to match your column index)
                    $(`tr td:has(button.btn-checking[data-faktur="${faktur}"])`)
                        .closest('tr')
                        .find('td').eq(16) // Change this index to match your Status Check column
                        .html('<span class="badge bg-success">Sudah</span>');

                    // Remove only the checking button with smooth fade effect
                    $(`button.btn-checking[data-faktur="${faktur}"]`)
                        .fadeOut(300, function() {
                            $(this).remove();
                        });

                    modal.hide();
                    showToast('Status checking berhasil diperbarui', 'success');
                } else {
                    showToast(response.message || 'Gagal memperbarui status', 'error');
                }
            },
            error: function() {
                showToast('Terjadi kesalahan saat menghubungi server', 'error');
            }
        });
    });
    // End

    // Start tandai checking select
    $('#selectAll').change(function() {
        $('.select-row').prop('checked', $(this).prop('checked'));
    });

    $('#multiCheckBtn').on('click', function() {
        const selected = $('.select-row:checked').map(function() {
            return $(this).val();
        }).get();

        if (selected.length === 0) {
            showToast('Pilih minimal satu faktur terlebih dahulu', 'warning');
            return;
        }

        if (!confirm(`Yakin tandai ${selected.length} faktur sebagai sudah dicek?`)) return;

        $.ajax({
            url: '<?= base_url("comparison/update_checking_batch") ?>',
            type: 'POST',
            data: {
                faktur_list: selected
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Update UI untuk semua yang dipilih
                    selected.forEach(faktur => {
                        $(`tr:has(input[value="${faktur}"])`).find('td:eq(16)').html('<span class="badge bg-success">Sudah</span>');
                        $(`button.btn-checking[data-faktur="${faktur}"]`).remove();
                    });
                    showToast(`${selected.length} faktur berhasil ditandai`, 'success');
                } else {
                    showToast(response.message || 'Gagal memperbarui status', 'error');
                }
            },
            error: function() {
                showToast('Terjadi kesalahan saat memproses', 'error');
            }
        });
    });
    // End
</script>