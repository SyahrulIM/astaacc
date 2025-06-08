            <!-- Page content-->
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <h1 class="mt-4">Realisasi Pengiriman</h1>
                    </div>
                </div>
                <?php if ($this->session->userdata('idrole') != 4) { ?>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDeliver">
                        <i class="fa-solid fa-plus"></i> Tambah Realisasi Pengiriman
                    </button>
                <?php } ?>

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

                <!-- Modal Tambah Delivery -->
                <div class="modal fade" id="addDeliver" tabindex="-1" aria-labelledby="addDeliverLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <form method="post" action="<?php echo base_url('Delivery_note/createDelivery') ?>">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="addDeliverLabel">Tambah Realisasi Pengiriman</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="inputNo" class="form-label">No Surat Jalan</label>
                                        <input type="text" class="form-control input-no" id="inputNo" name="inputNo">
                                    </div>
                                    <div class="mb-3">
                                        <label for="inputReceived" class="form-label">Nama Penerima</label>
                                        <select class="form-control input-received" name="inputReceived" id="inputReceived">
                                            <option selected>Pilih Penerima</option>
                                            <?php foreach ($admin as $akey => $avalue) { ?>
                                                <option value="<?php echo $avalue->iduser; ?>"><?php echo $avalue->full_name; ?> | <?php echo $avalue->nama_role; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="inputDate" class="form-label">Tanggal Kirim</label>
                                        <input class="form-control input-date" type="datetime-local" id="inputDate" name="inputDate">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- End -->
                <div class="row">
                    <div class="col">
                        <table id="tableproduct" class="display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nomer Surat Jalan</th>
                                    <th>Nama Penerima</th>
                                    <th>Tanggal Kirim</th>
                                    <th>Penginput</th>
                                    <th>Tanggal Input</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($delivery as $dkey => $dvalue) { ?>
                                    <tr>
                                        <td><?php echo $dkey + 1;?></td>
                                        <td><?php echo $dvalue->no_manual;?></td>
                                        <td><?php echo $dvalue->user_received;?></td>
                                        <td><?php echo $dvalue->send_date;?></td>
                                        <td><?php echo $dvalue->user_input;?></td>
                                        <td><?php echo $dvalue->created_date;?></td>
                                        <td><button type="button" class="btn btn-success">Detail</button></td>
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
                });
            </script>
            </body>

            </html>