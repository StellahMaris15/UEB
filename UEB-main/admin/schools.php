<?php
// schools.php - Public page to display schools
require_once 'config.php';

$schools = getSchools();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-88">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schools - EduPortal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-graduation-cap me-2"></i>EduPortal
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link active" href="schools.php">Schools</a></li>
                    <li class="nav-item"><a class="nav-link" href="activities.php">Activities</a></li>
                    <li class="nav-item"><a class="nav-link" href="bursaries.php">Bursaries</a></li>
                    <li class="nav-item"><a class="nav-link" href="agents.php">Agents</a></li>
                    <li class="nav-item"><a class="nav-link" href="e-library.php">E-Library</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-light text-primary px-3 ms-2" href="login.php">
                            <i class="fas fa-lock me-1"></i>Admin
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Schools Section -->
    <div class="container mt-5 pt-5">
        <h2 class="text-center mb-4" data-aos="fade-up">Our Schools</h2>
        
        <div class="row">
            <?php foreach ($schools as $school): ?>
            <div class="col-md-4 mb-4" data-aos="fade-up">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($school['name']); ?></h5>
                        <p class="card-text">
                            <i class="fas fa-map-marker-alt text-primary"></i> 
                            <?php echo htmlspecialchars($school['location']); ?><br>
                            <i class="fas fa-users text-primary"></i> 
                            <?php echo $school['students']; ?> students<br>
                            <i class="fas fa-star text-warning"></i> 
                            <?php echo $school['rating']; ?>/5.0
                        </p>
                        <p class="card-text"><?php echo htmlspecialchars($school['description']); ?></p>
                        <span class="badge bg-primary"><?php echo $school['type']; ?></span>
                    </div>
                    <div class="card-footer bg-white border-0">
                        <button class="btn btn-outline-primary w-100">View Details</button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000,
            once: true
        });
    </script>
</body>
</html>