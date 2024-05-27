<?php
function getMysqli() {
    $host = 'localhost';
    $db = 'smart_communication'; // Sostituisci con il nome del tuo database
    $user = 'root';
    $pass = ''; // La password di default per MySQL in XAMPP è vuota

    $mysqli = new mysqli($host, $user, $pass, $db);

    if ($mysqli->connect_error) {
        die('Errore di connessione (' . $mysqli->connect_error . ') '
            . $mysqli->connect_error);
    }

    return $mysqli;
}
?>