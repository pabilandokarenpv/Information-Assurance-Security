<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bootstrap Page Template</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Header with Navigation -->
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="#">MySite</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link active" href="#">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Services</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Contact</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Content Body -->
    <main class="container my-5">
        <h1 class="text-center mb-4">Welcome to NU Forum</h1>
        <p class="lead">This is a simple webpage template created with Bootstrap 5. It includes a responsive header, navigation bar, content body, and footer.</p>
        <p>Bootstrap makes it easy to create professional-looking web pages with minimal effort. Use this template as a starting point for your projects!</p>
        <hr>
        <div class="row">
            <div class="col-md-4">
                <h3>Feature 1</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin id orci sed justo consequat facilisis.</p>
            </div>
            <div class="col-md-4">
                <h3>Feature 2</h3>
                <p>Aliquam erat volutpat. Sed vel convallis ipsum. Nullam auctor magna vitae libero efficitur, a tempor ex dignissim.</p>
            </div>
            <div class="col-md-4">
                <h3>Feature 3</h3>
                <p>Praesent varius orci ut purus tincidunt, nec hendrerit tortor facilisis. Cras vehicula arcu eu nisl auctor dictum.</p>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3">
        <p class="mb-0">&copy; 2024 MySite. All rights reserved.</p>
        <p class="mb-0"><a href="#" class="text-white text-decoration-none">Privacy Policy</a> | <a href="#" class="text-white text-decoration-none">Terms of Service</a></p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
