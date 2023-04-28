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
    <link href="https://fonts.googleapis.com/css2?family=Pathway+Extreme&display=swap" rel="stylesheet">

</head>

<body>
    <?php
    // include database connection
    include 'config/database.php';
    try {
        function get_top_selling_product($con)
        {
            // fetch top selling product
            $query = "SELECT products.id, products.name, products.price, SUM(order_details.quantity) AS total 
            FROM order_details 
            JOIN products ON order_details.product_id = products.id
            JOIN orders ON orders.id = order_details.order_id
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

    <div class="container" style="margin-top: 20px;">
        <div class="card" style="border-width: 3px; border-color:#496058;">
            <div class="card-header" style="color: white;">
                <h2>Top Selling Product</h2>
            </div>
            <div class="card-body" style="background-color: white">
                <div class="row">
                    <?php if (isset($top_selling_product)) : ?>
                        <div class="col-md-4">
                            <div class="card-body" style="color: black;">
                                <h3 class="card-title" style="font-weight: bold; margin:0;"><?php echo ucfirst($top_selling_product['name']); ?></h3>
                                <p class="card-text"><?php echo $top_selling_product['total']; ?> units sold</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text">RM<?php echo $top_selling_product['price']; ?></small>
                                    <div class="btn-group">
                                        <a href="product_read_one.php?id=<?php echo $top_selling_product['id']; ?>" class="btn btn-sm btn-outline-secondary" style="font-weight: bold; border-color:#496058;">View</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</body>