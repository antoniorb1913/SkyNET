<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['cliente_id'])) {
    echo json_encode([]);
    exit;
}

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "SkyNET";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode([]);
    exit;
}

$cliente_id = $_SESSION['cliente_id'];

$sql = "SELECT id, direccion FROM direcciones WHERE cliente_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cliente_id);
$stmt->execute();
$result = $stmt->get_result();

$direcciones = [];
while ($row = $result->fetch_assoc()) {
    $direcciones[] = $row;
}

echo json_encode($direcciones);

$stmt->close();
$conn->close();
?>