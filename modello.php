<?php
session_start();

error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'nothingiverse_db';

function jsonResponse($data) {
    if (ob_get_level()) {
        ob_clean();
    }
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $conn = new mysqli($host, $username, $password, $database);
    
    if ($conn->connect_error) {
        jsonResponse([
            'success' => false,
            'message' => 'Errore connessione database'
        ]);
    }
    
    $conn->set_charset("utf8");
    
} catch (Exception $e) {
    jsonResponse([
        'success' => false,
        'message' => 'Errore database: ' . $e->getMessage()
    ]);
}

// Ottieni azione
$action = isset($_GET['action']) ? $_GET['action'] : 'get_all';

switch ($action) {
    case 'get_all':
        try {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 12;
            $offset = ($page - 1) * $limit;
            
            // Query principale
            $query = "
                SELECT 
                    m.id_modello,
                    m.nome_modello,
                    COALESCE(m.immagine, '') as immagine,
                    m.data_pubblicazione,
                    m.quantita_like,
                    m.id_user,
                    COALESCE(u.username, 'Sconosciuto') as nome_utente
                FROM modelli m
                LEFT JOIN utenti u ON m.id_user = u.id
                ORDER BY m.data_pubblicazione DESC
                LIMIT ? OFFSET ?
            ";
            
            $stmt = $conn->prepare($query);
            if (!$stmt) {
                jsonResponse([
                    'success' => false,
                    'message' => 'Errore preparazione query'
                ]);
            }
            
            $stmt->bind_param("ii", $limit, $offset);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $modelli = [];
            while ($row = $result->fetch_assoc()) {
                // Controlla se utente ha messo like
                $user_liked = false;
                if (isset($_SESSION['user_id'])) {
                    $like_check = $conn->prepare("SELECT 1 FROM likes WHERE id_utente = ? AND id_modello = ?");
                    if ($like_check) {
                        $like_check->bind_param("ii", $_SESSION['user_id'], $row['id_modello']);
                        $like_check->execute();
                        $user_liked = $like_check->get_result()->num_rows > 0;
                        $like_check->close();
                    }
                }
                
                $row['user_liked'] = $user_liked;
                $modelli[] = $row;
            }
            
            $stmt->close();
            
            // Conta totale
            $count_query = "SELECT COUNT(*) as total FROM modelli";
            $count_result = $conn->query($count_query);
            $total = 0;
            if ($count_result) {
                $total = $count_result->fetch_assoc()['total'];
            }
            
            jsonResponse([
                'success' => true,
                'modelli' => $modelli,
                'total' => (int)$total,
                'page' => $page,
                'total_pages' => ceil($total / $limit)
            ]);
            
        } catch (Exception $e) {
            jsonResponse([
                'success' => false,
                'message' => 'Errore caricamento: ' . $e->getMessage()
            ]);
        }
        break;
        
    default:
        jsonResponse([
            'success' => false,
            'message' => 'Azione non valida'
        ]);
        break;
}

$conn->close();
?>