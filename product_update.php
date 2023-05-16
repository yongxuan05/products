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
    <title>Update</title>
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
        <div class="page-header">
            <h1>Update Product</h1>
        </div>
        <!-- PHP read record by ID will be here -->
        <?php
        // get passed parameter value, in this case, the record ID
        // isset() is a PHP function used to verify if a value is there or not
        $id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Record ID not found.');

        //include database connection
        include 'config/database.php';

        // read current record's data
        try {

            // prepare select query
            $query = "SELECT * FROM products WHERE id = ? ";
            $stmt = $con->prepare($query);

            // this is the first question mark
            $stmt->bindParam(1, $id);

            // execute our query
            $stmt->execute();

            // store retrieved row to a variable
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // values to fill up our form
            $name = $row['name'];
            $description = $row['description'];
            $catname = $row['catname'];
            $price = $row['price'];
            $promotion_price = $row['promotion_price'];
            $manufacture_date = $row['manufacture_date'];
            $expired_date = $row['expired_date'];
        }

        // show error
        catch (PDOException $exception) {
            die('ERROR: ' . $exception->getMessage());
        }
        ?>

        <!-- HTML form to update record will be here -->
        <!-- PHP post to update record will be here -->
        <?php
        // check if form was submitted
        if ($_POST) {
            try {
                // write update query
                // in this case, it seemed like we have so many fields to pass and
                // it is better to label them and not use question marks
                $query = "UPDATE products SET name=:name, catname=:catname, description=:description, price=:price, promotion_price=:promotion_price, manufacture_date=:manufacture_date, expired_date=:expired_date WHERE id = :id";

                // prepare query for excecution
                $stmt = $con->prepare($query);

                // posted values
                $name = htmlspecialchars(strip_tags($_POST['name']));
                $catname = htmlspecialchars(strip_tags($_POST['catname']));
                $description = htmlspecialchars(strip_tags($_POST['description']));
                $price = htmlspecialchars(strip_tags($_POST['price']));
                $promotion_price = htmlspecialchars(strip_tags($_POST['promotion_price']));
                $manufacture_date = htmlspecialchars(strip_tags($_POST['manufacture_date']));
                $expired_date = htmlspecialchars(strip_tags($_POST['expired_date']));

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
                }

                // check if there are any errors
                if (!isset($name_error) && !isset($catname_error) && !isset($description_error) && !isset($price_error) && !isset($promotion_price_error) && !isset($manufacture_date_error) && !isset($expired_date_error)) {



                    // bind the parameters
                    $stmt->bindParam(':name', $name);
                    $stmt->bindParam(':catname', $catname);
                    $stmt->bindParam(':description', $description);
                    $stmt->bindParam(':price', $price);
                    $stmt->bindParam(':promotion_price', $promotion_price);
                    $stmt->bindParam(':manufacture_date', $manufacture_date);
                    $stmt->bindParam(':expired_date', $expired_date);
                    $stmt->bindParam(':id', $id);

                    // Execute the query
                    if ($stmt->execute()) {
                        echo "<div class='alert alert-success'>Record was updated.</div>";
                    } else {
                        echo "<div class='alert alert-danger'>Unable to update record. Please try again.</div>";
                    }
                }
            }
            // show errors
            catch (PDOException $exception) {
                die('ERROR: ' . $exception->getMessage());
            }
        } ?>


        <!--we have our html form here where new record information can be updated-->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id={$id}"); ?>" method="post">
            <table class='table table-hover table-responsive table-bordered'>
                <tr>
                    <td style="font-weight: bold;">Product Name</td>
                    <td><input type='text' name='name' class="form-control" value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" />
                        <?php if (isset($name_error)) { ?><span class="text-danger"><?php echo $name_error; ?></span><?php } ?></td>
                </tr>

                <tr>
                    <td style="font-weight: bold;">Category</td>
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
                    <div class="form-group">
                        <td style="font-weight: bold;">Description</td>
                        <td> <textarea class="form-control" name="description"><?php echo htmlspecialchars($description, ENT_QUOTES); ?></textarea>
                            <?php if (isset($description_error)) { ?><span class="text-danger"><?php echo  $description_error; ?></span><?php } ?></td>
                    </div>
                </tr>

                <tr>
                    <td style="font-weight: bold;">Price</td>
                    <td><input type="number" step="0.01" name="price" class="form-control" value="<?php echo isset($price) ? htmlspecialchars($price) : ''; ?>" />
                        <?php if (isset($price_error)) { ?><span class="text-danger"><?php echo $price_error; ?></span><?php } ?></td>
                </tr>

                <tr>
                    <td style="font-weight: bold;">Promotion Price</td>
                    <td><input type="number" step="0.01" name="promotion_price" class="form-control" value="<?php echo isset($promotion_price) ? htmlspecialchars($promotion_price) : ''; ?>" />
                        <?php if (isset($promotion_price_error)) { ?><span class="text-danger"><?php echo $promotion_price_error; ?></span><?php } ?></td>
                </tr>

                <tr>
                    <td style="font-weight: bold;">Manufacture Date</td>
                    <td><input type="date" name="manufacture_date" class="form-control" value="<?php echo isset($manufacture_date) ? htmlspecialchars($manufacture_date) : ''; ?>" />
                        <?php if (isset($manufacture_date_error)) { ?><span class="text-danger"><?php echo $manufacture_date_error; ?></span><?php } ?></td>
                </tr>

                <tr>
                    <td style="font-weight: bold;">Expired Date</td>
                    <td><input type="date" name="expired_date" class="form-control" value="<?php echo isset($expired_date) ? htmlspecialchars($expired_date) : ''; ?>" />
                        <?php if (isset($expired_date_error)) { ?><span class="text-danger"><?php echo $expired_date_error; ?></span><?php } ?></td>
                </tr>


                <tr>
                    <td></td>
                    <td>
                        <input type='submit' value='Save Changes' class='btn btn-primary' />
                        <a href='product_read.php' class='btn btn-danger'>Back to read products</a>
                    </td>
                </tr>
            </table>
        </form>

    </div>
    <!-- end .container -->
</body>

</html>