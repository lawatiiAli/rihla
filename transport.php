<?php
// transport.php

// 1) Connect
$db = new mysqli('localhost','root','','rihla');
if ($db->connect_errno) {
    die("DB Connection failed: " . $db->connect_error);
}
$db->set_charset('utf8');

// 2) Seed defaults if empty
$count = $db->query("SELECT COUNT(*) AS cnt FROM transport")
            ->fetch_assoc()['cnt'];
if ((int)$count === 0) {
    $defaults = [
      ['Taxi',       'Muscat',               3.00],
      ['Taxi',       'Salalah',             10.00],
      ['Public Bus', 'All Major Cities',     1.00],
      ['Car Rental', 'Airports and Hotels', 15.00],
    ];
    $stmt = $db->prepare(
      "INSERT INTO transport (`type`,`city`,`cost`)
       VALUES (?,?,?)"
    );
    foreach ($defaults as $d) {
        $stmt->bind_param('ssd', $d[0], $d[1], $d[2]);
        $stmt->execute();
    }
    $stmt->close();
}

// 3) Handle Add New (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $t = $db->real_escape_string($_POST['type']  ?? '');
    $c = $db->real_escape_string($_POST['city']  ?? '');
    $p = isset($_POST['cost']) ? (float)$_POST['cost'] : 0.0;

    if ($t !== '' && $c !== '') {
        $db->query(
          "INSERT INTO transport (`type`,`city`,`cost`)
           VALUES ('$t','$c',$p)"
        );
    }
}

// 4) Handle Search (GET)
$searchTerm = trim($_GET['search'] ?? '');
if ($searchTerm !== '') {
    $s = $db->real_escape_string($searchTerm);
    $sql = "SELECT * FROM transport
            WHERE `type`  LIKE '%$s%'
               OR `city` LIKE '%$s%'";
} else {
    $sql = "SELECT * FROM transport";
}
$result = $db->query($sql);

// 5) Render XHTML + Bootstrap
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <title>Transportation Options</title>
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
    rel="stylesheet"/>
</head>
<body class="p-4">

  <h2 class="mb-3">Transportation Options</h2>

  <?php if ($searchTerm !== ''): ?>
    <p class="fst-italic">
      Showing results for “<strong><?php echo htmlspecialchars($searchTerm); ?></strong>”
    </p>
  <?php endif; ?>

  <table class="table table-striped table-bordered">
    <thead class="table-dark">
      <tr>
        <th>Type</th>
        <th>City</th>
        <th>Cost (OMR)</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?php echo htmlspecialchars($row['type']); ?></td>
        <td><?php echo htmlspecialchars($row['city']); ?></td>
        <td><?php echo number_format($row['cost'],2); ?></td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <p>
    <a href="transport.html" class="btn btn-secondary">
      ← Back to Add/Search
    </a>
  </p>

</body>
</html>
