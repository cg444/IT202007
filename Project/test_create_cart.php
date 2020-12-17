<?php
//we'll put this at the top so both php block have access to it
if (isset($_GET["id"])) {
    $id = $_GET["id"];
}
?>
<?php
//saving
if (isset($_POST["save"])) {
    //TODO add proper validation/checks
    $product = $_POST["product_id"];
    if ($product <= 0) {
        $product = null;
    }

    $quantity = $_POST["quantity"];
    $price = calcPrice($product, $quantity);
    $user = get_user_id();
    $db = getDB();
    $stmt = $db->prepare(" INSERT INTO Cart (product_id, quantity, price, user_id ) VALUES(:product_id, :quantity, :price, :user_id)");
    $r = $stmt->execute([
        ":product_id" => $product,
        ":quantity" => $quantity,
        ":price" => $price,
        ":user_id" => $user,

    ]);
    if ($r) {
        flash("Created successfully with id: " . $db->lastInsertId());
    }
    else {
        $e = $stmt->errorInfo();
        flash("Error creating: " . var_export($e, true));
    }
}
?>
<?php

//get eggs for dropdown
$db = getDB();
$stmt = $db->prepare("SELECT id, name, price from Products LIMIT 10");
$r = $stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
    <h3>create cart</h3>
    <form method="POST">
        <label>Products</label>
        <select name="product_id" value="<?php echo $result["product_id"];?>" >
            <option value="-1">None</option>
            <?php foreach ($products as $product): ?>
                <option value="<?php safer_echo($product["id"]); ?>"><?php safer_echo($product["name"]); ?></option>
            <?php endforeach; ?>
        </select>

        <label>quantity</label>
        <input type="number" min="1" name="quantity" value="<?php echo $result["quantity"]; ?>"/>

        <input type="submit" name="save" value="Create"/>
    </form>


<?php require(__DIR__ . "/partials/flash.php");