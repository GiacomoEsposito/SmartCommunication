<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    <title>Visualizzatore di Immagini</title>
    <style>
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
        /* Stili base per le immagini thumbnail */
        .thumbnail {
            width: 300px;  /* Dimensione più grande */
            height: auto;
            margin: 20px auto;  /* Centrato orizzontalmente */
            cursor: pointer;
            display: block;
            border: 5px solid black;  /* Bordi neri */
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.9);
        }

        .modal-content {
            margin: auto;
            display: block;
            width: 80%;
            max-width: 700px;
        }

        .close, .prev, .next {
            cursor: pointer;
            position: absolute;
            top: 50%;
            width: auto;
            padding: 16px;
            margin-top: -50px;
            color: white;
            font-weight: bold;
            font-size: 20px;
            transition: 0.6s ease;
        }

        .close {
            top: 15px;
            right: 35px;
        }

        .prev { left: 0; }
        .next { right: 0; }

        .centered {
            text-align: center;
        }

        .download-button {
            display: block;
            width: 200px;
            margin: 20px auto;
            background-color: red;
            color: white;
            text-align: center;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
        }

        /* Stili per l'immagine all'interno del modal */
        .modal-content {
            margin: auto;  /* Centra l'immagine orizzontalmente */
            display: block;
            width: 80%;  /* Larghezza massima dell'immagine */
            max-width: 700px;  /* Non supera i 700px */
        }

        /* Bottone per chiudere il modal */
        .close {
            position: absolute;
            top: 15px;
            right: 35px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
        }

        /* Stili per le frecce di navigazione */
        .prev, .next {
            cursor: pointer;
            position: absolute;
            top: 50%;
            width: auto;
            padding: 16px;
            margin-top: -50px;
            color: white;
            font-weight: bold;
            font-size: 20px;
            transition: 0.6s ease;
            user-select: none;  /* Impedisce la selezione del testo */
        }

        .prev {
            left: 0;
            border-radius: 3px 0 0 3px;
        }

        .next {
            right: 0;
            border-radius: 0 3px 3px 0;
        }

        /* Centratura delle thumbnails nel container */
        #gallery {
            display: flex;  /* Usa Flexbox */
            justify-content: center;  /* Allinea le immagini orizzontalmente al centro */
            align-items: center;  /* Allinea le immagini verticalmente al centro */
            height: 80vh;  /* Altezza del contenitore */
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

    <div class="centered">
        <h2>Anteprima</h2>
        <?php
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $directory = "moduli/png/";
            $files = glob($directory . $id . "-*.png");

            if (count($files) > 0) {
                // Mostra solo la prima immagine come anteprima
                echo '<img src="' . $files[0] . '" alt="Clicca per vedere di più" class="thumbnail" id="previewImage">';
                echo '<a href="moduli/moduli/pdf/' . $id . '.pdf" class="download-button" download>Download</a>';
            }
        } else {
            echo "Nessun ID specificato.";
        }
        ?>
    </div>

    <!-- Modal per visualizzare tutte le immagini -->
    <div id="myModal" class="modal">
        <span class="close">&times;</span>
        <button class="prev">&#10094;</button>
        <button class="next">&#10095;</button>
        <img class="modal-content" id="img01">
        <div id="caption"></div>
    </div>

    <script>
        var modal = document.getElementById("myModal");
        var imgModal = document.getElementById("img01");
        var close = document.getElementsByClassName("close")[0];
        var files = <?php echo json_encode($files); ?>;
        var currentIndex = 0;

        document.getElementById("previewImage").onclick = function() {
            modal.style.display = "block";
            imgModal.src = files[0];
            currentIndex = 0;
        };

        function changeImage(step) {
            currentIndex += step;
            if (currentIndex >= files.length) {
                currentIndex = 0;
            } else if (currentIndex < 0) {
                currentIndex = files.length - 1;
            }
            imgModal.src = files[currentIndex];
        }

        document.getElementsByClassName("prev")[0].onclick = function() {
            changeImage(-1);
        };

        document.getElementsByClassName("next")[0].onclick = function() {
            changeImage(1);
        };

        close.onclick = function() {
            modal.style.display = "none";
        };
    </script>
</body>
</html>
