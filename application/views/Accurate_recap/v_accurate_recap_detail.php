            <!-- Page content-->
            <div class="container-fluid">
                <div class="row mt-4">
                    <div class="col">
                        <h1>Accurate Pembayaran</h1>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="card shadow-sm p-3 mb-3">
                            <form method="get">
                                <input type="hidden" name="idacc_accurate" value="<?= $this->input->get('idacc_accurate') ?>">
                                <div class="row align-items-end">
                                    <div class="col-md-2">
                                        <label for="persen" class="form-label">Pembayaran dari Total Faktur</label>
                                        <div style="position: relative; display: inline-block;">
                                            <input type="number" step="1" min="0" max="100" class="form-control" name="persen" value="<?= $this->input->get('persen') ?>" style="padding-right: 30px;">
                                            <span style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); color: #6c757d;">%</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="date_from" class="form-label">Dari Tanggal</label>
                                        <input type="date" class="form-control" name="date_from" value="<?= $this->input->get('date_from') ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="date_to" class="form-label">Sampai Tanggal</label>
                                        <input type="date" class="form-control" name="date_to" value="<?= $this->input->get('date_to') ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-primary w-100">Cari</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <table id="tableproduct" class="display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nomer Faktur</th>
                                    <th>Tanggal Pembayaran</th>
                                    <th>Total Faktur</th>
                                    <th>Bayar</th>
                                    <th>Diskon</th>
                                    <th>Pembayaran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $persen = is_numeric($persen_input) ? floatval($persen_input) / 100 : null;

                                foreach ($acc_accurate_detail as $aadkey => $aadvalue) {
                                    $style = '';
                                    if ($persen !== null && $aadvalue->payment < ($persen * $aadvalue->total_faktur)) {
                                        $style = 'style="background-color: #f8d7da;"';
                                    }
                                ?>
                                    <tr <?= $style ?>>
                                        <td><?= $aadkey + 1 ?></td>
                                        <td><?= $aadvalue->no_faktur ?></td>
                                        <td><?= $aadvalue->pay_date ?></td>
                                        <td><?= number_format($aadvalue->total_faktur) ?></td>
                                        <td><?= number_format($aadvalue->pay) ?></td>
                                        <td><?= number_format($aadvalue->discount) ?></td>
                                        <td><?= number_format($aadvalue->payment) ?></td>
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