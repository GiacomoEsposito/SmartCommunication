<?php
    session_start();
    if ($_SESSION['ident'] == 'ADMIN1'):
    ?>
    <?php
    

    // Verifica se l'utente è loggato
    $loggedIn = isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'];

    if (!$loggedIn) {
        header("Location: ../login.php");
        exit;
    }
    // Gestione del tentativo di login
    if (isset($_POST['categoria']) && isset($_POST['nome'])) {
        $ch = curl_init();
        $url = 'http://localhost/P002_SmartCommunication/serverRest.php/?action=newGroup';
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('categoria' => $_POST['categoria'], 'nome' => $_POST['nome'])));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
    
        if ($response) {
            $response_data = json_decode($response, true);
            if (isset($response_data['id'])) {
                header("Location: gruppiEdit.php");
                exit;
            } else {
                $error_msg = "Errore nella creazione del gruppo";
            }
        } else {
            $error_msg = "Errore di comunicazione con il server";
        }
    }

    // Gestione del logout
    if (isset($_POST['logout'])) {
        $_SESSION['loggedIn'] = false;
        session_destroy();
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
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

        </style></head>
    <body class="vh-100" style="background-color: #9A616D;">

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
                <a class="navbar-brand mt-2 mt-lg-0" href="../index.php">
                    <img
                    src="../image/logo.webp"
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
                        href="../profile.php"
                        id="navbarDropdownMenuAvatar"
                        role="button"
                        aria-expanded="false"
                        >
                        <img
                            src="../image/profilo.webp"
                            class="navbar-logo"
                            height="25"
                            
                        />
                        </a>
                        
                    </div>
            
                <!-- Right elements -->
            </div>
            <!-- Container wrapper -->
        </nav>

        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col col-xl-10">
                    <div class="card" style="border-radius: 1rem;">
                        <div class="row g-0">
                            <div class="col-md-6 col-lg-5 d-none d-md-block">
                                <img src="../image/crea.webp"
                                    alt="login form" class="img-fluid" style="border-radius: 1rem 0 0 1rem;" />
                            </div>
                            <div class="col-md-6 col-lg-7 d-flex align-items-center">
                                <div class="card-body p-4 p-lg-5 text-black">
                                
                                    <form method="post">
                                        <div class="d-flex align-items-center mb-3 pb-1">
                                            <i class="fas fa-cubes fa-2x me-3" style="color: #ff6219;"></i>
                                            <span class="h1 fw-bold mb-0">Crea un gruppo</span>
                                        </div>

                                        <div class="form-outline mb-4">
                                            <label class="form-label" for="categoria">categoria</label>
                                            <input type="text" id="categoria" name="categoria" class="form-control form-control-lg" required>
                                            
                                        </div>

                                        <div class="form-outline mb-4">
                                            <label class="form-label" for="nome">nome</label>
                                            <input type="text" id="nome" name="nome" class="form-control form-control-lg" required>
                                            
                                        </div>

                                        <div class="pt-1 mb-4">
                                            <button type="submit" class="btn btn-dark btn-lg btn-block">create</button>
                                        </div>
                                    </form>

                                
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>

<?php
    else:
        header("Location: ../index.php");
        exit;
?>
    <p>NON HAI LE AUTORIZZAZIONI PER UTILIZZARE QUESTA PAGINA</p>
<?php
    endif;
?>