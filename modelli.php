<?php
session_start();

error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');

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
        jsonResponse(['success' => false, 'message' => 'Errore connessione database']);
    }
    $conn->set_charset("utf8");
} catch (Exception $e) {
    jsonResponse(['success' => false, 'message' => 'Errore database: ' . $e->getMessage()]);
}

$action = $_GET['action'] ?? $_POST['action'] ?? 'get_all';

switch ($action) {
    case 'get_all':
        try {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 12;
            $offset = ($page - 1) * $limit;
            $user_id = $_SESSION['user_id'] ?? null;

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
            if (!$stmt) jsonResponse(['success' => false, 'message' => 'Errore preparazione query']);
            $stmt->bind_param("ii", $limit, $offset);
            $stmt->execute();
            $result = $stmt->get_result();

            $modelli = [];
            while ($row = $result->fetch_assoc()) {
                $user_liked = false;
                if ($user_id) {
                    $like_check = $conn->prepare("SELECT 1 FROM likes WHERE id_utente = ? AND id_modello = ?");
                    if ($like_check) {
                        $like_check->bind_param("ii", $user_id, $row['id_modello']);
                        $like_check->execute();
                        $like_result = $like_check->get_result();
                        $user_liked = $like_result->num_rows > 0;
                        $like_check->close();
                    }
                }
                $row['quantita_like'] = $row['quantita_like'];
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
            jsonResponse(['success' => false, 'message' => 'Errore caricamento: ' . $e->getMessage()]);
        }
        break;

    case 'toggle_like':
        try {
            $user_id = $_SESSION['user_id'] ?? null;
            if (!$user_id) jsonResponse(['success' => false, 'message' => 'Utente non autenticato']);

            $id_modello = isset($_POST['id_modello']) ? (int)$_POST['id_modello'] : 0;
            if (!$id_modello) jsonResponse(['success' => false, 'message' => 'ID modello mancante']);

            $check_like = $conn->prepare("SELECT id FROM likes WHERE id_utente = ? AND id_modello = ?");
            if (!$check_like) jsonResponse(['success' => false, 'message' => 'Errore query check']);
            $check_like->bind_param("ii", $user_id, $id_modello);
            $check_like->execute();
            $check_result = $check_like->get_result();
            $like_exists = $check_result->num_rows > 0;
            $check_like->close();

            $conn->begin_transaction();
            if ($like_exists) {
                $remove_like = $conn->prepare("DELETE FROM likes WHERE id_utente = ? AND id_modello = ?");
                if (!$remove_like) { $conn->rollback(); jsonResponse(['success' => false, 'message' => 'Errore query remove']); }
                $remove_like->bind_param("ii", $user_id, $id_modello);
                if (!$remove_like->execute()) { $conn->rollback(); jsonResponse(['success' => false, 'message' => 'Errore rimozione like']); }
                $remove_like->close();

                $update_count = $conn->prepare("UPDATE modelli SET quantita_like = GREATEST(quantita_like - 1, 0) WHERE id_modello = ?");
                $update_count->bind_param("i", $id_modello);
                $update_count->execute();
                $update_count->close();

                $action_performed = 'removed';
                $user_liked = false;
            } else {
                $add_like = $conn->prepare("INSERT INTO likes (id_utente, id_modello) VALUES (?, ?)");
                if (!$add_like) { $conn->rollback(); jsonResponse(['success' => false, 'message' => 'Errore query add: ' . $conn->error]); }
                $add_like->bind_param("ii", $user_id, $id_modello);
                if (!$add_like->execute()) { $conn->rollback(); jsonResponse(['success' => false, 'message' => 'Errore inserimento like: ' . $conn->error]); }
                $add_like->close();

                $update_count = $conn->prepare("UPDATE modelli SET quantita_like = quantita_like + 1 WHERE id_modello = ?");
                $update_count->bind_param("i", $id_modello);
                $update_count->execute();
                $update_count->close();

                $action_performed = 'added';
                $user_liked = true;
            }
            $conn->commit();

            $get_count = $conn->prepare("SELECT quantita_like FROM modelli WHERE id_modello = ?");
            $get_count->bind_param("i", $id_modello);
            $get_count->execute();
            $count_result = $get_count->get_result();
            $new_count = 0;
            if ($count_result) {
                $row = $count_result->fetch_assoc();
                $new_count = (int)$row['quantita_like'];
            }
            $get_count->close();

            jsonResponse([
                'success' => true,
                'action' => $action_performed,
                'new_count' => $new_count,
                'user_liked' => $user_liked
            ]);
        } catch (Exception $e) {
            $conn->rollback();
            jsonResponse(['success' => false, 'message' => 'Errore toggle like: ' . $e->getMessage()]);
        }
        break;

    case 'get_favorites':
        try {
            $user_id = $_SESSION['user_id'] ?? null;
            if (!$user_id) jsonResponse(['success' => false, 'message' => 'Utente non autenticato']);

            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
            $offset = ($page - 1) * $limit;

            $query = "
                SELECT 
                    m.id_modello,
                    m.nome_modello,
                    COALESCE(m.immagine, '') as immagine,
                    m.data_pubblicazione,
                    m.quantita_like,
                    m.id_user,
                    COALESCE(u.username, 'Sconosciuto') as nome_utente,
                    l.data_like
                FROM modelli m
                INNER JOIN likes l ON m.id_modello = l.id_modello
                LEFT JOIN utenti u ON m.id_user = u.id
                WHERE l.id_utente = ?
                ORDER BY l.data_like DESC
                LIMIT ? OFFSET ?
            ";
            $stmt = $conn->prepare($query);
            if (!$stmt) jsonResponse(['success' => false, 'message' => 'Errore preparazione query favorites']);
            $stmt->bind_param("iii", $user_id, $limit, $offset);
            $stmt->execute();
            $result = $stmt->get_result();

            $modelli = [];
            while ($row = $result->fetch_assoc()) {
                $row['user_liked'] = true;
                $row['downloads'] = 0; 
                $modelli[] = $row;
            }
            $stmt->close();

            $count_query = "SELECT COUNT(*) as total FROM likes WHERE id_utente = ?";
            $count_stmt = $conn->prepare($count_query);
            $count_stmt->bind_param("i", $user_id);
            $count_stmt->execute();
            $count_result = $count_stmt->get_result();
            $total = 0;
            if ($count_result) {
                $total = $count_result->fetch_assoc()['total'];
            }
            $count_stmt->close();

            jsonResponse([
                'success' => true,
                'modelli' => $modelli,
                'total' => (int)$total,
                'page' => $page,
                'total_pages' => ceil($total / $limit)
            ]);
        } catch (Exception $e) {
            jsonResponse(['success' => false, 'message' => 'Errore caricamento preferiti: ' . $e->getMessage()]);
        }
        break;

    case 'download':
        try {
            $model_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            if (!$model_id) jsonResponse(['success' => false, 'message' => 'ID modello mancante']);

            $check_query = "SELECT nome_modello FROM modelli WHERE id_modello = ?";
            $check_stmt = $conn->prepare($check_query);
            $check_stmt->bind_param("i", $model_id);
            $check_stmt->execute();
            $result = $check_stmt->get_result();

            if ($result->num_rows === 0) jsonResponse(['success' => false, 'message' => 'Modello non trovato']);
            $model_data = $result->fetch_assoc();
            $check_stmt->close();

            jsonResponse([
                'success' => true,
                'message' => 'Download simulato per: ' . $model_data['nome_modello'],
                'download_url' => 'path/to/file.zip',
                'filename' => 'modello_' . $model_id . '.zip'
            ]);
        } catch (Exception $e) {
            jsonResponse(['success' => false, 'message' => 'Errore download: ' . $e->getMessage()]);
        }
        break;

    default:
        jsonResponse(['success' => false, 'message' => 'Azione non valida']);
        break;
}

$conn->close();
?>