<!-- Page content-->
<div class="container-fluid">
    <div class="row">
        <div class="col">
            <h1 class="mt-4">Bottom Price</h1>
        </div>
    </div>

    <!-- Import Excel -->
    <form action="<?= base_url('shopee_bottom/createBottom') ?>" method="post" enctype="multipart/form-data">
        <div class="card mt-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Import Bottom Price (xlsx)</strong>
                <a href="<?= base_url('assets/template_excel/harga bottom online.xlsx') ?>" class="btn btn-sm btn-success" download>
                    Download Template Bottom
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

    <!-- Modal Tambah Bottom Price -->
    <div class="modal fade" id="addBottom" tabindex="-1" aria-labelledby="addBottomLabel" aria-hidden="true">
        <form id="formAddBottom" method="post">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5">Tambah Bottom Price</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="alertMessage"></div>
                        <div class="mb-3">
                            <label for="sku" class="form-label">SKU</label>
                            <input type="text" class="form-control" id="sku" name="sku" required>
                        </div>
                        <div class="mb-3">
                            <label for="bottom" class="form-label">Bottom Price</label>
                            <input type="number" class="form-control" id="bottom" name="bottom" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <!-- End -->

    <div class="row mt-4">
        <div class="col">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBottom">
                <i class="fa-solid fa-plus"></i> Tambah Bottom Price
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <table id="tableproduct" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>SKU</th>
                        <th>Price Bottom</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($acc_shopee_bottom as $asbkey => $asbvalue) { ?>
                        <tr>
                            <td><?= $asbkey + 1; ?></td>
                            <td><?= $asbvalue->sku; ?></td>
                            <td><?= number_format($asbvalue->price_bottom); ?></td>
                        </tr>
                    <?php } ?>
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

        // Handle form submission for adding bottom price
        $('#formAddBottom').on('submit', function(e) {
            e.preventDefault();

            // Get form data
            var formData = {
                sku: $('#sku').val(),
                bottom: $('#bottom').val()
            };

            // Send AJAX request
            $.ajax({
                url: '<?php echo base_url("shopee_bottom/addBottom") ?>',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        // Show success message
                        $('#alertMessage').html('<div class="alert alert-success">' + response.message + '</div>');

                        // Clear form fields
                        $('#sku').val('');
                        $('#bottom').val('');

                        // Reload page after 1.5 seconds to show updated data
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        // Show error message
                        $('#alertMessage').html('<div class="alert alert-danger">' + response.message + '</div>');
                    }
                },
                error: function() {
                    $('#alertMessage').html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
                }
            });
        });

        // Clear alert message when modal is closed
        $('#addBottom').on('hidden.bs.modal', function() {
            $('#alertMessage').html('');
            $('#sku').val('');
            $('#bottom').val('');
        });
    });
</script>
</body>

</html>