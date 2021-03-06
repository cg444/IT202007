<link rel="stylesheet" href="static/css/styles.css">
<?php
//we'll be including this on most/all pages so it's a good place to include anything else we want on those pages
require_once(__DIR__ . "/../lib/helpers.php");
?>
<nav>
    <ul class="nav">
        <li><a href="home.php">Home</a></li>
        <?php if (!is_logged_in()): ?>
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php">Register</a></li>
            <li><a href="customer_list_products.php">View Products</a></li>
        <?php endif; ?>
        <?php if (has_role("Admin")): ?>
            <li><a href="test_create_products.php">Create Products</a></li>
            <li><a href="test_edit_products.php">Edit Products</a></li>
        <?php endif; ?>
        <?php if (is_logged_in()): ?>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="logout.php">Logout</a></li>
            <li><a href="customer_list_products.php">View Products</a></li>
            <li><a href="my_cart.php">View Cart</a></li>
            <li><a href="shop.php">Create Cart</a></li>
            <li><a href="checkout.php">Checkout</a></li>
        <?php endif; ?>
    </ul>
</nav>
