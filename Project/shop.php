<?php require_once(__DIR__ . "/partials/nav.php"); ?>

<?php

$results = [];
$cat = 0;
$db = getDB();



if (has_role("Admin")) {
    if (isset($_POST["sort"])) {
        $stmt = $db->prepare("SELECT id,name, price, description FROM Products WHERE quantity > 0  ORDER BY price LIMIT 10");
        $r = $stmt->execute();
    }
    elseif (isset($_POST["category"])) {
        $cat = $_POST["category"];
        $stmt = $db->prepare("SELECT id,name, price, description FROM Products WHERE quantity > 0  AND category = :q LIMIT 10");
        $r = $stmt->execute( [":q" => $cat]);
    }else{
        $stmt = $db->prepare("SELECT id,name, price, description FROM Products  LIMIT 10");
        $r = $stmt->execute();
    }
}else{
    if (isset($_POST["sort"])) {
        $stmt = $db->prepare("SELECT id,name, price, description FROM Products WHERE quantity > 0 AND visibility = 0  ORDER BY price LIMIT 10");
        $r = $stmt->execute();
    }
    elseif (isset($_POST["category"])) {
        $cat = $_POST["category"];
        $stmt = $db->prepare("SELECT id,name, price, description FROM Products WHERE quantity > 0  AND category = :q AND visibility = 0 LIMIT 10");
        $r = $stmt->execute( [":q" => $cat]);
    }else{
        $stmt = $db->prepare("SELECT id,name, price, description FROM Products WHERE quantity > 0  AND visibility = 0 LIMIT 10");
        $r = $stmt->execute();
    }
}

if ($r) {
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
else {
    flash("There was a problem fetching the products " . var_export($stmt->errorInfo(), true));
}



// feching category to populate dropdown
$stmt = $db->prepare("SELECT DISTINCT category  FROM Products where visibility = 0 LIMIT 10");
$r = $stmt->execute();
if ($r) {
    $category = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

    <script>
        //php will exec first so just the value will be visible on js side
        function addToCart(product_id){
            //https://www.w3schools.com/xml/ajax_xmlhttprequest_send.asp
            let xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    let json = JSON.parse(this.responseText);
                    if (json) {
                        if (json.status == 200) {
                            alert(json.message);
                        } else {
                            alert(json.error);
                        }
                    }
                }
            };
            xhttp.open("POST", "<?php echo "add_to_cart.php";?>", true);
            //this is required for post ajax calls to submit it as a form
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            //map any key/value data similar to query params
            xhttp.send("product_id="+product_id);
        }
    </script>


    <h3>PRODUCTS</h3>
        <?php if (count($results) > 0): ?>
            <?php foreach ($results as $r): ?>
                    <div class="card-body">
                        <a href = "customer_view_products.php?id=<?php safer_echo($r['id']); ?>" <h5 class="card-title"><?php safer_echo($r["name"]); ?></h5></a>
                        <h6 class="card-title"><?php safer_echo($r["price"]); ?></h6>
                        <p class="card-text"><?php safer_echo($r["description"]); ?></p>
                        <?php if (is_logged_in()): ?>
                            <button form = "form1" type="button" onclick="addToCart(<?php echo $r["id"];?>);" class="btn btn-primary btn-lg">Add to Cart</button>
                        <?php endif;?>
                        <?php if (has_role("Admin")): ?>
                            <a href="test_edit_products.php?id=<?php safer_echo($r['id']); ?>" <button style= "margin: 1em; float: right;" type="submit" class="btn btn-success"</button>Edit</a>
                        <?php endif; ?>
                    </div>
            <?php endforeach; ?>
        <?php endif; ?>
<?php require_once(__DIR__ . "/partials/flash.php"); ?>