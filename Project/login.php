<?php require_once(__DIR__ . "/partials/nav.php"); ?>
    <form method="POST">
        <label for="email">Email or Username:</label>
        <input type="text" id="email" name="email" required/>
            <label for="p1">Password:</label>
            <input type="password" id="p1" name="password" required/>
        <input type="submit" name="login" value="Login"/>
    </form>

<?php
if (isset($_POST["login"])) {
    $email = null;
    $password = null;
    if (isset($_POST["email"])) {
        $email = $_POST["email"]; }

    if (isset($_POST["password"])) {
        $password = $_POST["password"]; }

    $isValid = true;
    if (!isset($email) || !isset($password)) {
        $isValid = false;
        flash("Email or password missing"); }

    /*if (!strpos($email, "@")) {
        $isValid = false;
        //echo "<br>Invalid email<br>";
        flash("Invalid email"); } */

    if ($isValid) {
        $db = getDB();
        if (isset($db)) {
            $stmt = $db->prepare("SELECT id, email, username, password from Users WHERE email = :email OR username = :email LIMIT 1");

            $params = array(":email" => $email);
            $r = $stmt->execute($params);

            $e = $stmt->errorInfo();
            if ($e[0] != "00000") {
                flash("Something went wrong, please try again");  }
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result && isset($result["password"])) {
                $password_hash_from_db = $result["password"];

                if (password_verify($password, $password_hash_from_db)) {
                    $stmt = $db->prepare("
SELECT Roles.name FROM Roles JOIN UserRoles on Roles.id = UserRoles.role_id where UserRoles.user_id = :user_id and Roles.is_active = 1 and UserRoles.is_active = 1");
                    $stmt->execute([":user_id" => $result["id"]]);
                    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    unset($result["password"]);//remove password so we don't leak it beyond this page
                    //let's create a session for our user based on the other data we pulled from the table
                    $_SESSION["user"] = $result;//we can save the entire result array since we removed password

                    if ($roles) {
                        $_SESSION["user"]["roles"] = $roles; }
                    else {
                        $_SESSION["user"]["roles"] = []; }
                    //on successful login let's serve-side redirect the user to the home page.
                    flash("Log in successful");
                    die(header("Location: home.php"));  }
                else {
                    flash("Invalid password"); }
            }
            else {
                flash("Invalid user"); }
        }
    }
    else {
        flash("There was a validation issue"); }
}
?>
<?php require(__DIR__ . "/partials/flash.php");
/* design intentionally made horrible to see who doesn't change it*/
body {
    font-family: system;
    font-size: 1.5em;
}

ul[class="nav"]{
    list-style-type: none;
    margin: 0;
    padding: 0;
    overflow: hidden;
    background-color: #026e02;
}

.nav > li {
    float: left;
}

.nav > li a {
    display: block;
    color: #ffffff;
    text-align: center;
    padding: 16px;
    text-decoration: none;
}

.nav > li a:hover {
    background-color: #39ca40;
}

input, select {
    width: 100%;
    padding: 12px 20px;
    margin: 8px 0;
    display: inline-block;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}

input[type=submit] {
    width: 100%;
    background-color: #026e02;
    color: white;
    padding: 14px 20px;
    margin: 8px 0;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

input:nth-of-type(1) {
    margin-left: 5px !important;
    margin-right: 5px !important;
}

input:nth-of-type(2) {
    margin-left: 10px !important;
    margin-right: 10px !important;
}

input:nth-of-type(3) {
    margin-left: 15px !important;
    margin-right: 15px !important;
}

input:nth-of-type(4) {
    margin-left: 20px !important;
    margin-right: 20px !important;
}

input:nth-of-type(5) {
    margin-left: 25px !important;
    margin-right: 25px !important;
}