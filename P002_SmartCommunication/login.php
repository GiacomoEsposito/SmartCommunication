<?php
session_start();

// Verifica se l'utente è loggato
$loggedIn = isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'];

// Gestione del tentativo di login
if (isset($_POST['username']) && isset($_POST['password']) && !$loggedIn) {
    $ch_login = curl_init();
    $url_login = 'http://localhost/P002_SmartCommunication/serverRest.php/?action=login';
    curl_setopt($ch_login, CURLOPT_URL, $url_login);
    curl_setopt($ch_login, CURLOPT_POSTFIELDS, http_build_query(array('username' => $_POST['username'], 'password' => $_POST['password'])));
    curl_setopt($ch_login, CURLOPT_RETURNTRANSFER, true);
    $response_login = curl_exec($ch_login);

    $_SESSION['server_response'] = $response_login;
    $loginData = json_decode($response_login, true);

    if ($loginData && isset($loginData['ident']) && isset($loginData['token'])) {
        $_SESSION['loggedIn'] = true;
        $_SESSION['ident'] = $loginData['ident'];
        $_SESSION['firstName'] = $loginData['firstName'];
        $_SESSION['lastName'] = $loginData['lastName'];
        $_SESSION['token'] = $loginData['token'];
        $_SESSION['username'] = $loginData['username'];  // Salvare l'username nella sessione

        // Redirezione a index.php dopo il login riuscito
        header("Location: index.php");
        exit;
    } else {
        
        $_SESSION['error'] = $response_login;
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
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body class="vh-100" style="background-color: #9A616D;">
    <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col col-xl-10">
                <div class="card" style="border-radius: 1rem;">
                    <div class="row g-0">
                        <div class="col-md-6 col-lg-5 d-none d-md-block">
                            <img src="image/logo.webp"
                                alt="login form" class="img-fluid" style="border-radius: 1rem 0 0 1rem;" />
                        </div>
                        <div class="col-md-6 col-lg-7 d-flex align-items-center">
                            <div class="card-body p-4 p-lg-5 text-black">
                                <?php if (!$loggedIn): ?>
                                    <?php
                                        if (isset($_SESSION['error'])) {
                                            echo "<p>errore che non so cosa sia</p>";
                                            unset($_SESSION['error']);
                                        }
                                    ?>
                                <form method="post">
                                    <div class="d-flex align-items-center mb-3 pb-1">
                                        <i class="fas fa-cubes fa-2x me-3" style="color: #ff6219;"></i>
                                        <span class="h1 fw-bold mb-0">SmartCommunication</span>
                                    </div>


                                    <div class="form-outline mb-4">
                                        <label class="form-label" for="username">Username/Email</label>
                                        <input type="text" id="username" name="username" class="form-control form-control-lg" required>
                                        
                                    </div>

                                    <div class="form-outline mb-4">
                                        <label class="form-label" for="password">Password</label>
                                        <input type="password" id="password" name="password" class="form-control form-control-lg" required>
                                        
                                    </div>

                                    <div class="pt-1 mb-4">
                                        <button type="submit" class="btn btn-dark btn-lg btn-block">Login</button>
                                    </div>
                                </form>

                                <?php else: ?>
                                <h1>Benvenuto!</h1>
                                <p>Sei loggato come <?php echo $_SESSION['ident']; ?>.</p>
                                <p>Risposta del server:</p>
                                <pre><?php echo htmlspecialchars($_SESSION['server_response']); ?></pre>
                                <form method="post">
                                    <input type="hidden" name="logout" value="true">
                                    <button type="submit" class="btn btn-dark btn-lg btn-block">Logout</button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

