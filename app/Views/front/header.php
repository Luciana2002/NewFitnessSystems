<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Fitness Systems</title>

    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    <script src="<?= base_url('assets/js/main.js') ?>"></script>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Montserrat', sans-serif;
        }

        .hero {
            height: 100vh;
            background: linear-gradient(rgba(0,0,0,0.55), rgba(0,0,0,0.55)),
                        url('<?= base_url('assets/img/inicio.jpg') ?>');
            background-size: cover;
            background-position: center;
            color: white;
        }

        .hero h1 {
            font-size: 4rem;
            font-weight: 700;
        }

        .btn-main {
            background-color: black;
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
        }

        .btn-main:hover {
            background-color: #333;
            color: white;
        }

        .section-light {
            background-color: #f5f5f5;
            padding: 80px 0;
        }

        .card-program {
            transition: transform 0.3s ease;
        }

        .card-program:hover {
            transform: scale(1.03);
        }

        footer {
            background-color: #f8f8f8;
            padding: 50px 0;
        }
    </style>
</head>
<body>