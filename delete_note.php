<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['note_id'])) {
    $note_id = $_POST['note_id'];
    $user_id = $_SESSION['user_id'];
    
    // Verify note ownership before deletion
    $stmt = $pdo->prepare("SELECT image_path FROM notes WHERE id = ? AND user_id = ?");
    $stmt->execute([$note_id, $user_id]);
    $note = $stmt->fetch();
    
    if ($note) {
        // Delete the image file if it exists
        if ($note['image_path'] && file_exists('Image/' . $note['image_path'])) {
            unlink('Image/' . $note['image_path']);
        }
        
        // Delete the note from database
        $stmt = $pdo->prepare("DELETE FROM notes WHERE id = ? AND user_id = ?");
        $stmt->execute([$note_id, $user_id]);
    }
}

header("Location: index.php");
exit(); 