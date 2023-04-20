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
    <title>Add Product</title>
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
            <h1>Add Product</h1>
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
                $name = htmlspecialchars(strip_tags($_POST['name']));
                $catname = htmlspecialchars(strip_tags($_POST['catname']));
                $description = htmlspecialchars(strip_tags($_POST['description']));
                $price = htmlspecialchars(strip_tags($_POST['price']));
                $promotion_price = htmlspecialchars(strip_tags($_POST['promotion_price']));
                $manufacture_date = htmlspecialchars(strip_tags($_POST['manufacture_date']));
                $expired_date = htmlspecialchars(strip_tags($_POST['expired_date']));

                // fetch categories from the database
                $query = "SELECT id, catname FROM category";
                $stmt = $con->prepare($query);
                $stmt->execute();
                $category = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // check if any field is empty
                if (empty($name)) {
                    $name_error = "Please enter product name";
                }
                if (empty($catname)) {
                    $catname_error = "Please select category";
                }
                if (empty($description)) {
                    $description_error = "Please enter product description";
                }
                if (empty($price)) {
                    $price_error = "Please enter product price";
                }

                if (empty($manufacture_date)) {
                    $manufacture_date_error = "Please enter manufacture date";
                }

                // check if expired date  fill up & later than manufacture date
                if (!empty($expired_date)) {
                    if (strtotime($expired_date) <= strtotime($manufacture_date)) {
                        $expired_date_error = "Expired date should be later than manufacture date";
                    }
                }

                // check if user fill up promotion price & must cheaper than original price 
                if (!empty($promotion_price)) {
                    if ($promotion_price >= $price) {
                        $promotion_price_error = "Promotion price must be cheaper than original price";
                    }
                }

                if (!empty($catname)) {
                    $stmt->bindParam(':catname', $catname);
                } else {
                    $stmt->bindValue(':catname', $category[0]['id']);
                }

                // check if there are any errors
                if (!isset($name_error) && !isset($catname_error) && !isset($description_error) && !isset($price_error) && !isset($promotion_price_error) && !isset($manufacture_date_error) && !isset($expired_date_error)) {


                    // insert query
                    $query = "INSERT INTO products SET name=:name, catname=:catname, description=:description, price=:price, promotion_price=:promotion_price, manufacture_date=:manufacture_date, expired_date=:expired_date, created=:created"; // info insert to blindParam

                    // prepare query for execution
                    $stmt = $con->prepare($query);

                    // bind the parameters
                    $stmt->bindParam(':name', $name);
                    $stmt->bindParam(':catname', $catname);
                    $stmt->bindParam(':description', $description);
                    $stmt->bindParam(':price', $price);
                    $stmt->bindParam(':promotion_price', $promotion_price);
                    $stmt->bindParam(':manufacture_date', $manufacture_date);
                    $stmt->bindParam(':expired_date', $expired_date);


                    // specify when this record was inserted to the database
                    $created = date('Y-m-d H:i:s');
                    $stmt->bindParam(':created', $created);

                    // Execute the query
                    if ($stmt->execute()) {
                        echo "<div class='alert alert-success'>Record was saved.</div>";
                        $name = "";
                        $catname = "";
                        $description = "";
                        $price = "";
                        $promotion_price = "";
                        $manufacture_date = "";
                        $expired_date = "";
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
                    <td>Name</td>
                    <td><input type='text' name='name' class="form-control" value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" />
                        <?php if (isset($name_error)) { ?><span class="text-danger"><?php echo $name_error; ?></span><?php } ?></td>
                </tr>

                <tr>
                    <td>Categories</td>
                    <td>
                        <select name="catname" class="form-control">
                            <option value="">-- Select Category --</option>
                            <?php
                            // include database connection
                            include 'config/database.php';
                            // fetch categories from the database
                            $query = "SELECT id, catname FROM category ORDER BY catname";
                            $stmt = $con->prepare($query);
                            $stmt->execute();
                            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            // dynamically populate the dropdown list with categories
                            foreach ($categories as $category) {
                                $selected = isset($catname) && $catname == $category['catname'] ? 'selected' : '';
                                echo "<option value='{$category['catname']}' {$selected}>{$category['catname']}</option>";
                            }
                            ?>
                        </select>
                        <?php if (isset($catname_error)) : ?>
                            <span class="text-danger"><?php echo $catname_error; ?></span>
                        <?php endif; ?>
                    </td>
                </tr>

                <tr>
                    <td>Description</td>
                    <td><textarea name='description' class="form-control" value="<?php echo isset($description) ? htmlspecialchars($description) : ''; ?>"></textarea>
                        <?php if (isset($description_error)) { ?><span class="text-danger"><?php echo  $description_error; ?></span><?php } ?></td>
                </tr>

                <tr>
                    <td>Price</td>
                    <td><input type="number" name="price" class="form-control" value="<?php echo isset($price) ? htmlspecialchars($price) : ''; ?>" />
                        <?php if (isset($price_error)) { ?><span class="text-danger"><?php echo $price_error; ?></span><?php } ?></td>
                </tr>

                <tr>
                    <td>Promotion Price</td>
                    <td><input type="number" name="promotion_price" class="form-control" value="<?php echo isset($promotion_price) ? htmlspecialchars($promotion_price) : ''; ?>" />
                        <?php if (isset($promotion_price_error)) { ?><span class="text-danger"><?php echo $promotion_price_error; ?></span><?php } ?></td>
                </tr>

                <tr>
                    <td>Manufacture Date</td>
                    <td><input type="date" name="manufacture_date" class="form-control" value="<?php echo isset($manufacture_date) ? htmlspecialchars($manufacture_date) : ''; ?>" />
                        <?php if (isset($manufacture_date_error)) { ?><span class="text-danger"><?php echo $manufacture_date_error; ?></span><?php } ?></td>
                </tr>

                <tr>
                    <td>Expired Date</td>
                    <td><input type="date" name="expired_date" class="form-control" value="<?php echo isset($expired_date) ? htmlspecialchars($expired_date) : ''; ?>" />
                        <?php if (isset($expired_date_error)) { ?><span class="text-danger"><?php echo $expired_date_error; ?></span><?php } ?></td>
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