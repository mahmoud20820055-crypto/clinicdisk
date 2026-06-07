<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> - <?= $pageTitle ?? 'Dashboard' ?></title>
    <!-- Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- AdminLTE -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/adminlte/css/adminlte.min.css">
    <style>
        .sidebar-dark-primary { background-color: #343a40; }
        .content-wrapper { background-color: #f4f6f9; }
        .small-box { border-radius: 10px; }
        .status-badge { padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .status-pending { background-color: #ffc107; color: #000; }
        .status-confirmed { background-color: #17a2b8; color: #fff; }
        .status-completed { background-color: #28a745; color: #fff; }
        .status-cancelled { background-color: #dc3545; color: #fff; }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper"> 