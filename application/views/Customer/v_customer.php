            <!-- Page content-->
            <div class="container-fluid">
                <div class="row mt-4">
                    <div class="col">
                        <h1>Database Pelanggan</h1>
                    </div>
                </div>
                <!-- Button trigger modal Tambah Produk -->
                <?php if ($this->session->userdata('idrole') != 4 && $this->session->userdata('idrole') != 2) { ?>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCustomer">
                        <i class="fa-solid fa-plus"></i> Tambah Pelanggan
                    </button>
                <?php } ?>

                <?php if ($this->session->flashdata('error')) : ?>
                    <div class="alert alert-danger"><?= $this->session->flashdata('error') ?></div>
                <?php endif; ?>
                <?php if ($this->session->flashdata('success')) : ?>
                    <div class="alert alert-success"><?= $this->session->flashdata('success') ?></div>
                <?php endif; ?>

                <!-- Modal Tambah Produk -->
                <div class="modal fade" id="addCustomer" tabindex="-1" aria-labelledby="addCustomerLabel" aria-hidden="true">
                    <form method="post" action="<?php echo base_url('product/addCustomer') ?>" enctype="multipart/form-data">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="addCustomerLabel">Tambah Pelanggan</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="inputNama" class="form-label">Nama Pelanggan</label>
                                        <input type="text" class="form-control" id="inputNama" name="inputNama" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="inputEmail" class="form-label">Email</label>
                                        <input type="text" class="form-control" id="inputEmail" name="inputEmail" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="inputHandphone" class="form-label">Nomer Handphone</label>
                                        <input type="text" class="form-control" id="inputHandphone" name="inputHandphone" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="inputFoto">Foto</label>
                                        <input type="file" class="form-control" id="inputFoto" name="inputFoto" accept="image/*">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- End -->
                <div class="row">
                    <div class="col">
                        <table id="tablecustomer" class="display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Pelanggan</th>
                                    <th>Email</th>
                                    <th>Nomer Handphone</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($customer as $ckey => $cvalue){?>
                                <tr>
                                        <td><?php echo $ckey + 1;?></td>
                                        <td><?php echo $cvalue->name_customer;?></td>
                                        <td><?php echo $cvalue->email;?></td>
                                        <td><?php echo $cvalue->handphone;?></td>
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
            <script>
                $(document).ready(function() {
                    new DataTable('#tablecustomer', {
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