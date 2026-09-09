<?php
// api/products.php
require_once __DIR__ . '/../includes/security.php';
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized.']);
    exit;
}

// GET: Fetch products for marketplace or supplier
if ($method === 'GET') {
    $supplierId = $_GET['supplierId'] ?? null;

    if ($supplierId) {
        if ($_SESSION['user_role'] !== 'supplier' || (string)$supplierId !== (string)$_SESSION['user_id']) {
            http_response_code(403);
            echo json_encode(['error' => 'You may only view your own inventory.']);
            exit;
        }
        $stmt = $pdo->prepare("SELECT * FROM products WHERE supplierId = ? ORDER BY createdAt DESC");
        $stmt->execute([$supplierId]);
    } else {
        // Active marketplace items
        $stmt = $pdo->query("
            SELECT p.*, u.businessName as supplierBusinessName 
            FROM products p 
            JOIN users u ON p.supplierId = u.id 
            WHERE p.status = 'approved' AND u.role = 'supplier' AND u.isActive = 1 
            ORDER BY p.createdAt DESC
        ");
    }

    echo json_encode($stmt->fetchAll());
    exit;
}

// POST: Add new product (Supplier only)
if ($method === 'POST') {
    requireCsrfToken();
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'supplier') {
        http_response_code(403);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    if (!is_array($input)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid product data.']);
        exit;
    }

    $name = trim((string)($input['name'] ?? ''));
    $price = filter_var($input['price'] ?? null, FILTER_VALIDATE_FLOAT);
    $stockQuantity = filter_var($input['stockQuantity'] ?? null, FILTER_VALIDATE_INT);
    $description = trim((string)($input['description'] ?? ''));
    $category = trim((string)($input['category'] ?? 'General'));
    $demandStatus = (string)($input['demandStatus'] ?? 'medium');
    $imageUrl = trim((string)($input['imageUrl'] ?? ''));
    $allowedCategories = ['Grains', 'Poultry', 'Produce', 'Baking', 'Oils', 'General'];
    $allowedDemandStatuses = ['low', 'medium', 'high'];
    $validImageUrl = $imageUrl === ''
        || preg_match('/^uploads\/products\/prod_[A-Za-z0-9_-]+\.(jpg|png|webp)$/', $imageUrl) === 1
        || (filter_var($imageUrl, FILTER_VALIDATE_URL) && in_array(strtolower((string)parse_url($imageUrl, PHP_URL_SCHEME)), ['https'], true));
    $localImageExists = $imageUrl === '' || preg_match('/^uploads\/products\//', $imageUrl) !== 1 || is_file(dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $imageUrl));

    if ($name === '' || strlen($name) > 150 || $price === false || $price < 0 || $price > 99999999.99 || $stockQuantity === false || $stockQuantity < 0 || strlen($description) > 5000 || !in_array($category, $allowedCategories, true) || !in_array($demandStatus, $allowedDemandStatuses, true) || !$validImageUrl || !$localImageExists) {
        http_response_code(400);
        echo json_encode(['error' => 'Please provide valid product details.']);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO products (name, price, description, stockQuantity, demandStatus, category, supplierId, supplierName, imageUrl, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'approved')
        ");

        $stmt->execute([
            $name,
            number_format((float)$price, 2, '.', ''),
            $description,
            $stockQuantity,
            $demandStatus,
            $category,
            $_SESSION['user_id'],
            $_SESSION['business_name'] ?? $_SESSION['user_name'],
            $imageUrl
        ]);
    } catch (Throwable $e) {
        error_log('VendLink product creation error: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Product could not be created.']);
        exit;
    }

    echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
    exit;
}

http_response_code(405);
header('Allow: GET, POST');
echo json_encode(['error' => 'Method not allowed.']);