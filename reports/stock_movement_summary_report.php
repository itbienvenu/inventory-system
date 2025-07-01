<?php
include_once "../config/auth.php";
include_once "../config/config.php";

// Query directly from stock_movements without any JOINs
$result = mysqli_query($conn, "
  SELECT id, product_id, movement_type, quantity_change, current_stock_after, 
         reference_document_type, reference_document_id, reference_document_number, 
         notes, moved_by, movement_timestamp 
  FROM stock_movements
  ORDER BY movement_timestamp DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Stock Movement Summary</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Bootstrap CSS -->
  <link href="../vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet">
  
  <!-- DataTables Core CSS from CDN -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  
  <!-- DataTables Buttons CSS from CDN -->
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

  <style>
    /* Basic styling for better readability and spacing */
    body {
      font-family: 'Inter', sans-serif; /* Using Inter font for modern look */
      background-color: #f8f9fa;
    }
    .container {
      background-color: #ffffff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      margin-top: 20px;
    }
    h2 {
      color: #343a40;
      margin-bottom: 20px;
      text-align: center;
    }
    .table thead th {
      background-color: #343a40;
      color: #ffffff;
      border-color: #454d55;
    }
    .table tbody tr:nth-child(even) {
      background-color: #f2f2f2;
    }
    .table tbody tr:hover {
      background-color: #e9ecef;
    }
    /* Style for DataTables buttons container */
    .dt-buttons {
      margin-bottom: 15px;
    }
    .dt-buttons .dt-button {
      background-color: #007bff;
      color: white;
      border: none;
      padding: 8px 15px;
      margin-right: 5px;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    .dt-buttons .dt-button:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body class="p-4">
  <div class="container">
    <h2>Stock Movement Summary</h2>
    <table id="movementTable" class="table table-bordered table-striped">
      <thead class="thead-dark">
        <tr>
          <th>ID</th>
          <th>Product ID</th>
          <th>Type</th>
          <th>Quantity Change</th>
          <th>Stock After</th>
          <th>Reference Type</th>
          <th>Reference ID</th>
          <th>Document No.</th>
          <th>Notes</th>
          <th>Moved By</th>
          <th>Timestamp</th>
        </tr>
      </thead>
      <tbody>
        <?php while($r = mysqli_fetch_assoc($result)): ?>
          <tr>
            <td><?= $r['id'] ?></td>
            <td><?= $r['product_id'] ?></td>
            <td><?= htmlspecialchars($r['movement_type']) ?></td>
            <td><?= $r['quantity_change'] ?></td>
            <td><?= $r['current_stock_after'] ?></td>
            <td><?= htmlspecialchars($r['reference_document_type']) ?></td>
            <td><?= $r['reference_document_id'] ?></td>
            <td><?= htmlspecialchars($r['reference_document_number']) ?></td>
            <td><?= htmlspecialchars($r['notes']) ?></td>
            <td><?php 
                // Fetch user name for 'moved_by' ID
                $userId = $r['moved_by'];
                $userQuery = mysqli_query($conn, "SELECT names FROM users WHERE id = {$userId}");
                if ($userQuery && mysqli_num_rows($userQuery) > 0) {
                    $user = mysqli_fetch_assoc($userQuery);
                    echo htmlspecialchars($user['names']);
                } else {
                    echo "N/A"; // Or handle the case where user is not found
                }
            ?></td>
            <td><?= $r['movement_timestamp'] ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <!-- JavaScript Libraries (jQuery first, then DataTables and its extensions) -->
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

  <!-- DataTables Core JS -->
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

  <!-- DataTables Buttons Extension JS -->
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
  
  <!-- File export dependencies for Buttons -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script> <!-- For Excel and CSV -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script> <!-- For PDF -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script> <!-- For PDF fonts -->

  <!-- Specific Button types -->
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script> <!-- HTML5 export buttons (CSV, Excel, PDF) -->
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script> <!-- Print button -->

  <!-- Initialize DataTable -->
  <script>
    $(function() {
      console.log("jQuery document ready. Attempting to initialize DataTable...");
      $('#movementTable').DataTable({
        dom: 'Bfrtip', // 'B' for Buttons, 'f' for filtering, 'r' for processing, 't' for table, 'i' for information, 'p' for pagination
        buttons: [
          'copy',
          'csv',
          'excel',
          'pdf',
          'print'
        ],
        pageLength: 25
      });
      console.log("DataTable initialization attempt complete.");
    });
  </script>

</body>
</html>
