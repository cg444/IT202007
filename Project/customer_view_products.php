<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
//we'll put this at the top so both php block have access to it
if (isset($_GET["id"])) {
    $userID = get_user_id();
    $productID = $_GET["id"];
    $price = 0;
}
?>
<?php
//fetching
$result = [];
if (isset($productID)) {
    $db = getDB();
    $stmt = $db->prepare("SELECT name, price, quantity, description, user_id, Users.username FROM Products JOIN Users on Products.user_id = Users.id where Products.id = :id");
    $r = $stmt->execute([":id" => $productID]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$result) {
        $e = $stmt->errorInfo();
        flash($e[2]);
    }
}
?>

<?php
if(isset($_POST["quantity"])) {
    $db = getDB();
    $price = $_POST["price"];
    echo $price;
    $stmt = $db->prepare ("INSERT into Cart (`product_id`, `user_id`, `quantity`, `price`) VALUES (:productID, :userID, :quantity, :price) on duplicate key update quantity = :quantity");
    $r = $stmt->execute([
        ":productID" => $productID,
        ":userID" => $userID,
        ":quantity" => $_POST["quantity"],
        ":price" => $price
        ]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$result) {
        $e = $stmt->errorInfo();
        flash($e[2]);
    }
}
?>
<?php if (isset($result) && !empty($result)): ?>
    <div class="card">
        <div class="card-title">
            <?php safer_echo($result["name"]); ?>
        </div>
        <div class="card-body">
            <div>
                <p>Stats</p>
                <div>Price: <?php safer_echo($result["price"]); ?></div>
                <?php if ($result["quantity"] < 10): ?>
                        <div><?php safer_echo("Only " . $result["quantity"] . " left, order now!"); ?></div>
                   <?php endif;?>
                <div>Description: <?php safer_echo($result["description"]); ?></div></p>
                    <div class="form-group">
                        <label>Quantity</label>
                            <input type="number" min="0" name="quantity" value="<?php echo $result["quantity"]; ?>"/>
                        <input type="submit" name="save" value="Add to Cart"/>
                        <input type="hidden" name="price" value="<?php echo $result["price"]; ?>"/>
                </form>
        </div>
    </div>
<?php endif; ?>
<?php require(__DIR__ . "/partials/flash.php");