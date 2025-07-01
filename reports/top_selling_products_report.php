<?php
include_once "../config/auth.php";
include_once "../config/config.php";
$sql = "
    SELECT p.name, SUM(soi.quantity) AS sold_qty, SUM(soi.quantity * soi.unit_price) AS revenue
    FROM sales_order_items soi
    JOIN products p ON soi.product_id = p.id
    GROUP BY soi.product_id
    ORDER BY sold_qty DESC
    LIMIT 10";
$res = mysqli_query($conn, $sql);
$labels=[]; $qtys=[];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Top Selling Products</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
  <h2>Top Selling Products</h2>
  <canvas id="topChart" height="100"></canvas>
  <table id="topTable" class="table table-bordered table-striped mt-3">
    <thead><tr><th>Product</th><th>Qty Sold</th><th>Revenue</th></tr></thead><tbody>
    <?php while($r=mysqli_fetch_assoc($res)):
      $labels[] = $r['name'];
      $qtys[] = $r['sold_qty'];
    ?>
      <tr>
        <td><?= htmlspecialchars($r['name']) ?></td>
        <td><?= htmlspecialchars($r['sold_qty']) ?></td>
        <td><?= number_format($r['revenue'],2) ?></td>
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

<!-- Initialize Chart and DataTable -->
<script>
$(function(){
  console.log("jQuery document ready for Top Selling Products. Attempting to initialize Chart and DataTable...");

  // Initialize Chart.js
  $('#topChart').Chart = new Chart($('#topChart'), {
    type: 'bar',
    data: {
      labels: <?=json_encode($labels)?>,
      datasets: [{ label: 'Quantity Sold', backgroundColor: '#007bff', data: <?=json_encode($qtys)?> }]
    },
    options: { 
      responsive: true, 
      maintainAspectRatio: false, // Allow canvas to resize freely
      scales: { 
        y: { 
          beginAtZero: true,
          title: {
            display: true,
            text: 'Quantity Sold'
          }
        },
        x: {
          title: {
            display: true,
            text: 'Product'
          }
        }
      },
      plugins: {
        legend: {
          display: true,
          position: 'top',
        },
        title: {
          display: true,
          text: 'Top 10 Products by Quantity Sold'
        }
      }
    }
  });

  // Initialize DataTable with Buttons
  $('#topTable').DataTable({
    dom: 'Bfrtip',
    buttons: [
      'copy',
      'csv',
      'excel',
      'pdf',
      'print'
    ],
    pageLength: 10
  });
  console.log("Chart and DataTable initialization attempt complete for Top Selling Products.");
});
</script>
</body>
</html>
