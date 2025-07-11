            <!-- Page content-->
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <h1 class="mt-4">Shopee Additional</h1>
                    </div>
                </div>

                <!-- form periode -->
                <form action="<?= base_url('shopee_additional/createAdditional') ?>" method="post">
                    <div class="card mt-3">
                        <div class="card-header">
                            <strong>Tambah Additional Revenue Shopee</strong>
                        </div>
                        <div class="card-body">

                            <!-- Pilih Bulan -->
                            <div class="mb-3">
                                <label for="month" class="form-label">Bulan:</label>
                                <select class="form-select" name="month" id="month" required>
                                    <option value="">-- Pilih Bulan --</option>
                                    <?php
                                    $months = [
                                        '01' => 'Januari',
                                        '02' => 'Februari',
                                        '03' => 'Maret',
                                        '04' => 'April',
                                        '05' => 'Mei',
                                        '06' => 'Juni',
                                        '07' => 'Juli',
                                        '08' => 'Agustus',
                                        '09' => 'September',
                                        '10' => 'Oktober',
                                        '11' => 'November',
                                        '12' => 'Desember'
                                    ];
                                    foreach ($months as $num => $name) {
                                        echo "<option value=\"$num\">$name</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- Pilih Tahun -->
                            <div class="mb-3">
                                <label for="year" class="form-label">Tahun:</label>
                                <select class="form-select" name="year" id="year" required>
                                    <option value="">-- Pilih Tahun --</option>
                                    <?php
                                    $currentYear = date('Y');
                                    for ($i = $currentYear; $i >= $currentYear - 5; $i--) {
                                        echo "<option value=\"$i\">$i</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- Input Additional Revenue -->
                            <div class="mb-3">
                                <label for="additional_revenue" class="form-label">Additional Revenue (Rp):</label>
                                <input type="number" class="form-control" name="additional_revenue" id="additional_revenue" required>
                            </div>

                            <button type="submit" class="btn btn-success">Update</button>
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
                        <table id="tablepadditional" class="display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Periode</th>
                                    <th>Additional Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                foreach ($acc_shopee_additional->result() as $row) :
                                ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td>
                                            <?= date('F Y', strtotime($row->start_date)) ?>
                                            <br>
                                            <small>(<?= date('d M Y', strtotime($row->start_date)) ?> - <?= date('d M Y', strtotime($row->end_date)) ?>)</small>
                                        </td>
                                        <td>Rp <?= number_format($row->additional_revenue, 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
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
                    new DataTable('#tablepadditional', {
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