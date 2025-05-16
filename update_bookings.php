<?php
// update_bookings.php

header('Content-Type: application/xhtml+xml; charset=UTF-8');
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
  <title>Update Bookings</title>
  <meta http-equiv="Content-Type" content="application/xhtml+xml; charset=UTF-8"/>
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
    rel="stylesheet" type="text/css"/>
</head>
<body class="bg-secondary text-white">
  <main class="container py-5">
    <h1 class="text-center mb-4">Update Bookings</h1>

<?php
$db = new mysqli('localhost','root','','rihla');
if ($db->connect_errno) {
    echo '<div class="alert alert-danger">DB error: '
       . htmlspecialchars($db->connect_error) . '</div>';
    exit;
}
$db->set_charset('utf8');

// 1) Handle update POST
if ($_SERVER['REQUEST_METHOD']==='POST' && !empty($_POST['booking_id'])) {
    $id   = (int)$_POST['booking_id'];
    $place= trim($_POST['place'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $price= (float)$_POST['price'] ?? 0;
    if ($place) {
        $stmt = $db->prepare(
          "UPDATE bookings 
           SET place=?, description=?, price=? 
           WHERE booking_id=?"
        );
        $stmt->bind_param('ssdi',$place,$desc,$price,$id);
        $stmt->execute();
        echo '<div class="alert alert-success">'
           . "Booking #$id updated."
           . '</div>';
        $stmt->close();
    }
}

// 2) If edit requested via GET, load that record
$editing = false;
if (isset($_GET['id'])) {
    $eid = (int)$_GET['id'];
    $res = $db->query(
      "SELECT booking_id, place, description, price
       FROM bookings
       WHERE booking_id=$eid"
    );
    if ($row = $res->fetch_assoc()) {
        $editing     = true;
        $edit_id     = $row['booking_id'];
        $edit_place  = $row['place'];
        $edit_desc   = $row['description'];
        $edit_price  = $row['price'];
    }
    $res->free();
}

// 3) If editing, show form
if ($editing):
?>
    <form action="update_bookings.php" method="post"
          class="bg-light text-dark p-4 rounded mb-5">
      <h2>Edit Booking #<?php echo $edit_id; ?></h2>
      <input type="hidden" name="booking_id" value="<?php echo $edit_id; ?>"/>
      <div class="mb-3">
        <label class="form-label">Place</label>
        <input type="text" name="place" class="form-control"
               value="<?php echo htmlspecialchars($edit_place); ?>" required="required"/>
      </div>
      <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control"
                  rows="3"><?php echo htmlspecialchars($edit_desc); ?></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Price (OMR)</label>
        <input type="number" step="0.01" name="price" class="form-control"
               value="<?php echo htmlspecialchars($edit_price); ?>" required="required"/>
      </div>
      <button type="submit" class="btn btn-primary">Save Changes</button>
      <a href="update_bookings.php" class="btn btn-secondary">Cancel</a>
    </form>
<?php endif; ?>

    <!-- 4) List all with Edit links -->
    <div class="table-responsive bg-light text-dark rounded p-3">
      <table class="table table-bordered table-hover text-center mb-0">
        <thead class="table-primary"><tr>
          <th>ID</th><th>Place</th><th>Description</th><th>Price</th><th>Action</th>
        </tr></thead>
        <tbody>
<?php
$res2 = $db->query(
  "SELECT booking_id, place, description, price
   FROM bookings
   ORDER BY booking_id"
);
while ($r = $res2->fetch_assoc()) {
    printf(
      '<tr>
         <td>%d</td>
         <td>%s</td>
         <td>%s</td>
         <td>%.2f</td>
         <td>
           <a class="btn btn-sm btn-warning"
              href="update_bookings.php?id=%d">
             Edit
           </a>
         </td>
       </tr>',
      $r['booking_id'],
      htmlspecialchars($r['place']),
      htmlspecialchars($r['description']),
      $r['price'],
      $r['booking_id']
    );
}
$db->close();
?>
        </tbody>
      </table>
    </div>

  </main>
</body>
</html>
