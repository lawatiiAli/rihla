<?php
// calculator.php

// 1) Connect to the database
$db = new mysqli('localhost','root','','rihla');
if ($db->connect_errno) {
    die("Database connection failed: " . $db->connect_error);
}
$db->set_charset('utf8');

// 2) Fetch all bookings into memory (id→[place,price])
$res = $db->query("SELECT booking_id, place, price FROM bookings");
$bookings = [];
while ($row = $res->fetch_assoc()) {
    $bookings[$row['booking_id']] = $row;
}

// 3) Process form submission
$name   = htmlspecialchars($_POST['name'] ?? '');
$age    = (int) ($_POST['age'] ?? 0);
$chosen = $_POST['booking'] ?? [];  // array of booking_id
$total  = 0.0;

foreach ($chosen as $id) {
    if (isset($bookings[$id])) {
        $total += (float) $bookings[$id]['price'];
    }
}

// 4) Senior discount
if ($age > 60) {
    $total *= 0.9;
}

// 5) Render result as XHTML + Bootstrap
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <title>Calculation Result</title>
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
    rel="stylesheet"/>
</head>
<body class="p-4">
  <h2>Bill for <strong><?php echo $name; ?></strong></h2>
  <table class="table table-bordered">
    <tr>
      <th>Age</th>
      <td><?php echo $age; ?></td>
    </tr>
    <tr>
      <th>Selected Tours</th>
      <td>
        <ul>
        <?php foreach ($chosen as $id): 
            if (isset($bookings[$id])): ?>
          <li>
            <?php 
              echo htmlspecialchars($bookings[$id]['place'])
                   . " (OMR " 
                   . number_format($bookings[$id]['price'],2)
                   . ")";
            ?>
          </li>
        <?php 
            endif;
          endforeach; ?>
        </ul>
      </td>
    </tr>
    <tr>
      <th>Total Payable</th>
      <td><strong>OMR <?php echo number_format($total,2); ?></strong></td>
    </tr>
  </table>
</body>
</html>
