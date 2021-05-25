<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <title>Juega y Gana</title>

    <!-- Bootstrap icons-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-wEmeIV1mKuiNpC+IOBjI7aAzPcEZeedi5yW5f2yOq55WWLwNGmvvx4Um1vskeMj0" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">

    <link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico">
    <link rel="apple-touch-icon" sizes="57x57" href="img/apple-touch-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="img/apple-touch-icon-60x60.png">
    <link rel="icon" type="image/png" href="img/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="img/favicon-16x16.png" sizes="16x16">
    <link rel="shortcut icon" href="img/favicon.ico">

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
<body id="page-top" class="d-flex flex-column h-100">
    <main class="masthead inter">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12 col-sm-12 col-md-10 col-lg-10 col-xl-10 py-4">
                <?php
                    include_once 'api/config/config.php';
                    include_once 'api/config/database.php';
                    include_once 'api/objects/db_log.php';
                    include_once 'api/objects/award.php';

                    // prevent notices

                    if (!$GLOBALS['debug'] ) {
                        error_reporting(0);
                    }

                    if (!isset($_GET["pass"]) || $_GET["pass"] != "Cristal") {
                        echo "Not authorized";
                        exit();
                    }

                    // get database connection
                    $database = new Database();
                    $db = $database->getConnection();
                        
                    $award = new Award($db);
                    $data = $award->report();
                    Award::writeTableResponse($data);
                ?>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
