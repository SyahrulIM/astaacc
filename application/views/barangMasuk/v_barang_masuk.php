            <!-- Page content-->
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <h1 class="mt-4">Daftar Transaksi Barang Masuk</h1>
                    </div>
                </div>
                <!-- Button trigger modal Tambah Produk -->
                <?php if ($this->session->userdata('idrole') != 4) { ?>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProduct">
                    <i class="fa-solid fa-plus"></i> Tambah Transaksi Barang Masuk
                </button>
                <?php } ?>

                <!-- Modal Tambah Produk -->
                <div class="modal fade" id="addProduct" tabindex="-1" aria-labelledby="addProductLabel" aria-hidden="true">
                    <form method="post" action="<?php echo base_url('barangmasuk/stockIn') ?>">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="addProductLabel">Tambah Barang Masuk</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col">
                                            <button type="button" class="btn btn-primary btn-tambah-barang" id="btn-tambah-barang"><i class="fa-solid fa-plus"></i> Tambah Barang</button>
                                        </div>
                                        <div class="col">
                                            <input type="text" class="form-control input-no" id="inputNo" name="inputNo" placeholder="No Manual" required>
                                        </div>
                                        <div class="col">
                                            <select class="form-select" aria-label="Default select example" id="inputGudang" name="inputGudang" required>
                                                <option value="" selected disabled>Pilih Gudang</option>
                                                <?php foreach ($gudang as $gkey => $gvalue) { ?>
                                                <option value="<?php echo $gvalue->idgudang; ?>"><?php echo $gvalue->nama_gudang; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col">
                                            <select class="form-select" aria-label="Default select example" id="inputKategori" name="inputKategori" required>
                                                <option value="" selected disabled>Pilih Kategori</option>
                                                <option value="Barang Masuk">Barang Masuk</option>
                                                <option value="Mutasi">Mutasi</option>
                                            </select>
                                        </div>
                                    </div>
                                    <table id="tableTambahBarangMasuk" class="display" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>SKU</th>
                                                <th>Nama Produk</th>
                                                <th>Jumlah</th>
                                                <th>Keterangan</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>
                                                    <input list="skuList" class="form-control sku-input" name="inputSKU[]" placeholder="Pilih SKU" required>
                                                    <datalist id="skuList">
                                                        <?php foreach ($product as $pkey => $pvalue) { ?>
                                                        <option value="<?php echo $pvalue->sku; ?>" data-nama="<?php echo htmlspecialchars($pvalue->nama_produk); ?>">
                                                            <?php } ?>
                                                    </datalist>
                                                </td>
                                                <td><input type="text" class="form-control input-nama-produk" name="inputNamaProduk[]" readonly></td>
                                                <td><input type="number" class="form-control" name="inputJumlah[]" required></td>
                                                <td><input type="text" class="form-control" name="inputKeterangan[]"></td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- End -->
                <div class="row">
                    <div class="col">
                        <table id="tableproduct" class="display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode InStock</th>
                                    <th>Nomer</th>
                                    <th>Tanggal</th>
                                    <th>User Penginput</th>
                                    <th>Kategori</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($instock as $iskey => $isvalue) { ?>
                                <tr>
                                    <td><?php echo $iskey + 1; ?></td>
                                    <td><?php echo $isvalue->instock_code; ?></td>
                                    <td><?php echo $isvalue->no_manual; ?></td>
                                    <td><?php echo $isvalue->datetime; ?></td>
                                    <td><?php echo $isvalue->user; ?></td>
                                    <td><?php echo $isvalue->kategori; ?></td>
                                    <td><a href="<?php echo base_url('barangmasuk/detail_instock?instock_code=' . $isvalue->instock_code) ?>"><button type="button" class="btn btn-success"><i class="fas fa-list"></i> Details</button></a></td>
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

                $(document).ready(function() {
                    // Tambah baris
                    $('#btn-tambah-barang').click(function() {
                        let rowCount = $('#tableTambahBarangMasuk tbody tr').length + 1;
                        let newRow = `
                <tr>
                    <td>${rowCount}</td>
                    <td>
                        <input list="skuList" class="form-control sku-input" name="inputSKU[]" placeholder="Pilih SKU" required>
                    </td>
                    <td><input type="text" class="form-control input-nama-produk" name="inputNamaProduk[]" readonly></td>
                    <td><input type="number" class="form-control" name="inputJumlah[]" required></td>
                    <td><input type="text" class="form-control" name="inputKeterangan[]"></td>
                    <td><button type="button" class="btn btn-danger btn-hapus-barang form-control">
                        <i class="fa-solid fa-trash-can"></i></button>
                    </td>
                </tr>
            `;
                        $('#tableTambahBarangMasuk tbody').append(newRow);
                    });

                    // Hapus baris
                    $('#tableTambahBarangMasuk').on('click', '.btn-hapus-barang', function() {
                        $(this).closest('tr').remove();

                        // Re-number ulang kolom "No"
                        $('#tableTambahBarangMasuk tbody tr').each(function(index) {
                            $(this).find('td:first').text(index + 1);
                        });
                    });
                });

                $(document).ready(function() {
                    const skuData = {
                        <?php foreach ($product as $pkey => $pvalue) { ?> "<?php echo $pvalue->sku; ?>": "<?php echo htmlspecialchars($pvalue->nama_produk); ?>",
                        <?php } ?>
                    };

                    // Live handler for both existing and newly added .sku-input
                    $('#tableTambahBarangMasuk').on('input', '.sku-input', function() {
                        const val = $(this).val();
                        const namaProduk = skuData[val] || '';
                        $(this).closest('tr').find('.input-nama-produk').val(namaProduk);
                    });
                });

                $(document).ready(function() {
                    $('#addProduct form').on('keydown', function(e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            return false;
                        }
                    });
                });

                $(document).ready(function() {
                    $('#tableTambahBarangMasuk').on('input', '.sku-input', function() {
                        const currentVal = $(this).val();
                        let duplicate = false;

                        $('.sku-input').each(function() {
                            if ($(this).val() === currentVal && $(this)[0] !== event.target) {
                                duplicate = true;
                                return false; // break loop
                            }
                        });

                        if (duplicate) {
                            alert('SKU tersebut sudah dipilih di baris lain. Silakan pilih SKU lain.');
                            $(this).val(''); // clear the input
                            $(this).addClass('is-invalid');
                        } else {
                            $(this).removeClass('is-invalid');
                        }

                        // auto-fill nama produk if available
                        const namaProduk = skuData[currentVal] || '';
                        $(this).closest('tr').find('.input-nama-produk').val(namaProduk);
                    });
                });
            </script>
            </body>

            </html>