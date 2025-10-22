            <!-- Page content-->
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <h1 class="mt-4">Recap Pembayaran</h1>
                    </div>
                </div>
                <!-- Import Excel -->
                <form action="<?= base_url('recap/createRecap') ?>" method="post" enctype="multipart/form-data">
                    <div class="card mt-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <strong>Import Recap (xlsx)</strong>
                            <div class="d-flex flex-wrap gap-2">
                                <strong>
                                    Template:
                                </strong>
                                <a href="<?= base_url('assets/template_excel/accurate/pembayaran_faktur_suryajayamakmur_250828155006.xlsx') ?>" class="btn btn-sm btn-success" download>
                                    <i class="fas fa-file-excel"></i> Income (Accurate)
                                </a>
                                <a href="<?= base_url('assets/template_excel/shopee/Income.sudah dilepas.id.20250801_20250822.xlsx') ?>" class="btn btn-sm btn-success" download>
                                    <i class="fas fa-file-excel"></i> Income (Shopee)
                                </a>
                                <a href="<?= base_url('assets/template_excel/shopee/Order.completed.20250801_20250820.xlsx') ?>" class="btn btn-sm btn-info" download>
                                    <i class="fas fa-file-excel"></i> Order (Shopee)
                                </a>
                                <a href="<?= base_url('assets/template_excel/tiktok/income_20250823043937.xlsx') ?>" class="btn btn-sm btn-success" download>
                                    <i class="fas fa-file-excel"></i> Income (Tiktok)
                                </a>
                                <a href="<?= base_url('assets/template_excel/tiktok/Selesai pesanan-2025-08-20-14_38.xlsx') ?>" class="btn btn-sm btn-info" download>
                                    <i class="fas fa-file-excel"></i> Selesai (Tiktok)
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="marketplace" class="form-label">Marketplace</label>
                                <select name="marketplace" id="marketplace" class="form-select">
                                    <option value="" selected disabled>Pilih Marketplace</option>
                                    <option value="shopee">Shopee</option>
                                    <option value="tiktok">Tiktok / Tokopedia</option>
                                    <option value="accurate">Accurate</option>
                                </select>
                            </div>

                            <div class="mb-3" id="typeExcelContainer">
                                <label for="typeExcel" class="form-label">Type Excel</label>
                                <select name="typeExcel" id="typeExcel" class="form-select">
                                    <!-- Options will be populated by JavaScript -->
                                </select>
                            </div>

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
                <div class="row">
                    <div class="col">
                        <!-- Tabs Navigation -->
                        <ul class="nav nav-tabs mt-4" id="listTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="list-tab" data-bs-toggle="tab" data-bs-target="#list" type="button" role="tab">List</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab">All</button>
                            </li>
                        </ul>

                        <!-- Tabs Content -->
                        <div class="tab-content" id="listTabsContent">
                            <!-- Tab 1: list -->
                            <div class="tab-pane fade show active" id="list" role="tabpanel" aria-labelledby="list-tab">
                                <div class="row mt-3">
                                    <div class="col">
                                        <table id="tableproduct" class="display" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>User Penginput</th>
                                                    <th>Marketplace</th>
                                                    <th>Type Excel</th>
                                                    <th>Tanggal Import</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                foreach ($acc_recap as $arkey => $arvalue) {
                                                    // Determine row style based on source
                                                    $row_style = '';
                                                    if (strtolower($arvalue->source) === 'shopee') {
                                                        $row_style = 'style="background-color: #EE4D2D; color: white;"'; // Orange Shopee
                                                    } elseif (strtolower($arvalue->source) === 'tiktok') {
                                                        $row_style = 'style="background-color: #5da96a; color: white;"'; // Green TikTok
                                                    } else {
                                                        $row_style = '';
                                                    }
                                                    ?>
                                                <tr <?= $row_style ?>>
                                                    <td><?= $arkey + 1 ?></td>
                                                    <td><?= $arvalue->full_name ?></td>
                                                    <td>
                                                        <?php if ($arvalue->source == 'tiktok') { ?>
                                                        <img src="https://cdn.brandfetch.io/idoruRsDhk/theme/dark/symbol.svg?c=1bxid64Mup7aczewSAYMX&t=1668515567929" alt="Tiktok Logo" style="height:20px; vertical-align:middle; margin-right:5px;">
                                                        Tiktok / Tokopedia
                                                        <?php } else if ($arvalue->source == 'shopee') { ?>
                                                        <img src="https://cdn.brandfetch.io/idgVhUUiaD/w/500/h/500/theme/dark/icon.jpeg?c=1bxid64Mup7aczewSAYMX&t=1750904105236" alt="Shopee Logo" style="height:20px; vertical-align:middle; margin-right:5px;">
                                                        Shopee
                                                        <?php } else { ?>
                                                        <img src="https://penjualanonline.id/wp-content/uploads/2022/01/Logo-Accurate-Cloud.png" alt="Shopee Logo" style="height:20px; vertical-align:middle; margin-right:5px;">
                                                        Accurate
                                                        <?php } ?>
                                                    </td>
                                                    <?php if ($arvalue->type == 'income') { ?>
                                                    <td>Income</td>
                                                    <?php } else if ($arvalue->type == 'order') { ?>
                                                    <td>Order</td>
                                                    <?php } else { ?>
                                                    <td>Selesai</td>
                                                    <?php } ?>
                                                    <td><?= $arvalue->created_date ?></td>
                                                    <td>
                                                        <a href="<?php echo base_url('recap/detail_payment?idacc_recap=' . $arvalue->id_data . '&marketplace=' . $arvalue->source) ?>">
                                                            <button type="button" class="btn btn-success"><i class="fas fa-list"></i> Details</button>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab 2: all (example filter - you can adjust based on backend or JS) -->
                            <div class="tab-pane fade" id="all" role="tabpanel" aria-labelledby="all-tab">
                                <div class="row mt-3">
                                    <div class="col">
                                        <table id="tableall" class="display" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nomer Faktur</th>
                                                    <th>Marketplace</th>
                                                    <th>Tanggal Pesanan</th>
                                                    <th>Tanggal Pembayaran</th>
                                                    <th>Total Faktur</th>
                                                    <th>Bayar</th>
                                                    <th>Diskon</th>
                                                    <th>Refund</th>
                                                    <th>Pembayaran</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($acc_recap_detail as $ardkey => $ardvalue) {
                                                    // Tentukan warna background per baris
                                                    $row_style = '';
                                                    if (strtolower($ardvalue->source) === 'shopee') {
                                                        $row_style = 'style="background-color: #EE4D2D; color: white;"'; // Orange Shopee
                                                    } elseif (strtolower($ardvalue->source) === 'tiktok') {
                                                        $row_style = 'style="background-color: #5da96a; color: white;"'; // Green TikTok
                                                    } else {
                                                        $row_style = '';
                                                    }
                                                    ?>
                                                <tr <?= $row_style ?>>
                                                    <td><?= $ardkey + 1 ?></td>
                                                    <td><?= $ardvalue->no_faktur ?></td>
                                                    <td>
                                                        <?php if ($ardvalue->source == 'tiktok') { ?>
                                                        <img src="https://cdn.brandfetch.io/idoruRsDhk/theme/dark/symbol.svg?c=1bxid64Mup7aczewSAYMX&t=1668515567929" alt="Tiktok Logo" style="height:20px; vertical-align:middle; margin-right:5px;">
                                                        Tiktok / Tokopedia
                                                        <?php } else if ($ardvalue->source == 'shopee') { ?>
                                                        <img src="https://cdn.brandfetch.io/idgVhUUiaD/w/500/h/500/theme/dark/icon.jpeg?c=1bxid64Mup7aczewSAYMX&t=1750904105236" alt="Shopee Logo" style="height:20px; vertical-align:middle; margin-right:5px;">
                                                        Shopee
                                                        <?php } else { ?>
                                                        <img src="https://penjualanonline.id/wp-content/uploads/2022/01/Logo-Accurate-Cloud.png" alt="Shopee Logo" style="height:20px; vertical-align:middle; margin-right:5px;">
                                                        Accurate
                                                        <?php } ?>
                                                    </td>
                                                    <td><?= $ardvalue->order_date ?></td>
                                                    <td><?= $ardvalue->pay_date ?></td>
                                                    <td><?= number_format($ardvalue->total_faktur) ?></td>
                                                    <td><?= number_format($ardvalue->pay) ?></td>
                                                    <td><?= number_format($ardvalue->discount) ?></td>
                                                    <td><?= number_format($ardvalue->refund) ?></td>
                                                    <td><?= number_format($ardvalue->payment) ?></td>
                                                    <td>
                                                        <?php if ($ardvalue->source === 'tiktok' || $ardvalue->source === 'shopee') { ?>
                                                        <a href="<?= base_url('recap/detail_faktur?no_faktur=' . $ardvalue->no_faktur . '&marketplace=' . $ardvalue->source) ?> ">
                                                            <button type="button" class="btn btn-success"><i class="fas fa-list"></i> Details</button>
                                                        </a>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                        responsive: false,
                        scrollX: true,
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
                    new DataTable('#tableall', {
                        responsive: false,
                        scrollX: true,
                        layout: {
                            bottomEnd: {
                                paging: {
                                    firstLast: false
                                }
                            }
                        }
                    });
                });

                document.addEventListener('DOMContentLoaded', function() {
                    const marketplaceSelect = document.getElementById('marketplace');
                    const typeExcelContainer = document.getElementById('typeExcelContainer');
                    const typeExcelSelect = document.getElementById('typeExcel');

                    // Define the options for each marketplace
                    const typeExcelOptions = {
                        shopee: [{
                                value: 'income',
                                text: 'Income'
                            },
                            {
                                value: 'order',
                                text: 'Order'
                            }
                        ],
                        tiktok: [{
                                value: 'income',
                                text: 'Income'
                            },
                            {
                                value: 'selesai',
                                text: 'Selesai'
                            }
                        ],
                        accurate: [] // No options for Accurate
                    };

                    // Function to update Type Excel dropdown
                    function updateTypeExcelOptions() {
                        const selectedMarketplace = marketplaceSelect.value;

                        // Clear existing options
                        typeExcelSelect.innerHTML = '';

                        // If no marketplace selected or Accurate is selected
                        if (!selectedMarketplace || selectedMarketplace === 'accurate') {
                            typeExcelContainer.style.display = 'none';
                            typeExcelSelect.removeAttribute('required');

                            // Add a disabled default option when hidden
                            const defaultOption = document.createElement('option');
                            defaultOption.value = '';
                            defaultOption.textContent = 'Pilih Marketplace terlebih dahulu';
                            defaultOption.disabled = true;
                            defaultOption.selected = true;
                            typeExcelSelect.appendChild(defaultOption);
                        } else {
                            typeExcelContainer.style.display = 'block';
                            typeExcelSelect.setAttribute('required', 'required');

                            // Add a disabled default option
                            const defaultOption = document.createElement('option');
                            defaultOption.value = '';
                            defaultOption.textContent = 'Pilih Type Excel';
                            defaultOption.disabled = true;
                            defaultOption.selected = true;
                            typeExcelSelect.appendChild(defaultOption);

                            // Add marketplace-specific options
                            const options = typeExcelOptions[selectedMarketplace];
                            options.forEach(option => {
                                const optionElement = document.createElement('option');
                                optionElement.value = option.value;
                                optionElement.textContent = option.text;
                                typeExcelSelect.appendChild(optionElement);
                            });
                        }
                    }

                    // Initial setup - hide typeExcel by default
                    typeExcelContainer.style.display = 'none';
                    updateTypeExcelOptions();

                    // Update when marketplace changes
                    marketplaceSelect.addEventListener('change', updateTypeExcelOptions);
                });
            </script>
            </body>

            </html>