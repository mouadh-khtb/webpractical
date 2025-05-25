<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

function getAverage($conn, $wilaya, $column) {
    $result = $conn->query("SELECT AVG($column) as avg FROM environment_data WHERE wilaya = '$wilaya'");
    return round($result->fetch_assoc()['avg'], 2);
}

$wilayas = ["Oran", "Algiers", "Blida", "Annaba", "Tlemcen", "Constantine"];
$data = [];
foreach ($wilayas as $w) {
    $data[$w] = [
        "temperature" => getAverage($conn, $w, "temperature"),
        "humidity" => getAverage($conn, $w, "humidity"),
        "wind_speed" => getAverage($conn, $w, "wind_speed"),
        "soil_salinity" => getAverage($conn, $w, "soil_salinity")
    ];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Oran Dashboard</title>
    <link rel="stylesheet" href="assets/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<h2>Welcome <?php echo $_SESSION['username']; ?> | Wilaya: Oran</h2>
<a href="logout.php">Logout</a>
<div class="dashboard-section">
    <h3>Wilaya Environmental Analysis</h3>
    <p>Temperature: <?php echo $data["Oran"]["temperature"]; ?> °C - <?php echo ($data["Oran"]["temperature"] >= 20 && $data["Oran"]["temperature"] <= 30) ? "Good" : "Poor"; ?></p>
    <p>Humidity: <?php echo $data["Oran"]["humidity"]; ?> % - <?php echo ($data["Oran"]["humidity"] >= 40 && $data["Oran"]["humidity"] <= 70) ? "Good" : "Poor"; ?></p>
    <p>Wind Speed: <?php echo $data["Oran"]["wind_speed"]; ?> km/h - <?php echo ($data["Oran"]["wind_speed"] > 20) ? "High (Energy potential)" : "Moderate"; ?></p>
    <p>Soil Salinity: <?php echo $data["Oran"]["soil_salinity"]; ?> dS/m - <?php echo ($data["Oran"]["soil_salinity"] > 4) ? "Problematic" : "Acceptable"; ?></p>
</div>
<div class="dashboard-section">
    <h3>Comparison Chart</h3>
    <canvas id="compareChart"></canvas>
</div>
<script>
const labels = <?php echo json_encode($wilayas); ?>;
const tempData = <?php echo json_encode(array_map(fn($w) => $data[$w]["temperature"], $wilayas)); ?>;
new Chart(document.getElementById("compareChart"), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: "Avg Temperature (°C)",
            data: tempData,
            backgroundColor: "rgba(75,192,192,0.6)"
        }]
    }
});
</script>
</body>
</html>