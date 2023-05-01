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
    <title>CategoryDetails</title>
    <meta charset="utf-8">
    <!-- Latest compiled and minified Bootstrap CSS (Apply your Bootstrap here -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link href="style.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Pathway+Extreme&display=swap" rel="stylesheet">

</head>

<body>
    <?php include 'nav.php' ?>


    <!-- PHP read one record will be here -->
    <?php

    // isset() is a PHP function used to verify if a value is there or not
    $id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Record ID not found.');

    // include database connection
    include 'config/database.php';

    // validate category ID
    if (!is_numeric($id)) {

        // redirect to error page or display error message
        header("Location: error.php");
        exit();
    }

    // query to select the category name
    $category_query = "SELECT * FROM category WHERE id = ?";
    $category_stmt = $con->prepare($category_query);

    // this is the first question mark
    $category_stmt->bindParam(1, $id);

    // execute our query
    $category_stmt->execute();

    // store retrieved row to a variable
    $category_row = $category_stmt->fetch(PDO::FETCH_ASSOC);

    $catname = $category_row['catname'];

    // display the header with the category name
    echo "<div class='container' style='margin-top: 90px'>";
    echo "<div class='page-header'>";

    //let the first letter to capital
    echo "<h1>" . ucfirst($catname) . "</h1>";
    echo "</div>";

    // query to select all products that belong to the category name
    $products_query = "SELECT products.id AS products_id, products.name, products.description, products.price, products.promotion_price, products.manufacture_date, products.expired_date FROM products JOIN category ON products.catname = category.catname WHERE category.id = ?";
    $products_stmt = $con->prepare($products_query);
    $products_stmt->bindParam(1, $id);
    $products_stmt->execute();

    // check if more than 0 record found
    $num = $products_stmt->rowCount();

    if ($num > 0) {
        // display products in a table format
        echo "<table class='table table-hover table-responsive table-bordered'>";
        echo "<tr>";
        echo "<th>ID</th>";
        echo "<th>Name</th>";
        echo "<th>Description</th>";
        echo "<th>Price</th>";
        echo "<th>Promotion Price</th>";
        echo "<th>Manufacture Date</th>";
        echo "<th>Expiry Date</th>";
        echo "<th>Action</th>";
        echo "</tr>";

        // store retrieved row to a variable
        while ($row = $products_stmt->fetch(PDO::FETCH_ASSOC)) {

            extract($row);

            // creating new table row per record
            echo "<tr>";
            echo "<td>{$products_id}</td>";
            echo "<td>{$name}</td>";
            echo "<td>{$description}</td>";
            echo "<td>" . number_format($price, 2) . "</td>";
            echo "<td>" . ($promotion_price ? number_format($promotion_price, 2) : '-') . "</td>"; // display dash if no promotion price
            echo "<td>" . (($manufacture_date && $manufacture_date != '0000-00-00') ? $manufacture_date : '-') . "</td>"; // display dash if no manufacture_date
            echo "<td>" . (($expired_date && $expired_date != '0000-00-00') ? $expired_date : '-') . "</td>";
            echo "<td>";

            echo "<a href='update.php?id={$id}' class='btn btn-primary m-r-1em'>Edit</a>";
        }

        // end table
        echo "</table>";
    }
    // if no records found
    else {
        echo "<div class='alert alert-danger'>No records found.</div>";
    }
    echo "<a href='category_read.php' class='btn btn-danger'>Back to read categories</a>";
    ?>

    </div> <!-- end .container -->

    <!-- confirm delete record will be here -->

</body>

</html>