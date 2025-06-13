<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Asta ACOL - <?php echo $title; ?></title>
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
</head>

<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar-->
        <div class="border-end bg-white" id="sidebar-wrapper">
            <div class="sidebar-heading border-bottom bg-light text-center"><img src="<?php echo base_url('assets/image/logo A asta biru.png') ?>" alt="Asta Logo" width="20px"> Accounting Online</div>
            <?php
            $current = $this->uri->segment(1);
            ?>
            <div class="list-group list-group-flush">
                <a class="list-group-item list-group-item-action list-group-item-light p-3 <?= ($current == 'dashboard') ? 'active' : ''; ?>" href="<?php echo base_url('dashboard/'); ?>">Dashboard</a>
                <div class="list-group-item p-0">
                    <a class="list-group-item list-group-item-action list-group-item-light p-3 d-flex justify-content-between align-items-center <?= in_array($current, ['']) ? 'active' : ''; ?>" data-bs-toggle="collapse" href="#transaksiSubmenu" role="button" aria-expanded="<?= in_array($current, ['verification', 'barangmasuk', 'outstock']) ? 'true' : 'false'; ?>" aria-controls="transaksiSubmenu">
                        Database Recap
                        <i class="fas fa-chevron-down small"></i>
                    </a>
                    <div class="collapse <?= in_array($current, ['shopee_recap', 'accurate_recap']) ? 'show' : ''; ?>" id="transaksiSubmenu">
                        <a class="list-group-item list-group-item-action list-group-item-light ps-5 <?= ($current == 'shopee_recap') ? 'active' : ''; ?>" href="<?= base_url('shopee_recap'); ?>">Shopee Recap</a>
                        <a class="list-group-item list-group-item-action list-group-item-light ps-5 <?= ($current == 'accurate_recap') ? 'active' : ''; ?>" href="<?= base_url('accurate_recap'); ?>">Accurate Recap</a>
                    </div>
                </div>
                <div class="list-group-item p-0">
                    <a class="list-group-item list-group-item-action list-group-item-light p-3 d-flex justify-content-between align-items-center <?= in_array($current, ['']) ? 'active' : ''; ?>" data-bs-toggle="collapse" href="#transaksiSubmenu" role="button" aria-expanded="<?= in_array($current, ['comparison','']) ? 'true' : 'false'; ?>" aria-controls="transaksiSubmenu">
                        Tools
                        <i class="fas fa-chevron-down small"></i>
                    </a>
                    <div class="collapse <?= in_array($current, ['comparison', 'accurate_recap']) ? 'show' : ''; ?>" id="transaksiSubmenu">
                        <a class="list-group-item list-group-item-action list-group-item-light ps-5 <?= ($current == 'comparison') ? 'active' : ''; ?>" href="<?= base_url('comparison'); ?>">Comparison</a>
                    </div>
                </div>
                <?php if ($this->session->userdata('idrole') == 1) { ?>
                    <a class="list-group-item list-group-item-action list-group-item-light p-3 <?= ($current == 'user') ? 'active' : ''; ?>" href="<?php echo base_url('user/'); ?>">Pengguna</a>
                <?php } ?>
            </div>
        </div>
        <!-- Page content wrapper-->
        <div id="page-content-wrapper">
            <!-- Top navigation-->
            <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
                <div class="container-fluid">
                    <button class="btn btn-primary d-inline d-xl-none" id="sidebarToggle">Menu</button>
                    <div class="navbar-nav ms-auto mt-2 mt-lg-0" id="navbarSupportedContent">
                        <ul class="navbar-nav">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <img src="<?php echo base_url('assets/image/user/' . $this->session->userdata('foto')); ?>" alt="" width="21px" height="21px" style="border-radius: 50%; object-fit: cover;">
                                    <?php echo $this->session->userdata('username'); ?>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="<?php echo base_url('auth/logout'); ?>">Log Out</a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>