<?php
session_start();

// Veri dosyaları
$cartFile = 'data/cart.json';
$productsFile = 'data/products.json';

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

// Sepetten çıkar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cart = loadCart();
    
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'remove':
                $productId = intval($_POST['product_id']);
                $cart = array_filter($cart, function($item) use ($productId) {
                    return $item['product_id'] !== $productId;
                });
                saveCart(array_values($cart));
                $_SESSION['message'] = 'Ürün sepetten çıkarıldı!';
                break;
                
            case 'update':
                $productId = intval($_POST['product_id']);
                $quantity = intval($_POST['quantity']);
                
                if ($quantity > 0) {
                    foreach ($cart as &$item) {
                        if ($item['product_id'] === $productId) {
                            $item['quantity'] = $quantity;
                            break;
                        }
                    }
                    saveCart($cart);
                    $_SESSION['message'] = 'Sepet güncellendi!';
                }
                break;
                
            case 'clear':
                saveCart([]);
                $_SESSION['message'] = 'Sepet temizlendi!';
                break;
        }
    }
    
    header('Location: cart.php');
    exit;
}

$cart = loadCart();
$cartCount = array_sum(array_column($cart, 'quantity'));

// Toplam hesapla
$subtotal = 0;
foreach ($cart as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$tax = $subtotal * 0.18; // %18 KDV
$total = $subtotal + $tax;

$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sepetim - E-Ticaret Mağazası</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <h1>🛒 E-Ticaret Mağazası</h1>
            <nav>
                <a href="index.php" class="nav-link">Ürünler</a>
                <a href="cart.php" class="nav-link cart-link active">
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

            <h2 class="page-title">Sepetim</h2>

            <?php if (empty($cart)): ?>
                <div class="empty-cart">
                    <div class="empty-cart-icon">🛒</div>
                    <h3>Sepetiniz boş</h3>
                    <p>Alışverişe başlamak için ürünler sayfasına gidin.</p>
                    <a href="index.php" class="btn btn-primary">Alışverişe Başla</a>
                </div>
            <?php else: ?>
                <div class="cart-layout">
                    <div class="cart-items">
                        <?php foreach ($cart as $item): ?>
                            <div class="cart-item">
                                <div class="cart-item-image"><?php echo htmlspecialchars($item['image']); ?></div>
                                <div class="cart-item-info">
                                    <h3 class="cart-item-name"><?php echo htmlspecialchars($item['name']); ?></h3>
                                    <div class="cart-item-price"><?php echo number_format($item['price'], 2, ',', '.'); ?> ₺</div>
                                </div>
                                <div class="cart-item-quantity">
                                    <form method="POST" action="cart.php" class="update-form">
                                        <input type="hidden" name="action" value="update">
                                        <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                        <label for="qty_<?php echo $item['product_id']; ?>">Adet:</label>
                                        <input type="number" 
                                               id="qty_<?php echo $item['product_id']; ?>" 
                                               name="quantity" 
                                               value="<?php echo $item['quantity']; ?>" 
                                               min="1" 
                                               class="quantity-input-small"
                                               onchange="this.form.submit()">
                                    </form>
                                </div>
                                <div class="cart-item-total">
                                    <strong><?php echo number_format($item['price'] * $item['quantity'], 2, ',', '.'); ?> ₺</strong>
                                </div>
                                <div class="cart-item-actions">
                                    <form method="POST" action="cart.php" class="remove-form">
                                        <input type="hidden" name="action" value="remove">
                                        <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-small" onclick="return confirm('Bu ürünü sepetten çıkarmak istediğinize emin misiniz?')">
                                            ✕
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="cart-summary">
                        <h3>Sipariş Özeti</h3>
                        <div class="summary-row">
                            <span>Ara Toplam:</span>
                            <span><?php echo number_format($subtotal, 2, ',', '.'); ?> ₺</span>
                        </div>
                        <div class="summary-row">
                            <span>KDV (%18):</span>
                            <span><?php echo number_format($tax, 2, ',', '.'); ?> ₺</span>
                        </div>
                        <div class="summary-row total">
                            <span>Toplam:</span>
                            <span><?php echo number_format($total, 2, ',', '.'); ?> ₺</span>
                        </div>
                        <div class="summary-actions">
                            <a href="index.php" class="btn btn-secondary">Alışverişe Devam Et</a>
                            <button class="btn btn-primary btn-large">Ödemeye Geç</button>
                            <form method="POST" action="cart.php" style="margin-top: 10px;">
                                <input type="hidden" name="action" value="clear">
                                <button type="submit" class="btn btn-danger btn-small" onclick="return confirm('Sepeti temizlemek istediğinize emin misiniz?')">
                                    Sepeti Temizle
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
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

