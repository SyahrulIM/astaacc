<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pengguna - <?php echo $user->full_name; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            background-color: #fff;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #4CAF50;
        }

        .report-title {
            margin: 0;
            font-size: 24px;
            color: #333;
        }

        .report-subtitle {
            font-size: 18px;
            color: #4CAF50;
            margin: 10px 0;
        }

        .report-date {
            font-size: 14px;
            color: #666;
        }

        .user-info {
            margin: 20px 0;
        }

        .info-row {
            display: flex;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }

        .info-label {
            flex: 1;
            font-weight: bold;
            color: #555;
        }

        .info-value {
            flex: 2;
            color: #333;
        }

        .user-photo {
            text-align: center;
            margin: 20px 0;
        }

        .photo-placeholder {
            width: 150px;
            height: 150px;
            border: 2px dashed #ccc;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: #f9f9f9;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }

        .signature-area {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }

        .signature {
            width: 45%;
            text-align: center;
        }

        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #333;
            width: 100%;
        }

        @media print {
            @page {
                margin: 0.5in;
                size: portrait;
            }

            body {
                padding: 0;
                background-color: #fff;
            }

            .container {
                border: none;
                box-shadow: none;
                padding: 0;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1 class="report-title">DATA PENGGUNA</h1>
            <h2 class="report-subtitle"><?php echo $user->full_name; ?></h2>
            <p class="report-date">Dicetak pada: <?php echo date('d F Y H:i:s'); ?></p>
        </div>

        <div class="user-photo">
            <?php if (!empty($user->foto)) : ?>
            <img src="<?php echo base_url('assets/image/user/' . $user->foto); ?>" alt="User Photo" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 3px solid #4CAF50;">
            <?php else : ?>
            <div class="photo-placeholder">
                <span>Tidak ada foto</span>
            </div>
            <?php endif; ?>
        </div>

        <div class="user-info">
            <div class="info-row">
                <div class="info-label">Nama Lengkap:</div>
                <div class="info-value"><?php echo $user->full_name; ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Username:</div>
                <div class="info-value"><?php echo $user->username; ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value"><?php echo $user->email; ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Role:</div>
                <div class="info-value"><?php echo $user->nama_role; ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value">Aktif</div>
            </div>
        </div>

        <div class="signature-area">
            <div class="signature">
                <p>Disetujui oleh,</p>
                <div class="signature-line"></div>
                <p>Manajer</p>
            </div>
            <div class="signature">
                <p>Dicetak oleh,</p>
                <div class="signature-line"></div>
                <p><?php echo $this->session->userdata('username'); ?></p>
            </div>
        </div>

        <div class="footer">
            <p>&copy; <?php echo date('Y'); ?> Asta People</p>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>

</html>