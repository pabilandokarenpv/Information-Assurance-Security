<?php
ob_start();
session_start();

include 'db_connect.php'; // Your database connection file

// Security Headers (Important!)
// Prevent the page from being cached by the browser to avoid storing form data when clicking the back button
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Session Timeout Check
$session_timeout = 1800; // 30 minutes

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $session_timeout)) {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}

$_SESSION['last_activity'] = time(); // Update last activity

// Login Process
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, role, password FROM users WHERE username = ?");
    if ($stmt) {
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) { // Use === for strict comparison
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['user_id'] = $user['id'];

                if ($user['role'] === 'admin') {
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: user_dashboard.php");
                }
                exit();
            } else {
                $error_message = "Incorrect password.";
            }
        } else {
            $error_message = "User not found.";
        }
        $stmt->close();
    } else {
        $error_message = "Database error: " . $conn->error;
    }
}

ob_end_flush();

// HTML Form (Place this AFTER the PHP code)
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Login</h2>
        <form method="POST" action="" class="mt-4">
          <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required autocomplete="off">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required autocomplete="off">
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
            <small>Don't have an account? <a href="register.php">Register here</a></small>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


<!--
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NU Forum Login</title> 
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Arial', sans-serif;
    }

    .hero {
      background-color: #003366;
      color: white;
      padding: 60px 0;
    }

    .hero h1 {
      font-size: 3rem;
      font-weight: bold;
    }

    .hero p {
      font-size: 1.2rem;
    }

    .login-section {
      margin-top: 20px;
    }

    .card {
      border: none;
      box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.1);
    }

    .card-header {
      background-color: #003366;
      color: white;
      text-align: center;
      padding: 15px;
    }

    .form-control {
      border: 1px solid #ccc;
      padding: 10px;
      margin-bottom: 30px;
    }

    .btn-dark {
      background-color: #003366;
      color: white;
      border: none;
      padding: 10px;
      font-size: 1rem;
      transition: background-color 0.3s ease;
    }

    .btn-dark:hover {
      background-color: #000000;
      color: white;
    }

    .footer {
      background-color: #003366;
      color: white;
      text-align: center;
      padding: 10px 0;
      position: absolute;
      width: 100%;
      bottom: 0;
    }
  </style>
</head>
<body>

  <header class="hero text-center">
    <div class="container">
      <h1>Welcome to NU Forum</h1>
      <p>Don't have an account? <a href="register.php" class="text-white">Register here</a> to join.</p>
    </div>
  </header>

  <section id="loginForm" class="login-section py-5">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-6">
          <div class="card">
            <div class="card-header">
              <h4><b>Login to Your Account</b></h4>
            </div>
            <div class="card-body">
              <form action="login.php" method="POST">
                <div class="mb-3">
                  <label for="username" class="form-label">Username</label>
                  <input type="text" class="form-control" id="username" name="username" required placeholder="">
                </div>
                <div class="mb-3">
                  <label for="password" class="form-label">Password</label>
                  <input type="password" class="form-control" id="password" name="password" required placeholder="">
                </div>

                <button type="submit" class="btn btn-dark w-100">Login</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <footer class="footer">
    <p>&copy; 2024 NU Forum. All rights reserved.</p>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz4fnFO9gyb6e3L2FfmJz8lJQY8pZyl4gPQtXx5A4Q4c47s1C9z5byH5g6R" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-cuK9Lr6Stmb9V0J3/ftAfovgZ4E5CT5Lr3g0v1xVtW6gEjrtHXn/xLSy0X5PtFxi" crossorigin="anonymous"></script>
</body>
</html>
-->
