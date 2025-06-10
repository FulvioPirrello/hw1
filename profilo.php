<?php
session_start();

$host = 'localhost';
$username = 'root';  
$password = '';
$database = 'nothingiverse_db';

$conn = new mysqli($host, $username, $password, $database);  

if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM utenti WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    session_destroy();
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profilo - Nothingiverse</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js"></script>
    <script src="profilo_utente.js"></script>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <main class="profilo_container">
        <div class="profilo_header">
            <h1 class="profilo">Profilo di <?php echo htmlspecialchars($user['username']); ?></h1>
            <p>Membro dal: <?php echo date('d/m/Y', strtotime($user['data_registrazione'])); ?></p>
        </div>
        
        <div class="box_profilo">
            <div class="info_profilo">
                <h2 class="informazioni">Informazioni Personali</h2>
                <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            </div>
            
            <div class="pulsanti_profilo">
                <a href="modifica_profilo.php" class="pulsante">Modifica Profilo</a>
                <a href="i_miei_modelli.php" class="pulsante">I Miei Modelli</a>
                <a href="preferiti.php" class="pulsante">I Miei Like</a>
                <a href="account.php?logout=true" class="pulsante">Logout</a>
            </div>
        </div>
    </main>
    
    <?php include 'footer.php'; ?>
</body>
</html>

<?php $conn->close(); ?>

<style>
    .pulsante {
        height: auto;
        width: 150px;
        padding: 15px;
        background-color: #196ef0;
        color: white;
        border-radius: 20px;
        text-decoration: none;
        margin: 10px 0;
        display: block;
        text-align: center;
    }
    
    nav {
        height: 40px;
    }

    .welcome {
        display: none;
    }
    footer {
        display: none;
    }
    .profilo {
        margin: 30px;
    }
    .informazioni {
        margin: 30px;
    }
    .pulsanti_profilo {
        display: flex;
        justify-content: flex-start;
        align-items: flex-start; 
        flex-wrap: wrap;
        flex-direction: column;
        margin: 30px;
        gap: 10px;
    }
    p {
        margin: 40px;
    }
</style>