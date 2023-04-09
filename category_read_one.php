<!DOCTYPE HTML>
<html>

<head>
    <title>CategoryDetails</title>
    <meta charset="utf-8">
    <!-- Latest compiled and minified Bootstrap CSS (Apply your Bootstrap here -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link href="style.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@600&display=swap" rel="stylesheet">


</head>

<body>
    <?php include 'nav.php' ?>


    <!-- PHP read one record will be here -->
    <?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    // include database connection
    include 'config/database.php';

    // retrieve category ID from URL parameter or set default value
    $id = isset($_GET['id']) ? $_GET['id'] : 1;


    // validate category ID
    if (!is_numeric($id)) {

        // redirect to error page or display error message
        header("Location: error.php");
        exit();
    }

    // query to select the category name
    $category_query = "SELECT catname FROM category WHERE id = ?";
    $category_stmt = $con->prepare($category_query);
    $category_stmt->bindParam(1, $id);
    $category_stmt->execute();
    $category_row = $category_stmt->fetch(PDO::FETCH_ASSOC);
    $catname = $category_row['catname'];

    // display the header with the category name
    echo "<div class='container'>";
    echo "<div class='page-header'>";
    echo "<h1>" . ucfirst($catname) . "</h1>";
    echo "</div>";

    // query to select all products that belong to the category name
    $products_query = "SELECT * FROM products JOIN category ON products.catname = category.catname WHERE category.id = ?";
    $products_stmt = $con->prepare($products_query);
    $products_stmt->bindParam(1, $id);
    $products_stmt->execute();

    // check if more than 0 record found
    $num = $products_stmt->rowCount();



    if ($num > 0) {
        // display products in a table format
        echo "<table class='table table-hover table-responsive table-bordered'>";
        echo "<tr>";
        echo "<th>Product ID</th>";
        echo "<th>Product Name</th>";
        echo "<th>Description</th>";
        echo "<th>Price</th>";
        echo "<th>Promotion Price</th>";
        echo "<th>Manufacture Date</th>";
        echo "<th>Expiry Date</th>";
        echo "</tr>";

        while ($row = $products_stmt->fetch(PDO::FETCH_ASSOC)) {
            // extract row
            // this will make $row['firstname'] to just $firstname only
            extract($row);

            // creating new table row per record
            echo "<tr>";
            echo "<td>{$id}</td>";
            echo "<td>{$name}</td>";
            echo "<td>{$description}</td>";
            echo "<td>" . number_format($price, 2) . "</td>";
            echo "<td>" . number_format($promotion_price, 2) . "</td>";
            echo "<td>{$manufacture_date}</td>";
            echo "<td>{$expired_date}</td>";
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