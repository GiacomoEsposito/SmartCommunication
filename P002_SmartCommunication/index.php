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
if ($loggedIn) {
    $ch = curl_init();
    $url = 'http://localhost/P002_SmartCommunication/serverRest.php/?action=insert';
    
    // Encode the token and potentially other fields to ensure special characters are handled correctly
    //$encodedToken = urlencode($_SESSION['token']);
    $postData = [
        'ident' => $_SESSION['ident'],
        'token' => $_SESSION['token'],
        'firstName' => $_SESSION['firstName'],
        'lastName' => $_SESSION['lastName']
    ];
    
    // Convert array to query string while preserving encoding
    $postDataQueryString = http_build_query($postData);
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postDataQueryString);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if (!$response) {
        $error_msg = curl_error($ch);
    } else {
        // Gestisci la risposta
        $response_data = json_decode($response, true);
        $insert_message = isset($response_data['status']) && $response_data['status'] === 'success' ? "Utente inserito con successo nel database." : "Errore durante l'inserimento: " . ($response_data['message'] ?? 'Errore sconosciuto');
    }
    curl_close($ch);

    $ch = curl_init();
    $url = 'http://localhost/P002_SmartCommunication/serverRest.php/?action=noticeBoard';
    
    // Encode the token and potentially other fields to ensure special characters are handled correctly
    $postData = [
        'ident' => $_SESSION['ident'],
        'token' => $_SESSION['token'],
    ];
    
    // Convert array to query string while preserving encoding
    $postDataQueryString = http_build_query($postData);
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postDataQueryString);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    if (!$response) {
        $error_msg = curl_error($ch);
    } else {
        // Gestisci la risposta
        $response_data = json_decode($response, true);
        $insert_message = isset($response_data['status']) && $response_data['status'] === 'success' ? "Utente inserito con successo nel database." : "Errore durante l'inserimento: " . ($response_data['message'] ?? 'Errore sconosciuto');
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

        a, a:hover, a:focus, a:active {
            text-decoration: none; /* Removes underline */
            color: inherit; /* Keeps the default color */
        }
        .card a {
            text-decoration: none; /* Specific for links within cards */
        }

        .btn-outline-danger {
            color: #dc3545;
            border-color: #dc3545;
        }

        .btn-outline-danger:hover {
            color: #fff;
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .btn-danger {
            color: #fff;
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }
        .bg-danger {
            background-color: #dc3545 !important;
        }

        .bg-danger.text-white {
            color: #fff;
        }
        

    </style>
</head>
<body>
<div class="modal fade" id="cookieconsent2" tabindex="-1" aria-labelledby="cookieconsentLabel2" aria-hidden="true" data-mdb-backdrop="static" data-mdb-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="cookieconsentLabel2">Cookies & Privacy</h5>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-3 d-flex align-items-center justify-content-center">
                        <i class="fas fa-cookie-bite fa-4x text-primary"></i>
                    </div>

                    <div class="col-9">
                        <p>Questo sito utilizza cookie per migliorare la tua esperienza (i cookie tecnici vengono accettati automaticamente).<a class="d-block" href="https://www.garanteprivacy.it/documents/10160/0/Regolamento+UE+2016+679.+Arricchito+con+riferimenti+ai+Considerando+Aggiornato+alle+rettifiche+pubblicate+sulla+Gazzetta+Ufficiale++dell%27Unione+europea+127+del+23+maggio+2018" target="blank">Questo banner segue il GDPR</a></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-danger" data-mdb-dismiss="modal" onclick="setCookieConsent('refused'); location.reload();">Rifiuto</button>
                <button type="button" class="btn btn-danger" data-mdb-dismiss="modal" onclick="setCookieConsent(true); location.reload();">Accetto</button>

            </div>
        </div>
    </div>
</div>

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

<section style="background-color: #eee;">
  <div class="container py-5">
    <div class="row">
    
    <?php
    if ($_SESSION['ident'] != 'ADMIN1'):
    ?>
        <div class="col-md-6 col-lg-4 mb-4 mb-md-0">
            <a href="circolari.php">
                <div class="card">
                    <div class="d-flex justify-content-between p-3">
                        <p class="lead mb-0">Circolari</p>
                    </div>
                    <img src="image/agenda.webp" class="card-img-top" alt="Agenda"/>
                </div>
            </a>
        </div>

        <div class="col-md-6 col-lg-4 mb-4 mb-md-0">
            <a href="moduli.php">
                <div class="card">
                    <div class="d-flex justify-content-between p-3">
                        <p class="lead mb-0">Moduli</p>
                    </div>
                    <img src="image/moduli.webp" class="card-img-top" alt="Moduli"/>
                </div>
            </a>
        </div>

        <div class="col-md-6 col-lg-4 mb-4 mb-md-0">
            <a href="preferiti.php">
                <div class="card">
                    <div class="d-flex justify-content-between p-3">
                        <p class="lead mb-0">Preferiti</p>
                    </div>
                    <img src="image/star.webp" class="card-img-top" alt="Preferiti"/>
                </div>
            </a>
        </div>

        <div class="col-md-6 col-lg-4 mb-4 mb-md-0">
            <a href="visualizzati.php">
                <div class="card">
                    <div class="d-flex justify-content-between p-3">
                        <p class="lead mb-0">Visualizzati</p>
                    </div>
                    <img src="image/eye.webp" class="card-img-top" alt="Visualizzati"/>
                </div>
            </a>
        </div>
    <?php
    endif;
    ?>

      <div class="col-md-6 col-lg-4 mb-4 mb-md-0">
      <a href = "gruppi.php">
        <div class="card">
          <div class="d-flex justify-content-between p-3">
            <p class="lead mb-0">Gruppi</p>
          </div>
          <img src="image/group.webp" class="card-img-top" alt="Laptop" />
          
        </div>
        </a> 
      </div>

      
</div>

<script>
        document.addEventListener('DOMContentLoaded', function () {
        var consent = checkCookieConsent();
            if (consent !== "true" && consent !== "refused") { // Mostra il banner solo se il consenso non è stato dato o rifiutato in precedenza
                var cookieModal = new bootstrap.Modal(document.getElementById('cookieconsent2'));
                cookieModal.show();
            }
        });

        function setCookieConsent(consent) {
            document.cookie = "cookie_consent=" + consent + "; expires=Fri, 31 Dec 9999 23:59:59 GMT; path=/";
            if (consent === "true" || consent === "refused") { // Chiudi il modal solo se il consenso è stato dato o rifiutato
                var cookieModal = new bootstrap.Modal(document.getElementById('cookieconsent2'));
                cookieModal.hide();
            }
        }

        function checkCookieConsent() {
            var cookies = document.cookie.split(";");
            for (var i = 0; i < cookies.length; i++) {
                var cookie = cookies[i].trim();
                if (cookie.startsWith("cookie_consent=")) {
                    var consent = cookie.substring("cookie_consent=".length);
                    return consent === "true" ? "true" : (consent === "refused" ? "refused" : "false");
                }
            }
            return null;
        }

    </script>

</body>
</html>
