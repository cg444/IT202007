<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!is_logged_in()) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You must be logged in to access this page");
    die(header("Location: login.php"));
}
?>

<?php
$isValid = true;
$address = "";
$Payment = "";
if (isset($_POST["place"])){

    if( isset($_POST["address"])){
        $address = $_POST["address"];
    }

    if( isset($_POST["address2"])){
        $address = $address . ", " .$_POST["address2"];
    }
    if( isset($_POST["city"])){
        $address = $address . ", " .$_POST["city"];
    }
    if( isset($_POST["state"])){
        $address = $address . ", " .$_POST["state"];
    }
    if( isset($_POST["zip"])){
        $address = $address . ", " .$_POST["zip"];
    }
    if( isset($_POST["payment"])){
        $Payment = $_POST["payment"];
    }

    if (!isset($_POST["address"])  || !isset($_POST["city"]) || !isset($_POST["state"]) ||
        !isset($_POST["zip"]) || !isset($_POST["payment"]) ){
        $isValid = false;

    }


    if ($isValid){
        //pull data from user's cart
        $db = getDB();
        $id = get_user_id();
        $results = [];

        if (isset($id)) {
            $stmt = $db->prepare("SELECT Cart.product_id, Cart.quantity, Cart.user_id, Cart.price, Products.name, Products.quantity as originalQ,
      (Products.price * Cart.quantity) as sub from Cart JOIN Users on Users.id = Cart.user_id JOIN Products on Products.id = Cart.product_id
       WHERE Users.id = :q AND Products.visibility = 0 ");

            $r = $stmt->execute([":q" => $id]);
            if ($r) {
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            else {
                flash("There was a problem fetching the results ");
            }
        } else{flash("You do not have a valid ID");}

        // calculating total and checking for quantity
        $quantityCheck = true;
        $total = 0;
        foreach($results as $a){
            if ($a["sub"]){
                $total += $a["sub"];
            }

            if($a["quantity"] > $a["originalQ"]){
                $originalQ = $a["originalQ"];
                $name = $a["name"];
                $quantityCheck = false;
            }
        }

        if ($quantityCheck){
            // creating an order for user
            $stmt = $db->prepare("INSERT INTO Orders(user_id, total_price, address, Payment) VALUES(:user_id,:total_price, :address, :Payment)");

            $r = $stmt->execute([":user_id" => $id,
                ":total_price" => $total,
                ":address" => $address,
                ":Payment" => $Payment
            ]);
            $e = $stmt->errorInfo();
            if ($e[0] == "00000") {
                echo "processing...";
            }else{
                flash("oops, something went wrong");
            }

            //fetching last order entered in table by MAX(id)
            $orderid = [];
            $stmt = $db->prepare("SELECT MAX(id) as last_order_id from Orders where user_id = :id ");
            $r = $stmt->execute([":id" => $id]);
            if ($r) {
                $orderid = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            else {
                flash("There was a problem fetching the results ");
            }


            // populating order items that are confirmed

            foreach($results as $data){
                $stmt = $db->prepare("INSERT INTO ordersItems(product_id, user_id, quantity, price, order_id) VALUES(:product_id,:user_id, :quantity, :price, :order_id)");
                $r = $stmt->execute([":product_id" => $data["product_id"],
                    ":user_id" => $id,
                    ":quantity" => $data["quantity"],
                    ":price" => $data["price"],
                    ":order_id" => $orderid["last_order_id"]
                ]);

                //update quantity in products.

                $stmt = $db->prepare("UPDATE Products set  quantity =quantity - :desired where id = :id");
                $r = $stmt->execute([":desired" => $data["quantity"], ":id" => $data["product_id"]]);
                if ($r) {
                    echo "Updated quantity";
                }
                else {
                    echo "Error updating quantity";
                }
            }

            //when all said and done, delete all items from cart...

            if(isset($_POST["place"])){
                $stmt = $db->prepare("DELETE FROM Cart where user_id = :uid");
                $r = $stmt->execute([":uid"=>get_user_id()]);
                if($r){
                    echo "items were deleted";
                }
            }

            //redirect to Confirmation
            echo "<script> location.href='confirmation.php'; </script>";
            exit;

        }
        else{ //if quantitycheck is false
            flash ("$name quantity selected, is greater than our current stock of $originalQ ");
        }


    }else{ //if missing fields
        flash("missing fields, please fill out form");
    }
}


?>

<form method = "POST">
    <div class="form-group">
        <h3> ADDRESS </h3>
        <label for="Address">Address</label>
        <input type="text" name="address" class="form-control" id="Address" placeholder="Street Address" required>
    </div>
    <div class="form-group">
        <label for="Appt.">Address 2</label>
        <input type="text" name="address2" class="form-control" id="Appt." placeholder="">
    </div>
    <div class="form-group">
        <div class="form-group col-md-6">
            <label for="City">City</label>
            <input type="text" name="city" class="form-control" id="City" placeholder="ie. Newark" required>
        </div>
        <div class="form-group">
            <label for="State">State</label>
            <input type="text" name="state" class="form-control" id="State" placeholder="ie New Jersey">
        </div>
        <div class="form-group col-md-2">
            <label for="Zip">Zip</label>
            <input type="text" name="zip" class="form-control" id="Zip" required>
        </div>
    </div>
    <h4> PAYMENT METHOD </h4>
    <div class="form-group">
        <select name = "payment" id="PAYMENT" class="form-control" required>
            <option value="visa">Visa</option>
            <option value="discover">Discover</option>
            <option value="mastercard">MasterCard</option>
            <option value="AMEX">AMEX</option>
            <option value="cash">Cash</option>
        </select>
    </div>
    <button class="btn btn-secondary btn-lg btn-block" type="submit" name="place" value="place">Place Order</button>
</form>

<?php require_once(__DIR__ . "/partials/flash.php"); ?>