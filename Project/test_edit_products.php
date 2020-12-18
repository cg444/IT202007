?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!has_role("Admin")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>
<?php
//we'll put this at the top so both php block have access to it
if(isset($_GET["id"])){
    $id = $_GET["id"];
}
?>
<?php
//saving
if(isset($_POST["save"])){
    //TODO add proper validation/checks
    $name = $_POST["name"];
    $quantity = $_POST["quantity"];
    $price = $_POST["price"];
    $description = $_POST["description"];
    $user = get_user_id();
    $db = getDB();
    if(isset($id)){
        $stmt = $db->prepare("UPDATE Products set name=:name, user_id=:user, quantity=:quantity, price=:price, description=:description where id=:id");
        $r = $stmt->execute([
            ":name"=>$name,
            ":quantity"=>$quantity,
            ":price"=>$price,
            ":description"=>$description,
            ":user"=>$user,
            //	":nst"=>$nst,
            ":id"=>$id
        ]);
        if($r){
            flash("Updated successfully with id: " . $id);
        }
        else{
            $e = $stmt->errorInfo();
            flash("Error updating: " . var_export($e, true));
        }
    }
    else{
        flash("ID isn't set, we need an ID in order to update");
    }
}
?>
<?php
//fetching
$result = [];
if(isset($id)){
    $id = $_GET["id"];
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM Products where id = :id");
    $r = $stmt->execute([":id"=>$id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<form method="POST">
    <label>Name</label>
    <input name="name" placeholder="name"/>

    <label>quantity</label>
    <input type="number" min="1" name="quantity"/>

    <label>price</label>
    <input type="number" min="1" name="price"/>

    <label>description</label>
    <input type="TEXT"  name="description"/>


    <input type="submit" name="save" value="Update"/>
</form>

<?php require(__DIR__ . "/partials/flash.php");