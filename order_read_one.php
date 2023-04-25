<?php
session_start();
if (!isset($_SESSION['username'])) { // If the user is not logged in
    $_SESSION['not_authorized'] = "You are not authorized to access this page!";
    header('Location: login.php'); // Redirect to the login page
    exit;
}
?>

<!DOCTYPE HTML>
<html>

<head>
    <title>OrderDetails</title>
    <meta charset="utf-8">
    <!-- Latest compiled and minified Bootstrap CSS (Apply your Bootstrap here -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link href="style.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Pathway+Extreme&display=swap" rel="stylesheet">

</head>

<body>
    <?php include 'nav.php' ?>


    <!-- container -->
    <div class="container">
        <div class="page-header">
            <h1>Details</h1>
        </div>

        <!-- PHP read one record will be here -->
        <?php
        // get passed parameter value, in this case, the record ID
        // isset() is a PHP function used to verify if a value is there or not
        $id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Record ID not found.');

        //include database connection
        include 'config/database.php';

        // read current record's data
        try {
            // prepare select query
            $query = "SELECT o.id, o.customer_name, p.name, p.price, od.quantity, o.created 
            FROM orders o
            JOIN customers c ON o.customer_name = c.username
            JOIN order_details od ON o.id = od.order_id
            JOIN products p ON od.product_id = p.id
            WHERE o.id = ? LIMIT 0,11";

            $stmt = $con->prepare($query);

            // bind parameter value
            $stmt->bindParam(1, $id);

            // execute query
            $stmt->execute();

            // check if any rows are returned
            if ($stmt->rowCount() > 0) {

                // initialize total price
                $total_price = 0;

                // display table header
                echo "<table class='table table-hover table-responsive table-bordered'>";
                echo "<tr>";
                echo "<td><strong>Product</strong></td>";
                echo "<td><strong>Quantity</strong></td>";
                echo "<td><strong>Price</strong></td>";
                echo "<td><strong>Sub Price</strong></td>";
                echo "<td><strong>Order Date</strong></td>";
                echo "</tr>";

                $row_number = 0;
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    // extract row
                    extract($row);

                    // set font weight based on row number
                    $font_weight = ($row_number % 2 == 0) ? 'lighter' : 'bold';

                    // calculate total price for each order detail row
                    $total_price_row = $price * $quantity;

                    // add total price to total price for all rows
                    $total_price += $total_price_row;

                    echo "<tr style='font-weight: {$font_weight}'>";
                    echo "<td>" . htmlspecialchars($name, ENT_QUOTES) . "</td>";
                    echo "<td>" . htmlspecialchars($quantity, ENT_QUOTES) . "</td>";
                    echo "<td>" . htmlspecialchars($price, ENT_QUOTES) . "</td>";
                    echo "<td>" . htmlspecialchars($total_price_row, ENT_QUOTES) . "</td>";
                    echo "<td>" . htmlspecialchars($created, ENT_QUOTES) . "</td>";
                    echo "</tr>";

                    // increment row number
                    $row_number++;
                }
                echo "</table>";

                // display total price
                echo "<div style='text-align: right; margin-top: 15px;'><strong>Total Price: " . htmlspecialchars($total_price, ENT_QUOTES) . "</strong></div>";
            } else {
                // no rows returned for the given order_id
                echo "No orders found.";
            }
        }

        // show error
        catch (PDOException $exception) {
            die('ERROR: ' . $exception->getMessage());
        }
        ?>


        <tr>
            <td></td>
            <td>
                <a href='order_read.php' class='btn btn-danger'>Back to read orders</a>
            </td>
        </tr>
        </table>


    </div> <!-- end .container -->

</body>

</html>