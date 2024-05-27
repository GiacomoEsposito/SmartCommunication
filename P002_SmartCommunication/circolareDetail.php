<?php
session_start();

// Verifica se l'utente è loggato
$loggedIn = isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'];

// Se l'utente non è loggato, reindirizza a login.php
if (!$loggedIn) {
    header("Location: login.php");
    exit;
}

// Prepara i dati per l'invio al server REST
if ($loggedIn && isset($_GET['id'])) {
    $ch = curl_init();
    $url = 'http://localhost/P002_SmartCommunication/serverRest.php/?action=circDetail';
    
    $postData = [
        'ident' => $_SESSION['ident'],
        'token' => $_SESSION['token'],
        'cntTitle'=> $_GET['id']
    ];
    
    $postDataQueryString = http_build_query($postData);
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postDataQueryString);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    
    if (!$response) {
        $error_msg = curl_error($ch);
    } else {
        // Decode the response
        $circolari = json_decode($response, true);
    }
    curl_close($ch);
}
?>



<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        import { Dropdown, Collapse, initMDB } from "mdb-ui-kit";

        initMDB({ Dropdown, Collapse });
    </script>
     <style>
        /* Adjust the navbar height and logo size here */
        .navbar-custom {
            height: 50px; /* Example height */
        }
        .navbar-logo {
            height: 60%; /* Full height of the navbar */
            width: 50px; /* Same as the height to make it a square */
            border-radius: 50%; /* Fully rounded corners for a circle */
            object-fit: cover; /* To make sure the image covers the area without stretching */
        }

    

    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light navbar-custom">
        <!-- Container wrapper -->
        <div class="container-fluid">
            <!-- Toggle button -->
            <button
            data-mdb-collapse-init
            class="navbar-toggler"
            type="button"
            data-mdb-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent"
            aria-expanded="false"
            aria-label="Toggle navigation"
            >
            <i class="fas fa-bars"></i>
            </button>

            <!-- Collapsible wrapper -->
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Navbar brand -->
            <a class="navbar-brand mt-2 mt-lg-0" href="index.php">
                <img
                src="image\logo.webp"
                class="navbar-logo"
                height="15"
                alt="MDB Logo"
                loading="lazy"
                />
            </a>

           
            </div>
            <!-- Collapsible wrapper -->

            <!-- Right elements -->
            <div class="d-flex align-items-center">
                <!-- Avatar -->
            
                    <a
                    data-mdb-dropdown-init
                    class="d-flex align-items-center"
                    href="profile.php"
                    id="navbarDropdownMenuAvatar"
                    role="button"
                    aria-expanded="false"
                    >
                    <img
                        src="image/profilo.webp"
                        class="navbar-logo"
                        height="25"
                        
                    />
                    </a>
                    
                </div>
        
            <!-- Right elements -->
        </div>
        <!-- Container wrapper -->
    </nav>


    <?php
        echo htmlspecialchars("risposta ".$response);
    ?>        
</body>
</html>