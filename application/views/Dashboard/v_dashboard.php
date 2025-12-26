<!-- Page content-->
<div class="container-fluid">
    <div class="row mt-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Faktur Shopee</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="shopee-count">
                                <?php echo number_format($shopee_count); ?>
                            </div>
                            <div class="text-xs text-muted mt-1">
                                <i class="fas fa-calendar-day"></i> Hari ini: <?php echo number_format($shopee_today); ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-store fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Faktur Accurate</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="accurate-count">
                                <?php echo number_format($accurate_count); ?>
                            </div>
                            <div class="text-xs text-muted mt-1">
                                <i class="fas fa-calendar-day"></i> Hari ini: <?php echo number_format($accurate_today); ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calculator fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tambahan card jika diperlukan -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Semua Faktur</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-count">
                                <?php echo number_format($shopee_count + $accurate_count); ?>
                            </div>
                            <div class="text-xs text-muted mt-1">
                                <i class="fas fa-sync-alt"></i> Diperbarui: <span id="last-update"><?php echo date('H:i:s'); ?></span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-bar fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading spinner (tersembunyi) -->
        <div id="loading-spinner" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 1000;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo base_url(); ?>js/scripts.js"></script>

<script>
    $(document).ready(function() {
        // Fungsi untuk refresh data real-time (opsional)
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
                        $('#shopee-count').text(response.shopee_count.toLocaleString());
                        $('#accurate-count').text(response.accurate_count.toLocaleString());
                        $('#total-count').text((response.shopee_count + response.accurate_count).toLocaleString());
                        $('#last-update').text(response.updated_at);
                    }
                },
                complete: function() {
                    $('#loading-spinner').hide();
                },
                error: function() {
                    console.log('Gagal memuat data real-time');
                }
            });
        }

        // Refresh setiap 30 detik (opsional)
        // setInterval(refreshStats, 30000);

        // Tombol refresh manual
        $('#refresh-btn').on('click', function() {
            refreshStats();
        });
    });
</script>
</body>

</html>