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
        function get_top_selling_product($con)
        {
            // fetch top selling product
            $query = "SELECT products.id, products.name, products.price, SUM(orders.quantity) AS total 
            FROM orders 
            JOIN products ON orders.product=products.name
            WHERE orders.created >= DATE(NOW()) - INTERVAL 7 DAY 
            GROUP BY products.id, products.name, products.price
            ORDER BY total DESC 
            LIMIT 1";
            $stmt = $con->prepare($query);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        // get top selling product
        $top_selling_product = get_top_selling_product($con);
    } catch (PDOException $exception) {
        die('ERROR: ' . $exception->getMessage());
    }
    ?>

    <div class="container">
        <h2>Top Selling Product</h2>
        <div class="row">
            <?php if (isset($top_selling_product)) : ?>
                <div class="col-md-4">
                    <div class="card mb-4 box-shadow">
                        <div class="card-body" style="color: white;">
                            <h5 class="card-title"><?php echo $top_selling_product['name']; ?></h5>
                            <p class="card-text"><?php echo $top_selling_product['total']; ?> units sold</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="btn-group">
                                    <a href="product_read_one.php?id=<?php echo $top_selling_product['id']; ?>" class="btn btn-sm btn-outline-secondary" style="color: white;">View</a>
                                </div>
                                <small class="text" style="color: white;">RM<?php echo $top_selling_product['price']; ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>