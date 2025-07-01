            <!-- Page content-->
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <h1 class="mt-4">Clustering</h1>
                    </div>
                </div>

                <!-- Filter Form -->
                <div class="card mt-4">
                    <div class="card-header">
                        <strong>Filter</strong>
                    </div>
                    <div class="card-body">
                        <form method="get" class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label for="order_start" class="form-label">Tanggal Pesanan (Start)</label>
                                <input type="date" id="order_start" name="order_start" class="form-control" value="<?= $this->input->get('order_start') ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label for="order_end" class="form-label">Tanggal Pesanan (End)</label>
                                <input type="date" id="order_end" name="order_end" class="form-control" value="<?= $this->input->get('order_end') ?>" required>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <div>
                                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                                    <a href="<?= base_url('clustering') ?>" class="btn btn-secondary">Reset</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
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

                <div class="row mb-4">
                    <div class="col-4 align-self-end">
                        <canvas id="donutChart"></canvas>
                    </div>
                    <div class="col-8 align-self-end">
                        <canvas id="barChart"></canvas>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col">
                        <a href="<?= base_url('clustering/export_excel' . (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '')) ?>" class="btn btn-success">
                            Export Excel
                        </a>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <table id="datatable" class="display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Provinsi</th>
                                    <th>Jumlah Faktur</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                foreach ($clustering_data as $row) : ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= $row->province_name ?? 'Tidak diketahui' ?></td>
                                        <td><?= $row->jumlah_no_faktur ?></td>
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
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                $(document).ready(function() {
                    // Inisialisasi DataTable
                    new DataTable('#datatable', {
                        responsive: true,
                        layout: {
                            bottomEnd: {
                                paging: {
                                    firstLast: false
                                }
                            }
                        }
                    });

                    // Data dari PHP ke JavaScript
                    const provinceLabels = <?= json_encode(array_column($clustering_data, 'province_name')) ?>;
                    const fakturCounts = <?= json_encode(array_column($clustering_data, 'jumlah_no_faktur')) ?>;

                    // Warna random untuk donut chart
                    const backgroundColors = provinceLabels.map(() => '#' + Math.floor(Math.random() * 16777215).toString(16));

                    // Donut Chart
                    const ctx = document.getElementById('donutChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: provinceLabels,
                            datasets: [{
                                label: 'Jumlah Faktur',
                                data: fakturCounts,
                                backgroundColor: backgroundColors,
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                },
                                title: {
                                    display: true,
                                    text: 'Distribusi Jumlah Faktur per Provinsi'
                                }
                            }
                        }
                    });

                    // Bar Chart (DIPINDAH KE SINI)
                    const ctxBar = document.getElementById('barChart').getContext('2d');
                    new Chart(ctxBar, {
                        type: 'bar',
                        data: {
                            labels: provinceLabels,
                            datasets: [{
                                label: 'Jumlah Faktur',
                                data: fakturCounts,
                                backgroundColor: '#4e73df',
                                borderColor: '#2e59d9',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                title: {
                                    display: true,
                                    text: 'Jumlah Faktur per Provinsi (Bar Chart)'
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        precision: 0
                                    }
                                },
                                x: {
                                    ticks: {
                                        autoSkip: false,
                                        maxRotation: 90,
                                        minRotation: 45
                                    }
                                }
                            }
                        }
                    });
                });
            </script>
            </body>

            </html>