<?php
// session_start(); // Ensure session is started at the very top

include_once (__DIR__."/../config/auth.php");
include_once (__DIR__."/../config/config.php");
include_once __DIR__. "/../includes/logger.php"; // Assuming logger.php exists and defines log_user_action
include_once __DIR__. "/../functions/message_functions.php"; // Include the message functions for formatMessageTime

// Only 'admin' or 'executive' roles can access this page
$allowed_roles = ['admin', 'executive'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    die("Unauthorized access.");
}

$id = $_SESSION['user_id'];
$logged_in_user_id = $_SESSION['user_id'];
$logged_in_user_role = $_SESSION['role'];



// Fetch user's name
$user_name_query = mysqli_query($conn, "SELECT names FROM users WHERE id=$logged_in_user_id");
$user_name = '';
if ($user_name_query && mysqli_num_rows($user_name_query) > 0) {
    $user_row = mysqli_fetch_assoc($user_name_query);
    $user_name = htmlspecialchars($user_row['names']);
}

// --- Dashboard Data Fetching ---

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

// 3. Recently Created Users (e.g., last 5)
$recent_users_query = mysqli_query($conn, "SELECT id, names, email, role, time FROM users ORDER BY time DESC LIMIT 5");
$recent_users = [];
if ($recent_users_query) {
    while ($row = mysqli_fetch_assoc($recent_users_query)) {
        $recent_users[] = $row;
    }
}

// 4. System Statistics (Placeholder - could be more detailed with log tables)
$total_products_query = mysqli_query($conn, "SELECT COUNT(id) AS total_products FROM products");
$total_products = $total_products_query ? mysqli_fetch_assoc($total_products_query)['total_products'] : 0;

$total_sales_orders_query = mysqli_query($conn, "SELECT COUNT(id) AS total_orders FROM sales_orders");
$total_sales_orders = $total_sales_orders_query ? mysqli_fetch_assoc($total_sales_orders_query)['total_orders'] : 0;


// --- Message Data Fetching for the Logged-in User (Admin or Executive) ---
// Fetch unread messages count for the logged-in user
$unread_messages_query = mysqli_query($conn, "SELECT COUNT(*) AS total_unread FROM messages WHERE receiver_id = $logged_in_user_id AND is_read = FALSE");
$new_messages_count = 0;
if ($unread_messages_query) {
    $unread_row = mysqli_fetch_assoc($unread_messages_query);
    $new_messages_count = $unread_row['total_unread'] ?? 0;
} else {
    error_log("Error fetching unread messages count for user $logged_in_user_id: " . mysqli_error($conn));
}

// Fetch recent messages for the logged-in user (e.g., last 5)
$recent_executive_messages_query = mysqli_query($conn, "
    SELECT m.id, m.subject, m.message_content, m.timestamp, m.is_read, m.sender_id,
           s.names AS sender_name, s.role AS sender_role
    FROM messages m
    JOIN users s ON m.sender_id = s.id
    WHERE m.receiver_id = $logged_in_user_id
    ORDER BY m.timestamp DESC
    LIMIT 5
");
$recent_executive_messages = [];
if ($recent_executive_messages_query) {
    while ($msg = mysqli_fetch_assoc($recent_executive_messages_query)) {
        $recent_executive_messages[] = $msg;
    }
} else {
    error_log("Error fetching recent messages for user $logged_in_user_id: " . mysqli_error($conn));
}

// Helper function to format time (defined here if not already in message_functions.php)
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

log_user_action("Visited Admin Dashboard", "$logged_in_user_role user $user_name viewed dashboard");

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

        /* Styles for message items */
        .au-message__item.unread, .mess__item.unread {
            font-weight: bold;
            background-color: #e6f7ff; /* Light blue for unread messages */
        }
        .au-message__item:hover, .mess__item:hover {
            cursor: pointer;
            background-color: #f0f0f0; /* Lighter background on hover */
        }
        .au-message__item-text .text p, .mess__item .content p {
            margin-bottom: 0.2rem; /* Reduce space between subject and content preview */
            line-height: 1.2;
        }
        .au-message__item-text .text small, .mess__item .content small {
            font-size: 0.85em;
            color: #666;
        }
        /* Style for the modal content */
        .message-detail-modal .modal-body {
            white-space: pre-wrap; /* Preserve whitespace and line breaks */
        }
        .reply-form-section {
            border-top: 1px solid #eee;
            padding-top: 15px;
            margin-top: 15px;
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
                            <!-- New Messages for Logged-in User (Admin or Executive) -->
                            <div class="col-lg-6">
                                <div class="au-card au-card--no-shadow au-card--no-pad m-b-40">
                                    <div class="au-card-title" style="background-image:url('../executive/images/bg-title-02.jpg');">
                                        <div class="bg-overlay bg-overlay--blue"></div>
                                        <h3>
                                            <i class="zmdi zmdi-comment-text"></i>New Messages
                                        </h3>
                                        <a href="../seller/send_message.php" class="au-btn-plus"> <!-- Link to send_message.php -->
                                            <i class="zmdi zmdi-plus"></i>
                                        </a>
                                    </div>
                                    <div class="au-inbox-wrap js-inbox-wrap">
                                        <div class="au-message js-list-load">
                                            <div class="au-message__noti">
                                                <p>You Have
                                                    <span><?= $new_messages_count ?></span>
                                                    new messages
                                                </p>
                                            </div>
                                            <div class="au-message-list" id="executiveMessageList">
                                                <?php if (!empty($recent_executive_messages)): ?>
                                                    <?php foreach ($recent_executive_messages as $message): ?>
                                                        <div class="au-message__item <?= $message['is_read'] == 0 ? 'unread' : '' ?>"
                                                             data-message-id="<?= htmlspecialchars($message['id']) ?>"
                                                             data-sender-id="<?= htmlspecialchars($message['sender_id']) ?>"
                                                             data-subject="<?= htmlspecialchars($message['subject']) ?>"
                                                             data-content="<?= htmlspecialchars($message['message_content']) ?>"
                                                             data-timestamp="<?= htmlspecialchars($message['timestamp']) ?>"
                                                             data-sender-name="<?= htmlspecialchars($message['sender_name']) ?>"
                                                             data-sender-role="<?= htmlspecialchars($message['sender_role']) ?>">
                                                            <div class="au-message__item-inner">
                                                                <div class="au-message__item-text">
                                                                    <div class="avatar-wrap">
                                                                        <div class="avatar">
                                                                            <!-- Using a generic placeholder image -->
                                                                            <img src="https://placehold.co/40x40/cccccc/333333?text=User" alt="<?= htmlspecialchars($message['sender_name']) ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="text">
                                                                        <h5 class="name"><?= htmlspecialchars($message['sender_name']) ?> (<?= htmlspecialchars(ucfirst($message['sender_role'])) ?>)</h5>
                                                                        <p><?= htmlspecialchars(substr($message['subject'], 0, 50)) ?><?= (strlen($message['subject']) > 50 ? '...' : '') ?></p>
                                                                        <small><?= htmlspecialchars(substr($message['message_content'], 0, 70)) ?><?= (strlen($message['message_content']) > 70 ? '...' : '') ?></small>
                                                                    </div>
                                                                </div>
                                                                <div class="au-message__item-time">
                                                                    <span><?= formatMessageTime($message['timestamp']) ?></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <div class="text-center p-3 text-muted">No new messages.</div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="au-message__footer">
                                                <!-- Link to a full inbox page for the logged-in user -->
                                                <a href="../seller/send_message.php?tab=inbox" class="au-btn au-btn-load js-load-btn">load more</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Tasks for User Block (remains unchanged) -->
                            <div class="col-lg-6">
                                <div class="au-card au-card--no-shadow au-card--no-pad m-b-40">
                                    <div class="au-card-title" style="background-image:url('../executive/images/bg-title-01.jpg');">
                                        <div class="bg-overlay bg-overlay--blue"></div>
                                        <h3>
                                            <i class="zmdi zmdi-account-calendar"></i>Tasks for <?php echo $user_name; ?></h3>
                                        <button class="au-btn-plus">
                                            <i class="zmdi zmdi-plus"></i>
                                        </button>
                                    </div>
                                    <div class="au-task js-list-load">
                                        <div class="au-task__title">
                                            <p>Your Recent Tasks</p>
                                        </div>
                                        <div class="au-task-list js-scrollbar3">
                                            <div class="au-task__item au-task__item--danger">
                                                <div class="au-task__item-inner">
                                                    <h5 class="task">
                                                        <a href="#">Review pending Sales Orders</a>
                                                    </h5>
                                                    <span class="time">Yesterday</span>
                                                </div>
                                            </div>
                                            <div class="au-task__item au-task__item--warning">
                                                <div class="au-task__item-inner">
                                                    <h5 class="task">
                                                        <a href="#">Follow up on overdue Invoices</a>
                                                    </h5>
                                                    <span class="time">2 days ago</span>
                                                </div>
                                            </div>
                                            <div class="au-task__item au-task__item--primary">
                                                <div class="au-task__item-inner">
                                                    <h5 class="task">
                                                        <a href="#">Check stock levels for low items</a>
                                                    </h5>
                                                    <span class="time">This Week</span>
                                                </div>
                                            </div>
                                            <div class="au-task__item au-task__item--success">
                                                <div class="au-task__item-inner">
                                                    <h5 class="task">
                                                        <a href="#">Process new Purchase Orders</a>
                                                    </h5>
                                                    <span class="time">Today</span>
                                                </div>
                                            </div>
                                            <div class="au-task__item au-task__item--danger js-load-item">
                                                <div class="au-task__item-inner">
                                                    <h5 class="task">
                                                        <a href="#">Update product descriptions</a>
                                                    </h5>
                                                    <span class="time">Last Month</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="au-task__footer">
                                            <button class="au-btn au-btn-load js-load-btn">load more</button>
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
                                                        <?php if ($user['role'] != "executive") : ?>
                                                        <td>
                                                            <a href="../edits/edit_user.php?id=<?php echo urlencode($user['id']); ?>" class="btn btn-warning btn-sm rounded-pill">Edit</a>
                                                            <?php if ($user['id'] !== $_SESSION['user_id']) : // Prevent user from deleting themselves ?>
                                                                <button type="button" class="btn btn-danger btn-sm rounded-pill delete-btn" data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal" data-user-id="<?php echo htmlspecialchars($user['id']); ?>" data-user-name="<?php echo htmlspecialchars($user['names']); ?>">Delete</button>
                                                            <?php endif; ?>
                                                        </td>
                                                        <?php else : ?>
                                                        <td>
                                                            <p class="text text-warning">Can't edit or delete executive</p>
                                                        </td>
                                                        <?php endif; ?>
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

    <!-- Message Detail Modal (for dashboard context) -->
    <div class="modal fade" id="messageDetailModal" tabindex="-1" aria-labelledby="messageDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="messageDetailModalLabel">Message Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>From:</strong> <span id="modalSender"></span></p>
                    <p><strong>Subject:</strong> <span id="modalSubject"></span></p>
                    <p><strong>Date:</strong> <span id="modalDate"></span></p>
                    <hr>
                    <p id="modalContent"></p>

                    <!-- Reply Form Section (initially hidden) -->
                    <div id="replyFormSection" class="reply-form-section" style="display:none;">
                        <h5>Reply to this message</h5>
                        <form id="messageReplyForm">
                            <input type="hidden" id="replyOriginalMessageId" name="original_message_id">
                            <input type="hidden" id="replyReceiverId" name="receiver_id">
                            <div class="mb-3">
                                <label for="replySubject" class="form-label">Subject</label>
                                <input type="text" class="form-control" id="replySubject" name="subject" required>
                            </div>
                            <div class="mb-3">
                                <label for="replyContent" class="form-label">Your Reply</label>
                                <textarea class="form-control" id="replyContent" name="message_content" rows="4" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-success">Send Reply</button>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-info" id="toggleReplyFormBtn">Reply</button> <!-- Changed ID -->
                </div>
            </div>
        </div>
    </div>

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
    <!-- Chart.js is included via CDN at the top of head, no need for vendor/chartjs/Chart.bundle.min.js here -->
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
            const roleColors = roleLabels.map(item => {
                switch (item.toLowerCase()) {
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


            // Function to format time for display in JS (client-side)
            function formatTimeForDisplay(timestamp) {
                const messageTime = new Date(timestamp);
                const now = new Date();
                const diffSeconds = Math.floor((now.getTime() - messageTime.getTime()) / 1000);

                if (diffSeconds < 60) { // Less than 1 minute
                    return diffSeconds + " Sec ago";
                } else if (diffSeconds < 3600) { // Less than 1 hour
                    return Math.round(diffSeconds / 60) + " Min ago";
                } else if (diffSeconds < 86400) { // Less than 24 hours (today)
                    return messageTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                } else if (diffSeconds < 172800) { // Less than 48 hours (yesterday)
                    return "Yesterday";
                } else { // Older than yesterday
                    return messageTime.toLocaleDateString([], { month: 'short', day: 'numeric', year: 'numeric' });
                }
            }

            // Function to display a Bootstrap alert message
            function displayMessage(type, text) {
                let container = $('#systemMessageContainer');
                if (container.length === 0) {
                    // If not found, create it (e.g., at the top of the main content)
                    $('.main-content .section__content').prepend('<div id="systemMessageContainer" class="container-fluid mt-3"></div>');
                    container = $('#systemMessageContainer');
                }

                const alertHtml = `
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        ${text}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                container.append(alertHtml);
                setTimeout(function() {
                    container.find('.alert').alert('close');
                }, 5000);
            }

            // Event listener for clicking on a message item (both main block and header dropdown)
            $(document).on('click', '.au-message__item, .mess__item', function() {
                const messageElement = $(this);
                const messageId = messageElement.data('message-id');
                const senderId = messageElement.data('sender-id');
                const subject = messageElement.data('subject');
                const content = messageElement.data('content');
                const timestamp = messageElement.data('timestamp');
                const senderName = messageElement.data('sender-name');
                const senderRole = messageElement.data('sender-role');

                // Populate modal with message details
                $('#modalSender').text(`${senderName} (${senderRole.charAt(0).toUpperCase() + senderRole.slice(1)})`);
                $('#modalSubject').text(subject);
                $('#modalDate').text(formatTimeForDisplay(timestamp));
                $('#modalContent').text(content);

                // Set data for reply form
                $('#replyOriginalMessageId').val(messageId);
                $('#replyReceiverId').val(senderId);
                $('#replySubject').val(`Re: ${subject}`);
                $('#replyContent').val(''); // Clear previous reply content

                // Hide reply form initially when modal opens
                $('#replyFormSection').hide();
                $('#toggleReplyFormBtn').text('Reply'); // Reset button text

                // Mark message as read via AJAX if it's currently unread
                if (messageElement.hasClass('unread')) {
                    $.ajax({
                        url: '../seller/mark_message_read_ajax.php', // Corrected path from admin/index.php
                        type: 'POST',
                        data: { message_id: messageId },
                        dataType: 'json', // Explicitly expect JSON
                        success: function(response) {
                            if (response.status === 'success') {
                                messageElement.removeClass('unread'); // Remove unread styling
                                // Update unread count in both places
                                $('.au-message__noti span').each(function() {
                                    let currentCount = parseInt($(this).text());
                                    if (!isNaN(currentCount) && currentCount > 0) {
                                        $(this).text(currentCount - 1);
                                    }
                                });
                                $('.noti__item .quantity').each(function() {
                                    let currentCount = parseInt($(this).text());
                                    if (!isNaN(currentCount) && currentCount > 0) {
                                        $(this).text(currentCount - 1);
                                    }
                                });
                                $('.mess__title p').each(function() {
                                    let currentText = $(this).text();
                                    let currentCountMatch = currentText.match(/\d+/);
                                    if (currentCountMatch && !isNaN(currentCountMatch[0])) {
                                        let currentCount = parseInt(currentCountMatch[0]);
                                        if (currentCount > 0) {
                                            $(this).text(currentText.replace(currentCount, currentCount - 1));
                                        }
                                    }
                                });

                            } else {
                                console.error("Failed to mark message as read:", response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX Error marking message as read: ", status, error, xhr.responseText);
                        }
                    });
                }

                // Show the modal using Bootstrap 4 syntax
                $('#messageDetailModal').modal('show');
            });

            // Toggle Reply Form visibility
            $('#toggleReplyFormBtn').on('click', function() {
                $('#replyFormSection').slideToggle(function() {
                    if ($(this).is(':visible')) {
                        $('#toggleReplyFormBtn').text('Hide Reply Form');
                        $('#replyContent').focus(); // Focus on textarea when shown
                    } else {
                        $('#toggleReplyFormBtn').text('Reply');
                    }
                });
            });

            // Handle Reply Form Submission via AJAX
            $('#messageReplyForm').on('submit', function(e) {
                e.preventDefault(); // Prevent default form submission

                const formData = {
                    original_message_id: $('#replyOriginalMessageId').val(),
                    receiver_id: $('#replyReceiverId').val(),
                    subject: $('#replySubject').val(),
                    message_content: $('#replyContent').val()
                };

                $.ajax({
                    url: '../seller/send_reply_ajax.php', // Corrected path from admin/index.php
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            displayMessage('success', response.message);
                            // Close the modal using Bootstrap 4 syntax
                            $('#messageDetailModal').modal('hide');
                            // Clear form after successful send
                            $('#messageReplyForm')[0].reset();
                            $('#replyOriginalMessageId').val('');
                            $('#replyReceiverId').val('');

                            // You might want to re-fetch and update the message list here
                            // For simplicity, we're relying on a page refresh or manual update for now.
                        } else {
                            displayMessage('danger', response.message || 'Failed to send reply.');
                        }
                    },
                    error: function(xhr, status, error) {
                        displayMessage('danger', 'An error occurred while sending the reply.');
                        console.error("AJAX Error sending reply: ", status, error, xhr.responseText);
                    }
                });
            });
        });
    </script>

</body>

</html>
