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
        function get_latest_order_summary($con)
        {
            $query = "SELECT orders.id AS order_id, customers.username AS username, orders.created, order_details.id AS order_details_id, SUM(order_details.quantity * products.price) AS purchase_amount
              FROM orders
              JOIN customers ON orders.customer_name = customers.username
              JOIN order_details ON orders.id = order_details.order_id
              JOIN products ON order_details.product_id = products.id
              GROUP BY orders.id, customers.username, orders.created, order_details.id
              ORDER BY orders.created DESC
              LIMIT 1";
            $stmt = $con->prepare($query);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        // get latest order summary
        $latest_order_summary = get_latest_order_summary($con);
    } catch (PDOException $exception) {
        die('ERROR: ' . $exception->getMessage());
    }
    ?>

    <div class="container" style="margin-top: 20px;">
        <div class="card" style="border-width: 3px; border-color:#496058; background-color: #496058;">
            <div class="card-header" style="color: white;">
                <h2>Latest Order</h2>
            </div>
            <div class="card-body" style="background-color: white">
                <div class="row">
                    <?php if ($latest_order_summary && is_array($latest_order_summary)) : ?>
                        <div class="col-md-4">
                            <div class="card-body" style="color: black;">
                                <h4 class="card-text" style="font-weight: bold; margin-bottom:17px;">Order ID: <span style="font-weight: lighter;"><?php echo $latest_order_summary['order_id']; ?></h4>
                                <p class="card-text" style="font-weight: bold; margin:0;">Customer Name: <span style="font-weight: lighter;"><?php echo $latest_order_summary['username']; ?></span></p>
                                <p class="card-text" style="font-weight: bold; margin:0;">Order Date: <span style="font-weight: lighter;"><?php echo $latest_order_summary['created']; ?></p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <p class="card-text" style="font-weight: bold; margin:0;">Purchase Amount: <span style="font-weight: lighter;">RM<?php echo $latest_order_summary['purchase_amount']; ?></p>
                                    <div class="btn-group">
                                        <a href="order_read_one.php?id=<?php echo $latest_order_summary['order_id']; ?>" class="btn btn-sm btn-outline-secondary" style="font-weight: bold; border-color:#496058;">View</a>
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