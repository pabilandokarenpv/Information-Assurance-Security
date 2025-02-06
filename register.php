<?php
// New Code
session_start();
if (!isset($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Security Headers
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'");
header("Referrer-Policy: no-referrer");

// Database Connection
include 'db_connect.php';

// New Code
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("CSRF validation failed!");
  }

  $username = htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8');
  $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
  $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

  // Old Code
  // $username = $_POST['username'];
  // $email = $_POST['email'];
  // $password = $_POST['password'];

  $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
  $stmt->bind_param("sss", $username, $email, $password);

  if ($stmt->execute()) {
    echo "<div class='alert alert-success'>Registration successful! <a href='login.php'>Login Here</a></div>";
  } else {
    echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
  }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Register</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<h1>Welcome to NU Forum</h1>
<p>Do you have an account? <a href="login.php" class="text-black">Login here</a> to access your dashboard.</p>
    <div class="container mt-5">
        <h2 class="text-center">Register</h2>
        <form method="POST" action="" class="mt-4" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required autocomplete="new-password">
            </div>
            <button type="submit" class="btn btn-primary w-100">Register</button>
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
  <title>NU Forum Registration</title>
  Bootstrap CSS
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Arial', sans-serif;
    }

    .hero {
      background-color: #003366; /* Darker blue */
      color: white;
      padding: 20px 0;
    }

    .hero h1 {
      font-size: 3rem;
      font-weight: bold;
    }

    .hero p {
      font-size: 1.2rem;
    }

    .register-section {
      margin-top: 3px;
    }

    .card {
      border: none;
      box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.1);
    }

    .card-header {
      background-color: #003366; /* Darker blue */
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
      background-color: #000000; /* Black on hover */
      color: white;
    }

    .footer {
      background-color: #003366; /* Darker blue */
      color: white;
      text-align: center;
      padding: 10px 0;
      position: absolute;
      width: 100%;
      bottom: 50;

    }
  </style>
</head>
<body>


  Hero Section
  <header class="hero text-center">
    <div class="container">
      <h1>Welcome to NU Forum</h1>
      <p>Do you have an account? <a href="login.php" class="text-black">Login here</a> to access your dashboard.</p>
    </div>
  </header>

  Register Section
  <section id="registerForm" class="register-section py-5">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-6">
          <div class="card">
            <div class="card-header">
              <h4><b>Account Registration</b></h4>
            </div>
            <div class="card-body">
              <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="mb-3">
                  <label for="regUsername" class="form-label">Username</label>
                  <input type="text" class="form-control" id="regUsername" name="username" required placeholder="">
                </div>
                <div class="mb-3">
                  <label for="regEmail" class="form-label">Email Address</label>
                  <input type="email" class="form-control" id="regEmail" name="email" required placeholder="">
                </div>
                <div class="mb-3">
                  <label for="regPassword" class="form-label">Password</label>
                  <input type="password" class="form-control" id="regPassword" name="password" required placeholder="">
                </div>
                <button type="submit" class="btn btn-dark w-100">Register</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  Footer 
  <footer class="footer">
    <p>&copy; 2024 NU Forum. All rights reserved.</p>
  </footer>

  Bootstrap JS and dependencies
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz4fnFO9gyb6e3L2FfmJz8lJQY8pZyl4gPQtXx5A4Q4c47s1C9z5byH5g6R" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-cuK9Lr6Stmb9V0J3/ftAfovgZ4E5CT5Lr3g0v1xVtW6gEjrtHXn/xLSy0X5PtFxi" crossorigin="anonymous"></script>
</body>
</html>
-->