<?php
// search_bookings.php

header('Content-Type: application/xhtml+xml; charset=UTF-8');
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
  <title>Search Bookings</title>
  <meta http-equiv="Content-Type" content="application/xhtml+xml; charset=UTF-8"/>
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
    rel="stylesheet" type="text/css"/>
</head>
<body class="bg-secondary text-white">
  <main class="container py-5">
    <h1 class="text-center mb-4">Search Bookings</h1>

    <!-- Search Form -->
    <form action="search_bookings.php" method="get"
          class="row g-3 bg-light text-dark p-4 rounded mb-5">
      <div class="col-md-4">
        <label for="place" class="form-label">Place contains</label>
        <input type="text" id="place" name="place"
               value="<?php echo htmlspecialchars($_GET['place'] ?? '');?>"
               class="form-control"/>
      </div>
      <div class="col-md-2">
        <label for="min_price" class="form-label">Min Price</label>
        <input type="number" step="0.01" id="min_price" name="min_price"
               value="<?php echo htmlspecialchars($_GET['min_price'] ?? '');?>"
               class="form-control"/>
      </div>
      <div class="col-md-2">
        <label for="max_price" class="form-label">Max Price</label>
        <input type="number" step="0.01" id="max_price" name="max_price"
               value="<?php echo htmlspecialchars($_GET['max_price'] ?? '');?>"
               class="form-control"/>
      </div>
      <div class="col-md-4 text-end align-self-end">
        <button type="submit" class="btn btn-primary">Search</button>
      </div>
    </form>

<?php
// 1) Connect to DB
$db = new mysqli('localhost','root','','rihla');
if ($db->connect_errno) {
    echo '<div class="alert alert-danger">DB error: '
       . htmlspecialchars($db->connect_error) . '</div>';
    exit;
}
$db->set_charset('utf8');

// 2) Build WHERE clause
$clauses = []; $params = []; $types = '';
if (!empty($_GET['place'])) {
    $clauses[] = 'place LIKE ?';
    $params[]  = '%'.$_GET['place'].'%';
    $types    .= 's';
}
if (is_numeric($_GET['min_price'] ?? null)) {
    $clauses[] = 'price >= ?';
    $params[]  = $_GET['min_price'];
    $types    .= 'd';
}
if (is_numeric($_GET['max_price'] ?? null)) {
    $clauses[] = 'price <= ?';
    $params[]  = $_GET['max_price'];
    $types    .= 'd';
}
$where = $clauses ? ('WHERE '.implode(' AND ', $clauses)) : '';

// 3) Prepare & execute
$sql  = "SELECT booking_id, place, description, price
         FROM bookings $where
         ORDER BY place";
$stmt = $db->prepare($sql);
if ($clauses) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$res = $stmt->get_result();

// 4) Booking class + array
class Booking {
    public $id, $place, $desc, $price;
    public function __construct($r) {
        $this->id    = $r['booking_id'];
        $this->place = $r['place'];
        $this->desc  = $r['description'];
        $this->price = $r['price'];
    }
}
$bookings = [];
while ($row = $res->fetch_assoc()) {
    $bookings[] = new Booking($row);
}
$stmt->close();
$db->close();

// 5) Display function
function displayBookings($arr) {
    if (empty($arr)) {
        echo '<p class="text-warning">No bookings found.</p>';
        return;
    }
    echo '<div class="table-responsive">',
           '<table class="table table-bordered table-hover bg-light text-dark">',
             '<thead class="table-primary"><tr>',
               '<th>ID</th><th>Place</th><th>Description</th><th>Price (OMR)</th>',
             '</tr></thead><tbody>';
    foreach ($arr as $b) {
        printf(
          '<tr><td>%d</td><td>%s</td><td>%s</td><td>%.2f</td></tr>',
          $b->id,
          htmlspecialchars($b->place),
          htmlspecialchars($b->desc),
          $b->price
        );
    }
    echo     '</tbody></table></div>';
}

// 6) Render
displayBookings($bookings);
?>

  </main>
</body>
</html>
