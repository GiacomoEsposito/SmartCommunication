<?php
session_start();

// Verifica se l'utente è loggato
$loggedIn = isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'];

if (!$loggedIn) {
    header("Location: ../login.php");
    exit;
}

if ($_SESSION['ident'] != 'ADMIN1'){
    header("Location: ../index.php");
    exit;
}
// Inizializza l'array delle circolari
$gruppi = [];

if ($loggedIn) {
    $ch = curl_init();
    $url = 'http://localhost/P002_SmartCommunication/serverRest.php?action=getGruppi';
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);

    $response = curl_exec($ch);
    if (!$response) {
        $error_msg = curl_error($ch);
    } else {
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        function rimuoviGruppo(idGruppo) {
          
                var xhr = new XMLHttpRequest();
                xhr.open('DELETE', `http://localhost/P002_SmartCommunication/serverRest.php?action=deleteGroup&idGruppo=${idGruppo}`, true);
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
          <a class="navbar-brand mt-2 mt-lg-0" href="../index.php">
            <img
              src="..\image\logo.webp"
              class="navbar-logo"
              height="15"
              alt="MDB Logo"
              loading="lazy"
            />
          </a>

          <!-- Search form centered with mx-auto and specific width -->
          <form class="d-flex mx-auto" style="max-width: 300px;">
              <input class="form-control me-2" type="search" placeholder="Cerca" aria-label="Cerca">
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



<table class="table table-striped">
    <thead>
        <tr>
            <th>ID Gruppo</th>
            <th>Categoria</th>
            <th>Nome</th>
            <th>Azioni</th>
        </tr>
    </thead>
    <tbody>
      <?php foreach ($gruppi as $gruppo): ?>
      <tr>
        <td onclick="window.location.href='gruppiDetail.php?id=<?php echo $gruppo['id_gruppo']; ?>';">
              <?php echo htmlspecialchars($gruppo['id_gruppo']); ?>
          </td>
          <td onclick="window.location.href='gruppiDetail.php?id=<?php echo $gruppo['id_gruppo']; ?>';">
              <?php echo htmlspecialchars($gruppo['categoria_gruppo']); ?>
          </td>
          <td onclick="window.location.href='gruppiDetail.php?id=<?php echo $gruppo['id_gruppo']; ?>';">
              <?php echo htmlspecialchars($gruppo['nome_gruppo']); ?>
          </td>
          <td>
              <button class="btn btn-danger btn-sm" onclick="rimuoviGruppo('<?php echo $gruppo['id_gruppo']; ?>')">
                  <i class="fas fa-minus"></i>
              </button>
          </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
</table>




</body>
</html>
