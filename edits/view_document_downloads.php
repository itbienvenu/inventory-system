<?php
include_once "../config/auth.php"; // Adjust path as necessary
include_once "../config/config.php"; // Adjust path as necessary

$allowed_roles = ['executive','admin'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    die("Unauthorized access.");
}

// Fetch all document downloads
$downloads_query = mysqli_query($conn, "
    SELECT dd.*, u.names AS downloaded_by_username
    FROM document_downloads dd
    LEFT JOIN users u ON dd.downloaded_by = u.id
    ORDER BY dd.downloaded_at DESC
");

if (!$downloads_query) {
    die("Error fetching document download logs: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Download Log</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 30px;
            margin-bottom: 30px;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #6c757d; /* Secondary color for downloads */
            color: white;
            font-weight: bold;
            border-radius: 8px 8px 0 0 !important;
            padding: 15px;
        }
        .table thead th {
            background-color: #e9ecef;
            border-bottom: 2px solid #dee2e6;
        }
        .table tbody tr:hover {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-4 text-center">Document Download Log</h2>

        <!-- Success/Error Message Display -->
        <?php if (isset($_SESSION['message'])) : ?>
            <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
            ?>
        <?php endif; ?>

        <div class="card mb-4 rounded-3">
            <div class="card-header">
                All Document Downloads
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Download ID</th>
                                <th>Document Type</th>
                                <th>Document Number</th>
                                <th>Downloaded By</th>
                                <th>Downloaded At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($downloads_query) > 0) : ?>
                                <?php while ($download = mysqli_fetch_assoc($downloads_query)) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($download['id']); ?></td>
                                        <td><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $download['document_type']))); ?></td>
                                        <td><?php echo htmlspecialchars($download['document_number']); ?></td>
                                        <td><?php echo htmlspecialchars($download['downloaded_by_username'] ? $download['downloaded_by_username'] : 'N/A (User Deleted)'); ?></td>
                                        <td><?php echo htmlspecialchars($download['downloaded_at']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="5" class="text-center">No document downloads recorded yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="text-center">
            <a href="javascript:history.back()" class="btn btn-secondary rounded-pill px-4">Go Back</a>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
