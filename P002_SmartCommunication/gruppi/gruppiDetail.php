<?php
session_start();

// Verifica se l'utente è loggato
$loggedIn = isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'];

// Se l'utente non è loggato, reindirizza a login.php
if (!$loggedIn) {
    header("Location: ../login.php");
    exit;
}

// Prepara i dati per l'invio al server REST
$response = [];

if ($loggedIn && isset($_GET['id'])) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        $searchTerm = $_POST['searchTerm'] ?? null;
        $filterBy = $_POST['filterBy'] ?? null;
        $orderBy = $_POST['orderBy'] ?? null;
        if ($_SESSION['ident'] == 'ADMIN1'){
            $searchBy = $_POST['searchBy'] ?? null;
        } else {
            $searchBy = 'Circolare';
        }
        
        // Invio dei dati al server REST
        $ch = curl_init();
        $url = 'http://localhost/P002_SmartCommunication/serverRest.php?action=search';
        
        $postData = [
            'page' => "gruppiDetail",
            'searchBy' => $searchBy,
            'searchTerm' => $searchTerm,
            'filterBy' => $filterBy,
            'orderBy' => $orderBy,
            'ident' => $_SESSION['ident'],
            'groupID' => $_GET['id']
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
            // Decodifica la risposta se necessario
            $response = json_decode($response, true);
            // Gestione della risposta
        }
        curl_close($ch);
    } else {
        $ch = curl_init();
        $url = 'http://localhost/P002_SmartCommunication/serverRest.php?action=groupDetail&id=' . $_GET['id'];
    
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
        $response = curl_exec($ch);
        if (!$response) {
            $error_msg = curl_error($ch);
        } else {
            // Decode the response
            $response = json_decode($response, true);
        }
        curl_close($ch);
    }
   
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        function rimuoviElementoDalGruppo(tipo, idElemento, idGruppo) {
            var xhr = new XMLHttpRequest();
            xhr.open('DELETE', `http://localhost/P002_SmartCommunication/serverRest.php?action=rimuoviDalGruppo&tipo=${tipo}&idElemento=${idElemento}&idGruppo=${idGruppo}`, true);
            xhr.onload = function() {
                if (xhr.status >= 200 && xhr.status < 300) {
                    console.log('Success:', xhr.responseText);
                    window.location.reload(); // Ricarica la pagina per riflettere le modifiche
                } else {
                    console.error('Failed:', xhr.responseText);
                }
            };
            xhr.send();
        }

    </script>
    
    <style>
        .header-with-button {
            display: flex;
            align-items: center;
        }
        .add-button {
            margin-left: 10px;
            color: white;
            background-color: green;
            border: none;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            justify-content: center;
            align-items: center;
            text-decoration: none; /* Rimuove la sottolineatura */
        }
        .add-button i {
            color: white;
        }
        .table-container {
            margin-top: 20px;
        }

        .star-icon {
            color: black; /* Colore di default */
            transition: color 0.3s ease; /* Transizione fluida per il cambio colore */
        }
        .star-icon:hover {
            color: yellow; /* Cambia in giallo quando il mouse passa sopra */
        }
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

        .link {
        text-decoration: none; /* Rimuove la sottolineatura */
        color: inherit; /* I link useranno il colore di testo normale dell'elemento genitore */
    }

    .link:hover {
        color: #0056b3; /* Cambia il colore del link al passaggio del mouse */
        text-decoration: underline; /* Aggiunge una sottolineatura al passaggio del mouse, opzionale */
    }

    /* Stili aggiuntivi per la tabella per una migliore presentazione */
    table {
        width: 100%; /* Imposta la larghezza della tabella al 100% del contenitore */
        border-collapse: collapse; /* Rimuove gli spazi tra le celle */
    }

    th, td {
        padding: 10px; /* Aggiunge spazio intorno al testo nelle celle */
        border: 1px solid #ccc; /* Aggiunge un bordo leggero intorno alle celle */
    }

    th {
        background-color: #f8f9fa; /* Colore di sfondo per le celle dell'intestazione */
        text-align: left; /* Allinea il testo a sinistra nelle celle dell'intestazione */
    }
    tr:hover {
        cursor: pointer; /* Cambia il cursore in un puntatore quando il mouse è sopra una riga */
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
          <a class="navbar-brand mt-2 mt-lg-0" href="..\index.php">
            <img
              src="..\image\logo.webp"
              class="navbar-logo"
              height="15"
              alt="MDB Logo"
              loading="lazy"
            />
          </a>
        
          <!-- Search form centered with mx-auto and specific width -->
          <form class="d-flex align-items-center mx-auto" style="max-width: 600px;" method="POST" action="gruppiDetail.php?id=<?php echo $_GET['id']?>"> <!-- aggiungi action se necessario -->
                <input class="form-control me-2" type="search" placeholder="Cerca" aria-label="Cerca" name="searchTerm">

                <?php
                    if ($_SESSION['ident'] == 'ADMIN1'):
                ?>
                    <select class="form-select me-2" aria-label="Ricerca" name="searchBy">
                        <option selected value="">Ricerca</option>
                        <option value="Utente">Utente</option>
                        <option value="Circolare">Circolare</option>
                    </select>
                <?php
                    endif;
                ?>
                <!-- Aggiunta del filtro -->
                <select class="form-select me-2" aria-label="Filtra per" name="filterBy">
                    <option selected value="">Filtra</option>
                    <option value="id_circolare">Nome della circolare</option>
                    <option value="categoria_circolare">Categoria</option>
                    <option value="data_circolare">Data della circolare</option>
                    <?php
                        if ($_SESSION['ident'] == 'ADMIN1'):
                    ?>
                        <option value="id_utente">Id utente</option>
                        <option value="nome_utente">Nome utente</option>
                        <option value="cognome_utente">Cognome utente</option>
                        <option value="data_nascita">Data di nascita</option>
                    <?php
                        endif;
                    ?>
                </select>

                <!-- Aggiunta dell'ordine -->
                <select class="form-select me-2" aria-label="Ordine" name="orderBy">
                    <option selected value="">Ordine</option>
                    <option value="ASC">Crescente</option>
                    <option value="DESC">Decrescente</option>
                </select>

                <button class="btn btn-outline-danger" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </form>
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

    <div class="container mt-4">
        <div class="header-with-button">
            <h2>Circolari del Gruppo</h2>
            <?php
                if ($_SESSION['ident'] == 'ADMIN1'):
            ?>
            <a href="editCircolari.php?id=<?php echo $_GET['id']?>" class="add-button" title="Aggiungi utente"><i class="fas fa-plus"></i></a>
            <?php
                endif;
            ?>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Categoria</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($response['circolari'])) {
                    foreach ($response['circolari'] as $circolare) {
                        echo "<tr><td>{$circolare['id_circolare']}</td><td>{$circolare['categoria_circolare']}</td><td>{$circolare['data_circolare']}</td>";

                        if ($_SESSION['ident'] == 'ADMIN1'){
                            echo "<td>
                            <button onclick=\"rimuoviElementoDalGruppo('circolare', '{$circolare['id_circolare']}', '{$_GET['id']}')\" class='btn btn-danger btn-sm'><i class='fas fa-minus'></i></button>
                            </td>";
                        }

                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>Nessuna circolare trovata</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <?php
        if ($_SESSION['ident'] == 'ADMIN1'):
        ?>

        <div class="header-with-button">
            <h2>Utenti del Gruppo</h2>
            <a href="editUtente.php?id=<?php echo $_GET['id']?>" class="add-button" title="Aggiungi utente"><i class="fas fa-plus"></i></a>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Cognome</th>
                    <th>Data di nascita</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($response['utenti'])) {
                    foreach ($response['utenti'] as $utente) {
                        echo "<tr><td>{$utente['id_utente']}</td><td>{$utente['nome_utente']}</td><td>{$utente['cognome_utente']}</td><td>{$utente['data_nascita']}</td><td>
                                <button onclick=\"rimuoviElementoDalGruppo('utente', '{$utente['id_utente']}', '{$_GET['id']}')\" class='btn btn-danger btn-sm'><i class='fas fa-minus'></i></button>
                                </td></tr>";


                    }
                } else {
                    echo "<tr><td colspan='5'>Nessun utente trovato</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <?php
            endif;
        ?>
    </div>
</body>
</html>
