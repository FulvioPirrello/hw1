<?php
error_reporting(0);
ini_set('display_errors', 0);

session_start();
header('Content-Type: application/json; charset=utf-8');

// Connessione database
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

$action = isset($_GET['action']) ? $_GET['action'] : 'get_all';

switch ($action) {
    case 'get_all':
        try {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 12;
            $offset = ($page - 1) * $limit;
            
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
        
    case 'like_dislike':
        // Like/Dislike functionality
        if (!isset($_SESSION['user_id'])) {
            jsonResponse(['success' => false, 'controlla_login' => true, 'message' => 'Devi essere loggato per mettere like']);
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $id_modello = $input['id_modello'] ?? null;
        $action = $input['action'] ?? null;
        $id_utente = $_SESSION['user_id'];
        
        if (!$id_modello || !$action) {
            jsonResponse(['success' => false, 'errore' => 'Dati mancanti']);
        }
        
        try {
            // Controlla se il like esiste già (usa like_id dal tuo db.txt)
            $stmt = $conn->prepare("SELECT like_id FROM likes WHERE id_utente = ? AND id_modello = ?");
            $stmt->bind_param("ii", $id_utente, $id_modello);
            $stmt->execute();
            $result = $stmt->get_result();
            $like_exists = $result->num_rows > 0;

            if ($action === 'like' && !$like_exists) {
                // Aggiungi like
                $stmt = $conn->prepare("INSERT INTO likes (id_utente, id_modello) VALUES (?, ?)");
                $stmt->bind_param("ii", $id_utente, $id_modello);
                $stmt->execute();
                
                // Incrementa contatore (usa numero_like dal tuo db.txt)
                $stmt = $conn->prepare("UPDATE modelli SET numero_like = numero_like + 1 WHERE id_modello = ?");
                $stmt->bind_param("i", $id_modello);
                $stmt->execute();
                
                $mi_piace = true;
                
            } elseif ($action === 'dislike' && $like_exists) {
                // Rimuovi like
                $stmt = $conn->prepare("DELETE FROM likes WHERE id_utente = ? AND id_modello = ?");
                $stmt->bind_param("ii", $id_utente, $id_modello);
                $stmt->execute();
                
                // Decrementa contatore
                $stmt = $conn->prepare("UPDATE modelli SET numero_like = GREATEST(numero_like - 1, 0) WHERE id_modello = ?");
                $stmt->bind_param("i", $id_modello);
                $stmt->execute();
                
                $mi_piace = false;
            } else {
                $mi_piace = $like_exists;
            }
            
            // Ottieni conteggio aggiornato
            $stmt = $conn->prepare("SELECT numero_like FROM modelli WHERE id_modello = ?");
            $stmt->bind_param("i", $id_modello);
            $stmt->execute();
            $result = $stmt->get_result();
            $modello = $result->fetch_assoc();
            
            echo json_encode([
                'success' => true,
                'accesso_effettuato' => true,
                'mi_piace' => $mi_piace,
                'conta' => (int)$modello['numero_like'],
                'message' => $mi_piace ? 'Like aggiunto!' : 'Like rimosso!'
            ]);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'errore' => 'Errore: ' . $e->getMessage()]);
        }
        break;
        
    default:
        $conn->close();
        jsonResponse([
            'success' => false,
            'message' => 'Azione non riconosciuta'
        ]);
        break;
}

$conn->close();
?>