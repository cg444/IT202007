<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!is_logged_in()) {
    flash("You must be logged in to access this page");
    die(header("Location: login.php"));
}
?>
<?php
//we'll put this at the top so both php block have access to it
if (isset($_GET["id"])) {
    $id = $_GET["id"];
}
?>
<?php
//fetching
$result = [];
if (isset($id)) {
    $db = getDB();
    $stmt = $db->prepare("SELECT Cart.price, Products.name,Products.description, Cart.quantity from Cart join Products on Cart.product_id = product_id where Cart.id = :id");
    $stmt = $db->prepare("SELECT price from Cart");
    $r = $stmt->execute([":id" => $id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$result) {
        $e = $stmt->errorInfo();
        flash($e[2]);
    }
}
?>
    <h3>View Cart Items</h3>
<?php if (isset($result) && !empty($result)): ?>
    <div class="card">
        <div class="card-title">
            <?php safer_echo($result["name"]); ?>
        </div>
        <div class="card-body">
            <div>
                <p>Stats</p>
                <div>Price: <?php safer_echo($result["price"]); ?></div>
                <div>Quantity: <?php safer_echo($result["quantity"]); ?></div>
                <div>Description: <?php safer_echo($result["description"]); ?></div>
            </div>
        </div>
    </div>
<?php else: ?>
    <p>Error looking up id...</p>
<?php endif; ?>
<?php require(__DIR__ . "/partials/flash.php");
