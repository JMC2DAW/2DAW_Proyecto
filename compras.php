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

<main class="shopping-main" style="position: relative; z-index: 10;">
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
                                <span class="summary-value" style="color: #00d4ff;">-<?php echo number_format($discountAmount, 2); ?>€</span>
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

<style>
/* Estilos específicos de compras */
.shopping-main {
    max-width: 1200px;
    margin: 0 auto;
    padding: 3rem 2rem;
    min-height: 70vh;
}

.shopping-header {
    text-align: center;
    margin-bottom: 3rem;
    color: white;
    background: rgba(26, 26, 26, 0.85);
    border-radius: 10px;
    padding: 2rem;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.6);
}

.shopping-header h1 {
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
    color: #00d4ff;
}

.shopping-header p {
    font-size: 1.1rem;
    color: #e0e0e0;
}

.shopping-container {
    background: rgba(26, 26, 26, 0.85);
    border-radius: 10px;
    padding: 2rem;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.6);
}

/* Carrito vacío */
.empty-cart {
    text-align: center;
    padding: 4rem 2rem;
    color: white;
}

.empty-cart-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.8;
}

.empty-cart h2 {
    font-size: 1.8rem;
    color: #00d4ff;
    margin-bottom: 0.5rem;
}

.empty-cart p {
    color: #d0d0d0;
    margin-bottom: 2rem;
    font-size: 1.1rem;
}

/* Contenido del carrito */
.cart-content {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 2rem;
}

.cart-items h2,
.cart-summary h2 {
    font-size: 1.5rem;
    color: #00d4ff;
    margin-bottom: 1.5rem;
    border-bottom: 2px solid #00d4ff;
    padding-bottom: 0.5rem;
}

.items-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.cart-item {
    display: grid;
    grid-template-columns: 80px 1fr auto auto auto;
    gap: 1rem;
    align-items: center;
    background-color: #1a1a1a;
    padding: 1rem;
    border-radius: 8px;
    border-left: 4px solid #00d4ff;
}

.item-image {
    width: 80px;
    height: 80px;
    border-radius: 6px;
    overflow: hidden;
}

.item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.item-details {
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
}

.item-details h3 {
    color: #00d4ff;
    font-size: 1rem;
    margin: 0;
}

.item-price {
    color: #b0b0b0;
    font-size: 0.9rem;
    margin: 0;
}

.item-quantity {
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
    align-items: center;
}

.item-quantity label {
    color: #b0b0b0;
    font-size: 0.85rem;
}

.quantity-form select {
    background-color: #2d2d2d;
    color: #00d4ff;
    border: 1px solid #00d4ff;
    padding: 0.3rem 0.5rem;
    border-radius: 4px;
    font-weight: 600;
}

.item-subtotal {
    text-align: right;
    min-width: 100px;
}

.subtotal-label {
    color: #b0b0b0;
    font-size: 0.85rem;
    margin: 0;
}

.subtotal-value {
    color: #00d4ff;
    font-size: 1.1rem;
    font-weight: bold;
    margin: 0.2rem 0 0 0;
}

.item-actions form {
    margin: 0;
}

.btn-remove {
    padding: 0.5rem 1rem;
    background-color: #ff4444;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.85rem;
    transition: all 0.3s;
}

.btn-remove:hover {
    background-color: #dd3333;
    transform: translateY(-2px);
}

/* Resumen del carrito */
.cart-summary {
    background-color: #1a1a1a;
    padding: 1.5rem;
    border-radius: 8px;
    border: 1px solid #00d4ff;
    height: fit-content;
    position: sticky;
    top: 100px;
}

.summary-section {
    margin-bottom: 1.5rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #444;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.8rem;
    color: #d0d0d0;
}

.summary-label {
    font-size: 0.95rem;
}

.summary-value {
    font-weight: 600;
}

.summary-total {
    background-color: #2d2d2d;
    padding: 0.8rem;
    border-radius: 4px;
    border-left: 3px solid #00d4ff;
    margin-top: 0.5rem;
}

.summary-total .summary-label {
    color: #00d4ff;
    font-weight: bold;
    font-size: 1.05rem;
}

.summary-total .summary-value {
    color: #00d4ff;
    font-size: 1.3rem;
}

.summary-actions {
    display: flex;
    flex-direction: column;
    gap: 0.8rem;
    margin-bottom: 1rem;
}

.summary-actions a,
.summary-actions button {
    width: 100%;
    padding: 0.8rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 600;
    text-decoration: none;
    text-align: center;
    font-size: 0.9rem;
    transition: all 0.3s;
}

.summary-actions .btn-primary {
    background-color: #00d4ff;
    color: #1a1a1a;
}

.summary-actions .btn-primary:hover {
    background-color: #00a8cc;
    transform: translateY(-2px);
}

.summary-actions .btn-secondary {
    background-color: transparent;
    color: #00d4ff;
    border: 2px solid #00d4ff;
}

.summary-actions .btn-secondary:hover {
    background-color: #00d4ff;
    color: #1a1a1a;
}

.clear-cart-form {
    margin-top: 1rem;
}

.btn-danger {
    width: 100%;
    padding: 0.8rem;
    background-color: #ff4444;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s;
    font-size: 0.9rem;
}

.btn-danger:hover {
    background-color: #dd3333;
    transform: translateY(-2px);
}

/* Discount Section Styling */
.discount-section {
    background: linear-gradient(135deg, rgba(45, 45, 45, 0.6), rgba(30, 30, 30, 0.8));
    border: 2px solid #00d4ff;
    border-radius: 10px;
    padding: 1.5rem;
    margin-top: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 4px 15px rgba(0, 212, 255, 0.1);
}

.discount-section.negative-discount {
    background: linear-gradient(135deg, rgba(80, 30, 30, 0.6), rgba(60, 20, 20, 0.8));
    border: 2px solid #ff6b6b;
    box-shadow: 0 4px 15px rgba(255, 107, 107, 0.1);
}

.discount-message {
    padding: 0.9rem;
    border-radius: 6px;
    margin-bottom: 1rem;
    font-weight: 600;
    text-align: center;
    animation: slideDown 0.3s ease-out;
}

.discount-message.success {
    background-color: rgba(34, 197, 94, 0.2);
    color: #4ade80;
    border: 1px solid #22c55e;
}

.discount-message.error {
    background-color: rgba(239, 68, 68, 0.2);
    color: #f87171;
    border: 1px solid #ef4444;
}

.discount-message.negative {
    background-color: rgba(239, 68, 68, 0.2);
    color: #f87171;
    border: 1px solid #ef4444;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.discount-applied {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.discount-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    background: rgba(0, 212, 255, 0.1);
    padding: 1rem;
    border-radius: 8px;
    border-left: 4px solid #00d4ff;
}

.discount-code-badge {
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
}

.discount-code-label {
    font-size: 0.85rem;
    color: #b0b0b0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.discount-code-value {
    font-size: 1.2rem;
    color: #00d4ff;
    font-weight: bold;
    font-family: 'Courier New', monospace;
    word-break: break-all;
    overflow-wrap: break-word;
}

.discount-percent-badge {
    background: linear-gradient(135deg, #00d4ff, #0099cc);
    padding: 0.7rem 1rem;
    border-radius: 6px;
    min-width: 80px;
    text-align: center;
}

.discount-percent-badge.negative {
    background: linear-gradient(135deg, #ff6b6b, #ee5a52);
}

.discount-percent {
    color: #1a1a1a;
    font-size: 1.3rem;
    font-weight: bold;
}

.discount-text-label {
    color: #00d4ff;
    font-size: 0.9rem;
    font-weight: 600;
    margin-top: 0.8rem;
    text-align: center;
    word-break: break-all;
    overflow-wrap: break-word;
}

.discount-section.negative-discount .discount-text-label {
    color: #ff6b6b;
}

.discount-remove-form {
    margin: 0;
}

.btn-discount-remove {
    width: 100%;
    padding: 0.7rem;
    background: linear-gradient(135deg, #ff6b6b, #ee5a52);
    color: white;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0.9rem;
}

.btn-discount-remove:hover {
    background: linear-gradient(135deg, #ff5252, #dd4444);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 107, 107, 0.3);
}

.discount-form {
    display: flex;
    flex-direction: column;
    gap: 0.8rem;
}

.discount-label {
    color: #00d4ff;
    font-weight: 600;
    font-size: 0.95rem;
}

.discount-input-group {
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
    align-items: stretch;
}

.discount-input-group input {
    flex: 1;
    padding: 0.7rem 1rem;
    background-color: #1a1a1a;
    border: 2px solid #00d4ff;
    border-radius: 6px;
    color: #00d4ff;
    font-size: 0.95rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.discount-input-group input::placeholder {
    color: #666;
}

.discount-input-group input:focus {
    outline: none;
    background-color: #252525;
    border-color: #00ffff;
    box-shadow: 0 0 8px rgba(0, 212, 255, 0.4);
}

.btn-apply-discount {
    padding: 0.7rem 1.5rem;
    background: linear-gradient(135deg, #00d4ff, #0099cc);
    color: #1a1a1a;
    border: none;
    border-radius: 6px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0.9rem;
    white-space: nowrap;
}

.btn-apply-discount:hover {
    background: linear-gradient(135deg, #00ffff, #00a8cc);
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 212, 255, 0.4);
}

.btn-apply-discount:active {
    transform: translateY(0);
}

/* Responsive */
@media (max-width: 768px) {
    .cart-content {
        grid-template-columns: 1fr;
    }
    
    .cart-summary {
        position: static;
    }
    
    .cart-item {
        grid-template-columns: 70px 1fr auto;
        gap: 0.8rem;
    }
    
    .item-quantity,
    .item-subtotal {
        grid-column: 2;
        text-align: left;
    }
    
    .item-actions {
        grid-column: 3;
        grid-row: 2 / 4;
    }
}

@media (max-width: 480px) {
    .shopping-main {
        padding: 1.5rem 1rem;
    }
    
    .shopping-header h1 {
        font-size: 1.8rem;
    }
    
    .shopping-container {
        padding: 1rem;
    }
    
    .cart-item {
        grid-template-columns: 1fr;
    }
    
    .item-quantity,
    .item-subtotal,
    .item-actions {
        grid-column: 1;
        width: 100%;
    }
    
    .item-subtotal {
        display: flex;
        justify-content: space-between;
    }
}
</style>

/* Carrito vacío */
.empty-cart {
    text-align: center;
    padding: 4rem 2rem;
    color: white;
}

.empty-cart-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.8;
}

.empty-cart h2 {
    font-size: 1.8rem;
    color: #00d4ff;
    margin-bottom: 0.5rem;
}

.empty-cart p {
    color: #d0d0d0;
    margin-bottom: 2rem;
    font-size: 1.1rem;
}

/* Contenido del carrito */
.cart-content {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 2rem;
}

.cart-items h2,
.cart-summary h2 {
    font-size: 1.5rem;
    color: #00d4ff;
    margin-bottom: 1.5rem;
    border-bottom: 2px solid #00d4ff;
    padding-bottom: 0.5rem;
}

.items-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.cart-item {
    display: grid;
    grid-template-columns: 80px 1fr auto auto auto;
    gap: 1rem;
    align-items: center;
    background-color: #1a1a1a;
    padding: 1rem;
    border-radius: 8px;
    border-left: 4px solid #00d4ff;
}

.item-image {
    width: 80px;
    height: 80px;
    border-radius: 6px;
    overflow: hidden;
}

.item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.item-details {
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
}

.item-details h3 {
    color: #00d4ff;
    font-size: 1rem;
    margin: 0;
}

.item-price {
    color: #b0b0b0;
    font-size: 0.9rem;
    margin: 0;
}

.item-quantity {
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
    align-items: center;
}

.item-quantity label {
    color: #b0b0b0;
    font-size: 0.85rem;
}

.quantity-form select {
    background-color: #2d2d2d;
    color: #00d4ff;
    border: 1px solid #00d4ff;
    padding: 0.3rem 0.5rem;
    border-radius: 4px;
    font-weight: 600;
}

.item-subtotal {
    text-align: right;
    min-width: 100px;
}

.subtotal-label {
    color: #b0b0b0;
    font-size: 0.85rem;
    margin: 0;
}

.subtotal-value {
    color: #00d4ff;
    font-size: 1.1rem;
    font-weight: bold;
    margin: 0.2rem 0 0 0;
}

.item-actions form {
    margin: 0;
}

.btn-remove {
    padding: 0.5rem 1rem;
    background-color: #ff4444;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.85rem;
    transition: all 0.3s;
}

.btn-remove:hover {
    background-color: #dd3333;
    transform: translateY(-2px);
}

/* Resumen del carrito */
.cart-summary {
    background-color: #1a1a1a;
    padding: 1.5rem;
    border-radius: 8px;
    border: 1px solid #00d4ff;
    height: fit-content;
    position: sticky;
    top: 100px;
}

.summary-section {
    margin-bottom: 1.5rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #444;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.8rem;
    color: #d0d0d0;
}

.summary-label {
    font-size: 0.95rem;
}

.summary-value {
    font-weight: 600;
}

.summary-total {
    background-color: #2d2d2d;
    padding: 0.8rem;
    border-radius: 4px;
    border-left: 3px solid #00d4ff;
    margin-top: 0.5rem;
}

.summary-total .summary-label {
    color: #00d4ff;
    font-weight: bold;
    font-size: 1.05rem;
}

.summary-total .summary-value {
    color: #00d4ff;
    font-size: 1.3rem;
}

.summary-actions {
    display: flex;
    flex-direction: column;
    gap: 0.8rem;
    margin-bottom: 1rem;
}

.summary-actions a,
.summary-actions button {
    width: 100%;
    padding: 0.8rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 600;
    text-decoration: none;
    text-align: center;
    font-size: 0.9rem;
    transition: all 0.3s;
}

.summary-actions .btn-primary {
    background-color: #00d4ff;
    color: #1a1a1a;
}

.summary-actions .btn-primary:hover {
    background-color: #00a8cc;
    transform: translateY(-2px);
}

.summary-actions .btn-secondary {
    background-color: transparent;
    color: #00d4ff;
    border: 2px solid #00d4ff;
}

.summary-actions .btn-secondary:hover {
    background-color: #00d4ff;
    color: #1a1a1a;
}

.clear-cart-form {
    margin-top: 1rem;
}

.btn-danger {
    width: 100%;
    padding: 0.8rem;
    background-color: #ff4444;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s;
    font-size: 0.9rem;
}

.btn-danger:hover {
    background-color: #dd3333;
    transform: translateY(-2px);
}

/* Responsive */
@media (max-width: 768px) {
    .cart-content {
        grid-template-columns: 1fr;
    }
    
    .cart-summary {
        position: static;
    }
    
    .cart-item {
        grid-template-columns: 70px 1fr auto;
        gap: 0.8rem;
    }
    
    .item-quantity,
    .item-subtotal {
        grid-column: 2;
        text-align: left;
    }
    
    .item-actions {
        grid-column: 3;
        grid-row: 2 / 4;
    }
}

@media (max-width: 480px) {
    .shopping-main {
        padding: 1.5rem 1rem;
    }
    
    .shopping-header h1 {
        font-size: 1.8rem;
    }
    
    .shopping-container {
        padding: 1rem;
    }
    
    .cart-item {
        grid-template-columns: 1fr;
    }
    
    .item-quantity,
    .item-subtotal,
    .item-actions {
        grid-column: 1;
        width: 100%;
    }
    
    .item-subtotal {
        display: flex;
        justify-content: space-between;
    }
}
</style>
