<?php
// session_start();
include_once "../config/auth.php"; // Adjust path as necessary
include_once "../config/config.php"; // Adjust path as necessary
// include_once "../helpers/auth_helper.php"; // Include the auth helper
include_once __DIR__. "/../includes/logger.php"; // Assuming logger.php exists and defines log_user_action
include_once __DIR__. "/../functions/message_functions.php"; // Include the message functions for formatMessageTime

// Only 'admin' role can access this page
$allowed_roles = ['admin', 'executive'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    die("Unauthorized access.");
}
// --- Admin Dashboard Data Fetching ---
    $id = $_SESSION['user_id'];
    $executive_user_id = $_SESSION['user_id'];
// Fetch unread messages count for the executive
$unread_messages_query = mysqli_query($conn, "SELECT COUNT(*) AS total_unread FROM messages WHERE receiver_id = $executive_user_id AND is_read = FALSE");
$new_messages_count = 0;
if ($unread_messages_query) {
    $unread_row = mysqli_fetch_assoc($unread_messages_query);
    $new_messages_count = $unread_row['total_unread'] ?? 0;
} else {
    error_log("Error fetching unread messages count for executive: " . mysqli_error($conn));
}
// 1. Total Number of Users
$total_users_query = mysqli_query($conn, "SELECT COUNT(id) AS total_users FROM users");
$total_users = $total_users_query ? mysqli_fetch_assoc($total_users_query)['total_users'] : 0;

// 2. Users by Role
$users_by_role_query = mysqli_query($conn, "SELECT role, COUNT(id) AS count FROM users GROUP BY role ORDER BY count DESC");
$users_by_role = [];
if ($users_by_role_query) {
    while ($row = mysqli_fetch_assoc($users_by_role_query)) {
        $users_by_role[] = $row;
    }
}

$recent_executive_messages_query = mysqli_query($conn, "
    SELECT m.id, m.subject, m.message_content, m.timestamp, m.is_read,
           s.names AS sender_name, s.role AS sender_role
    FROM messages m
    JOIN users s ON m.sender_id = s.id
    WHERE m.receiver_id = $executive_user_id
    ORDER BY m.timestamp DESC
    LIMIT 5
");
$recent_executive_messages = [];
if ($recent_executive_messages_query) {
    while ($msg = mysqli_fetch_assoc($recent_executive_messages_query)) {
        $recent_executive_messages[] = $msg;
    }
} else {
    error_log("Error fetching recent messages for executive: " . mysqli_error($conn));
}


// 3. Recently Created Users (e.g., last 5)
$recent_users_query = mysqli_query($conn, "SELECT id, names, email, role, time FROM users ORDER BY time DESC LIMIT 5");
$recent_users = [];
if ($recent_users_query) {
    while ($row = mysqli_fetch_assoc($recent_users_query)) {
        $recent_users[] = $row;
    }
}
if (!function_exists('formatMessageTime')) {
    function formatMessageTime($timestamp) {
        $message_time = strtotime($timestamp);
        $current_time = time();
        $diff = $current_time - $message_time;

        if ($diff < 60) { // Less than 1 minute
            return $diff . " Sec ago";
        } elseif ($diff < 3600) { // Less than 1 hour
            return round($diff / 60) . " Min ago";
        } elseif ($diff < 86400) { // Less than 24 hours (today)
            return date('h:i A', $message_time);
        } elseif ($diff < 172800) { // Less than 48 hours (yesterday)
            return "Yesterday";
        } else { // Older than yesterday
            return date('M j, Y', $message_time);
        }
    }
}
// 4. System Statistics (Placeholder - could be more detailed with log tables)
// For now, we'll just show total products and sales orders as examples of system activity
$total_products_query = mysqli_query($conn, "SELECT COUNT(id) AS total_products FROM products");
$total_products = $total_products_query ? mysqli_fetch_assoc($total_products_query)['total_products'] : 0;

$total_sales_orders_query = mysqli_query($conn, "SELECT COUNT(id) AS total_orders FROM sales_orders");
$total_sales_orders = $total_sales_orders_query ? mysqli_fetch_assoc($total_sales_orders_query)['total_orders'] : 0;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Admin Dashboard for Inventory Management System">
    <meta name="author" content="Your Name/Company Name">
    <meta name="keywords" content="admin, dashboard, users, roles, system, inventory">

    <!-- Title Page-->
    <title>Admin Dashboard</title>

    <!-- Fontfaces CSS-->
    <link href="../css/font-face.css" rel="stylesheet" media="all">
    <link href="../vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
    <link href="../vendor/font-awesome-5/css/fontawesome-all.min.css" rel="stylesheet" media="all">
    <link href="../vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">

    <!-- Bootstrap CSS-->
    <link href="../vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet" media="all">

    <!-- Vendor CSS-->
    <link href="../vendor/animsition/animsition.min.css" rel="stylesheet" media="all">
    <link href="../vendor/bootstrap-progressbar/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet" media="all">
    <link href="../vendor/wow/animate.css" rel="stylesheet" media="all">
    <link href="../vendor/css-hamburgers/hamburgers.min.css" rel="stylesheet" media="all">
    <link href="../vendor/slick/slick.css" rel="stylesheet" media="all">
    <link href="../vendor/select2/select2.min.css" rel="stylesheet" media="all">
    <link href="../vendor/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet" media="all">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.bundle.min.js"></script>


    <!-- Main CSS-->
    <link href="../css/theme.css" rel="stylesheet" media="all">

    <style>
        /* Custom styles for overview cards */
        .overview-item--c1 { background-color: #6f42c1; /* Purple for Admin */ }
        .overview-item--c2 { background-color: #0d6efd; /* Blue for Users by Role */ }
        .overview-item--c3 { background-color: #28a745; /* Green for System Stats */ }
        .overview-item--c4 { background-color: #dc3545; /* Red for Alerts/Issues */ }

        .overview-item .icon i {
            font-size: 48px;
            color: rgba(255,255,255,0.7);
        }
        .overview-item .text h2 {
            font-size: 2.2rem;
        }
        .overview-item .text span {
            font-size: 1rem;
            opacity: 0.9;
        }

        .user-role-badge {
            padding: .35em .65em;
            border-radius: .25rem;
            font-size: 75%;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            display: inline-block;
        }
        .user-role-admin { background-color: #dc3545; color: white; } /* Red */
        .user-role-executive { background-color: #ffc107; color: #212529; } /* Yellow */
        .user-role-sales { background-color: #0d6efd; color: white; } /* Blue */
        .user-role-warehouse { background-color: #28a745; color: white; } /* Green */
        .user-role-viewer { background-color: #6c757d; color: white; } /* Grey */

        /* Dark mode adjustments */
        .dark-mode body { background-color: #2c2c2c; color: #f0f0f0; }
        .dark-mode .page-wrapper { background-color: #2c2c2c; }
        .dark-mode .header-desktop, .dark-mode .aside-wrap .aside-menu { background-color: #3a3a3a; }
        .dark-mode .au-card { background-color: #4a4a4a; color: #f0f0f0; }
        .dark-mode .au-card-title { background-image: none !important; background-color: #343a40; }
        .dark-mode .table-responsive table th,
        .dark-mode .table-responsive table td {
            background-color: #4a4a4a;
            color: #f0f0f0;
            border-color: #666;
        }
        .dark-mode .table-responsive table.table-earning thead th {
             background-color: #343a40;
        }
        .dark-mode .au-btn-icon { background-color: #0d6efd; color: white; }
        .dark-mode .au-btn-icon i { color: white; }
        .toggle-theme-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            background-color: #0d6efd;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
    </style>
</head>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const theme = localStorage.getItem('theme') || 'light';
        document.body.classList.add(`${theme}-mode`);
        const themeButton = document.getElementById('toggleThemeButton');
        if (themeButton) {
            themeButton.textContent = theme === 'dark' ? 'Switch to Light Mode' : 'Switch to Dark Mode';
        }
    });

    function toggleTheme() {
        const isDark = document.body.classList.contains('dark-mode');
        document.body.classList.toggle('dark-mode', !isDark);
        document.body.classList.toggle('light-mode', isDark);
        localStorage.setItem('theme', isDark ? 'light' : 'dark');

        const themeButton = document.getElementById('toggleThemeButton');
        if (themeButton) {
            themeButton.textContent = isDark ? 'Switch to Dark Mode' : 'Switch to Light Mode';
        }
    }
</script>

<body class="animsition">
    <div class="page-wrapper">
        <!-- HEADER MOBILE-->
        <?php include_once '../executive/templates/header_mobile_menu.php'; ?>
        <!-- END HEADER MOBILE-->

        <!-- MENU SIDEBAR-->
        <?php include_once '../executive/templates/side_menu.php'; ?>
        <!-- END MENU SIDEBAR-->

        <!-- PAGE CONTAINER-->
        <div class="page-container">
            <!-- HEADER DESKTOP-->
            <?php include_once '../executive/templates/header_pc_menu.php'; ?>
            <!-- HEADER DESKTOP-->

            <!-- MAIN CONTENT-->
            <div class="main-content">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="overview-wrap">
                                    <h2 class="title-1">Admin Dashboard</h2>
                                    <a href="create_user.php" class="au-btn au-btn-icon au-btn--blue">
                                        <i class="zmdi zmdi-plus"></i>Add New User</a>
                                </div>
                            </div>
                        </div>
                        <div class="row m-t-25">
                            <!-- Total Users Card -->
                            <div class="col-sm-6 col-lg-3">
                                <div class="overview-item overview-item--c1">
                                    <div class="overview__inner">
                                        <div class="overview-box clearfix">
                                            <div class="icon">
                                                <i class="fas fa-users"></i>
                                            </div>
                                            <div class="text">
                                                <h2><?php echo number_format($total_users); ?></h2>
                                                <span>Total System Users</span>
                                            </div>
                                        </div>
                                        <div class="overview-chart">
                                            <!-- Chart can be added here if needed, e.g., daily new users -->
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Users by Role Card -->
                            <div class="col-sm-6 col-lg-3">
                                <div class="overview-item overview-item--c2">
                                    <div class="overview__inner">
                                        <div class="overview-box clearfix">
                                            <div class="icon">
                                                <i class="fas fa-user-tag"></i>
                                            </div>
                                            <div class="text">
                                                <h2>&nbsp;</h2> <!-- Placeholder for visual alignment -->
                                                <span>Users by Role</span>
                                            </div>
                                        </div>
                                        <div class="overview-chart">
                                            <canvas id="usersByRoleChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- System Statistics Card -->
                            <div class="col-sm-6 col-lg-3">
                                <div class="overview-item overview-item--c3">
                                    <div class="overview__inner">
                                        <div class="overview-box clearfix">
                                            <div class="icon">
                                                <i class="fas fa-cogs"></i>
                                            </div>
                                            <div class="text">
                                                <h2><?php echo number_format($total_products); ?></h2>
                                                <span>Total Products</span>
                                            </div>
                                        </div>
                                        <div class="overview-chart">
                                            <!-- Chart can be added here -->
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Another System Stat / Quick Link Card -->
                            <div class="col-sm-6 col-lg-3">
                                <div class="overview-item overview-item--c4">
                                    <div class="overview__inner">
                                        <div class="overview-box clearfix">
                                            <div class="icon">
                                                <i class="fas fa-shopping-cart"></i>
                                            </div>
                                            <div class="text">
                                                <h2><?php echo number_format($total_sales_orders); ?></h2>
                                                <span>Total Sales Orders</span>
                                            </div>
                                        </div>
                                        <div class="overview-chart">
                                            <!-- Chart can be added here -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <h2 class="title-1 m-b-25">Recently Created Users</h2>
                                <div class="table-responsive table--no-card m-b-40">
                                    <table class="table table-borderless table-striped table-earning">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Role</th>
                                                <th>Created At</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($recent_users)): ?>
                                                <?php foreach ($recent_users as $user): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                                                        <td><?php echo htmlspecialchars($user['names']); ?></td>
                                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                                        <td>
                                                            <span class="user-role-badge user-role-<?php echo strtolower($user['role']); ?>">
                                                                <?php echo htmlspecialchars(ucwords($user['role'])); ?>
                                                            </span>
                                                        </td>
                                                        <td><?php echo date("Y-m-d H:i", strtotime($user['time'])); ?></td>
                                                        <?php if ($user['role'] != "executive") 
                                                        {
                                                        ?>
                                                        <td>
                                                            <a href="../edits/edit_user.php?id=<?php echo urlencode($user['id']); ?>" class="btn btn-warning btn-sm rounded-pill">Edit</a>
                                                            <?php if ($user['id'] !== $_SESSION['user_id']) : // Prevent user from deleting themselves ?>
                                                                <button type="button" class="btn btn-danger btn-sm rounded-pill delete-btn" data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal" data-user-id="<?php echo htmlspecialchars($user['id']); ?>" data-user-name="<?php echo htmlspecialchars($user['names']); ?>">Delete</button>
                                                            <?php endif; ?>
                                                        </td>
                                                            <?php } else {
                                                                ?>
                                                                <td>
                                                                    <p class="text text-warning">Can't edit or delete executive</p>
                                                                </td>
                                                            <?php } ?>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="6" class="text-center">No recently created users found.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="copyright">
                                    <p>Copyright © 2018 Colorlib. All rights reserved. Template by <a href="https://colorlib.com">Colorlib</a>.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END MAIN CONTENT-->
        </div>
        <!-- END PAGE CONTAINER-->

        <button class="toggle-theme-btn" id="toggleThemeButton" onclick="toggleTheme()">Switch to Dark Mode</button>
    </div>

    <!-- Jquery JS-->
    <script src="../vendor/jquery-3.2.1.min.js"></script>
    <!-- Bootstrap JS-->
    <script src="../vendor/bootstrap-4.1/popper.min.js"></script>
    <script src="../vendor/bootstrap-4.1/bootstrap.min.js"></script>
    <!-- Vendor JS-->
    <script src="../vendor/slick/slick.min.js"></script>
    <script src="../vendor/wow/wow.min.js"></script>
    <script src="../vendor/animsition/animsition.min.js"></script>
    <script src="../vendor/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>
    <script src="../vendor/counter-up/jquery.waypoints.min.js"></script>
    <script src="../vendor/counter-up/jquery.counterup.min.js"></script>
    <script src="../vendor/circle-progress/circle-progress.min.js"></script>
    <script src="../vendor/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../vendor/select2/select2.min.js"></script>

    <!-- Main JS-->
    <script src="../js/main.js"></script>

    <script>
        $(document).ready(function() {
            // Data for Users by Role Chart
            const usersByRoleData = <?php echo json_encode($users_by_role); ?>;
            const roleLabels = usersByRoleData.map(item => item.role);
            const roleCounts = usersByRoleData.map(item => item.count);

            // Dynamically assign colors based on role
            const roleColors = roleLabels.map(role => {
                switch (role.toLowerCase()) {
                    case 'admin': return '#dc3545'; // Red
                    case 'executive': return '#ffc107'; // Yellow
                    case 'sales': return '#0d6efd'; // Blue
                    case 'warehouse': return '#28a745'; // Green
                    case 'viewer': return '#6c757d'; // Grey
                    default: return '#666'; // Default for unknown roles
                }
            });

            var ctxUsersByRole = document.getElementById("usersByRoleChart");
            if (ctxUsersByRole) {
                ctxUsersByRole.height = 130;
                var myChartUsersByRole = new Chart(ctxUsersByRole, {
                    type: 'doughnut',
                    data: {
                        datasets: [{
                            data: roleCounts,
                            backgroundColor: roleColors,
                            hoverBackgroundColor: roleColors,
                            borderWidth: 0
                        }],
                        labels: roleLabels
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutoutPercentage: 70, // Make it a doughnut chart
                        legend: {
                            display: false // Hide default legend, we'll create a custom one if needed
                        },
                        tooltips: {
                            enabled: true,
                            mode: 'index',
                            callbacks: {
                                label: function(tooltipItem, data) {
                                    var label = data.labels[tooltipItem.index];
                                    var value = data.datasets[0].data[tooltipItem.index];
                                    return label + ': ' + value + ' user(s)';
                                }
                            }
                        }
                    }
                });
            }

            // Delete Confirmation Modal Logic (copied from manage_users.php)
            let userIdToDelete = '';
            let userNameToDelete = '';

            $('.delete-btn').on('click', function() {
                userIdToDelete = $(this).data('user-id');
                userNameToDelete = $(this).data('user-name');
                $('#userIdToDelete').text(userIdToDelete);
                $('#userNameToDelete').text(userNameToDelete);
            });

            $('#confirmDeleteButton').on('click', function() {
                window.location.href = '../edits/delete_user.php?id=' + encodeURIComponent(userIdToDelete);
            });
        });
    </script>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete user: <strong id="userNameToDelete"></strong> (ID: <strong id="userIdToDelete"></strong>)? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger rounded-pill" id="confirmDeleteButton">Delete</button>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
<?php {
    // This redirect should ideally be handled by auth.php or require_role()
    // but kept here as a fallback if auth.php is not configured to redire
}
?>
