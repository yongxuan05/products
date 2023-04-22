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

    <div class="container">
        <h2>Latest Product</h2>
        <div class="row">
            <?php if (isset($product)) : ?>
                <div class="col-md-4">
                    <div class="card mb-4 box-shadow">
                        <div class="card-body" style="color: white;">
                            <h5 class="card-title"><?php echo $product['name']; ?></h5>
                            <p class="card-text"><?php echo $product['description']; ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="btn-group">
                                    <a href="product_read_one.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-secondary" style="color: white;">View</a>
                                </div>
                                <small class="text" style="color: white;">RM<?php echo $product['price']; ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>


</body>

</html>