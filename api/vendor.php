<?php
// api/vendor.php
require_once __DIR__ . '/../includes/security.php';
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'vendor') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized: Vendor session required']);
    exit;
}

$vendorId = $_SESSION['user_id'];
$action = $_GET['action'] ?? 'summary';

// 1. Fetch Vendor Dashboard Statistics
if ($action === 'summary') {
    $ordersStmt = $pdo->prepare("SELECT COUNT(*) as total, SUM(totalAmount) as totalSpend FROM orders WHERE vendorId = ?");
    $ordersStmt->execute([$vendorId]);
    $stats = $ordersStmt->fetch();

    $pendingStmt = $pdo->prepare("SELECT COUNT(*) as pendingCount FROM orders WHERE vendorId = ? AND status = 'pending'");
    $pendingStmt->execute([$vendorId]);
    $pendingCount = $pendingStmt->fetchColumn();

    echo json_encode([
        'totalOrders' => (int)($stats['total'] ?? 0),
        'pendingOrders' => (int)$pendingCount,
        'totalSpend' => (float)($stats['totalSpend'] ?? 0)
    ]);
    exit;
}

// 2. Fetch Vendor Orders with Line Items
if ($action === 'orders') {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE vendorId = ? ORDER BY createdAt DESC");
    $stmt->execute([$vendorId]);
    $orders = $stmt->fetchAll();

    foreach ($orders as &$order) {
        $itemStmt = $pdo->prepare("SELECT name, priceAtOrder, quantity, imageUrl FROM order_items WHERE orderId = ?");
        $itemStmt->execute([$order['id']]);
        $order['items'] = $itemStmt->fetchAll();
    }

    echo json_encode($orders);
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Invalid vendor action']);