<?php
// markets.php
// 1) Connect to MySQL
$mysqli = new mysqli('localhost', 'root', '', 'rihla_db');
if ($mysqli->connect_errno) {
    // If this is being included into an HTML table, output a single row
    echo '<tr><td colspan="4">Database connection failed: '
         . htmlspecialchars($mysqli->connect_error)
         . '</td></tr>';
    exit;
}
$mysqli->set_charset('utf8');

// 2) Handle “Add New Market” submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name']  ?? '');
    $city  = trim($_POST['city']  ?? '');
    $items = trim($_POST['items'] ?? '');
    if ($name !== '' && $city !== '' && $items !== '') {
        $stmt = $mysqli->prepare(
            "INSERT INTO markets (`name`,`city`,`items`) VALUES (?,?,?)"
        );
        $stmt->bind_param('sss', $name, $city, $items);
        $stmt->execute();
        $stmt->close();
    }
}

// 3) Determine whether we’re searching or listing all
$search = trim($_GET['search'] ?? '');
if ($search !== '') {
    $like = '%' . $mysqli->real_escape_string($search) . '%';
    $sql  = "SELECT `name`,`city`,`items`,`created_at`
             FROM markets
             WHERE `name`  LIKE ?
                OR `city`  LIKE ?
             ORDER BY created_at DESC";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('ss', $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $mysqli->query(
      "SELECT `name`,`city`,`items`,`created_at`
       FROM markets
       ORDER BY created_at DESC"
    );
}

// 4) Output <tr> rows
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        printf(
          '<tr>
             <td>%s</td>
             <td>%s</td>
             <td>%s</td>
             <td>%s</td>
           </tr>',
          htmlspecialchars($row['name']),
          htmlspecialchars($row['city']),
          htmlspecialchars($row['items']),
          htmlspecialchars($row['created_at'])
        );
    }
} else {
    echo '<tr><td colspan="4" class="text-center text-warning">'
         . ($search !== '' 
            ? 'No markets found for “' . htmlspecialchars($search) . '”.'
            : 'No markets found.')
         . '</td></tr>';
}

// 5) Clean up
if (isset($stmt) && $stmt instanceof mysqli_stmt) {
    $stmt->close();
}
$mysqli->close();
?>
