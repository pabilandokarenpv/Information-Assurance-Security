<?php
session_start();
var_dump($_SESSION);
exit();

// Check if the user is logged in
if (!isset($_SESSION['username']) || $_SESSION['role'] != "user") {
    header("Location: login.php");
    exit;
}

// Define session timeout duration (30 seconds for testing)
$session_timeout = 30;

// Check for session timeout
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $session_timeout)) {
    session_unset();
    session_destroy();
    header("Location: logout.php");
    exit;
}

// Update last activity timestamp
$_SESSION['last_activity'] = time();

// Generate CSRF Token If not set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Security Headers
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'");
header("Referrer-Policy: no-referrer");

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

// Handle new comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF Token Validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF validation failed. Please refresh and try again.");
    }

    $comment = $_POST['comment'];
    $postId = $_POST['post_id'];
    $userId = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, comment, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iis", $postId, $userId, $comment);

    if ($stmt->execute()) {
        // Regenerate CSRF Token after successful request
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        header("Location: user_dashboard.php");
        exit;

    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <script>
        function checkSession() {
            fetch('check_session.php')
                .then(response => response.json())
                .then(data => {
                    if (!data.active) {
                        alert("Your session has expired. You will be logged out.");
                        window.location.href = 'logout.php';
                    }
                })
                .catch(error => console.error("Error checking session status:", error));
        }

        setInterval(checkSession, 10000); // Check every 10 seconds
    </script>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="user_dashboard.php">User Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="user_dashboard.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>News feed</h2>
        <?php if (empty($newsfeed)): ?>
            <p class="text-muted">No posts available. Start sharing content!</p>  <?php else: ?>
            <?php foreach ($newsfeed as $postId => $post): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h5>
                        <p class="card-text"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                        <p class="text-muted">Posted by <?php echo htmlspecialchars($post['author']); ?> on <?php echo htmlspecialchars($post['created_at']); ?></p>
                    </div>
                    <div class="card-footer">
                        <h6>Comments</h6>
                        <?php if (empty($post['comments'])): ?>
                            <p class="text-muted">No comments yet. Be the first to comment!</p> <?php else: ?>
                            <?php foreach ($post['comments'] as $comment): ?>
                                <div class="mb-2">
                                    <strong><?php echo htmlspecialchars($comment['author']); ?></strong>
                                    <span><?php echo htmlspecialchars($comment['content']); ?></span><br>
                                    <small class="text-muted">Posted on <?php echo htmlspecialchars($comment['created_at']); ?></small>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>  <form action="user_dashboard.php" method="POST">
                            <div class="mb-3">
                                <textarea class="form-control" name="comment" placeholder="Write your comment..." required></textarea>
                            </div>
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>" />
                            <input type="hidden" name="post_id" value="<?php echo $postId; ?>" />
                            <button type="submit" class="btn btn-primary">Post Comment</button>
                        </form>

                    </div>
                </div>
            <?php endforeach; ?>  <?php endif; ?>  </div>
</body>
</html>

<!--
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    Navbar
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="user_dashboard.php">User Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    Newsfeed
    <div class="container mt-4">
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
                        <form method="POST" action="user_dashboard.php">
                            <div class="mb-3">
                                <textarea class="form-control" name="comment" rows="2" placeholder="Add a comment..." required></textarea>
                                <input type="hidden" name="post_id" value="<?php echo $postId; ?>">
                            </div>
                            <button type="submit" class="btn btn-primary">Comment</button>
                        </form>
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
-->