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
    <link href="https://fonts.googleapis.com/css2?family=Pathway+Extreme&display=swap" rel="stylesheet">

</head>

<body>
    <?php include 'nav.php' ?>


    <!-- container -->
    <div class="container" style="margin-top: 90px;">
        <div class="cp-page-header">
            <h1>Add Order</h1>
        </div>

        <!-- html form to create product will be here -->
        <?php
        // Check if form is submitted
        if ($_POST) {
            // Include database connection
            include 'config/database.php';
            try {
                // Sanitize and validate input fields
                $customer_name = htmlspecialchars(strip_tags($_POST['customer_name']));
                $products = isset($_POST['product']) ? $_POST['product'] : array();
                $quantities = isset($_POST['quantity']) ? $_POST['quantity'] : array();

                // check if any field is empty

                if (empty($customer_name)) {
                    $customer_name_error = "Please select username";
                }

                if (empty($products)) {
                    $products_error = "Please select product";
                }

                if (empty($quantities)) {
                    $quantity_error = "Please enter quantity";
                } else {
                    // Check if any quantity is 0
                    foreach ($quantities as $quantity) {
                        if ($quantity == 0) {
                            $quantity_error = "Quantity cannot be 0";
                            break;
                        }
                    }
                }


                // check if there are any errors
                if (!isset($customer_name_error) && !isset($products_error) && !isset($quantity_error)) {

                    // Begin transaction
                    $con->beginTransaction();

                    // Insert data into orders table
                    $query = "INSERT INTO orders SET customer_name=:customer_name, created=:created";
                    $stmt = $con->prepare($query);
                    $created = date('Y-m-d H:i:s');
                    $stmt->bindParam(':customer_name', $customer_name);
                    $stmt->bindParam(':created', $created);

                    // Execute the query
                    if ($stmt->execute()) {
                        $order_id = $con->lastInsertId();

                        // Prepare the query to insert data into order_details table
                        $query2 = "INSERT INTO order_details SET order_id=:order_id, product_id=:product_id, quantity=:quantity";
                        $stmt2 = $con->prepare($query2);

                        // Insert data into order_details table for each product
                        for ($i = 0; $i < count($products); $i++) {
                            $product_id = htmlspecialchars(strip_tags($products[$i]));
                            $quantity = htmlspecialchars(strip_tags($quantities[$i]));

                            $stmt2->bindParam(':order_id', $order_id);
                            $stmt2->bindParam(':product_id', $product_id);
                            $stmt2->bindParam(':quantity', $quantity);
                            $stmt2->execute();
                        }

                        // Commit the transaction
                        $con->commit();

                        echo "<div class='alert alert-success'>Record was saved.</div>";
                        $customer_name = "";
                        $products = array();
                        $quantities = array();
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
                    <td style="font-weight: bold;">Username</td>
                    <td>
                        <?php
                        // Assign the value of customer_name to a variable
                        $selected_customer = isset($_POST['customer_name']) ? $_POST['customer_name'] : '';
                        ?>
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
                            foreach ($customer_name as $customer) {
                                $selected = ($selected_customer == $customer['username']) ? 'selected' : '';
                                echo "<option value='{$customer['username']}' {$selected}>{$customer['username']}</option>";
                            }
                            ?>
                        </select>

                        <?php if (isset($customer_name_error)) : ?>
                            <span class="text-danger"><?php echo $customer_name_error; ?></span>
                        <?php endif; ?>

                    </td>
                </tr>

                <tr>
                    <td style="font-weight: bold;">Product</td>
                    <td>
                        <div class="product-list">
                            <div class="form-group product-item">
                                <select name="product[]" class="form-control">
                                    <option value="">-- Select Products --</option>
                                    <?php
                                    // include database connection
                                    include 'config/database.php';
                                    // fetch products from the database
                                    $query = "SELECT id, name FROM products ORDER BY name";
                                    $stmt = $con->prepare($query);
                                    $stmt->execute();
                                    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    // dynamically populate the dropdown list with products
                                    foreach ($products as $p) {
                                        $selected = isset($_POST['product']) && in_array($p['id'], $_POST['product']) ? 'selected' : '';
                                        echo "<option value='{$p['id']}' {$selected}>{$p['name']}</option>";
                                    }
                                    ?>
                                </select>
                                <?php if (isset($products_error)) : ?>
                                    <div class="error-message">
                                        <span class="text-danger"><?php echo $products_error; ?></span>
                                    </div>
                                <?php endif; ?>

                                <label>Quantity</label>
                                <?php
                                $count = isset($quantities) ? count($quantities) : 1;
                                for ($i = 0; $i < $count; $i++)
                                ?>
                                <input type="number" name="quantity[]" class="form-control" value="<?php echo isset($quantities[$i]) ? htmlspecialchars($quantities[$i]) : ''; ?>" />
                                <?php if (isset($quantity_error)) { ?>
                                    <div class="error-message">
                                        <span class="text-danger"><?php echo $quantity_error; ?></span>
                                    </div>
                                <?php } ?>

                            </div>
                        </div>

                        <div class=button style="float: right; display: block;">
                            <input type="button" value="+" class="add_one btn" style="background-color: #496058; color:white" />
                            <input type="button" value="-" class="delete_one btn " style="background-color: #496058; color:white" />
                        </div>
                    </td>
                </tr>

                <tr>
                    <td></td>
                    <td>
                        <input type='submit' value='Save' class='btn btn-primary' />
                        <a href='order_read.php' class='btn btn-danger'>Back to read orders</a>
                    </td>
                </tr>

            </table>
        </form>


    </div>
    <!-- end .container -->

    <script>
        document.addEventListener('click', function(event) {
            if (event.target.matches('.add_one')) {
                var element = document.querySelector('.product-list');
                var clone = element.cloneNode(true);
                element.after(clone);
            }
            if (event.target.matches('.delete_one')) {
                var total = document.querySelectorAll('.product-list').length;
                if (total > 1) {
                    var element = document.querySelector('.product-list');
                    element.remove(element);
                }
            }
        }, false);
    </script>

    <script src=" https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>

</html>