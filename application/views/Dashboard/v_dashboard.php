<!-- Page content-->
<div class="container-fluid">
    <div class="row mt-4">
        <!-- Shopee Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                <i class="fas fa-store me-1"></i> Total Faktur Shopee
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="shopee-count">
                                <?php echo number_format($shopee_count); ?>
                            </div>
                            <div class="text-xs text-muted mt-1">
                                <i class="fas fa-calendar-day me-1"></i> Hari ini: <span id="shopee-today"><?php echo number_format($shopee_today); ?></span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fab fa-shopify fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Accurate Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                <i class="fas fa-calculator me-1"></i> Total Faktur Accurate
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="accurate-count">
                                <?php echo number_format($accurate_count); ?>
                            </div>
                            <div class="text-xs text-muted mt-1">
                                <i class="fas fa-calendar-day me-1"></i> Hari ini: <span id="accurate-today"><?php echo number_format($accurate_today); ?></span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-invoice-dollar fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TikTok Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                <i class="fab fa-tiktok me-1"></i> Total Faktur TikTok
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="tiktok-count">
                                <?php echo isset($tiktok_count) ? number_format($tiktok_count) : '0'; ?>
                            </div>
                            <div class="text-xs text-muted mt-1">
                                <i class="fas fa-calendar-day me-1"></i> Hari ini: <span id="tiktok-today"><?php echo isset($tiktok_today) ? number_format($tiktok_today) : '0'; ?></span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fab fa-tiktok fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lazada Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                <i class="fas fa-shopping-bag me-1"></i> Total Faktur Lazada
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="lazada-count">
                                <?php echo isset($lazada_count) ? number_format($lazada_count) : '0'; ?>
                            </div>
                            <div class="text-xs text-muted mt-1">
                                <i class="fas fa-calendar-day me-1"></i> Hari ini: <span id="lazada-today"><?php echo isset($lazada_today) ? number_format($lazada_today) : '0'; ?></span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fab fa-laravel fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row: Additional Stats -->
    <div class="row">
        <!-- Total All Invoices -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                <i class="fas fa-chart-bar me-1"></i> Total Semua Faktur
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-count">
                                <?php
                                $total_all = $shopee_count + $accurate_count;
                                if (isset($tiktok_count)) $total_all += $tiktok_count;
                                if (isset($lazada_count)) $total_all += $lazada_count;
                                echo number_format($total_all);
                                ?>
                            </div>
                            <div class="text-xs text-muted mt-1">
                                <i class="fas fa-calendar-day me-1"></i> Hari ini:
                                <span id="total-today">
                                    <?php
                                    $total_today = $shopee_today + $accurate_today;
                                    if (isset($tiktok_today)) $total_today += $tiktok_today;
                                    if (isset($lazada_today)) $total_today += $lazada_today;
                                    echo number_format($total_today);
                                    ?>
                                </span>
                            </div>
                            <div class="text-xs text-muted mt-1">
                                <i class="fas fa-sync-alt me-1"></i> Diperbarui: <span id="last-update"><?php echo date('H:i:s'); ?></span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-invoice fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Summary -->
        <div class="col-xl-9 col-md-12 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie me-2"></i>Statistik Platform
                    </h6>
                    <button class="btn btn-sm btn-primary" id="refresh-btn">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless">
                                    <thead>
                                        <tr>
                                            <th>Platform</th>
                                            <th class="text-end">Total</th>
                                            <th class="text-end">Hari Ini</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><i class="fab fa-shopify text-primary me-2"></i>Shopee</td>
                                            <td class="text-end" id="stat-shopee"><?php echo number_format($shopee_count); ?></td>
                                            <td class="text-end" id="stat-shopee-today"><?php echo number_format($shopee_today); ?></td>
                                        </tr>
                                        <tr>
                                            <td><i class="fas fa-calculator text-success me-2"></i>Accurate</td>
                                            <td class="text-end" id="stat-accurate"><?php echo number_format($accurate_count); ?></td>
                                            <td class="text-end" id="stat-accurate-today"><?php echo number_format($accurate_today); ?></td>
                                        </tr>
                                        <?php if (isset($tiktok_count)) : ?>
                                        <tr>
                                            <td><i class="fab fa-tiktok text-danger me-2"></i>TikTok</td>
                                            <td class="text-end" id="stat-tiktok"><?php echo number_format($tiktok_count); ?></td>
                                            <td class="text-end" id="stat-tiktok-today"><?php echo number_format($tiktok_today); ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        <?php if (isset($lazada_count)) : ?>
                                        <tr>
                                            <td><i class="fab fa-laravel text-warning me-2"></i>Lazada</td>
                                            <td class="text-end" id="stat-lazada"><?php echo number_format($lazada_count); ?></td>
                                            <td class="text-end" id="stat-lazada-today"><?php echo number_format($lazada_today); ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        <tr class="table-active">
                                            <td><strong><i class="fas fa-total text-info me-2"></i>Total</strong></td>
                                            <td class="text-end"><strong id="stat-total"><?php echo number_format($total_all); ?></strong></td>
                                            <td class="text-end"><strong id="stat-total-today"><?php echo number_format($total_today); ?></strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="chart-container">
                                <canvas id="platformChart" height="180"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading spinner -->
<div id="loading-spinner" class="position-fixed top-50 start-50 translate-middle" style="display: none; z-index: 1000;">
    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo base_url(); ?>js/scripts.js"></script>

<script>
    $(document).ready(function() {
        // Initialize Chart
        let platformChart;
        initializeChart();

        // Refresh button click
        $('#refresh-btn').on('click', function() {
            refreshStats();
            $(this).find('i').addClass('fa-spin');
            setTimeout(() => {
                $(this).find('i').removeClass('fa-spin');
            }, 1000);
        });

        // Auto-refresh every 60 seconds
        setInterval(refreshStats, 60000);

        // Initialize chart function
        function initializeChart() {
            const ctx = document.getElementById('platformChart').getContext('2d');

            // Prepare data
            const labels = ['Shopee', 'Accurate', 'TikTok', 'Lazada'];
            const data = [
                <?php echo $shopee_count; ?>,
                <?php echo $accurate_count; ?>,
                <?php echo isset($tiktok_count) ? $tiktok_count : 0; ?>,
                <?php echo isset($lazada_count) ? $lazada_count : 0; ?>
            ];
            const backgroundColors = [
                'rgba(54, 162, 235, 0.7)', // Shopee - blue
                'rgba(75, 192, 192, 0.7)', // Accurate - teal
                'rgba(255, 99, 132, 0.7)', // TikTok - red
                'rgba(255, 205, 86, 0.7)' // Lazada - yellow
            ];

            platformChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: backgroundColors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((value / total) * 100);
                                    return `${label}: ${value.toLocaleString()} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Refresh stats function
        function refreshStats() {
            $.ajax({
                url: '<?php echo base_url("dashboard/get_real_time_stats"); ?>',
                type: 'GET',
                dataType: 'json',
                beforeSend: function() {
                    $('#loading-spinner').show();
                },
                success: function(response) {
                    if (response.success) {
                        // Update card values
                        $('#shopee-count').text(response.shopee_count.toLocaleString());
                        $('#shopee-today').text(response.shopee_today.toLocaleString());
                        $('#accurate-count').text(response.accurate_count.toLocaleString());
                        $('#accurate-today').text(response.accurate_today.toLocaleString());

                        // Update TikTok if exists
                        if (response.tiktok_count !== undefined) {
                            $('#tiktok-count').text(response.tiktok_count.toLocaleString());
                            $('#tiktok-today').text(response.tiktok_today.toLocaleString());
                        }

                        // Update Lazada if exists
                        if (response.lazada_count !== undefined) {
                            $('#lazada-count').text(response.lazada_count.toLocaleString());
                            $('#lazada-today').text(response.lazada_today.toLocaleString());
                        }

                        // Calculate total
                        let totalCount = response.shopee_count + response.accurate_count;
                        let totalToday = response.shopee_today + response.accurate_today;

                        if (response.tiktok_count !== undefined) {
                            totalCount += response.tiktok_count;
                            totalToday += response.tiktok_today;
                        }

                        if (response.lazada_count !== undefined) {
                            totalCount += response.lazada_count;
                            totalToday += response.lazada_today;
                        }

                        $('#total-count').text(totalCount.toLocaleString());
                        $('#total-today').text(totalToday.toLocaleString());

                        // Update statistics table
                        $('#stat-shopee').text(response.shopee_count.toLocaleString());
                        $('#stat-shopee-today').text(response.shopee_today.toLocaleString());
                        $('#stat-accurate').text(response.accurate_count.toLocaleString());
                        $('#stat-accurate-today').text(response.accurate_today.toLocaleString());

                        if (response.tiktok_count !== undefined) {
                            $('#stat-tiktok').text(response.tiktok_count.toLocaleString());
                            $('#stat-tiktok-today').text(response.tiktok_today.toLocaleString());
                        }

                        if (response.lazada_count !== undefined) {
                            $('#stat-lazada').text(response.lazada_count.toLocaleString());
                            $('#stat-lazada-today').text(response.lazada_today.toLocaleString());
                        }

                        $('#stat-total').text(totalCount.toLocaleString());
                        $('#stat-total-today').text(totalToday.toLocaleString());

                        // Update chart
                        updateChart(response);

                        // Update timestamp
                        $('#last-update').text(response.updated_at);
                    }
                },
                complete: function() {
                    $('#loading-spinner').hide();
                },
                error: function(xhr, status, error) {
                    console.log('Gagal memuat data real-time:', error);
                    showNotification('error', 'Gagal memperbarui data');
                }
            });
        }

        // Update chart function
        function updateChart(response) {
            if (platformChart) {
                platformChart.data.datasets[0].data = [
                    response.shopee_count,
                    response.accurate_count,
                    response.tiktok_count || 0,
                    response.lazada_count || 0
                ];
                platformChart.update();
            }
        }

        // Notification function
        function showNotification(type, message) {
            const notification = $(`
                <div class="alert alert-${type} alert-dismissible fade show position-fixed" 
                     style="top: 20px; right: 20px; z-index: 1050;">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `);
            $('body').append(notification);
            setTimeout(() => notification.alert('close'), 3000);
        }
    });
</script>
</body>

</html>