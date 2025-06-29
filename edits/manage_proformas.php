<?php
session_start();
include_once "../config/config.php"; // Adjust path as necessary for your config file

// Check if the user is authorized (e.g., an executive or admin)
// You might want to allow 'admin' or other roles to view this list.
if ($_SESSION['role'] !== 'executive') {
    die("Unauthorized access.");
}

// Fetch all proforma invoices
$proformas_query = mysqli_query($conn, "
    SELECT pi.*, u.names AS created_by_username
    FROM proforma_invoices pi
    JOIN users u ON pi.created_by = u.id
    ORDER BY pi.created_at DESC
");

if (!$proformas_query) {
    die("Error fetching proforma invoices: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Proforma Invoices</title>
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
            background-color: #6c757d; /* Darker header for list page */
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
        .action-buttons a {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-4 text-center">All Proforma Invoices</h2>

        <div class="card mb-4 rounded-3">
            <div class="card-header">
                List of Proformas
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Invoice Number</th>
                                <th>Company</th>
                                <th>Country</th>
                                <th>Created By</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($proformas_query) > 0) : ?>
                                <?php while ($proforma = mysqli_fetch_assoc($proformas_query)) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($proforma['invoice_number']); ?></td>
                                        <td><?php echo htmlspecialchars($proforma['company']); ?></td>
                                        <td><?php echo htmlspecialchars($proforma['country']); ?></td>
                                        <td><?php echo htmlspecialchars($proforma['created_by_username']); ?></td>
                                        <td><?php echo htmlspecialchars($proforma['created_at']); ?></td>
                                        <td class="action-buttons text-center">
                                            <a href="view_proforma.php?invoice=<?php echo urlencode($proforma['invoice_number']); ?>" class="btn btn-primary btn-sm rounded-pill">View</a>
                                            <a href="edit_proforma.php?invoice=<?php echo urlencode($proforma['invoice_number']); ?>" class="btn btn-warning btn-sm rounded-pill">Edit</a>
                                            <a href="generate_proforma_pdf.php?invoice=<?php echo urlencode($proforma['invoice_number']); ?>" class="btn btn-info btn-sm rounded-pill">PDF</a>
                                            <!-- Add a delete button later if needed, with a confirmation via JavaScript -->
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="6" class="text-center">No proforma invoices found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="text-center">
            <a href="javascript:history.back()" class="btn btn-secondary rounded-pill px-4">Go Back</a>
            <!-- You might also link back to the "create proforma" page here -->
            <a href="../executive/form.php#proforma-part" class="btn btn-success rounded-pill px-4 ms-2">Create New Proforma</a>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
