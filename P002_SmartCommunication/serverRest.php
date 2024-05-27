<?php
    session_start();
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once 'db.php';
    require_once 'class\Classeviva.php';
    use Classeviva\Studenti\Classeviva;

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'POST':
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action'])) {
                $action = $_GET['action'];
                switch ($action) {
                    case 'login':
                        login();
                        break;
                    case 'insert':
                        addUser();
                        break;
                    case 'user': //per associare le circolari dell'utente
                        UserData();
                        break;
                    case 'noticeBoard':
                        noticeBoard();
                        break;
                    case 'printCirc':
                        printCirc();
                        break;
                    case 'circDetail':
                        circDetail();
                        break;
                    case 'visualizzati':
                        printVisualizzati();
                        break;
                    case 'favourite':
                        editFavourite();
                        break;

                    case 'printFavourite';
                        printFavourite();
                        break;  
                    
                    case 'newGroup':
                        newGroup();
                        break;

                    case 'prtintGroup':
                        prtintGroup();
                        break;

                    case 'search':
                        search();
                        break;

                    default:
                        http_response_code(405);
                        echo json_encode(['error' => 'Action Not Allowed']);
                        break;
                }
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Method Not Allowed or Action Missing']);
            }
            break;

        case 'GET':
            if (isset($_GET['action'])) {
                $action = $_GET['action'];
                switch ($action) {
                    case 'getGruppi':
                        getGruppi();
                        break;
                    case 'groupDetail':
                        groupDetail();
                        break;
                    case 'getAllCirc':
                        getAllCirc();
                        break;    
                    case 'getAllUser':
                        getAllUser();
                        break;    
                    
                    case 'printMod':
                        printMod();
                        break;    
                    default:
                        http_response_code(405);
                        echo json_encode(['error' => 'Action Not Allowed']);
                        break;
                }
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Action Missing']);
            }
        break;
        case 'PUT':
            if (isset($_GET['action'])) {
                $action = $_GET['action'];
                switch ($action) {
                    case 'addCirc':
                        addCirc();
                        break;   
                    case 'addUtente':
                        addUtente();
                        break;     
                    default:
                        http_response_code(405);
                        echo json_encode(['error' => 'Action Not Allowed']);
                        break;
                }
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Action Missing']);
            }
            break;
        case 'DELETE':
            if (isset($_GET['action'])) {
                $action = $_GET['action'];
                switch ($action) {
                    case 'rimuoviDalGruppo':
                        rimuoviDalGruppo();
                        break;
                    case 'deleteGroup':
                        deleteGroup();
                        break;        
                    default:
                        http_response_code(405);
                        echo json_encode(['error' => 'Action Not Allowed']);
                        break;
                }
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Action Missing']);
            }
            break;    
        default:
            header('HTTP/1.1 405 Method Not Allowed');
            echo json_encode(['error' => 'Method Not Allowed']);
            break;
    }
    



    function login(){
        if (!isset($_POST['username']) || !isset($_POST['password'])) {
            http_response_code(400);
            echo 'Username e password sono obbligatori';
            return;
        }
    
        $username = $_POST['username'];
        $password = $_POST['password'];
        
        if($username == 'admin' && $password == 'cisco'){
            $responseLogin = [
                "ident" => "ADMIN1",
                "firstName" => "admin",
                "lastName" => "gestione",
                "showPwdChangeReminder" => false,
                "tokenAP" => "ADMINAP",
                "token" => "ADMINTOKEN",
                "release" => "2024-04-29T11:05:10+02:00",
                "expire" => "2025-04-29T12:35:10+02:00"
            ];
            header('Content-Type: application/json');
            header('Access-Control-Allow-Origin: *');
            echo json_encode($responseLogin);
        } else {
            try {
                $classeviva = new Classeviva();
                $responseLogin = $classeviva ->login($username,$password);
                header('Content-Type: application/json');
                header('Access-Control-Allow-Origin: *');
                echo $responseLogin;
            } catch (Exception $e) {
                $responseLogin = ['error' => $e->getMessage()];
                header('Content-Type: application/json');
                http_response_code(500);
                header('Access-Control-Allow-Origin: *');
                echo $responseLogin;
            }
        }
        
    }

    function addUser(){
        $mysqli = getMysqli();
        $ident = $mysqli->real_escape_string($_POST['ident']);
        $token = $mysqli->real_escape_string($_POST['token']);
        $firstName = $mysqli->real_escape_string($_POST['firstName']);
        $lastName = $mysqli->real_escape_string($_POST['lastName']);

        
        if($ident == 'ADMIN1'){
            $sql = "INSERT INTO utente (id_utente, nome_utente, cognome_utente, data_nascita) VALUES ('$ident', '$firstName', '$lastName', '104-06-09')";
            if ($mysqli->query($sql) === TRUE) {
                echo json_encode(['status' => 'success', 'message' => 'Utente inserito con successo']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Errore durante lo inserimento: ' . $mysqli->error]);
            }
        
            $mysqli->close();
        } else {
            $token = replaceSpacesWithPlus($token);

            try {
                $classeviva = new Classeviva();

                $status = $classeviva->card(removeLetters($ident), $token);

                $cardInfo = json_decode($status, true);

                if (!isset($cardInfo['card']) || !isset($cardInfo['card']['birthDate'])) {
                    echo json_encode(['status' => 'error', 'message' => 'Invalid card data']);
                    exit;
                }



                $birthDate = $mysqli->real_escape_string($cardInfo['card']['birthDate']);
                header('Content-Type: application/json');
                header('Z-Dev-Apikey: Tg1NWEwNGIgIC0K');
                header('Z-Auth-Token: ' . $token);

            } catch (Exception $e) {
                $response = ['error' => $e->getMessage()];
                header('Content-Type: application/json');
                http_response_code(500);
                header('Access-Control-Allow-Origin: *');

            }
        
            $sql = "INSERT INTO utente (id_utente, nome_utente, cognome_utente, data_nascita) VALUES ('$ident', '$firstName', '$lastName', '$birthDate')";
            if ($mysqli->query($sql) === TRUE) {
                echo json_encode(['status' => 'success', 'message' => 'Utente inserito con successo']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Errore durante lo inserimento: ' . $mysqli->error]);
            }
        
            $mysqli->close();
        }
        
        

    }

    function UserData(){
        $ident = $_POST['ident'];
                
                $mysqli = getMysqli();
                $query = "SELECT id_utente, nome_utente, cognome_utente, data_nascita FROM utente WHERE id_utente = '".$mysqli->real_escape_string($ident)."';";
            
                $result = $mysqli->query($query);
                
                if ($result) {
                    if ($user = $result->fetch_assoc()) {
                        echo json_encode($user);
                    } else {
                        echo json_encode(['error' => 'User not found']);
                    }
                } else {
                    echo json_encode(['error' => 'Database query failed']);
                }
                $mysqli->close();
                
    }


    function noticeBoard(){
            $ident = $_POST['ident'];
            $token = $_POST['token'];
            $token = replaceSpacesWithPlus($token);
        
            try {
                $classeviva = new Classeviva();
                $response = $classeviva->noticeBoard(removeLetters($ident), $token);
                // Decodifica la risposta JSON
                $items = json_decode($response, true)['items'];
            
                $mysqli = getMysqli();
            
                foreach ($items as $item) {
                    // Controlla se la circolare esiste già
                    $checkQuery = $mysqli->prepare("SELECT id_circolare FROM circolare WHERE id_circolare = ?");
                    $checkQuery->bind_param("s", $item['cntTitle']);  // Usando cntTitle come identificativo unico, sostituire con un vero ID se disponibile
                    $checkQuery->execute();
                    $result = $checkQuery->get_result();
                    $checkQuery->close();
            
                    if ($result->num_rows == 0) {
                        // Inserisci la circolare se non esiste
                        $stmt = $mysqli->prepare("INSERT INTO circolare (id_circolare, categoria_circolare, data_circolare) VALUES (?, ?, ?)");
                        $stmt->bind_param("sss", 
                            $item['cntTitle'], 
                            $item['cntCategory'], 
                            $item['cntValidFrom']
                        );
            
                        if (!$stmt->execute()) {
                            echo "Errore durante l'inserimento della circolare: " . $stmt->error;
                        }
                        $stmt->close();
                    }
            
                    // Gestione dell'associazione circolare-utente
                    $checkAssocQuery = $mysqli->prepare("SELECT * FROM circolare_utente WHERE id_circolare = ? AND id_utente = ?");
                    $checkAssocQuery->bind_param("ss", $item['cntTitle'], $ident);
                    $checkAssocQuery->execute();
                    $assocResult = $checkAssocQuery->get_result();
                    $checkAssocQuery->close();
            
                    if ($assocResult->num_rows == 0) {
                        $query = $mysqli->prepare("INSERT INTO circolare_utente (id_circolare, id_utente) VALUES (?, ?)");
                        $query->bind_param("ss", 
                            $item['cntTitle'], 
                            $ident
                        );
            
                        if (!$query->execute()) {
                            echo "Errore durante l'inserimento nella circolare_utente: " . $query->error;
                        }
                        $query->close();
                    }


                }
            
                echo json_encode(['status' => 'success', 'message' => 'Inserimento completato con successo']);
            } catch (Exception $e) {
                $response = ['error' => $e->getMessage()];
                header('Content-Type: application/json');
                http_response_code(500);
                header('Access-Control-Allow-Origin: *');
                echo json_encode($response);
            }
            
            $conn = getMysqli();
            $directory = "moduli/moduli/pdf";

            // Leggi i nomi dei file dalla cartella
            $files = scandir($directory);

            // Loop attraverso i file
            foreach ($files as $file) {
                // Ignora le voci speciali "." e ".."
                if ($file == "." || $file == "..") {
                    continue;
                }

                // Rimuovi l'estensione ".pdf" dal nome del file
                $filename = pathinfo($file, PATHINFO_FILENAME);

                // Inserisci il nome del file nel database
                $sql = "INSERT INTO modulo (id_modulo) VALUES ('$filename')";

                if ($conn->query($sql) === TRUE) {
                    echo "Record inserito con successo per il file: " . $filename . "<br>";
                } else {
                    echo "Errore durante l'inserimento del record: " . $conn->error . "<br>";
                }
            }

            $conn->close();

            try {
                $classeviva = new Classeviva();
                $response = $classeviva->noticeBoard(removeLetters($ident), $token);
                $items = json_decode($response, true)['items'];
            
                $mysqli = getMysqli();  // Assicurati che questa funzione esista e crei una connessione al database
            
                foreach ($items as $item) {
                    // Verifica se la coppia id_circolare e id_utente esiste già
                    $checkQuery = $mysqli->prepare("SELECT * FROM circolare_utente WHERE id_circolare = ? AND id_utente = ?");
                    $checkQuery->bind_param("ss", $item['cntTitle'], $ident);
                    $checkQuery->execute();
                    $result = $checkQuery->get_result();
                    
                    if ($result->num_rows == 0) {
                        // Solo se non esiste, inserisci la nuova coppia
                        $insertQuery = $mysqli->prepare("INSERT INTO circolare_utente (id_circolare, id_utente) VALUES (?, ?)");
                        $insertQuery->bind_param("ss", $item['cntTitle'], $ident);
            
                        if (!$insertQuery->execute()) {
                            echo "Errore durante l'inserimento: " . $insertQuery->error;
                        }
                        $insertQuery->close();
                    }
                    $checkQuery->close();
                }
            
                $mysqli->close();
                echo json_encode(['status' => 'success', 'message' => 'Inserimento completato con successo']);
            } catch (Exception $e) {
                $response = ['error' => $e->getMessage()];
                header('Content-Type: application/json');
                http_response_code(500);
                header('Access-Control-Allow-Origin: *');
                echo json_encode($response);
            }
    }    

    function printCirc(){
        $ident = $_POST['ident'];
                
        // You would have some kind of token validation here
        
        // Fetch user data from the database
        $mysqli = getMysqli();
        // Encapsulate $ident in single quotes
        $query = "SELECT circolare_utente.*, circolare.*, 
                CASE WHEN utente_preferiti.id_utente IS NOT NULL THEN TRUE ELSE FALSE END AS preferiti
        FROM circolare_utente
        INNER JOIN circolare ON circolare.id_circolare = circolare_utente.id_circolare
        LEFT JOIN utente_preferiti ON utente_preferiti.id_circolare = circolare.id_circolare AND utente_preferiti.id_utente = circolare_utente.id_utente
        WHERE circolare_utente.id_utente = '".$mysqli->real_escape_string($ident)."'";

        $result = $mysqli->query($query);

        if ($result) {
            $circolari = [];  // Array to hold all circulares
            while ($row = $result->fetch_assoc()) {
                $circolari[] = $row;  // Append each row to the array
            }
            if (count($circolari) > 0) {
                echo json_encode($circolari);  // Encode the array of circulares to JSON
            } else {
                echo json_encode(['error' => 'No circulares found']);  // Return an error if no circulares
            }
        } else {
            echo json_encode(['error' => 'Database query failed: ' . $mysqli->error]);  // Display error if query failed
        }
        $mysqli->close();
    }

    function circDetail(){
        $ident = $_POST['ident'];
        $token = $_POST['token'];
        $token = replaceSpacesWithPlus($token);
       
                

                try {
                    $classeviva = new Classeviva();
                    $response = $classeviva->noticeBoard(removeLetters($ident), $token);
                    $items = json_decode($response, true)['items'];
            
                    $mysqli = getMysqli();
            
                    foreach ($items as $item) {
                        // Controlla se la circolare esiste gia

                        if($item['cntTitle'] == $_POST['cntTitle'] && isset($item['attachments']) && is_array($item['attachments']) && count($item['attachments']) > 0){
            
                            $mysqli = getMysqli(); 
                            $checkQuery = $mysqli->prepare("SELECT * FROM utente_visualizzati WHERE id_utente = ? AND id_circolare = ?");
                            $checkQuery->bind_param("ss", $ident, $item['cntTitle']);
                            $checkQuery->execute();
                            $result = $checkQuery->get_result();
                                    
                            if ($result->num_rows == 0) {
                                        // Solo se non esiste, inserisci la nuova coppia
                                $insertQuery = $mysqli->prepare("INSERT INTO utente_visualizzati (id_utente, id_circolare) VALUES (?, ?)");
                                $insertQuery->bind_param("ss", $ident, $item['cntTitle']);
                            
                                if (!$insertQuery->execute()) {
                                    echo "Errore durante l'inserimento: " . $insertQuery->error;
                                }
                                    $insertQuery->close();
                            }
                            $checkQuery->close();

                            $attachment = $item['attachments'][0]; // Accedi al primo allegato
            
                            if(isset($attachment['attachNum'])) {
                                try {
                                    $classeviva1 = new Classeviva();
                                    $response1 = $classeviva1->noticeBoard(removeLetters($ident), $token, 1, $attachment['attachNum'], $item['evtCode'], $item['pubId']);
                                    echo json_encode($response1);
                                } catch (Exception $e) {
                                    $response = ['error' => $e->getMessage()];
                                    header('Content-Type: application/json');
                                    http_response_code(500);
                                    header('Access-Control-Allow-Origin: *');
                                    echo json_encode($response);
                                }
                            }
                        }


                    }
            
                    
                } catch (Exception $e) {
                    $response = ['error' => $e->getMessage()];
                    header('Content-Type: application/json');
                    http_response_code(500);
                    header('Access-Control-Allow-Origin: *');
                    echo json_encode($response);
                }
    }

    function printVisualizzati(){
        $ident = $_POST['ident'];
                
                // You would have some kind of token validation here
                
                // Fetch user data from the database
                $mysqli = getMysqli();
                // Encapsulate $ident in single quotes
                $query = "SELECT * FROM `utente_visualizzati` 
                INNER JOIN circolare ON circolare.id_circolare = utente_visualizzati.id_circolare
                WHERE utente_visualizzati.id_utente = '".$mysqli->real_escape_string($ident)."'";

                $result = $mysqli->query($query);

                if ($result) {
                    $circolari = [];  // Array to hold all circulares
                    while ($row = $result->fetch_assoc()) {
                        $circolari[] = $row;  // Append each row to the array
                    }
                    if (count($circolari) > 0) {
                        echo json_encode($circolari);  // Encode the array of circulares to JSON
                    } else {
                        echo json_encode(['error' => 'No circulares found']);  // Return an error if no circulares
                    }
                } else {
                    echo json_encode(['error' => 'Database query failed: ' . $mysqli->error]);  // Display error if query failed
                }
                $mysqli->close();


    }

    function editFavourite(){
        $ident = $_POST['ident'];
        
        $circId = $_POST['cntTitle'];


            
        $mysqli = getMysqli(); 
        $checkQuery = $mysqli->prepare("SELECT * FROM utente_preferiti WHERE id_utente = ? AND id_circolare = ?");
        $checkQuery->bind_param("ss", $ident, $circId);
        $checkQuery->execute();
        $result = $checkQuery->get_result();
                                    
            if ($result->num_rows == 0) {
                                        // Solo se non esiste, inserisci la nuova coppia
                $insertQuery = $mysqli->prepare("INSERT INTO utente_preferiti (id_utente, id_circolare) VALUES (?, ?)");
                $insertQuery->bind_param("ss", $ident, $circId);
                            
            if (!$insertQuery->execute()) {
                echo "Errore durante l'inserimento: " . $insertQuery->error;
            }
                $insertQuery->close();
            } else {
                $insertQuery = $mysqli->prepare("DELETE FROM utente_preferiti WHERE id_utente = ? AND id_circolare = ?");
                $insertQuery->bind_param("ss", $ident, $circId);
                            
                if (!$insertQuery->execute()) {
                    echo "Errore durante il toglimento: " . $insertQuery->error;
                }
            }
            $checkQuery->close();


    }

    function printFavourite(){
        $ident = $_POST['ident'];

        // You would have some kind of token validation here

        // Fetch user data from the database
        $mysqli = getMysqli();

        $query = "SELECT circolare_utente.*, circolare.*,
                CASE WHEN utente_preferiti.id_utente IS NOT NULL THEN TRUE ELSE FALSE END AS preferiti
                FROM circolare_utente
                INNER JOIN circolare ON circolare.id_circolare = circolare_utente.id_circolare
                LEFT JOIN utente_preferiti ON utente_preferiti.id_circolare = circolare.id_circolare 
                                            AND utente_preferiti.id_utente = circolare_utente.id_utente
                WHERE circolare_utente.id_utente = '".$mysqli->real_escape_string($ident)."' 
                AND utente_preferiti.id_utente IS NOT NULL";

        $result = $mysqli->query($query);

        if ($result) {
            $circolari = [];  // Array to hold all circulares
            while ($row = $result->fetch_assoc()) {
                $circolari[] = $row;  // Append each row to the array
            }
            if (count($circolari) > 0) {
                echo json_encode($circolari);  // Encode the array of circulares to JSON
            } else {
                echo json_encode(['error' => 'No circulares found']);  // Return an error if no circulares
            }
        } else {
            echo json_encode(['error' => 'Database query failed: ' . $mysqli->error]);  // Display error if query failed
        }
        $mysqli->close();

    }

   
    
    function newGroup() {
 
        if (isset($_POST['categoria']) && isset($_POST['nome'])) {
            $categoria = $_POST['categoria'];
            $nome = $_POST['nome'];
            $groupId = generateRandomString(); 
    
            $mysqli = getMysqli();
            $stmt = $mysqli->prepare("INSERT INTO gruppo (id_gruppo, categoria_gruppo, nome_gruppo) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $groupId, $categoria, $nome);
    
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Group created successfully', 'id' => $groupId]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error inserting group: ' . $mysqli->error]);
            }
    
            $stmt->close();
            $mysqli->close();
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Missing category or name']);
        }
    }
    
    
    function getGruppi() {
        $mysqli = getMysqli(); // Assicurati che questa funzione ritorni un oggetto mysqli valido
        $query = "SELECT * FROM gruppo";
        $result = $mysqli->query($query);
    
        if ($result) {
            $gruppi = [];
            while ($row = $result->fetch_assoc()) {
                $gruppi[] = $row;
            }
            echo json_encode($gruppi);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database query failed: ' . $mysqli->error]);
        }
        $mysqli->close();
    }


    function groupDetail() {
        if (!isset($_GET['id'])) {
            echo json_encode(['error' => 'No group ID specified']);
            return;
        }
    
        $groupId = $_GET['id'];
        $mysqli = getMysqli();
        $response = [
            'circolari' => [],
            'utenti' => []
        ];
        
        // Query per circolari del gruppo
        $circolariQuery = "SELECT circolare.* FROM circolare_gruppo
                           JOIN circolare ON circolare.id_circolare = circolare_gruppo.id_circolare
                           WHERE circolare_gruppo.id_gruppo = '".$groupId."'";
        if ($stmt = $mysqli->prepare($circolariQuery)) {
           
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $response['circolari'][] = $row;
            }
            $stmt->close();
        } else {
            echo json_encode(['error' => 'Database query failed: ' . $mysqli->error]);
            return;
        }
    
        // Query per utenti del gruppo
        $utentiQuery = "SELECT utente.* FROM utente_gruppo
                        JOIN utente ON utente.id_utente = utente_gruppo.id_utente
                        WHERE utente_gruppo.id_gruppo = '".$groupId."'";
        if ($stmt = $mysqli->prepare($utentiQuery)) {

            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $response['utenti'][] = $row;
            }
            $stmt->close();
        } else {
            echo json_encode(['error' => 'Database query failed: ' . $mysqli->error]);
            return;
        }
    
        $mysqli->close();
        echo json_encode($response);
    }

    function getAllCirc() {
        $mysqli = getMysqli(); // Assume getMysqli returns a mysqli connection object
    
        if (!isset($_GET['id'])) {
            echo json_encode(['error' => 'No group ID specified']);
            return;
        }
    
        $groupId = $_GET['id'];

        $query = "SELECT c.*
        FROM circolare c
        LEFT JOIN circolare_gruppo cg ON c.id_circolare = cg.id_circolare AND cg.id_gruppo = '".$groupId."'
        WHERE cg.id_circolare IS NULL;";  // Define the query to select all circulars
        $result = $mysqli->query($query);  // Execute the query
    
        if ($result) {
            $circolari = [];
            while ($row = $result->fetch_assoc()) {
                $circolari[] = $row;  // Store each row in the array
            }
            $mysqli->close();
            echo json_encode($circolari);  // Return all circulars as JSON
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Database query failed: ' . $mysqli->error]);  // Return error if query failed
        }
    }
    
    function addCirc(){
        $data = json_decode(file_get_contents('php://input'), true);
        $mysqli = getMysqli();
        
        if (!$data) {
            echo json_encode(['error' => 'Invalid input']);
            return;
        }
        
        $idCircolare = $mysqli->real_escape_string($data['id_circolare']);
        $idGruppo = $mysqli->real_escape_string($data['id_gruppo']);
        
        $query = "INSERT INTO circolare_gruppo (id_circolare, id_gruppo) VALUES ('".$idCircolare."', '".$idGruppo."')";
        $result = $mysqli->query($query);
        echo $result;
        $mysqli->close();
    }

    function rimuoviDalGruppo() {
        $tipo = $_GET['tipo'];
        $idElemento = $_GET['idElemento'];
        $idGruppo = $_GET['idGruppo'];
        $mysqli = getMysqli();
    
        if ($tipo == 'circolare') {
            $query = "DELETE FROM circolare_gruppo WHERE id_circolare = '".$idElemento."' AND id_gruppo = '".$idGruppo."'";
        } elseif ($tipo == 'utente') {
            $query = "DELETE FROM utente_gruppo WHERE id_utente = '".$idElemento."' AND id_gruppo = '".$idGruppo."'";
        } else {
            echo json_encode(['error' => 'Tipo non valido']);
            return;
        }
    
        $result = $mysqli->query($query);
        echo $result;
        $mysqli->close();
    }

    function getAllUser(){
        $mysqli = getMysqli(); // Assume getMysqli returns a mysqli connection object
    
        if (!isset($_GET['id'])) {
            echo json_encode(['error' => 'No group ID specified']);
            return;
        }
    
        $groupId = $_GET['id'];

        $query = "SELECT u.*
        FROM utente u
        LEFT JOIN utente_gruppo ug ON u.id_utente = ug.id_utente AND ug.id_gruppo = '".$groupId."'
        WHERE ug.id_utente IS NULL;";  // Define the query to select all circulars
        $result = $mysqli->query($query);  // Execute the query
    
        if ($result) {
            $utenti = [];
            while ($row = $result->fetch_assoc()) {
                $utenti[] = $row;  // Store each row in the array
            }
            $mysqli->close();
            echo json_encode($utenti);  // Return all circulars as JSON
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Database query failed: ' . $mysqli->error]);  // Return error if query failed
        }

    }
    
    function addUtente(){
        $data = json_decode(file_get_contents('php://input'), true);
        $mysqli = getMysqli();
        
        if (!$data) {
            echo json_encode(['error' => 'Invalid input']);
            return;
        }
        
        $idUtente = $mysqli->real_escape_string($data['id_utente']);
        $idGruppo = $mysqli->real_escape_string($data['id_gruppo']);
        
        $query = "INSERT INTO utente_gruppo (id_utente, id_gruppo) VALUES ('".$idUtente."', '".$idGruppo."')";
        $result = $mysqli->query($query);
        echo $result;
        $mysqli->close();
    }

    function deleteGroup() {
        $idGruppo = $_GET['idGruppo'];
        $mysqli = getMysqli();
    
        // Elimina le relazioni circolare-gruppo
        $mysqli->query("DELETE FROM circolare_gruppo WHERE id_gruppo = '".$idGruppo."'");
        // Elimina le relazioni utente-gruppo
        $mysqli->query("DELETE FROM utente_gruppo WHERE id_gruppo = '".$idGruppo."'");
        // Elimina il gruppo
        $result = $mysqli->query("DELETE FROM gruppo WHERE id_gruppo = '".$idGruppo."'");
    
        if ($result) {
            echo json_encode(['success' => 'Gruppo eliminato con successo']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Errore nell\'eliminazione del gruppo: ' . $mysqli->error]);
        }
    
        $mysqli->close();
    }

    function prtintGroup(){
        $ident = $_POST['ident'];
                
        // You would have some kind of token validation here
        
        // Fetch user data from the database
        $mysqli = getMysqli();
        // Encapsulate $ident in single quotes
        $query = "SELECT * FROM gruppo
        INNER JOIN utente_gruppo ON gruppo.id_gruppo = utente_gruppo.id_gruppo
        WHERE utente_gruppo.id_utente = '".$ident."'";

        $result = $mysqli->query($query);

        if ($result) {
            $gruppi = [];  // Array to hold all circulares
            while ($row = $result->fetch_assoc()) {
                $gruppi[] = $row;  // Append each row to the array
            }
            if (count($gruppi) > 0) {
                echo json_encode($gruppi);  // Encode the array of circulares to JSON
            } else {
                echo json_encode(['error' => 'No circulares found']);  // Return an error if no circulares
            }
        } else {
            echo json_encode(['error' => 'Database query failed: ' . $mysqli->error]);  // Display error if query failed
        }
        $mysqli->close();
    }
    
    function printMod(){
        $mysqli = getMysqli();
        $query = "SELECT * FROM modulo";
        $result = $mysqli->query($query);

        if ($result) {
            $moduli = [];  // Array to hold all circulares
            while ($row = $result->fetch_assoc()) {
                $moduli[] = $row;  // Append each row to the array
            }
            if (count($moduli) > 0) {
                echo json_encode($moduli);  // Encode the array of circulares to JSON
            } else {
                echo json_encode(['error' => 'No moduli found']);  // Return an error if no circulares
            }
        } else {
            echo json_encode(['error' => 'Database query failed: ' . $mysqli->error]);  // Display error if query failed
        }

    }


    function search(){
        $ident = $_POST['ident'] ?? null;
        $searchTerm = $_POST['searchTerm'] ?? null;
        $filterBy = $_POST['filterBy'] ?? null;
        $orderBy = $_POST['orderBy'] ?? null;

        switch ($_POST['page']) {
            case 'circolari': //FUNZIONA
                $mysqli = getMysqli();
                $query = "SELECT circolare_utente.*, circolare.*, CASE WHEN utente_preferiti.id_utente IS NOT NULL THEN TRUE ELSE FALSE END AS preferiti
                            FROM circolare_utente
                            INNER JOIN circolare ON circolare.id_circolare = circolare_utente.id_circolare
                            LEFT JOIN utente_preferiti ON utente_preferiti.id_circolare = circolare.id_circolare AND utente_preferiti.id_utente = circolare_utente.id_utente
                            WHERE circolare_utente.id_utente = '".$ident."' AND circolare.id_circolare LIKE '%".$searchTerm."%'";
                            
                if ($filterBy != null && $orderBy != null){
                    $query .= "ORDER BY circolare.".$filterBy." ".$orderBy."";
                }    

                $result = $mysqli->query($query);
        
                if ($result) {
                    $circolari = [];  // Array to hold all circulares
                    while ($row = $result->fetch_assoc()) {
                        $circolari[] = $row;  // Append each row to the array
                    }
                    if (count($circolari) > 0) {
                        echo json_encode($circolari);  // Encode the array of circulares to JSON
                    } else {
                        echo json_encode(['error' => 'No circulares found']);  // Return an error if no circulares
                    }
                } else {
                    echo json_encode(['error' => 'Database query failed: ' . $mysqli->error]);  // Display error if query failed
                }
                $mysqli->close();
                break;

            case 'preferiti': //FUNZIONA

                $mysqli = getMysqli();
                $query = "SELECT circolare_utente.*, circolare.*,
                        CASE WHEN utente_preferiti.id_utente IS NOT NULL THEN TRUE ELSE FALSE END AS preferiti
                        FROM circolare_utente
                        INNER JOIN circolare ON circolare.id_circolare = circolare_utente.id_circolare
                        LEFT JOIN utente_preferiti ON utente_preferiti.id_circolare = circolare.id_circolare 
                                                    AND utente_preferiti.id_utente = circolare_utente.id_utente
                        WHERE circolare_utente.id_utente = '".$ident."' AND utente_preferiti.id_utente IS NOT NULL AND circolare.id_circolare LIKE '%".$searchTerm."%'";
                            
                if ($filterBy != null && $orderBy != null){
                    $query .= "ORDER BY circolare.".$filterBy." ".$orderBy."";
                }    

                $result = $mysqli->query($query);
        
                if ($result) {
                    $circolari = [];  // Array to hold all circulares
                    while ($row = $result->fetch_assoc()) {
                        $circolari[] = $row;  // Append each row to the array
                    }
                    if (count($circolari) > 0) {
                        echo json_encode($circolari);  // Encode the array of circulares to JSON
                    } else {
                        echo json_encode(['error' => 'No circulares found']);  // Return an error if no circulares
                    }
                } else {
                    echo json_encode(['error' => 'Database query failed: ' . $mysqli->error]);  // Display error if query failed
                }
                $mysqli->close();
                break;
            
            case 'visualizzati':

                $mysqli = getMysqli();
                // Encapsulate $ident in single quotes
                $query = "SELECT * FROM `utente_visualizzati` 
                INNER JOIN circolare ON circolare.id_circolare = utente_visualizzati.id_circolare
                WHERE utente_visualizzati.id_utente = '".$ident."' AND circolare.id_circolare LIKE '%".$searchTerm."%'";

                if ($filterBy != null && $orderBy != null){
                    $query .= "ORDER BY circolare.".$filterBy." ".$orderBy."";
                }    

                $result = $mysqli->query($query);

                if ($result) {
                    $circolari = [];  // Array to hold all circulares
                    while ($row = $result->fetch_assoc()) {
                        $circolari[] = $row;  // Append each row to the array
                    }
                    if (count($circolari) > 0) {
                        echo json_encode($circolari);  // Encode the array of circulares to JSON
                    } else {
                        echo json_encode(['error' => 'No circulares found']);  // Return an error if no circulares
                    }
                } else {
                    echo json_encode(['error' => 'Database query failed: ' . $mysqli->error]);  // Display error if query failed
                }
                $mysqli->close();

                break;
            case 'gruppiDetail':
                
                if (!isset($_POST['groupID'])) {
                    echo json_encode(['error' => 'No group ID specified']);
                    return;
                }
                
                $groupId = $_POST['groupID'];
                $searchBy = $_POST['searchBy'];
                $mysqli = getMysqli();

                $response = [
                    'circolari' => [],
                    'utenti' => []
                ];
                
                $isUtente = false;
                // Query per circolari del gruppo
                $query = "SELECT circolare.* FROM circolare_gruppo
                                   JOIN circolare ON circolare.id_circolare = circolare_gruppo.id_circolare
                                   WHERE circolare_gruppo.id_gruppo = '".$groupId."'";
                if ($searchBy == 'Circolare'){
                    $query .= "AND circolare.id_circolare LIKE '%".$searchTerm."%' OR circolare.categoria_circolare LIKE '%".$searchTerm."%'";
                }
                    
                $allowedFilters = ['id_circolare', 'categoria_circolare', 'data_circolare'];
                if (in_array($filterBy, $allowedFilters) && $orderBy != null){
                    $query .= "ORDER BY circolare.".$filterBy." ".$orderBy."";
                } else {
                    $isUtente = true;
                }                       
                if ($stmt = $mysqli->prepare($query)) {
                   
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()) {
                        $response['circolari'][] = $row;
                    }
                    $stmt->close();
                } else {
                    echo json_encode(['error' => 'Database query failed: ' . $mysqli->error]);
                    return;
                }
            
                // Query per utenti del gruppo
                $query = "SELECT utente.* FROM utente_gruppo
                                JOIN utente ON utente.id_utente = utente_gruppo.id_utente
                                WHERE utente_gruppo.id_gruppo = '".$groupId."'";
                if ($searchBy == 'Utente'){
                    $query .= "AND utente.id_utente LIKE '%".$searchTerm."%' OR CONCAT(utente.nome_utente, ' ', utente.cognome_utente) LIKE '%".$searchTerm."%'";
                }      
                if ($filterBy != null && $orderBy != null && $isUtente){
                    $query .= "ORDER BY utente.".$filterBy." ".$orderBy."";
                }               
                if ($stmt = $mysqli->prepare($query)) {
        
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    while ($row = $result->fetch_assoc()) {
                        $response['utenti'][] = $row;
                    }
                    $stmt->close();
                } else {
                    echo json_encode(['error' => 'Database query failed: ' . $mysqli->error]);
                    return;
                }
            
                $mysqli->close();
                echo json_encode($response);
                break;   
            
            case 'circolariAdd':
                $mysqli = getMysqli(); // Assume getMysqli returns a mysqli connection object
    
                if (!isset($_POST['groupID'])) {
                    echo json_encode(['error' => 'No group ID specified']);
                    return;
                }
            
                $groupId = $_POST['groupID'];

                $query = "SELECT c.*
                FROM circolare c
                LEFT JOIN circolare_gruppo cg ON c.id_circolare = cg.id_circolare AND cg.id_gruppo = '".$groupId."'
                WHERE cg.id_circolare IS NULL AND c.id_circolare LIKE '%".$searchTerm."%'";  // Define the query to select all circulars
                if ($filterBy != null && $orderBy != null){
                    $query .= "ORDER BY c.".$filterBy." ".$orderBy."";
                }    
                $result = $mysqli->query($query);  // Execute the query
            
                if ($result) {
                    $circolari = [];
                    while ($row = $result->fetch_assoc()) {
                        $circolari[] = $row;  // Store each row in the array
                    }
                    $mysqli->close();
                    echo json_encode($circolari);  // Return all circulars as JSON
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Database query failed: ' . $mysqli->error]);  // Return error if query failed
                }
                break; 
            
            case 'moduli':
                $mysqli = getMysqli();
                $query = "SELECT * FROM modulo WHERE id_modulo LIKE '%".$searchTerm."%'";
                            
                if ($filterBy != null && $orderBy != null){
                    $query .= "ORDER BY ".$filterBy." ".$orderBy."";
                }    

                $result = $mysqli->query($query);
        
                if ($result) {
                    $circolari = [];  // Array to hold all circulares
                    while ($row = $result->fetch_assoc()) {
                        $circolari[] = $row;  // Append each row to the array
                    }
                    if (count($circolari) > 0) {
                        echo json_encode($circolari);  // Encode the array of circulares to JSON
                    } else {
                        echo json_encode(['error' => 'No circulares found']);  // Return an error if no circulares
                    }
                } else {
                    echo json_encode(['error' => 'Database query failed: ' . $mysqli->error]);  // Display error if query failed
                }
                $mysqli->close();
                
                break; 
            
            case 'utentiAdd':
                
                $mysqli = getMysqli(); // Assume getMysqli returns a mysqli connection object
    
                if (!isset($_POST['groupID'])) {
                    echo json_encode(['error' => 'No group ID specified']);
                    return;
                }
            
                $groupId = $_POST['groupID'];

                $query = "SELECT u.*
                FROM utente u
                LEFT JOIN utente_gruppo ug ON u.id_utente = ug.id_utente AND ug.id_gruppo = '".$groupId."'
                WHERE ug.id_utente IS NULL AND u.id_utente LIKE '%".$searchTerm."%' OR CONCAT(u.nome_utente, ' ', u.cognome_utente) LIKE '%".$searchTerm."%'";  // Define the query to select all circulars
                if ($filterBy != null && $orderBy != null){
                    $query .= "ORDER BY u.".$filterBy." ".$orderBy."";
                }    
                $result = $mysqli->query($query);  // Execute the query
            
                if ($result) {
                    $circolari = [];
                    while ($row = $result->fetch_assoc()) {
                        $circolari[] = $row;  // Store each row in the array
                    }
                    $mysqli->close();
                    echo json_encode($circolari);  // Return all circulars as JSON
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Database query failed: ' . $mysqli->error]);  // Return error if query failed
                }
                break; 
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Action Not Allowed']);
                break;
        }
            
            
       
    }

    

    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    function removeLetters($string) {
        return preg_replace('/[a-zA-Z]/', '', $string);
    }

    function replaceSpacesWithPlus($string) {
        return str_replace(' ', '+', $string);
    }
?>