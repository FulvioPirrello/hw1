<?php
session_start();
header('Content-Type: application/json');

if (isset($_GET['check_login_errors'])) {
    $response = ['login_errors' => []];
    if (isset($_SESSION['login_errors'])) {
        $response['login_errors'] = $_SESSION['login_errors'];
        unset($_SESSION['login_errors']);
    }
    echo json_encode($response);
    exit;
}

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && 
    isset($_SESSION['username']) && isset($_SESSION['user_id'])) {
    echo json_encode([
        'logged_in' => true, 
        'username' => $_SESSION['username'],
        'user_id' => $_SESSION['user_id']
    ]);
} else {
    echo json_encode(['logged_in' => false]);
}
?>