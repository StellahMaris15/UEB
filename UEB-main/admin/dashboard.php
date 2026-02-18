<?php
// admin/dashboard.php
require_once '../config.php';
requireAdmin();

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    switch ($_POST['action']) {
        case 'add_school':
            $result = addSchool($_POST);
            echo json_encode(['success' => $result, 'message' => $result ? 'School added successfully' : 'Error adding school']);
            break;
            
        case 'update_school':
            $result = updateSchool($_POST['id'], $_POST);
            echo json_encode(['success' => $result, 'message' => $result ? 'School updated successfully' : 'Error updating school']);
            break;
            
        case 'delete_school':
            $result = deleteSchool($_POST['id']);
            echo json_encode(['success' => $result, 'message' => $result ? 'School deleted successfully' : 'Error deleting school']);
            break;
            
        case 'get_schools':
            $schools = getSchools();
            echo json_encode($schools);
            break;
            
        case 'get_school':
            $school = getSchool($_POST['id']);
            echo json_encode($school);
            break;
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - EduPortal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 250px;
        }
        
        body {
            background: #f4f6f9;
            overflow-x: hidden;
        }
        
        .wrapper {
            display: flex;
            width: 100%;
        }
        
        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            transition: all 0.3s;
            z-index: 1000;
            overflow-y: auto;
        }
        
        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-header h3 {
            margin: 0;
            font-size: 1.5rem;
        }
        
        .sidebar-header p {
            margin: 5px 0 0;
            font-size: 0.9rem;
            opacity: 0.8;
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .sidebar-menu a {
            display: block;
            padding: 12px 25px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        
        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: white;
        }
        
        .sidebar-menu a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        /* Main Content */
        .content {
            width: calc(100% - var(--sidebar-width));
            margin-left: var(--sidebar-width);
            padding: 20px;
            transition: all 0.3s;
        }
        
        /* Navbar */
        .navbar-custom {
            background: white;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        /* Cards */
        .dashboard-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 20px;
        }
        
        .stats-card i {
            font-size: 3rem;
            opacity: 0.5;
        }
        
        /* Tables */
        .data-table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .action-btns .btn {
            padding: 5px 10px;
            margin: 0 2px;
        }
        
        /* Modal */
        .modal-content {
            border-radius: 15px;
        }
        
        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
        }
        
        .btn-close {
            filter: brightness(0) invert(1);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                margin-left: -250px;
            }
            .sidebar.active {
                margin-left: 0;
            }
            .content {
                width: 100%;
                margin-left: 0;
            }
            .content.active {
                margin-left: 250px;
            }
        }
        
        /* Toast Notifications */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }
        
        .toast {
            background: white;
            border-radius: 10px;
            padding: 15px 25px;
            margin-bottom: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        .toast-success {
            border-left: 4px solid #28a745;
        }
        
        .toast-error {
            border-left: 4px solid #dc3545;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <i class="fas fa-graduation-cap fa-3x mb-3"></i>
                <h3>EduPortal</h3>
                <p>Admin Dashboard</p>
            </div>
            
            <div class="sidebar-menu">
                <a href="#" class="active" data-section="dashboard">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="#" data-section="schools">
                    <i class="fas fa-school"></i> Manage Schools
                </a>
                <a href="#" data-section="activities">
                    <i class="fas fa-running"></i> Manage Activities
                </a>
                <a href="#" data-section="bursaries">
                    <i class="fas fa-coins"></i> Manage Bursaries
                </a>
                <a href="#" data-section="agents">
                    <i class="fas fa-user-tie"></i> Manage Agents
                </a>
                <a href="#" data-section="library">
                    <i class="fas fa-book"></i> Manage E-Library
                </a>
                <hr style="border-color: rgba(255,255,255,0.1);">
                <a href="#" data-section="settings">
                    <i class="fas fa-cog"></i> Settings
                </a>
                <a href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="content">
            <!-- Navbar -->
            <div class="navbar-custom d-flex justify-content-between align-items-center">
                <div>
                    <button class="btn btn-link d-md-none" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h4 class="mb-0" id="pageTitle">Dashboard</h4>
                </div>
                
                <div class="d-flex align-items-center">
                    <div class="dropdown me-3">
                        <button class="btn btn-link position-relative" data-bs-toggle="dropdown">
                            <i class="fas fa-bell"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                3
                            </span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="#">New school registered</a>
                            <a class="dropdown-item" href="#">5 new applications</a>
                            <a class="dropdown-item" href="#">System update available</a>
                        </div>
                    </div>
                    
                    <div class="dropdown">
                        <button class="btn btn-link dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle fa-2x"></i>
                            <span class="ms-2"><?php echo $_SESSION['admin_username']; ?></span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profile</a>
                            <a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Dashboard Section -->
            <div id="dashboardSection" class="content-section">
                <div class="row">
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-2">Total Schools</h6>
                                    <h2 class="mb-0" id="totalSchools">0</h2>
                                </div>
                                <i class="fas fa-school"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="stats-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-2">Total Activities</h6>
                                    <h2 class="mb-0" id="totalActivities">0</h2>
                                </div>
                                <i class="fas fa-running"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="stats-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-2">Total Bursaries</h6>
                                    <h2 class="mb-0" id="totalBursaries">0</h2>
                                </div>
                                <i class="fas fa-coins"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="stats-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-2">Total Agents</h6>
                                    <h2 class="mb-0" id="totalAgents">0</h2>
                                </div>
                                <i class="fas fa-user-tie"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Schools -->
                <div class="dashboard-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0">Recent Schools</h5>
                        <button class="btn btn-sm btn-primary" onclick="showSection('schools')">View All</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover" id="recentSchoolsTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Location</th>
                                    <th>Type</th>
                                    <th>Students</th>
                                    <th>Rating</th>
                                </tr>
                            </thead>
                            <tbody id="recentSchoolsBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Schools Management Section -->
            <div id="schoolsSection" class="content-section" style="display: none;">
                <div class="dashboard-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0">Manage Schools</h5>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#schoolModal">
                            <i class="fas fa-plus me-2"></i>Add New School
                        </button>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover" id="schoolsTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Location</th>
                                    <th>Type</th>
                                    <th>Students</th>
                                    <th>Rating</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="schoolsTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- School Modal -->
    <div class="modal fade" id="schoolModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add/Edit School</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="schoolForm">
                        <input type="hidden" id="schoolId">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">School Name</label>
                                <input type="text" class="form-control" id="schoolName" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Location</label>
                                <input type="text" class="form-control" id="schoolLocation" required>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Type</label>
                                <select class="form-control" id="schoolType" required>
                                    <option value="Public">Public</option>
                                    <option value="Private">Private</option>
                                    <option value="International">International</option>
                                </select>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Number of Students</label>
                                <input type="number" class="form-control" id="schoolStudents" required>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Rating (0-5)</label>
                                <input type="number" step="0.1" class="form-control" id="schoolRating" min="0" max="5" required>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" id="schoolDescription" rows="4" required></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveSchool()">Save School</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Load schools on page load
            loadSchools();
            loadDashboardStats();
            
            // Initialize DataTable
            $('#schoolsTable').DataTable({
                pageLength: 10,
                ordering: true,
                responsive: true
            });
        });
        
        // Load all schools
        function loadSchools() {
            $.ajax({
                url: 'dashboard.php',
                method: 'POST',
                data: { action: 'get_schools' },
                success: function(response) {
                    let schools = response;
                    let html = '';
                    
                    schools.forEach(function(school) {
                        html += `
                            <tr>
                                <td>${school.id}</td>
                                <td>${school.name}</td>
                                <td>${school.location}</td>
                                <td><span class="badge bg-primary">${school.type}</span></td>
                                <td>${school.students}</td>
                                <td>
                                    ${school.rating} 
                                    <i class="fas fa-star text-warning"></i>
                                </td>
                                <td>${new Date(school.created_at).toLocaleDateString()}</td>
                                <td class="action-btns">
                                    <button class="btn btn-sm btn-warning" onclick="editSchool(${school.id})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteSchool(${school.id})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    
                    $('#schoolsTableBody').html(html);
                    $('#recentSchoolsBody').html(html.slice(0, 5)); // Show only 5 recent schools
                    
                    // Update DataTable
                    $('#schoolsTable').DataTable().clear().rows.add($(html)).draw();
                }
            });
        }
        
        // Load dashboard stats
        function loadDashboardStats() {
            $.ajax({
                url: 'dashboard.php',
                method: 'POST',
                data: { action: 'get_schools' },
                success: function(schools) {
                    $('#totalSchools').text(schools.length);
                }
            });
        }
        
        // Save school
        function saveSchool() {
            let id = $('#schoolId').val();
            let action = id ? 'update_school' : 'add_school';
            
            let data = {
                action: action,
                name: $('#schoolName').val(),
                location: $('#schoolLocation').val(),
                type: $('#schoolType').val(),
                students: $('#schoolStudents').val(),
                rating: $('#schoolRating').val(),
                description: $('#schoolDescription').val()
            };
            
            if (id) {
                data.id = id;
            }
            
            $.ajax({
                url: 'dashboard.php',
                method: 'POST',
                data: data,
                success: function(response) {
                    if (response.success) {
                        showToast(response.message, 'success');
                        $('#schoolModal').modal('hide');
                        resetSchoolForm();
                        loadSchools();
                        loadDashboardStats();
                    } else {
                        showToast(response.message, 'error');
                    }
                }
            });
        }
        
        // Edit school
        function editSchool(id) {
            $.ajax({
                url: 'dashboard.php',
                method: 'POST',
                data: { action: 'get_school', id: id },
                success: function(school) {
                    $('#schoolId').val(school.id);
                    $('#schoolName').val(school.name);
                    $('#schoolLocation').val(school.location);
                    $('#schoolType').val(school.type);
                    $('#schoolStudents').val(school.students);
                    $('#schoolRating').val(school.rating);
                    $('#schoolDescription').val(school.description);
                    
                    $('#schoolModal').modal('show');
                }
            });
        }
        
        // Delete school
        function deleteSchool(id) {
            if (confirm('Are you sure you want to delete this school?')) {
                $.ajax({
                    url: 'dashboard.php',
                    method: 'POST',
                    data: { action: 'delete_school', id: id },
                    success: function(response) {
                        if (response.success) {
                            showToast(response.message, 'success');
                            loadSchools();
                            loadDashboardStats();
                        } else {
                            showToast(response.message, 'error');
                        }
                    }
                });
            }
        }
        
        // Reset school form
        function resetSchoolForm() {
            $('#schoolId').val('');
            $('#schoolForm')[0].reset();
        }
        
        // Show toast notification
        function showToast(message, type) {
            let toast = `
                <div class="toast toast-${type}">
                    <div class="d-flex align-items-center">
                        <i class="fas ${type === 'success' ? 'fa-check-circle text-success' : 'fa-exclamation-circle text-danger'} me-2"></i>
                        <div>${message}</div>
                    </div>
                </div>
            `;
            
            $('#toastContainer').append(toast);
            
            setTimeout(function() {
                $('.toast').first().remove();
            }, 3000);
        }
        
        // Section navigation
        function showSection(section) {
            $('.content-section').hide();
            $(`#${section}Section`).show();
            
            // Update active state in sidebar
            $('.sidebar-menu a').removeClass('active');
            $(`.sidebar-menu a[data-section="${section}"]`).addClass('active');
            
            // Update page title
            $('#pageTitle').text(section.charAt(0).toUpperCase() + section.slice(1));
            
            // Load section specific data
            if (section === 'schools') {
                loadSchools();
            }
        }
        
        $('[data-section]').click(function(e) {
            e.preventDefault();
            let section = $(this).data('section');
            showSection(section);
        });
        
        // Sidebar toggle for mobile
        $('#sidebarToggle').click(function() {
            $('.sidebar').toggleClass('active');
            $('.content').toggleClass('active');
        });
        
        // Reset form when modal is closed
        $('#schoolModal').on('hidden.bs.modal', function() {
            resetSchoolForm();
        });
    </script>
</body>
</html>