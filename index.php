<?php
session_start();

// Veri dosyaları
$productsFile = 'data/products.json';
$cartFile = 'data/cart.json';

// Klasörleri oluştur
if (!file_exists('data')) {
    mkdir('data', 0777, true);
}

// Ürünleri yükle
function loadProducts() {
    global $productsFile;
    if (file_exists($productsFile)) {
        $content = file_get_contents($productsFile);
        return json_decode($content, true) ?: [];
    }
    // Varsayılan ürünler
    $defaultProducts = [
        [
            'id' => 1,
            'name' => 'Laptop',
            'description' => 'Yüksek performanslı işlemci ve geniş ekran',
            'price' => 12999.99,
            'image' => '💻',
            'stock' => 15
        ],
        [
            'id' => 2,
            'name' => 'Akıllı Telefon',
            'description' => 'Son teknoloji kamera ve hızlı işlemci',
            'price' => 8999.99,
            'image' => '📱',
            'stock' => 25
        ],
        [
            'id' => 3,
            'name' => 'Kulaklık',
            'description' => 'Gürültü önleyici ve yüksek ses kalitesi',
            'price' => 1299.99,
            'image' => '🎧',
            'stock' => 50
        ],
        [
            'id' => 4,
            'name' => 'Tablet',
            'description' => 'Taşınabilir ve hafif tasarım',
            'price' => 5999.99,
            'image' => '📱',
            'stock' => 20
        ],
        [
            'id' => 5,
            'name' => 'Klavye',
            'description' => 'Mekanik klavye, RGB aydınlatma',
            'price' => 899.99,
            'image' => '⌨️',
            'stock' => 30
        ],
        [
            'id' => 6,
            'name' => 'Mouse',
            'description' => 'Kablosuz, ergonomik tasarım',
            'price' => 499.99,
            'image' => '🖱️',
            'stock' => 40
        ]
    ];
    saveProducts($defaultProducts);
    return $defaultProducts;
}

// Ürünleri kaydet
function saveProducts($products) {
    global $productsFile;
    file_put_contents($productsFile, json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// Sepeti yükle
function loadCart() {
    global $cartFile;
    if (file_exists($cartFile)) {
        $content = file_get_contents($cartFile);
        return json_decode($content, true) ?: [];
    }
    return [];
}

// Sepeti kaydet
function saveCart($cart) {
    global $cartFile;
    file_put_contents($cartFile, json_encode($cart, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// Sepete ekle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_to_cart') {
    $productId = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity'] ?? 1);
    
    $products = loadProducts();
    $cart = loadCart();
    
    $product = null;
    foreach ($products as $p) {
        if ($p['id'] === $productId) {
            $product = $p;
            break;
        }
    }
    
    if ($product) {
        $found = false;
        foreach ($cart as &$item) {
            if ($item['product_id'] === $productId) {
                $item['quantity'] += $quantity;
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            $cart[] = [
                'product_id' => $productId,
                'name' => $product['name'],
                'price' => $product['price'],
                'image' => $product['image'],
                'quantity' => $quantity
            ];
        }
        
        saveCart($cart);
        $_SESSION['message'] = $product['name'] . ' sepete eklendi!';
    }
    
    header('Location: index.php');
    exit;
}

$products = loadProducts();
$cart = loadCart();
$cartCount = array_sum(array_column($cart, 'quantity'));
$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Ticaret Mağazası</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <h1>🛒 E-Ticaret Mağazası</h1>
            <nav>
                <a href="index.php" class="nav-link active">Ürünler</a>
                <a href="cart.php" class="nav-link cart-link">
                    Sepetim 
                    <?php if ($cartCount > 0): ?>
                        <span class="cart-badge"><?php echo $cartCount; ?></span>
                    <?php endif; ?>
                </a>
            </nav>
        </div>
    </header>

    <main class="main">
        <div class="container">
            <?php if ($message): ?>
                <div class="message success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <div class="products-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <div class="product-image"><?php echo htmlspecialchars($product['image']); ?></div>
                        <div class="product-info">
                            <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                            <div class="product-footer">
                                <div class="product-price"><?php echo number_format($product['price'], 2, ',', '.'); ?> ₺</div>
                                <div class="product-stock">Stok: <?php echo $product['stock']; ?></div>
                            </div>
                            <form method="POST" action="index.php" class="add-to-cart-form">
                                <input type="hidden" name="action" value="add_to_cart">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <div class="quantity-input">
                                    <label for="quantity_<?php echo $product['id']; ?>">Adet:</label>
                                    <input type="number" 
                                           id="quantity_<?php echo $product['id']; ?>" 
                                           name="quantity" 
                                           value="1" 
                                           min="1" 
                                           max="<?php echo $product['stock']; ?>"
                                           required>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    Sepete Ekle
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 E-Ticaret Mağazası. Tüm hakları saklıdır.</p>
        </div>
    </footer>

    <script src="js/main.js"></script>
</body>
</html>

