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
        function get_lowest_quantity_product($con)
        {
            // fetch products with lowest quantity
            $query = "SELECT products.id, products.name, products.price, COALESCE(SUM(order_details.quantity), 0) AS total 
            FROM products
            LEFT JOIN order_details ON order_details.product_id = products.id
            LEFT JOIN orders ON orders.id = order_details.order_id AND orders.created >= DATE(NOW()) - INTERVAL 7 DAY 
            GROUP BY products.id, products.name, products.price
            HAVING total = (
                SELECT COALESCE(SUM(order_details.quantity), 0) AS lowest_total
                FROM products
                LEFT JOIN order_details ON order_details.product_id = products.id
                LEFT JOIN orders ON orders.id = order_details.order_id AND orders.created >= DATE(NOW()) - INTERVAL 7 DAY 
                GROUP BY products.id, products.name, products.price
                ORDER BY lowest_total ASC 
                LIMIT 1
            )";
            $stmt = $con->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // get product with lowest quantity
        $lowest_quantity_product = get_lowest_quantity_product($con);
    } catch (PDOException $exception) {
        die('ERROR: ' . $exception->getMessage());
    }
    ?>

    <div class="container" style="margin-top: 20px;">
        <h2>Products with Lowest Quantity</h2>
        <div class="row">
            <?php $products = get_lowest_quantity_product($con); ?>
            <?php foreach ($products as $product) : ?>
                <div class="col-md-4">
                    <div class="card mb-4 box-shadow">
                        <div class="card-body" style="color: white;">
                            <h5 class="card-title"><?php echo $product['name']; ?></h5>
                            <p class="card-text"><?php echo $product['total']; ?> units sold</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="btn-group">
                                    <a href="product_read_one.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-secondary" style="color: white;">View</a>
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