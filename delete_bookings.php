<?php
// delete_bookings.php

header('Content-Type: application/xhtml+xml; charset=UTF-8');
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
  <title>Delete Bookings</title>
  <meta http-equiv="Content-Type" content="application/xhtml+xml; charset=UTF-8"/>
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
    rel="stylesheet" type="text/css"/>
</head>
<body class="bg-secondary text-white">
  <main class="container py-5">
    <h1 class="text-center mb-4">Delete Bookings</h1>

<?php
$db = new mysqli('localhost','root','','rihla');
if ($db->connect_errno) {
    echo '<div class="alert alert-danger">DB error: '
       . htmlspecialchars($db->connect_error) . '</div>';
    exit;
}
$db->set_charset('utf8');

// Handle deletion
if ($_SERVER['REQUEST_METHOD']==='POST' && !empty($_POST['booking_id'])) {
    $id = (int)$_POST['booking_id'];
    $stmt = $db->prepare("DELETE FROM bookings WHERE booking_id = ?");
    $stmt->bind_param('i',$id);
    $stmt->execute();
    echo '<div class="alert alert-success">'
       . "Booking #$id deleted."
       . '</div>';
    $stmt->close();
}

// Fetch all bookings
$result = $db->query(
    "SELECT booking_id, place, description, price
     FROM bookings ORDER BY booking_id"
);
echo '<div class="table-responsive bg-light text-dark rounded p-3">',
       '<table class="table table-bordered table-hover text-center mb-0">',
         '<thead class="table-danger"><tr>',
           '<th>ID</th><th>Place</th><th>Description</th><th>Price</th><th>Action</th>',
         '</tr></thead><tbody>';
while ($r = $result->fetch_assoc()) {
    printf(
      '<tr>
         <td>%d</td>
         <td>%s</td>
         <td>%s</td>
         <td>%.2f</td>
         <td>
           <form method="post" action="delete_bookings.php" onsubmit="return confirm(\'Delete booking #%d?\');">
             <input type="hidden" name="booking_id" value="%d"/>
             <button type="submit" class="btn btn-sm btn-danger">Delete</button>
           </form>
         </td>
       </tr>',
      $r['booking_id'],
      htmlspecialchars($r['place']),
      htmlspecialchars($r['description']),
      $r['price'],
      $r['booking_id'],
      $r['booking_id']
    );
}
echo   '</tbody></table></div>';
$db->close();
?>

  </main>
</body>
</html>
