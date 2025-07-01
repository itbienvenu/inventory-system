<?php
include_once "../config/auth.php";
include_once "../config/config.php";
$result = mysqli_query($conn, "SELECT name, sku, quantity, cost_price, price FROM products");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Inventory Valuation</title>
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
    <h2 class="mb-4">Inventory Valuation Report</h2>
    <table id="valuationTable" class="table table-bordered table-striped">
      <thead>
        <tr><th>Product</th><th>SKU</th><th>Qty</th><th>Cost Price</th><th>Total Cost</th><th>Selling Price</th><th>Total Sell Value</th></tr>
      </thead>
      <tbody>
        <?php
        $totalCost = $totalSell = 0;
        while ($r = mysqli_fetch_assoc($result)) {
          $cost = $r['quantity'] * $r['cost_price'];
          $sell = $r['quantity'] * $r['price'];
          $totalCost += $cost;
          $totalSell += $sell;
          echo "<tr>
            <td>" . htmlspecialchars($r['name']) . "</td>
            <td>" . htmlspecialchars($r['sku']) . "</td>
            <td>" . htmlspecialchars($r['quantity']) . "</td>
            <td>" . number_format($r['cost_price'],2) . "</td>
            <td>" . number_format($cost,2) . "</td>
            <td>" . number_format($r['price'],2) . "</td>
            <td>" . number_format($sell,2) . "</td>
          </tr>";
        }
        ?>
      </tbody>
      <tfoot>
        <tr>
          <th colspan="4">TOTAL</th>
          <th><?=number_format($totalCost,2)?></th>
          <th></th>
          <th><?=number_format($totalSell,2)?></th>
        </tr>
      </tfoot>
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
    $(function(){
      console.log("jQuery document ready for Inventory Valuation. Attempting to initialize DataTable...");
      $('#valuationTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
          'copy',
          'csv',
          'excel',
          'pdf',
          'print'
        ],
        pageLength: 25
      });
      console.log("DataTable initialization attempt complete for Inventory Valuation.");
    });
  </script>
</body>
</html>
