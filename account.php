<?php
session_start();

$is_ajax = isset($_POST['ajax']) && $_POST['ajax'] === 'true';

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'nothingiverse_db';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    if ($is_ajax) {
        echo json_encode(['success' => false, 'errors' => ['Errore connessione database']]);
        exit;
    } else {
        die("Connessione fallita: " . $conn->connect_error);
    }
}

// REGISTRAZIONE
if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $error = [];

    if (empty($username)) {
        $error[] = "Username obbligatorio";
    } elseif (strlen($username) < 3) {
        $error[] = "Username troppo corto (min 3 caratteri)";
    }

    if (empty($email)) {
        $error[] = "Email obbligatoria";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error[] = "Email non valida";
    }

    if (empty($password)) {
        $error[] = "Password obbligatoria";
    } elseif (strlen($password) < 8 || strlen($password) > 16) {
        $error[] = "La password deve essere di minimo 8 e massimo 16 caratteri";
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $error[] = "La password deve contenere almeno un carattere maiuscolo";
    } elseif (!preg_match('/[0-9]/', $password)) {
        $error[] = "La password deve contenere almeno un numero";
    } elseif (!preg_match('/[\@\£\$\!\?]/', $password)) {
        $error[] = "La password deve contenere almeno un carattere speciale (@£$!?)";
    } elseif ($password != $password_confirm) {
        $error[] = "Le password non coincidono";
    }

    if ($is_ajax) {
        if (!empty($error)) {
            echo json_encode(['success' => false, 'errors' => [$error[0]]]);
            exit;
        }

        $stmt = $conn->prepare("SELECT id FROM utenti WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            echo json_encode(['success' => false, 'errors' => ['Email già registrata']]);
            exit;
        }

        $stmt = $conn->prepare("SELECT id FROM utenti WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            echo json_encode(['success' => false, 'errors' => ['Username già in uso']]);
            exit;
        }

        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO utenti (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password_hash);

        if ($stmt->execute()) {
            $_SESSION['user_id'] = $conn->insert_id;
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            $_SESSION['logged_in'] = true;
            echo json_encode(['success' => true, 'username' => $username]);
        } else {
            echo json_encode(['success' => false, 'errors' => ['Errore durante la registrazione']]);
        }
        exit;
    } else {
        if (empty($error)) {
            $stmt = $conn->prepare("SELECT id FROM utenti WHERE email = ? OR username = ?");
            $stmt->bind_param("ss", $email, $username);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result && $result->num_rows > 0) {
                $error[] = "Email o username già in uso";
            }
        }

        if (empty($error)) {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO utenti (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $password_hash);

            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Registrazione completata con successo!";
                header("Location: index.php");
                exit;
            } else {
                $error[] = "Errore durante la registrazione: " . $conn->error;
            }
        }

        if (!empty($error)) {
            $_SESSION['registration_errors'] = $error;
            header("Location: index.php?form=register");
            exit;
        }
    }
}

// LOGIN
if (isset($_POST['login'])) {
    $email_or_username = trim($_POST['email']);
    $password = $_POST['password'];
    $error = [];

    if (empty($email_or_username)) {
        $error[] = "Email o Username obbligatorio";
    }
    if (empty($password)) {
        $error[] = "Password obbligatoria";
    }

    if ($is_ajax) {
        if (!empty($error)) {
            echo json_encode(['success' => false, 'errors' => [$error[0]]]);
            exit;
        }

        $stmt = $conn->prepare("SELECT id, username, email, password FROM utenti WHERE email = ? OR username = ?");
        $stmt->bind_param("ss", $email_or_username, $email_or_username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['logged_in'] = true;
                echo json_encode(['success' => true, 'username' => $user['username']]);
                exit;
            } else {
                echo json_encode(['success' => false, 'errors' => ['Password non corretta']]);
                exit;
            }
        } else {
            echo json_encode(['success' => false, 'errors' => ['Email o Username non trovato']]);
            exit;
        }
    } else {
        if (empty($error)) {
            $stmt = $conn->prepare("SELECT id, username, email, password FROM utenti WHERE email = ? OR username = ?");
            $stmt->bind_param("ss", $email_or_username, $email_or_username);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['logged_in'] = true;
                header("Location: index.php");
                exit;
            } else {
                $error[] = "Email o Password errati";
            }
        }

        if (!empty($error)) {
            $_SESSION['login_errors'] = $error;
            header("Location: index.php?form=login");
            exit;
        }
    }
}

// LOGOUT
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

$conn->close();
?>