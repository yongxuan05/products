<!DOCTYPE HTML>
<html>

<head>
    <title>Create Product</title>
    <meta charset="utf-8">
    <!-- Latest compiled and minified Bootstrap CSS (Apply your Bootstrap here -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link href="style.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@600&display=swap" rel="stylesheet">


</head>

<body>
    <!-- navbar -->
    <nav class="custom-navbar navbar navbar navbar-expand-md navbar-dark bg-dark" arial-label="navigation bar">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.html">navbar<span>.</span></a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="custom-navbar-nav navbar-nav ms-auto mb-2 mb-md-0">
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="index.html">Home</a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="product_create.php">Create Product</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="customer_create.php">Create Customer</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.h">Contact Us</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- end Navbar -->

    <!-- container -->
    <div class="container">
        <div class="cp-page-header">
            <h1>Create Product</h1>
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
                $description = htmlspecialchars(strip_tags($_POST['description']));
                $price = htmlspecialchars(strip_tags($_POST['price']));
                $promotion_price = htmlspecialchars(strip_tags($_POST['promotion_price']));
                $manufacture_date = htmlspecialchars(strip_tags($_POST['manufacture_date']));
                $expired_date = htmlspecialchars(strip_tags($_POST['expired_date']));

                // check if any field is empty
                if (empty($name)) {
                    $name_error = "Please enter product name";
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

                // check if there are any errors
                if (!isset($name_error) && !isset($description_error) && !isset($price_error) && !isset($promotion_price_error) && !isset($manufacture_date_error) && !isset($expired_date_error)) {


                    // insert query
                    $query = "INSERT INTO products SET name=:name, description=:description, price=:price, promotion_price=:promotion_price, manufacture_date=:manufacture_date, expired_date=:expired_date, created=:created"; // info insert to blindParam

                    // prepare query for execution
                    $stmt = $con->prepare($query);

                    // bind the parameters
                    $stmt->bindParam(':name', $name);
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
                        $description = "";
                        $price = "";
                        $promotion_price = "";
                        $manufacture_date = "";
                        $expired_date = "";
                    } else {
                        echo "<div class='alert alert-danger'>Unable to save record.</div>";
                    }
                } else {
                    echo "<div class='alert alert-danger'>Unable to save record. Please fill in all required fields.</div>";
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