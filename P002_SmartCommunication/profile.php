<?php
session_start();

// Check if the user is logged in
$loggedIn = isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'];

// If the user is not logged in, redirect to login.php
if (!$loggedIn) {
    header("Location: login.php");
    exit;
}

$userData = [];

// Prepare data for sending to the REST server
if ($loggedIn) {
    $ch = curl_init();
    $url = 'http://localhost/P002_SmartCommunication/serverRest.php/?action=user';
    
    $postData = [
        'ident' => $_SESSION['ident']
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
        // Handle the response
        $userData = json_decode($response, true);
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

        .gradient-custom {
          /* fallback for old browsers */
          background: #f6d365;

          /* Chrome 10-25, Safari 5.1-6 */
          background: -webkit-linear-gradient(to right bottom, rgba(246, 211, 101, 1), rgba(253, 160, 133, 1));

          /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
          background: linear-gradient(to right bottom, rgba(246, 211, 101, 1), rgba(253, 160, 133, 1))
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

<section class="vh-100" style="background-color: #FF0000CF;">
  <div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col col-lg-6 mb-4 mb-lg-0">
        <div class="card mb-3" style="border-radius: .5rem;">
          <div class="row g-0">
            <div class="col-md-4 gradient-custom text-center text-white"
              style="border-top-left-radius: .5rem; border-bottom-left-radius: .5rem;">
              <img src="image/profilo.webp"
                alt="Avatar" class="img-fluid my-5" style="width: 80px;" />
              <h5><?php echo htmlspecialchars($userData['nome_utente'] ?? ''); ?> <?php echo htmlspecialchars($userData['cognome_utente'] ?? ''); ?></h5>
              <p><?php
                    $firstLetter = strtoupper(substr($userData['id_utente'], 0, 1)); // Gets the first letter and converts it to uppercase

                    if ($firstLetter === 'S') {
                        echo "studente";
                    } elseif ($firstLetter === 'G') {
                        echo "genitore";
                    } elseif ($firstLetter === 'D') {
                        echo "docente";
                    } else {
                        echo "altro";
                    }
              ?></p>
              <i class="far fa-edit mb-5"></i>
            </div>
            <div class="col-md-8">
              <div class="card-body p-4">
                <h6>Information</h6>
                <hr class="mt-0 mb-4">
                <div class="row pt-1">
                  <div class="col-6 mb-3">
                    <h6>Nome</h6>
                    <p class="text-muted"><?php echo htmlspecialchars($userData['nome_utente'] ?? ''); ?></p>
                  </div>
                  <div class="col-6 mb-3">
                    <h6>Cognome</h6>
                    <p class="text-muted"><?php echo htmlspecialchars($userData['cognome_utente'] ?? ''); ?></p>
                  </div>

                  <div class="col-6 mb-3">
                    <h6>Data di nascita</h6>
                    <p class="text-muted"><?php echo htmlspecialchars($userData['data_nascita'] ?? ''); ?></p>
                  </div>

                  <div class="col-6 mb-3">
                    <h6>id</h6>
                    <p class="text-muted"><?php echo htmlspecialchars($userData['id_utente'] ?? ''); ?></p>
                  </div>
                </div>
                <form method="post" action="login.php">
                  <input type="hidden" name="logout" value="true">
                  <button type="submit" class="btn btn-dark btn-lg btn-block">Logout</button>
                </form>
                
                
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>


   
</body>
</html>
