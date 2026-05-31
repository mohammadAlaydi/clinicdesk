<?php

if (!isset($pageTitle)) $pageTitle = APP_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> | <?= e(APP_NAME) ?></title>

    <link rel="stylesheet" href="<?= BASE_URL ?>public/assets/adminlte/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/assets/adminlte/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/assets/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">

    <style>
        .table-vmid td, .table-vmid th { vertical-align: middle !important; }
        .avatar-sm { width:32px; height:32px; border-radius:50%; object-fit:cover; }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
