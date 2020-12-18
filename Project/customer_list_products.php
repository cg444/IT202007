<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
$query = "";
$results = [];
if (isset($_POST["query"])) {
    $query = $_POST["query"];
}
if (isset($_POST["search"]) && !empty($query)) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * from Products WHERE category like :q and visibility = 0 LIMIT 10");
    $r = $stmt->execute([":q" => "%$query%"]);
    if ($r) {
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    else {
        flash("There was a problem fetching the results");
    }
}
?>
<h3>List Product</h3>
<form method="POST">
    <input name="query" placeholder="Search" value="<?php safer_echo($query); ?>"/>
    <input type="submit" value="Search" name="search"/>
</form>
<div class="results">
    <?php if (count($results) > 0): ?>
        <div class="list-group">
            <?php foreach ($results as $r): ?>
                <div class="list-group-item">
                    <div>
                        <td><a href = "customer_view_products.php?id=<?php safer_echo($r['product_id']); ?>"> <?php safer_echo($r["name"])?></a></td>
                        <div><?php safer_echo($r["name"]); ?></div>
                    </div>
                    <div>
                        <div>Price:</div>
                        <div><?php safer_echo($r["price"]); ?></div>
                    </div>
                    <div>
                        <div>Quantity:</div>
                        <div><?php safer_echo($r["quantity"]); ?></div>
                    </div>
                    <div>
                        <div>Description:</div>
                        <div><?php safer_echo($r["description"]); ?></div>
                    </div>
                    <div>
                        <a type="button" href="customer_view_products.php?id=<?php safer_echo($r['id']); ?>">View</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No results</p>
    <?php endif; ?>
</div>
<?php require(__DIR__ . "/partials/flash.php");
