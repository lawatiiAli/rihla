<?php
// questionnaire.php
// 1) Send XHTML header
header('Content-Type: application/xhtml+xml; charset=UTF-8');
echo '<?xml version="1.0" encoding="UTF-8"?>';

// 2) Connect to MySQL
$mysqli = new mysqli('localhost','root','','rihla_db');
if ($mysqli->connect_errno) {
    // If included in table body, output one row with error
    echo '<tr><td colspan="5" class="text-danger">'
       . 'DB Connection failed: ' . htmlspecialchars($mysqli->connect_error)
       . '</td></tr>';
    exit;
}
$mysqli->set_charset('utf8');

// 3) If form posted, insert new record
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email  = trim($_POST['email']  ?? '');
    $agegrp = trim($_POST['agegrp'] ?? '');
    $prefs  = $_POST['prefs'] ?? [];
    // Store preferences as comma-separated string
    $prefs_str = $mysqli->real_escape_string(implode(', ', $prefs));
    if ($email && $agegrp) {
        $stmt = $mysqli->prepare(
            "INSERT INTO questionnaire_responses
              (`email`,`age_group`,`preferences`)
             VALUES (?,?,?)"
        );
        $stmt->bind_param('sss', $email, $agegrp, $prefs_str);
        $stmt->execute();
        $stmt->close();
    }
}

// 4) Define a class to represent one response
class Response {
    public $id, $email, $age_group, $preferences, $submitted_at;
    public function __construct(array $row) {
        $this->id           = $row['id'];
        $this->email        = $row['email'];
        $this->age_group    = $row['age_group'];
        $this->preferences  = $row['preferences'];
        $this->submitted_at = $row['submitted_at'];
    }
}

// 5) Fetch all responses into an array of Response objects
$responses = [];
$sql = "SELECT `id`,`email`,`age_group`,`preferences`,`submitted_at`
        FROM questionnaire_responses
        ORDER BY submitted_at DESC";
if ($result = $mysqli->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        $responses[] = new Response($row);
    }
    $result->free();
}

// 6) Function to render the table rows
function renderResponses(array $arr) {
    if (empty($arr)) {
        echo '<tr><td colspan="5" class="text-warning">'
           . 'No responses yet.'
           . '</td></tr>';
        return;
    }
    foreach ($arr as $r) {
        printf(
          '<tr>
             <td>%d</td>
             <td>%s</td>
             <td>%s</td>
             <td>%s</td>
             <td>%s</td>
           </tr>',
          $r->id,
          htmlspecialchars($r->email),
          htmlspecialchars($r->age_group),
          htmlspecialchars($r->preferences),
          htmlspecialchars($r->submitted_at)
        );
    }
}

// 7) Output rows
renderResponses($responses);

// 8) Close connection
$mysqli->close();
?>