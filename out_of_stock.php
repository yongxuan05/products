<!DOCTYPE HTML>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <title>Home</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <?php
    // include database connection
    include 'config/database.php';

    try {
        function out_of_stock_product($con)
        {
            // fetch products with lowest quantity
            $query = "SELECT products.id, products.name, products.price, SUM(orders.quantity) AS total
            FROM products
            LEFT JOIN orders ON orders.product=products.name AND orders.created >= DATE(NOW()) - INTERVAL 7 DAY 
            GROUP BY products.id, products.name, products.price
            HAVING total >= 100";
            $stmt = $con->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // get product with out of stock
        $out_of_stock_product = out_of_stock_product($con);
    } catch (PDOException $exception) {
        die('ERROR: ' . $exception->getMessage());
    }
    ?>

    <div class="container">
        <h2>Products with Out of Stock</h2>
        <div class="row">
            <?php $products = out_of_stock_product($con); ?>
            <?php foreach ($products as $product) : ?>
                <div class="col-md-4">
                    <div class="card mb-4 box-shadow">
                        <div class="card-body" style="color: white;">
                            <h5 class="card-title"><?php echo $product['name']; ?></h5>
                            <?php if ($product['total'] > 100) : ?>
                                <p class="card-text"><?php echo $product['total']; ?> units sold</p>
                                <p class="card-text">Out of stock</p>
                            <?php endif; ?>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="btn-group">
                                    <a href="#" class="btn btn-sm btn-outline-secondary" style="color: white;">View</a>
                                </div>
                                <small class="text" style="color: white;">RM<?php echo $product['price']; ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>