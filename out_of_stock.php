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
            $query = "SELECT products.id, products.name, products.price, COALESCE(SUM(order_details.quantity), 0) AS total 
            FROM products
            LEFT JOIN order_details ON order_details.product_id = products.id
            LEFT JOIN orders ON orders.id = order_details.order_id AND orders.created >= DATE(NOW()) - INTERVAL 7 DAY 
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

    <div class="container" style="margin-top: 20px;">
        <div class="card" style="border-width: 3px; border-color:#496058; background-color: #496058;">
            <div class="card-header" style="color: white;">
                <h2>Out of Stock Product</h2>
            </div>
            <div class="card-body" style="background-color: white">
                <div class="row">
                    <?php $products = out_of_stock_product($con); ?>
                    <?php foreach ($products as $product) : ?>
                        <div class="col-md-4">
                            <div class="card-body" style="color: black;">
                                <h3 class="card-title" style="font-weight: bold; margin:0;"><?php echo ucfirst($product['name']); ?></h3>
                                <?php if ($product['total'] > 100) : ?>
                                    <p class="card-text" style="margin:0;"><?php echo $product['total']; ?> units sold</p>
                                    <div class='btn disabled btn-danger'>
                                        <p class="card-text" style="margin-bottom: 0;">Out of stock</p>
                                    </div>
                                <?php endif; ?>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text">RM<?php echo $product['price']; ?></small>
                                    <div class="btn-group">
                                        <a href="product_read_one.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-secondary" style="font-weight: bold; border-color:#496058;">View</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</body>