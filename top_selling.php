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
        function get_top_selling_products($con)
        {
            // fetch top selling products
            $query = "SELECT products.id, products.name, SUM(orders.quantity1 + orders.quantity2 + orders.quantity3) AS total 
            FROM orders 
            JOIN products ON orders.product1=products.name OR orders.product2=products.name OR orders.product3=products.name
            WHERE orders.created >= DATE(NOW()) - INTERVAL 7 DAY 
            GROUP BY products.id, products.name 
            ORDER BY total DESC 
            LIMIT 10";
            $stmt = $con->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // get top selling products
        $top_selling_products = get_top_selling_products($con);
    } catch (PDOException $exception) {
        die('ERROR: ' . $exception->getMessage());
    }
    ?>

    <?php if (!empty($top_selling_products)) { ?>
        <table class="table table-striped">
            <thead>
                <tr>

                    <th>Product Name</th>
                    <th>Total Quantity Sold</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($top_selling_products as $product) { ?>
                    <tr>
                        <td><?php echo $product['name']; ?></td>
                        <td><?php echo $product['total']; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <p>No top selling products found.</p>
    <?php } ?>
</body>

</html>