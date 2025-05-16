<?php
// add_activity.php

// 1) Connect to rihla_db
$db = new mysqli('localhost','root','','rihla_db');
if ($db->connect_errno) {
    // If included inside an HTML table, emit a single error row
    echo '<tr><td colspan="7" class="text-danger">'
       . 'Database connection failed: '
       . htmlspecialchars($db->connect_error)
       . '</td></tr>';
    exit;
}
$db->set_charset('utf8');

// 2) On POST, insert the new activity
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name']    ?? '');
    $desc    = trim($_POST['description']  ?? '');
    $price   = (float) ($_POST['price']   ?? 0);
    $loc     = trim($_POST['location'] ?? '');
    $contact = trim($_POST['contact_number'] ?? '');
    $img     = trim($_POST['image_link'] ?? '');

    if ($name && $desc && $loc && $contact) {
        $stmt = $db->prepare(
            "INSERT INTO user_activities
              (`name`,`description`,`price`,`location`,`contact_number`,`image_link`)
             VALUES (?,?,?,?,?,?)"
        );
        $stmt->bind_param(
            'ssdsss',
            $name, $desc, $price, $loc, $contact, $img
        );
        $stmt->execute();
        $stmt->close();
    }
}

// 3) Fetch all activities, most recent first
$res = $db->query(
    "SELECT `name`,`description`,`price`,`location`,
            `contact_number`,`image_link`,`submitted_at`
     FROM user_activities
     ORDER BY submitted_at DESC"
);

// 4) Emit each as a <tr>
if ($res && $res->num_rows) {
    while ($row = $res->fetch_assoc()) {
        printf(
          '<tr>
             <td>%s</td>
             <td>%s</td>
             <td>%.2f</td>
             <td>%s</td>
             <td>%s</td>
             <td>%s</td>
             <td>%s</td>
           </tr>',
          htmlspecialchars($row['name']),
          htmlspecialchars($row['description']),
          $row['price'],
          htmlspecialchars($row['location']),
          htmlspecialchars($row['contact_number']),
          $row['image_link']
            ? '<a href="' . htmlspecialchars($row['image_link'])
              . '" target="_blank">View</a>'
            : '—',
          htmlspecialchars($row['submitted_at'])
        );
    }
} else {
    echo '<tr><td colspan="7" class="text-center text-warning">'
       . 'No activities found.'
       . '</td></tr>';
}

$db->close();
?>
