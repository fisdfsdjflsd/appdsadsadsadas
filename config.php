<?php
$db_host = 'sql106.iceiy.com';
$db_user = 'icei_40409346';
$db_pass = 'JQtR5849TjNH';
$db_name = 'icei_40409346_mydb32132132131';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT * FROM configs ORDER BY id DESC LIMIT 1");
$row = $result ? $result->fetch_assoc() : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PID Configuration</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h2 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        .param { margin: 15px 0; padding: 10px; background: #f8f9fa; border-left: 4px solid #007bff; }
        .param strong { display: inline-block; width: 80px; color: #555; }
        .param .value { font-size: 1.2em; color: #000; font-weight: bold; }
        .timestamp { margin-top: 20px; padding: 10px; background: #e9ecef; border-radius: 4px; font-size: 0.9em; color: #666; }
        .no-data { color: #dc3545; text-align: center; padding: 20px; }
        a { color: #007bff; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Current PID Configuration</h2>
        <?php if ($row): ?>
            <div class="param"><strong>Kp:</strong> <span class="value"><?= number_format($row['kp'], 4) ?></span></div>
            <div class="param"><strong>Ki:</strong> <span class="value"><?= number_format($row['ki'], 4) ?></span></div>
            <div class="param"><strong>Kd:</strong> <span class="value"><?= number_format($row['kd'], 4) ?></span></div>
            <div class="param"><strong>T_LAG:</strong> <span class="value"><?= $row['t_lag'] ?> ms</span></div>
            <div class="timestamp"><strong>Last Updated:</strong> <?= $row['timestamp'] ?></div>
        <?php else: ?>
            <p class="no-data">No configuration data available yet.</p>
        <?php endif; ?>
        
        <p style="margin-top: 30px; text-align: center;">
            <a href="history.php">View History →</a>
        </p>
    </div>
</body>
</html>
<?php $conn->close(); ?>