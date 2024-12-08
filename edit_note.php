<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['note_id'])) {
    $note_id = $_POST['note_id'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];
    
    // Verify note ownership
    $stmt = $pdo->prepare("SELECT id FROM notes WHERE id = ? AND user_id = ?");
    $stmt->execute([$note_id, $user_id]);
    if (!$stmt->fetch()) {
        header("Location: index.php");
        exit();
    }
    
    // Update note
    $stmt = $pdo->prepare("UPDATE notes SET title = ?, content = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$title, $content, $note_id, $user_id]);

    // Handle new image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "Image/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $image_path = time() . '_' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_path;
        
        // Check file type
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        if(in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            // Delete old image if exists
            $stmt = $pdo->prepare("SELECT image_path FROM notes WHERE id = ?");
            $stmt->execute([$note_id]);
            $note = $stmt->fetch();
            
            if ($note['image_path'] && file_exists('Image/' . $note['image_path'])) {
                unlink('Image/' . $note['image_path']);
            }

            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $stmt = $pdo->prepare("UPDATE notes SET image_path = ? WHERE id = ?");
                $stmt->execute([$image_path, $note_id]);
            } else {
                $error = "Sorry, there was an error uploading your file.";
            }
        } else {
            $error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        }
    }

    header("Location: index.php");
    exit();
}

if (isset($_GET['id'])) {
    $note_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];
    
    // Verify note ownership
    $stmt = $pdo->prepare("SELECT * FROM notes WHERE id = ? AND user_id = ?");
    $stmt->execute([$note_id, $user_id]);
    $note = $stmt->fetch();
    
    if (!$note) {
        header("Location: index.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Note - Travel Notes</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Edit Travel Note</h1>
        
        <form action="edit_note.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="note_id" value="<?php echo $note['id']; ?>">
            
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($note['title']); ?>" required>
            </div>

            <div class="form-group">
                <label for="content">Content:</label>
                <textarea id="content" name="content" required><?php echo htmlspecialchars($note['content']); ?></textarea>
            </div>

            <?php if($note['image_path']): ?>
                <div class="current-image">
                    <img src="uploads/<?php echo $note['image_path']; ?>" alt="Current Image">
                    <p>Current Image</p>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="image">New Image (optional):</label>
                <input type="file" id="image" name="image" accept="image/*">
            </div>

            <button type="submit" class="btn">Save Changes</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html> 