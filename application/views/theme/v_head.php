<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Asta ACOL - <?php echo $title; ?></title>
    <!-- Meta Tag -->
    <meta name="description" content="Dashboard Accounting Online Asta Homeware.">
    <meta name="keywords" content="Asta, Accounting, Dashboard, Finance">
    <meta name="robots" content="index, follow">
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="<?php echo base_url(); ?>css/styles.css" rel="stylesheet" />
    <!-- DataTable CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/rowreorder/1.5.0/css/rowReorder.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.4/css/responsive.dataTables.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Font Libre Barcode -->
    <link href='https://fonts.googleapis.com/css?family=Libre Barcode 39' rel='stylesheet'>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo base_url('assets/image/favicon.ico'); ?>">
    <!-- Barcode  -->
    <script src="https://unpkg.com/bwip-js/dist/bwip-js-min.js"></script>

    <style>
        /* Warna tema biru dongker */
        :root {
            --primary-dark: #21488a;
            --primary-blue: #21488a;
            --primary-light: #0D6EFD;
            --accent-blue: #0D6EFD;
            --light-bg: #FFFFFF;
        }

        /* Sidebar - Biru Dongker */
        #sidebar-wrapper {
            background-color: var(--primary-dark) !important;
            border-color: var(--primary-blue) !important;
        }

        .sidebar-heading {
            background-color: var(--primary-blue) !important;
            color: white !important;
            border-bottom: 1px solid var(--primary-light) !important;
            font-weight: 600;
            font-size: 1.2rem;
        }

        /* Menu items - SEMUA TEKS PUTIH */
        .list-group-item {
            background-color: var(--primary-dark) !important;
            color: white !important;
            border-color: var(--primary-blue) !important;
            transition: all 0.3s ease;
        }

        .list-group-item:hover {
            background-color: var(--primary-blue) !important;
            color: white !important;
        }

        .list-group-item.active {
            background-color: var(--accent-blue) !important;
            color: white !important;
            border-color: var(--accent-blue) !important;
            font-weight: 500;
        }

        /* Submenu items - SEMUA TEKS PUTIH */
        .list-group-item-light {
            background-color: var(--primary-blue) !important;
            color: white !important;
        }

        .list-group-item-light:hover {
            background-color: var(--primary-light) !important;
            color: white !important;
        }

        /* Icons di sidebar - SEMUA PUTIH */
        #sidebar-wrapper .list-group-item i {
            color: white !important;
            margin-right: 10px;
        }

        #sidebar-wrapper .list-group-item.active i,
        #sidebar-wrapper .list-group-item:hover i {
            color: white !important;
        }

        /* Icon chevron dropdown */
        #sidebar-wrapper .fas.fa-chevron-down {
            color: white !important;
        }

        /* Top Navbar - Putih */
        .navbar.bg-light {
            background-color: white !important;
            border-bottom: 2px solid var(--accent-blue) !important;
        }

        /* User dropdown di navbar atas */
        .nav-link.dropdown-toggle {
            color: var(--primary-dark) !important;
            font-weight: 500;
        }

        .nav-link.dropdown-toggle:hover {
            color: var(--primary-blue) !important;
        }

        /* Dropdown menu */
        .dropdown-menu {
            border: 1px solid var(--accent-blue);
            box-shadow: 0 4px 12px rgba(13, 27, 42, 0.1);
        }

        .dropdown-item {
            color: var(--primary-dark);
        }

        .dropdown-item:hover {
            background-color: #f0f4f8;
            color: var(--primary-blue);
        }

        /* Button sidebar toggle */
        #sidebarToggle {
            background-color: var(--primary-blue);
            border-color: var(--primary-blue);
            color: white;
        }

        #sidebarToggle:hover {
            background-color: var(--primary-light);
            border-color: var(--primary-light);
        }

        /* Modal styling */
        .modal-header {
            background-color: var(--primary-blue);
            color: white;
        }

        .btn-primary {
            background-color: var(--primary-blue);
            border-color: var(--primary-blue);
        }

        .btn-primary:hover {
            background-color: var(--primary-light);
            border-color: var(--primary-light);
        }

        /* Logo styling */
        .sidebar-heading img {
            filter: brightness(0) invert(1);
            margin-right: 8px;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            #sidebar-wrapper {
                box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            }
        }

        /* Pastikan semua teks di sidebar putih */
        #sidebar-wrapper * {
            color: white !important;
        }

        /* Kecuali untuk submenu yang sudah punya class sendiri */
        #sidebar-wrapper .list-group-item-light {
            color: white !important;
        }
    </style>
</head>

<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar-->
        <div class="border-end" id="sidebar-wrapper">
            <div class="sidebar-heading border-bottom text-center">
                <img src="<?php echo base_url('assets/image/logo A asta biru.png') ?>" alt="Asta Logo" width="20px"> Accounting Online
            </div>
            <?php
            $current = $this->uri->segment(1);
            ?>
            <div class="list-group list-group-flush">
                <a class="list-group-item list-group-item-action p-3 <?= ($current == 'dashboard') ? 'active' : ''; ?>" href="<?php echo base_url('dashboard/'); ?>"><i class="fa-solid fa-gauge"></i> Dashboard</a>
                <div class="list-group-item p-0">
                    <a class="list-group-item list-group-item-action p-3 d-flex justify-content-between align-items-center <?= in_array($current, ['recap', 'shopee_bottom', 'additional']) ? 'active' : ''; ?>" data-bs-toggle="collapse" href="#recapSubmenu" role="button" aria-expanded="<?= in_array($current, ['recap', 'shopee_bottom', 'additional']) ? 'true' : 'false'; ?>" aria-controls="recapSubmenu">
                        <i class="fa-solid fa-database"></i> Data
                        <i class="fas fa-chevron-down small"></i>
                    </a>
                    <div class="collapse <?= in_array($current, ['recap', 'shopee_bottom', 'additional']) ? 'show' : ''; ?>" id="recapSubmenu">
                        <a class="list-group-item list-group-item-action ps-5 <?= ($current == 'recap') ? 'active' : ''; ?>" href="<?= base_url('recap'); ?>"><i class="fa-solid fa-file-import"></i> Import Payment</a>
                        <a class="list-group-item list-group-item-action ps-5 <?= ($current == 'shopee_bottom') ? 'active' : ''; ?>" href="<?= base_url('shopee_bottom'); ?>"><i class="fa-solid fa-tag"></i> Import Bottom Price</a>
                        <a class="list-group-item list-group-item-action ps-5 <?= ($current == 'additional') ? 'active' : ''; ?>" href="<?= base_url('additional'); ?>"><i class="fa-solid fa-plus-circle"></i> Additional Revenue</a>
                    </div>
                </div>
                <div class="list-group-item p-0">
                    <a class="list-group-item list-group-item-action p-3 d-flex justify-content-between align-items-center <?= in_array($current, ['comparison', 'clustering']) ? 'active' : ''; ?>" data-bs-toggle="collapse" href="#toolsSubmenu" role="button" aria-expanded="<?= in_array($current, ['comparison', 'clustering']) ? 'true' : 'false'; ?>" aria-controls="toolsSubmenu">
                        <i class="fa-solid fa-tools"></i> Tools
                        <i class="fas fa-chevron-down small"></i>
                    </a>
                    <div class="collapse <?= in_array($current, ['comparison', 'clustering']) ? 'show' : ''; ?>" id="toolsSubmenu">
                        <a class="list-group-item list-group-item-action ps-5 <?= ($current == 'comparison') ? 'active' : ''; ?>" href="<?= base_url('comparison'); ?>"><i class="fa-solid fa-chart-line"></i> Comparison</a>
                        <a class="list-group-item list-group-item-action ps-5 <?= ($current == 'clustering') ? 'active' : ''; ?>" href="<?= base_url('clustering'); ?>"><i class="fa-solid fa-object-group"></i> Clustering</a>
                    </div>
                </div>
                <?php if ($this->session->userdata('idrole') == 1) { ?>
                <a class="list-group-item list-group-item-action p-3 <?= ($current == 'user') ? 'active' : ''; ?>" href="<?php echo base_url('user/'); ?>"><i class="fa-solid fa-users"></i> Pengguna</a>
                <?php } ?>
            </div>
        </div>
        <!-- Page content wrapper-->
        <div id="page-content-wrapper">
            <!-- Start Modal Change Username -->
            <div class="modal fade" id="changeUsernameModal" tabindex="-1" aria-labelledby="changeUsernameModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="<?= base_url('user/changeUsername'); ?>" method="post" enctype="multipart/form-data">
                            <div class="modal-header">
                                <h5 class="modal-title" id="changeUsernameModalLabel">Change Username</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-center">
                                <div class="mb-3">
                                    <label class="form-label" for="inputUsernameLabel" name="inputUsernameLabel" id="inputUsernameLabel">Masukan Username</label>
                                    <input type="text" class="form-control" name="inputUsername" id="inputUsername" value="<?php echo $this->session->userdata('username'); ?>">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- End -->
            <!-- Start Modal Change Password -->
            <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="<?= base_url('user/changePassword'); ?>" method="post" enctype="multipart/form-data">
                            <div class="modal-header">
                                <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-center">
                                <div class="mb-3">
                                    <label class="form-label" for="inputPasswordLabel" name="inputPasswordLabel" id="inputPasswordLabel">Masukan Password</label>
                                    <div class="input-group">
                                        <!-- icon kiri -->
                                        <span class="input-group-text">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-key" viewBox="0 0 16 16">
                                                <path d="M0 8a4 4 0 0 1 7.465-2H14a.5.5 0 0 1 .354.146l1.5 1.5a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0L13 9.207l-.646.647a.5.5 0 0 1-.708 0L11 9.207l-.646.647a.5.5 0 0 1-.708 0L9 9.207l-.646.647A.5.5 0 0 1 8 10h-.535A4 4 0 0 1 0 8zm4-3a3 3 0 1 0 2.712 4.285A.5.5 0 0 1 7.163 9h.63l.853-.854a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.793-.793-1-1h-6.63a.5.5 0 0 1-.451-.285A3 3 0 0 0 4 5z" />
                                                <path d="M4 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0z" />
                                            </svg>
                                        </span>

                                        <!-- input password -->
                                        <input type="password" class="form-control" name="inputPassword" id="inputPassword">

                                        <!-- tombol eye -->
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                            <i class="fa fa-eye" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- End -->
            <!-- Start Modal Upload Foto -->
            <div class="modal fade" id="changeFotoModal" tabindex="-1" aria-labelledby="changeFotoModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="<?= base_url('user/change_foto'); ?>" method="post" enctype="multipart/form-data">
                            <div class="modal-header">
                                <h5 class="modal-title" id="changeFotoModalLabel">Upload Foto Baru</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-center">
                                <img src="<?php echo base_url('assets/image/user/' . $this->session->userdata('foto')); ?>" alt="Preview Foto" class="img-thumbnail mb-3" width="120px" id="previewFoto">
                                <input type="file" class="form-control" name="foto" id="fotoInput" accept="image/*" required>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- End -->
            <!-- Top navigation-->
            <nav class="navbar navbar-expand-lg navbar-light border-bottom">
                <div class="container-fluid">
                    <button class="btn btn-primary d-inline d-xl-none" id="sidebarToggle">Menu</button>
                    <div class="navbar-nav ms-auto mt-2 mt-lg-0" id="navbarSupportedContent">
                        <ul class="navbar-nav">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <?php
                                    $foto = $this->session->userdata('foto');
                                    if (!empty($foto)) :
                                        ?>
                                    <img src="<?php echo base_url('assets/image/user/' . $foto); ?>" alt="User Photo" width="21px" height="21px" style="border-radius: 50%; object-fit: cover;">
                                    <?php else : ?>
                                    <div style="display: inline-block; width: 21px; height: 21px; border-radius: 50%; background-color: #e9ecef; text-align: center; line-height: 21px;">
                                        <i class="fa-solid fa-user" style="font-size: 12px; color: #6c757d;"></i>
                                    </div>
                                    <?php endif; ?>
                                    <?php echo $this->session->userdata('username'); ?>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#changeUsernameModal">Change Username</a>
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#changePasswordModal">Change Password</a>
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#changeFotoModal">Change Foto</a>
                                    <a class="dropdown-item" href="<?php echo base_url('auth/logout'); ?>">Log Out</a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Page content area -->
            <div class="container-fluid" style="background-color: var(--light-bg); min-height: calc(100vh - 73px); padding: 20px;">
                <!-- Content akan ditampilkan di sini -->

                <script>
                    // Preview foto upload
                    document.getElementById("fotoInput").addEventListener("change", function(event) {
                        const [file] = event.target.files;
                        if (file) {
                            document.getElementById("previewFoto").src = URL.createObjectURL(file);
                        }
                    });

                    // Toggle password visibility
                    const togglePassword = document.querySelector('#togglePassword');
                    const passwordField = document.querySelector('#inputPassword');
                    const icon = togglePassword.querySelector('i');

                    togglePassword.addEventListener('click', function() {
                        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                        passwordField.setAttribute('type', type);

                        // ganti icon
                        if (type === 'password') {
                            icon.classList.remove('fa-eye-slash');
                            icon.classList.add('fa-eye');
                        } else {
                            icon.classList.remove('fa-eye');
                            icon.classList.add('fa-eye-slash');
                        }
                    });

                    // Sidebar toggle
                    document.addEventListener('DOMContentLoaded', function() {
                        const sidebarToggle = document.getElementById('sidebarToggle');
                        const sidebarWrapper = document.getElementById('sidebar-wrapper');

                        sidebarToggle.addEventListener('click', function() {
                            sidebarWrapper.classList.toggle('toggled');
                        });

                        // Inisialisasi tooltips jika ada
                        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                        tooltipTriggerList.map(function(tooltipTriggerEl) {
                            return new bootstrap.Tooltip(tooltipTriggerEl);
                        });
                    });
                </script>