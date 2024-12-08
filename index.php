<?php
require_once 'config/database.php';

// Fetch all notes
$stmt = $pdo->query("SELECT * FROM notes ORDER BY created_at DESC");
$notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Notes</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Travel Notes</h1>
        
        <div class="add-note">
            <a href="add_note.php" class="btn">Add New Note</a>
        </div>

        <div class="notes-grid">
            <?php foreach($notes as $note): ?>
                <div class="note-card">
                    <?php if($note['image_path']): ?>
                        <img src="uploads/<?php echo $note['image_path']; ?>" alt="Note Image">
                    <?php endif; ?>
                    <h3><?php echo htmlspecialchars($note['title']); ?></h3>
                    <p><?php echo nl2br(htmlspecialchars($note['content'])); ?></p>
                    <span class="date"><?php echo date('M d, Y', strtotime($note['created_at'])); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html> 