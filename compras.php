<?php include("templates/session.php"); ?>
<?php include("templates/header.php"); ?>

<?php
// Manejar acciones del carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['remove_item'])) {
        $gameId = intval($_POST['game_id']);
        $_SESSION['carrito'] = array_filter($_SESSION['carrito'], function($item) use ($gameId) {
            return $item['id'] != $gameId;
        });
        $_SESSION['carrito'] = array_values($_SESSION['carrito']);
        header('Location: compras.php');
        exit();
    }
    
    if (isset($_POST['update_quantity'])) {
        $gameId = intval($_POST['game_id']);
        $newQuantity = intval($_POST['quantity']);
        
        foreach ($_SESSION['carrito'] as &$item) {
            if ($item['id'] == $gameId) {
                if ($newQuantity > 0) {
                    $item['cantidad'] = $newQuantity;
                } else {
                    // Si cantidad es 0 o negativa, remover
                    $_SESSION['carrito'] = array_filter($_SESSION['carrito'], function($i) use ($gameId) {
                        return $i['id'] != $gameId;
                    });
                }
                break;
            }
        }
        $_SESSION['carrito'] = array_values($_SESSION['carrito']);
        header('Location: compras.php');
        exit();
    }
    
    if (isset($_POST['clear_cart'])) {
        $_SESSION['carrito'] = [];
        $_SESSION['discount_code'] = null;
        $_SESSION['discount_message'] = null;
        header('Location: compras.php');
        exit();
    }
    
    if (isset($_POST['apply_discount'])) {
        $codeInput = trim($_POST['discount_code'] ?? '');
        
        // Cargar códigos de descuento
        $discountFile = 'data/discount_codes.json';
        if (file_exists($discountFile)) {
            $discountData = json_decode(file_get_contents($discountFile), true);
            $codeFound = false;
            $discountValue = 0;
            $discountCustomMessage = "";
            
            foreach ($discountData['discountCodes'] as $discCode) {
                if (strtolower($discCode['code']) === strtolower($codeInput) && $discCode['active']) {
                    $codeFound = true;
                    $discountValue = $discCode['discount'];
                    $discountCustomMessage = $discCode['message'] ?? "";
                    break;
                }
            }
            
            if ($codeFound) {
                $_SESSION['discount_code'] = $codeInput;
                $_SESSION['discount_percentage'] = $discountValue;
                $_SESSION['discount_custom_message'] = $discountCustomMessage;
            } else {
                $_SESSION['discount_code'] = null;
                $_SESSION['discount_percentage'] = 0;
                $_SESSION['discount_custom_message'] = null;
            }
        }
        header('Location: compras.php');
        exit();
    }
    
    if (isset($_POST['remove_discount'])) {
        $_SESSION['discount_code'] = null;
        $_SESSION['discount_percentage'] = 0;
        $_SESSION['discount_custom_message'] = null;
        header('Location: compras.php');
        exit();
    }
}

// Inicializar carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Calcular totales
$subtotal = 0;
$totalGames = 0;
foreach ($_SESSION['carrito'] as $item) {
    $subtotal += $item['price'] * $item['cantidad'];
    $totalGames += $item['cantidad'];
}

// Aplicar descuento si existe
$discountPercentage = isset($_SESSION['discount_percentage']) ? $_SESSION['discount_percentage'] : 0;
$discountAmount = $subtotal * ($discountPercentage / 100);
$total = $subtotal - $discountAmount;
?>

<div class="page-background"></div>
<div class="page-overlay"></div>

<main class="shopping-main">
    <div class="shopping-header">
        <h1>Planeamiento de Compras</h1>
        <p>Gestiona tu carrito de juegos indie favoritos</p>
    </div>
    
    <div class="shopping-container">
        <?php if (empty($_SESSION['carrito'])): ?>
            <div class="empty-cart">
                <div class="empty-cart-icon">🛒</div>
                <h2>Tu carrito está vacío</h2>
                <p>No hay juegos en tu carrito. ¡Explora nuestro catálogo y añade algunos!</p>
                <a href="catalogue.php" class="btn btn-primary">Ir al Catálogo</a>
            </div>
        <?php else: ?>
            <div class="cart-content">
                <div class="cart-items">
                    <h2>Juegos en tu carrito</h2>
                    <div class="items-list">
                        <?php foreach ($_SESSION['carrito'] as $item): ?>
                        <div class="cart-item">
                            <div class="item-image">
                                <img src="img/games/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2280%22 height=%2280%22%3E%3Crect fill=%22%231a1a1a%22 width=%2280%22 height=%2280%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 font-size=%228%22 fill=%22%2300d4ff%22 text-anchor=%22middle%22 dominant-baseline=%22middle%22%3EN/A%3C/text%3E%3C/svg%3E'">
                            </div>
                            
                            <div class="item-details">
                                <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                                <p class="item-price">Precio: <?php echo number_format($item['price'], 2); ?>€</p>
                            </div>
                            
                            <div class="item-quantity">
                                <form method="POST" action="compras.php" class="quantity-form">
                                    <input type="hidden" name="game_id" value="<?php echo $item['id']; ?>">
                                    <label for="qty-<?php echo $item['id']; ?>">Cantidad:</label>
                                    <select id="qty-<?php echo $item['id']; ?>" name="quantity" onchange="this.form.submit()">
                                        <?php for ($i = 1; $i <= 10; $i++): ?>
                                            <option value="<?php echo $i; ?>" <?php echo ($item['cantidad'] == $i) ? 'selected' : ''; ?>>
                                                <?php echo $i; ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                    <input type="hidden" name="update_quantity">
                                </form>
                            </div>
                            
                            <div class="item-subtotal">
                                <p class="subtotal-label">Subtotal:</p>
                                <p class="subtotal-value"><?php echo number_format($item['price'] * $item['cantidad'], 2); ?>€</p>
                            </div>
                            
                            <div class="item-actions">
                                <form method="POST" action="compras.php">
                                    <input type="hidden" name="game_id" value="<?php echo $item['id']; ?>">
                                    <button type="submit" name="remove_item" class="btn btn-remove">
                                        ✕ Eliminar
                                    </button>
                                </form>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="cart-summary">
                    <h2>Resumen del Pedido</h2>
                    
                    <div class="summary-section">
                        <div class="summary-row">
                            <span class="summary-label">Total de juegos:</span>
                            <span class="summary-value"><?php echo $totalGames; ?></span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label">Subtotal:</span>
                            <span class="summary-value"><?php echo number_format($subtotal, 2); ?>€</span>
                        </div>
                        <?php if ($discountAmount > 0): ?>
                            <div class="summary-row discount-row">
                                <span class="summary-label">Descuento (<?php echo $discountPercentage; ?>%):</span>
                                <span class="summary-value discount-value">-<?php echo number_format($discountAmount, 2); ?>€</span>
                            </div>
                        <?php endif; ?>
                        <div class="summary-row summary-total">
                            <span class="summary-label">Total a pagar:</span>
                            <span class="summary-value"><?php echo number_format($total, 2); ?>€</span>
                        </div>
                    </div>
                    
                    <!-- Sección de Código de Descuento -->
                    <div class="discount-section <?php echo isset($_SESSION['discount_percentage']) && $_SESSION['discount_percentage'] < 0 ? 'negative-discount' : ''; ?>">
                        <?php if (!empty($_SESSION['discount_message'])): ?>
                            <div class="discount-message <?php if (isset($_SESSION['discount_code']) && $_SESSION['discount_code']) { echo isset($_SESSION['discount_percentage']) && $_SESSION['discount_percentage'] < 0 ? 'negative' : 'success'; } else { echo 'error'; } ?>">
                                <?php echo htmlspecialchars($_SESSION['discount_message']); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($_SESSION['discount_code']) && $_SESSION['discount_code']): ?>
                            <div class="discount-applied">
                                <div class="discount-info">
                                    <div class="discount-code-badge">
                                        <span class="discount-code-label">Código aplicado:</span>
                                        <span class="discount-code-value"><?php echo htmlspecialchars($_SESSION['discount_code']); ?></span>
                                    </div>
                                    <div class="discount-percent-badge <?php echo isset($_SESSION['discount_percentage']) && $_SESSION['discount_percentage'] < 0 ? 'negative' : ''; ?>">
                                        <?php if (isset($_SESSION['discount_percentage']) && $_SESSION['discount_percentage'] < 0): ?>
                                            <span class="discount-percent">+<?php echo abs($_SESSION['discount_percentage']); ?>%</span>
                                        <?php else: ?>
                                            <span class="discount-percent">-<?php echo $_SESSION['discount_percentage']; ?>%</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="discount-text-label">
                                    <?php echo htmlspecialchars($_SESSION['discount_custom_message'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                </div>
                                <form method="POST" action="compras.php" class="discount-remove-form">
                                    <button type="submit" name="remove_discount" class="btn btn-discount-remove">
                                        ✕ Remover código
                                    </button>
                                </form>
                            </div>
                        <?php else: ?>
                            <form method="POST" action="compras.php" class="discount-form">
                                <label for="discount_code" class="discount-label">¿Tienes un código de descuento?</label>
                                <div class="discount-input-group">
                                    <input type="text" id="discount_code" name="discount_code" placeholder="Ingresa el código aquí" required>
                                    <button type="submit" name="apply_discount" class="btn-apply-discount">
                                        Aplicar
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                    
                    <div class="summary-actions">
                        <a href="catalogue.php" class="btn btn-secondary">← Seguir comprando</a>
                        <button class="btn btn-primary" onclick="alert('¡Compra finalizada! Gracias por tu pedido.')">
                            ✓ Finalizar compra
                        </button>
                    </div>
                    
                    <form method="POST" action="compras.php" class="clear-cart-form">
                        <button type="submit" name="clear_cart" class="btn btn-danger">
                            🗑️ Vaciar carrito
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include("templates/footer.php"); ?>
