<?php
// config.php
session_start();

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'eduportal');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset
$conn->set_charset("utf8mb4");

// Site configuration
define('SITE_NAME', 'EduPortal');
define('SITE_URL', 'http://localhost/eduportal');

// File upload configuration
define('UPLOAD_PATH', $_SERVER['DOCUMENT_ROOT'] . '/eduportal/uploads/');
define('UPLOAD_URL', SITE_URL . '/uploads/');

// Create upload directory if not exists
if (!file_exists(UPLOAD_PATH)) {
    mkdir(UPLOAD_PATH, 0777, true);
}

// Admin check function
function isAdmin() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

// Redirect if not admin
function requireAdmin() {
    if (!isAdmin()) {
        header('Location: login.php');
        exit();
    }
}

// Sanitize input
function sanitize($input) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($input)));
}

// Get all schools
function getSchools() {
    global $conn;
    $query = "SELECT * FROM schools ORDER BY created_at DESC";
    $result = $conn->query($query);
    $schools = [];
    while ($row = $result->fetch_assoc()) {
        $schools[] = $row;
    }
    return $schools;
}

// Get single school
function getSchool($id) {
    global $conn;
    $id = sanitize($id);
    $query = "SELECT * FROM schools WHERE id = '$id'";
    $result = $conn->query($query);
    return $result->fetch_assoc();
}

// Add school
function addSchool($data) {
    global $conn;
    $name = sanitize($data['name']);
    $location = sanitize($data['location']);
    $type = sanitize($data['type']);
    $students = sanitize($data['students']);
    $rating = sanitize($data['rating']);
    $description = sanitize($data['description']);
    
    $query = "INSERT INTO schools (name, location, type, students, rating, description) 
              VALUES ('$name', '$location', '$type', '$students', '$rating', '$description')";
    
    return $conn->query($query);
}

// Update school
function updateSchool($id, $data) {
    global $conn;
    $id = sanitize($id);
    $name = sanitize($data['name']);
    $location = sanitize($data['location']);
    $type = sanitize($data['type']);
    $students = sanitize($data['students']);
    $rating = sanitize($data['rating']);
    $description = sanitize($data['description']);
    
    $query = "UPDATE schools SET 
              name = '$name',
              location = '$location',
              type = '$type',
              students = '$students',
              rating = '$rating',
              description = '$description'
              WHERE id = '$id'";
    
    return $conn->query($query);
}

// Delete school
function deleteSchool($id) {
    global $conn;
    $id = sanitize($id);
    $query = "DELETE FROM schools WHERE id = '$id'";
    return $conn->query($query);
}
?>