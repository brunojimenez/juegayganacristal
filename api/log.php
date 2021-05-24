<!doctype html>
<html lang="en" class="h-100">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <base href="/">
    
    <title>Juega y Gana</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-wEmeIV1mKuiNpC+IOBjI7aAzPcEZeedi5yW5f2yOq55WWLwNGmvvx4Um1vskeMj0" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/game.css">
    <style>
        body {
            color : white;
        }
        td {
            border: 1px white solid;
            padding: 5px;
        }
    </style>

    <link rel="shortcut icon" type="image/x-icon" href="/img/favicon.ico">
    <link rel="apple-touch-icon" sizes="57x57" href="/img/apple-touch-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/img/apple-touch-icon-60x60.png">
    <link rel="icon" type="image/png" href="/img/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="/img/favicon-16x16.png" sizes="16x16">
    <link rel="shortcut icon" href="/img/favicon.ico">
    
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff">
    <meta property="og:title" content="Cerveza Cristal">
    <meta property="og:site_name" content="Cerveza Cristal">
    <meta property="og:image" itemprop="image" content="">
    <meta property="og:image:type" content="image/jpg">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="628">
    <meta property="og:url" content="https://juegayganacristal.cl/">
    <meta property="og:description" content="Nos apasiona crear experiencias para compartir juntos un mejor vivir">
    <meta property="og:type" content="website">
</head>
<body class="d-flex flex-column h-100">
    <header class="header fixed-top">
        <div class="header-bg">
            <div class="logo-image">
                <img alt="Cerveza Cristal" src="/img/logo-cristal-ii.png">
            </div>
        </div>
    </header>

    <main class="mt-3">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12 col-sm-12 col-md-12 col-lg-10 col-xl-10 mb-3">
                    <div class="wrap">
                    <?php
                    include_once 'config/config.php';
                    include_once 'config/database.php';
                    include_once 'objects/db_log.php';
                    include_once 'util/util.php';

                    // prevent notices

                    if (!$GLOBALS['debug'] ) {
                        error_reporting(0);
                    }

                    // get database connection
                    $database = new Database();
                    $db = $database->getConnection();
                        
                    $log = new DbLog($db);
                    $data = $log->select();
                    DbLog::writeTableResponse($data);
                    ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer mt-auto py-3">
        <div class="container text-center">
            <span class="fs-7 text-white"><sup>©</sup> 2021 Cerveza Cristal.</span>
            <span class="fs-7 text-white">Todos los derechos reservados</span>
        </div>
    </footer>

  </body>
</html>
