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
    <title>Customer</title>
    <meta charset="utf-8">
    <!-- Latest compiled and minified Bootstrap CSS (Apply your Bootstrap here -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link href="style.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Pathway+Extreme:wght@300&display=swap" rel="stylesheet">


</head>

<body>
    <?php include 'nav.php' ?>


    <!-- container -->
    <div class="container" style="margin-top: 90px;">
        <div class=" page-header">
            <h1>Customers</h1>
        </div>

        <nav class="navbar bg-body-tertiary">
            <div class="container-fluid d-flex justify-content-between">
                <a href='customer_create.php' class='btn btn-primary m-b-1em'>Add New Customer</a>
                <form class="d-flex" role="search" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <input class="form-control me-2" type="search" name="find" placeholder="Search" aria-label="Search">
                    <button class="btn btn-outline-success " type="submit">Search</button>
                </form>
            </div>
        </nav>


        <!-- PHP code to read records will be here -->

        <?php
        // include database connection
        include 'config/database.php';


        // select all data
        $query = "SELECT * FROM customers ORDER BY id ASC";

        //search
        if ($_POST) {
            $search = htmlspecialchars(strip_tags($_POST['find']));
            $query = "SELECT * FROM `customers` WHERE username LIKE '%" . $search . "%' ";
        }

        // get total number of customer
        $total_query = "SELECT COUNT(*) as total FROM customers";
        $total_stmt = $con->prepare($total_query);
        $total_stmt->execute();
        $total = $total_stmt->fetch(PDO::FETCH_ASSOC)['total'];


        $stmt = $con->prepare($query);
        $stmt->execute();

        // this is how to get number of rows returned
        $num = $stmt->rowCount();

        // display total number of orders
        echo "<div class='row mt-3'>
         <div class='col-md-12'>
         <p style='text-align:right'>Total customers: " . $total . "</p>
         </div>
         </div>";

        //check if more than 0 record found
        if ($num > 0) {

            // data from database will be here
            echo "<table class='table table-hover table-responsive table-bordered'>"; //start table

            //creating our table heading
            echo "<tr>";
            echo "<th>ID</th>";
            echo "<th>Username</th>";
            echo "<th>Register Date & Time</th>";
            echo "<th>Status</th>";
            echo "<th>Action</th>";
            echo "</tr>";

            // retrieve our table contents
            $row_number = 0;
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // extract row
                // this will make $row['firstname'] to just $firstname only
                extract($row);

                // set font weight based on row number
                $font_weight = ($row_number % 2 == 0) ? 'lighter' : 'bold';

                // creating new table row per record
                echo "<tr style='font-weight: {$font_weight}'>";
                echo "<td>{$id}</td>";
                echo "<td>{$username}</td>";
                echo "<td>{$register}</td>";
                echo "<td>{$status}</td>";
                echo "<td>";

                // read one record
                echo "<a href='customer_read_one.php?id={$id}' class='btn btn-info m-r-1em' style='margin-right: 10px;'>Read</a>";

                // we will use this links on next part of this post
                echo "<a href='customer_update.php?id={$id}' class='btn btn-primary m-r-1em' style='margin-right: 10px;'>Edit</a>";

                // we will use this links on next part of this post
                echo "<a href='#' onclick='delete_user({$id});'  class='btn btn-danger' style='margin-right: 10px;'>Delete</a>";
                echo "</td>";
                echo "</tr>";

                // increment row number
                $row_number++;
            }


            // end table
            echo "</table>";
        }
        // if no records found
        else {
            echo "<div class='alert alert-danger'>No records found.</div>";
        }
        ?>



    </div> <!-- end .container -->

    <!-- confirm delete record will be here -->

</body>

</html>