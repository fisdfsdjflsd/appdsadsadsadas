<?php
$db_host = 'sql106.iceiy.com';
$db_user = 'icei_40409346';
$db_pass = 'JQtR5849TjNH';
$db_name = 'icei_40409346_mydb32132132131';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("
    SELECT * FROM data 
    WHERE timestamp >= DATE_SUB(NOW(), INTERVAL 1 HOUR) 
    ORDER BY timestamp ASC
");

$data = [];
$delays = [];
$na_count = 0;

while ($r = $result->fetch_assoc()) {
    $data[] = $r;
    if ($r['avg_delay'] > 0) {
        $delays[] = $r['avg_delay'];
    } else {
        $na_count++;
    }
}

$avg_delay = !empty($delays) ? round(array_sum($delays) / count($delays), 1) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tank History</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h2 { color: #333; border-bottom: 2px solid #28a745; padding-bottom: 10px; }
        canvas { margin: 20px 0; }
        .stats { display: flex; justify-content: space-around; margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px; }
        .stat-box { text-align: center; }
        .stat-box .label { color: #666; font-size: 0.9em; margin-bottom: 5px; }
        .stat-box .value { font-size: 1.8em; font-weight: bold; color: #007bff; }
        .no-data { text-align: center; padding: 40px; color: #666; }
        a { color: #007bff; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Water Level History - Last Hour</h2>
        
        <?php if (count($data) > 0): ?>
            <canvas id="levelChart"></canvas>
            
            <div class="stats">
                <div class="stat-box">
                    <div class="label">Avg Friend Delay</div>
                    <div class="value"><?= $avg_delay ?> ms</div>
                </div>
                <div class="stat-box">
                    <div class="label">N/A Occurrences</div>
                    <div class="value"><?= $na_count ?></div>
                </div>
                <div class="stat-box">
                    <div class="label">Data Points</div>
                    <div class="value"><?= count($data) ?></div>
                </div>
            </div>
            
            <script>
            const ctx = document.getElementById('levelChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: <?= json_encode(array_column($data, 'timestamp')) ?>,
                    datasets: [
                        {
                            label: 'Current Level (%)',
                            data: <?= json_encode(array_column($data, 'current_level')) ?>,
                            borderColor: '#007bff',
                            backgroundColor: 'rgba(0, 123, 255, 0.1)',
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Target Level (%)',
                            data: <?= json_encode(array_column($data, 'target_level')) ?>,
                            borderColor: '#28a745',
                            backgroundColor: 'rgba(40, 167, 69, 0.1)',
                            tension: 0.4,
                            borderDash: [5, 5],
                            fill: false
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'top' } },
                    scales: {
                        y: { min: 0, max: 100, title: { display: true, text: 'Level (%)' } },
                        x: { ticks: { maxRotation: 45, minRotation: 45 } }
                    }
                }
            });
            </script>
        <?php else: ?>
            <div class="no-data">
                <p>No data available for the last hour.</p>
                <p>Waiting for ESP32 to send data...</p>
            </div>
        <?php endif; ?>
        
        <p style="margin-top: 30px; text-align: center;">
            <a href="config.php">← View Configuration</a>
        </p>
    </div>
</body>
</html>
<?php $conn->close(); ?>