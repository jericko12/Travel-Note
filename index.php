<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle logout
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'logout') {
    session_destroy();
    header("Location: login.php");
    exit();
}

require_once 'config/database.php';

// Update the query to only show user's notes
$stmt = $pdo->prepare("SELECT * FROM notes WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Notes</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <h1>Travel Notes</h1>
        
        <div class="user-nav">
            <span class="username">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <form action="" method="POST" style="display: inline;">
                <input type="hidden" name="action" value="logout">
                <button type="submit" class="logout-btn" title="Logout">
                    <i class="fas fa-power-off"></i>
                </button>
            </form>
        </div>

        <div class="add-note-btn">
            <a href="add_note.php" class="floating-btn">
                <i class="fas fa-plus"></i>
            </a>
        </div>

        <div class="notes-grid">
            <?php foreach($notes as $note): ?>
                <div class="note-card" onclick="showPopup(this)">
                    <form action="delete_note.php" method="POST" class="delete-form" onsubmit="showDeleteConfirm(event, this)">
                        <input type="hidden" name="note_id" value="<?php echo $note['id']; ?>">
                        <button type="submit" class="delete-icon" onclick="event.stopPropagation()">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                    <?php if($note['image_path']): ?>
                        <img src="Image/<?php echo $note['image_path']; ?>" alt="Note Image">
                    <?php endif; ?>
                    <h3><?php echo htmlspecialchars($note['title']); ?></h3>
                    <p class="preview"><?php echo nl2br(htmlspecialchars(substr($note['content'], 0, 100))); ?>...</p>
                    <span class="date"><?php echo date('M d, Y', strtotime($note['created_at'])); ?></span>
                    <div class="note-full-content" style="display: none;">
                        <?php echo nl2br(htmlspecialchars($note['content'])); ?>
                    </div>
                    <input type="hidden" value="<?php echo $note['id']; ?>" class="note-id">
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Popup -->
        <div class="overlay" id="overlay"></div>
        <div class="popup" id="popup">
            <span class="popup-close" onclick="closePopup()">&times;</span>
            <div class="popup-title"></div>
            <div class="popup-content"></div>
            <div class="popup-image"></div>
            <div class="popup-actions">
                <a href="#" class="btn" id="editButton">Edit</a>
            </div>
        </div>

        <!-- Delete Confirmation Popup -->
        <div class="delete-confirm-overlay" id="deleteConfirmOverlay">
            <div class="delete-confirm-popup">
                <h3>Delete Note</h3>
                <p>Are you sure you want to delete this note?</p>
                <div class="delete-confirm-actions">
                    <button class="btn btn-secondary" onclick="cancelDelete()">Cancel</button>
                    <button class="btn btn-delete" id="confirmDeleteBtn">Delete</button>
                </div>
            </div>
        </div>

        <script>
        function showPopup(button) {
            const noteCard = button.parentElement;
            const title = noteCard.querySelector('h3').textContent;
            const content = noteCard.querySelector('.note-full-content').textContent;
            const noteId = noteCard.querySelector('.note-id').value;
            const img = noteCard.querySelector('img');
            
            document.querySelector('.popup-title').textContent = title;
            document.querySelector('.popup-content').textContent = content;
            document.getElementById('editButton').href = `edit_note.php?id=${noteId}`;
            
            // Handle image
            const popupImage = document.querySelector('.popup-image');
            if (img && popupImage) {
                popupImage.innerHTML = `<img src="${img.src}" alt="Note Image">`;
                popupImage.style.display = 'block';
            } else if (popupImage) {
                popupImage.style.display = 'none';
            }
            
            document.getElementById('overlay').style.display = 'block';
            document.getElementById('popup').style.display = 'block';
        }

        function closePopup() {
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('popup').style.display = 'none';
        }

        // Close popup when clicking outside
        document.getElementById('overlay').addEventListener('click', closePopup);

        let deleteForm = null;

        function showDeleteConfirm(event, form) {
            event.preventDefault();
            event.stopPropagation();
            deleteForm = form;
            document.getElementById('deleteConfirmOverlay').style.display = 'block';
        }

        function cancelDelete() {
            document.getElementById('deleteConfirmOverlay').style.display = 'none';
            deleteForm = null;
        }

        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (deleteForm) {
                deleteForm.submit();
            }
        });
        </script>
    </div>
</body>
</html> 