<?php
session_start();
if (!isset($_SESSION['username'])) { // If the user is not logged in
    $_SESSION['not_authorized'] = "You are not authorized to access this page!";
    header('Location: login.php'); // Redirect to the login page
    exit;
}
?>

<!DOCTYPE HTML>
<html>

<head>
    <title>Add Order</title>
    <meta charset="utf-8">
    <!-- Latest compiled and minified Bootstrap CSS (Apply your Bootstrap here -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link href="style.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@600&display=swap" rel="stylesheet">


</head>

<body>
    <?php include 'nav.php' ?>


    <!-- container -->
    <div class="container">
        <div class="cp-page-header">
            <h1>Add Order</h1>
        </div>

        <!-- html form to create product will be here -->
        <!-- PHP insert code will be here -->

        <?php

        //$_get (appear in url) and $_post (didnt appear in url) 是传送（隐形）
        if ($_POST) {
            // include database connection
            include 'config/database.php';
            try { //if insert wrong will go to catch

                // posted values
                //html是防止JavaScript&入侵
                $customer_name = htmlspecialchars(strip_tags($_POST['customer_name']));
                $product1 = htmlspecialchars(strip_tags($_POST['product1']));
                $product2 = htmlspecialchars(strip_tags($_POST['product2']));
                $product3 = htmlspecialchars(strip_tags($_POST['product3']));
                $quantity1 = htmlspecialchars(strip_tags($_POST['quantity1']));
                $quantity2 = htmlspecialchars(strip_tags($_POST['quantity2']));
                $quantity3 = htmlspecialchars(strip_tags($_POST['quantity3']));

                // fetch products from the database
                $query = "SELECT id, 'username' FROM customers";
                $query = "SELECT id, 'name' FROM products";
                $stmt = $con->prepare($query);
                $stmt->execute();
                $category = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // check if any field is empty
                if (empty($customer_name)) {
                    $customer_name_error = "Please select username";
                }
                if (empty($product1)) {
                    $product1_error = "Please select product";
                }
                if (empty($quantity1)) {
                    $quantity1_error = "Please enter product quantity";
                }

                if (!empty($customer_name)) {
                    $stmt->bindParam(':customer_name', $customer_name);
                } else {
                    $stmt->bindValue(':customer_name', $category[0]['id']);
                }

                if (!empty($product1)) {
                    $stmt->bindParam(':product1', $product1);
                } else {
                    $stmt->bindValue(':product1', $category[0]['id']);
                }
                if (!empty($product2)) {
                    $stmt->bindParam(':product2', $product2);
                } else {
                    $stmt->bindValue(':product2', $category[0]['id']);
                }
                if (!empty($product3)) {
                    $stmt->bindParam(':product3', $product3);
                } else {
                    $stmt->bindValue(':product3', $category[0]['id']);
                }

                // check if there are any errors
                if (!isset($customer_name_error) && !isset($product1_error) && !isset($quantity1_error)) {


                    // insert query
                    $query = "INSERT INTO orders SET customer_name=:customer_name, product1=:product1, product2=:product2, product3=:product3, quantity1=:quantity1, quantity2=:quantity2, quantity3=:quantity3, created=:created"; // info insert to blindParam

                    // prepare query for execution
                    $stmt = $con->prepare($query);

                    // bind the parameters
                    $stmt->bindParam(':customer_name', $customer_name);
                    $stmt->bindParam(':product1', $product1);
                    $stmt->bindParam(':product2', $product2);
                    $stmt->bindParam(':product3', $product3);
                    $stmt->bindParam(':quantity1', $quantity1);
                    $stmt->bindParam(':quantity2', $quantity2);
                    $stmt->bindParam(':quantity3', $quantity3);

                    // specify when this record was inserted to the database
                    $created = date('Y-m-d H:i:s');
                    $stmt->bindParam(':created', $created);

                    // Execute the query
                    if ($stmt->execute()) {
                        echo "<div class='alert alert-success'>Record was saved.</div>";
                        $customer_name = "";
                        $product1 = "";
                        $product2 = "";
                        $product3 = "";
                        $quantity1 = "";
                        $quantity2 = "";
                        $quantity3 = "";
                    } else {
                        echo "<div class='alert alert-danger'>Unable to save record. Please fill in all required fields.</div>";
                    }
                }
            }

            // show error
            catch (PDOException $exception) {
                die('ERROR: ' . $exception->getMessage());
            }
        }
        ?>

        <!-- html form here where the product information will be entered -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <table class='table table-hover table-responsive table-bordered'>
                <tr>
                    <td>Username</td>
                    <td>
                        <select name="customer_name" class="form-control">
                            <option value="">-- Select Username --</option>
                            <?php
                            // include database connection
                            include 'config/database.php';
                            // fetch categories from the database
                            $query = "SELECT id, username FROM customers ORDER BY username";
                            $stmt = $con->prepare($query);
                            $stmt->execute();
                            $customer_name = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            // dynamically populate the dropdown list with categories
                            foreach ($customer_name as $customer_name) {
                                $selected = isset($customer_name) && $customer_name == $customer_name['username'] ? 'selected' : '';
                                echo "<option value='{$customer_name['username']}' {$selected}>{$customer_name['username']}</option>";
                            }
                            ?>
                        </select>
                        <?php if (isset($customer_name_error)) : ?>
                            <span class="text-danger"><?php echo $customer_name_error; ?></span>
                        <?php endif; ?>

                    </td>
                </tr>

                <tr>
                    <td>Products 1</td>
                    <td>
                        <select name="product1" class="form-control">
                            <option value="">-- Select Products --</option>
                            <?php
                            // include database connection
                            include 'config/database.php';
                            // fetch categories from the database
                            $query = "SELECT id, name FROM products ORDER BY name";
                            $stmt = $con->prepare($query);
                            $stmt->execute();
                            $product1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            // dynamically populate the dropdown list with categories
                            foreach ($product1 as $product1) {
                                $selected = isset($product1) && $product1 == $product1['name'] ? 'selected' : '';
                                echo "<option value='{$product1['name']}' {$selected}>{$product1['name']}</option>";
                            }
                            ?>
                        </select>
                        <?php if (isset($product1_error)) : ?>
                            <span class="text-danger"><?php echo $product1_error; ?></span>
                        <?php endif; ?>


                    <td>Quantity</td>
                    <td><input type="number" name="quantity1" class="form-control" value="<?php echo isset($quantity1) ? htmlspecialchars($quantity1) : ''; ?>" />
                        <?php if (isset($quantity1_error)) { ?><span class="text-danger"><?php echo $quantity1_error; ?></span><?php } ?></td>

                    </td>
                </tr>

                <tr>
                    <td>Products 2</td>
                    <td>
                        <select name="product2" class="form-control">
                            <option value="">-- Select Products --</option>
                            <?php
                            // include database connection
                            include 'config/database.php';
                            // fetch categories from the database
                            $query = "SELECT id, name FROM products ORDER BY name";
                            $stmt = $con->prepare($query);
                            $stmt->execute();
                            $product2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            // dynamically populate the dropdown list with categories
                            foreach ($product2 as $product2) {
                                $selected = isset($product2) && $product2 == $product2['name'] ? 'selected' : '';
                                echo "<option value='{$product2['name']}' {$selected}>{$product2['name']}</option>";
                            }
                            ?>
                        </select>

                    <td>Quantity</td>
                    <td><input type="number" name="quantity2" class="form-control" value="<?php echo isset($quantity2) ? htmlspecialchars($quantity2) : ''; ?>" />
                    </td>
                </tr>

                <tr>
                    <td>Products 3</td>
                    <td>
                        <select name="product3" class="form-control">
                            <option value="">-- Select Products --</option>
                            <?php
                            // include database connection
                            include 'config/database.php';
                            // fetch categories from the database
                            $query = "SELECT id, name FROM products ORDER BY name";
                            $stmt = $con->prepare($query);
                            $stmt->execute();
                            $product3 = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            // dynamically populate the dropdown list with categories
                            foreach ($product3 as $product3) {
                                $selected = isset($product3) && $product3 == $product3['name'] ? 'selected' : '';
                                echo "<option value='{$product3['name']}' {$selected}>{$product3['name']}</option>";
                            }
                            ?>
                        </select>

                    <td>Quantity</td>
                    <td><input type="number" name="quantity3" class="form-control" value="<?php echo isset($quantity3) ? htmlspecialchars($quantity3) : ''; ?>" />

                    </td>
                </tr>

                <tr>
                    <td></td>
                    <td>
                        <input type='submit' value='Save' class='btn btn-primary' />
                        <a href='index.php' class='btn btn-danger'>Back to read products</a>
                    </td>
                </tr>
            </table>
        </form>


    </div>
    <!-- end .container -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>

</html>