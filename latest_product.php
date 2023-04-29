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

    if (isset($_POST['id'])) {
        try {
            // fetch product with specified id from the database
            $query = "SELECT * FROM products WHERE id = ? LIMIT 0,1";
            $stmt = $con->prepare($query);
            $stmt->execute([$_POST['id']]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            die('ERROR: ' . $exception->getMessage());
        }
    } else {
        try {
            // fetch product with the largest id from the database
            $query = "SELECT * FROM products ORDER BY id DESC LIMIT 1";
            $stmt = $con->prepare($query);
            $stmt->execute();
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            die('ERROR: ' . $exception->getMessage());
        }
    }
    ?>

    <div class="container" style="margin-top: 20px;">
        <div class="card" style="border-width: 3px; border-color:#496058; background-color: #496058;">
            <div class="card-header" style="color: white;">
                <h2>Latest Product</h2>
            </div>
            <div class="card-body" style="background-color: white">
                <div class="row">
                    <?php if (isset($product)) : ?>
                        <div class="col-md-4">
                            <div class="card-body">
                                <h3 class="card-title" style="font-weight: bold; margin:0;"><?php echo ucfirst($product['name']); ?></h3>
                                <p class="card-text"><?php echo $product['description']; ?></p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text">RM<?php echo $product['price']; ?></small>
                                    <div class="btn-group">
                                        <a href="product_read_one.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-secondary" style="font-weight: bold; border-color:#496058;">View</a>
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

</html>