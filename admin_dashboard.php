<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Database connection
include 'db_connect.php';

// Fetch posts and associated comments
$query = "SELECT posts.id AS post_id, posts.title, posts.content, posts.created_at, users.username AS post_author,
                 comments.comment AS comment_content, comments.created_at AS comment_date, comment_users.username AS comment_author
          FROM posts
          LEFT JOIN users ON posts.user_id = users.id
          LEFT JOIN comments ON comments.post_id = posts.id
          LEFT JOIN users AS comment_users ON comments.user_id = comment_users.id
          ORDER BY posts.created_at DESC, comments.created_at ASC";
$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}

$newsfeed = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $postId = $row['post_id'];
        if (!isset($newsfeed[$postId])) {
            $newsfeed[$postId] = [
                'title' => $row['title'],
                'content' => $row['content'],
                'created_at' => $row['created_at'],
                'author' => $row['post_author'],
                'comments' => []
            ];
        }
        if ($row['comment_content']) {
            $newsfeed[$postId]['comments'][] = [
                'content' => $row['comment_content'],
                'created_at' => $row['comment_date'],
                'author' => $row['comment_author']
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="admin_dashboard.php">Admin Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="post_dashboard.php">Post Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="comments_dashboard.php">Comments Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="account_management.php">Account Management</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        <h2>Welcome, Admin!</h2>
        <p>Manage posts, comments, and user accounts using the options in the navigation bar.</p>

        <h2>Newsfeed</h2>
        <?php if (!empty($newsfeed)): ?>
            <?php foreach ($newsfeed as $postId => $post): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h5>
                        <p class="card-text"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                        <p class="text-muted">Posted by <?php echo htmlspecialchars($post['author']); ?> on <?php echo htmlspecialchars($post['created_at']); ?></p>
                    </div>
                    <div class="card-footer">
                        <h6>Comments</h6>
                        <?php if (!empty($post['comments'])): ?>
                            <?php foreach ($post['comments'] as $comment): ?>
                                <div class="mb-2">
                                    <strong><?php echo htmlspecialchars($comment['author']); ?>:</strong>
                                    <span><?php echo htmlspecialchars($comment['content']); ?></span>
                                    <br>
                                    <small class="text-muted">Posted on <?php echo htmlspecialchars($comment['created_at']); ?></small>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">No comments yet. Be the first to comment!</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted">No posts available. Start sharing content!</p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>