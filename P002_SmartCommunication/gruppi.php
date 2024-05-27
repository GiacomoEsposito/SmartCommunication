<?php
session_start();

// Verifica se l'utente è loggato
$loggedIn = isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'];

// Se l'utente non è loggato, reindirizza a login.php
if (!$loggedIn) {
    header("Location: login.php");
    exit;
}
$gruppi = [];
if($_SESSION['ident'] != 'ADMIN1'){
    
    $ch = curl_init();
    $url = 'http://localhost/P002_SmartCommunication/serverRest.php/?action=prtintGroup';
    
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
        // Decode the response
        $gruppi = json_decode($response, true);
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

        a, a:hover, a:focus, a:active {
            text-decoration: none; /* Removes underline */
            color: inherit; /* Keeps the default color */
        }
        .card a {
            text-decoration: none; /* Specific for links within cards */
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

 <section style="background-color: #eee;">
    <div class="container py-5">
    <div class="row">
 <?php
    if ($_SESSION['ident'] == 'ADMIN1'):
    ?>
        <div class="col-md-6 col-lg-4 mb-4 mb-md-0">
            <a href="gruppi/gruppiCreate.php">
                <div class="card">
                    <div class="d-flex justify-content-between p-3">
                        <p class="lead mb-0">Crea un Gruppo</p>
                    </div>
                    <img src="image/crea.webp" class="card-img-top" alt="Agenda"/>
                </div>
            </a>
        </div>

        <div class="col-md-6 col-lg-4 mb-4 mb-md-0">
            <a href="gruppi/gruppiEdit.php">
                <div class="card">
                    <div class="d-flex justify-content-between p-3">
                        <p class="lead mb-0">Gestisci Gruppi</p>
                    </div>
                    <img src="image/edit.webp" class="card-img-top" alt="Moduli"/>
                </div>
            </a>
        </div>

        <?php
            else:
            ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID Gruppo</th>
                            <th>Categoria</th>
                            <th>Nome</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($gruppi as $gruppo): ?>
                    <tr onclick="window.location.href='gruppi/gruppiDetail.php?id=<?php echo $gruppo['id_gruppo']; ?>';">
                        <td >
                            <?php echo htmlspecialchars($gruppo['id_gruppo']); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($gruppo['categoria_gruppo']); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($gruppo['nome_gruppo']); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php
            endif;
        ?>
    </div>

    
            
</body>
</html>
