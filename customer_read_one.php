<!DOCTYPE HTML>
<html>

<head>
    <title>Read Customer</title>
    <meta charset="utf-8">
    <!-- Latest compiled and minified Bootstrap CSS (Apply your Bootstrap here -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link href="style.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@600&display=swap" rel="stylesheet">


</head>

<body>
    <!-- navbar -->
    <nav class="custom-navbar navbar navbar navbar-expand-md navbar-dark bg-dark" arial-label="navigation bar">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.html">navbar<span>.</span></a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="custom-navbar-nav navbar-nav ms-auto mb-2 mb-md-0">
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="index.html">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="product_create.php">Create Product</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="product_read.php">Read Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="customer_create.php">Create Customer</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="customer_read.php">Read Customer</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.html">Contact Us</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- end Navbar -->

    <!-- container -->
    <div class="container">
        <div class="page-header">
            <h1>Read Customer</h1>
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
            $query = "SELECT id, username, fname, lname, gender, dob, register, status FROM customers WHERE id = ? LIMIT 0,1";
            $stmt = $con->prepare($query);

            // this is the first question mark
            $stmt->bindParam(1, $id);

            // execute our query
            $stmt->execute();

            // store retrieved row to a variable
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // values to fill up our form
            $username = $row['username'];
            $fname = $row['fname'];
            $lname = $row['lname'];
            $gender = $row['gender'];
            $dob = $row['dob'];
            $register = $row['register'];
            $status = $row['status'];
        }

        // show error
        catch (PDOException $exception) {
            die('ERROR: ' . $exception->getMessage());
        }
        ?>



        <!-- HTML read one record table will be here -->
        <!--we have our html table here where the record will be displayed-->
        <table class='table table-hover table-responsive table-bordered'>
            <tr>
                <td>Username</td>
                <td><?php echo htmlspecialchars($username, ENT_QUOTES);  ?></td>
            </tr>

            <tr>
                <td>First Name</td>
                <td><?php echo htmlspecialchars($fname, ENT_QUOTES);  ?></td>
            </tr>

            <tr>
                <td>Last Name</td>
                <td><?php echo htmlspecialchars($lname, ENT_QUOTES);  ?></td>
            </tr>

            <tr>
                <td>Gender</td>
                <td><?php echo htmlspecialchars($gender, ENT_QUOTES);  ?></td>
            </tr>

            <tr>
                <td>Date of birth</td>
                <td><?php echo htmlspecialchars($dob, ENT_QUOTES);  ?></td>
            </tr>

            <tr>
                <td>Register Date & Time</td>
                <td><?php echo htmlspecialchars($register, ENT_QUOTES);  ?></td>
            </tr>

            <tr>
                <td>Status</td>
                <td><?php echo htmlspecialchars($status, ENT_QUOTES);  ?></td>
            </tr>

            <tr>
                <td></td>
                <td>
                    <a href='customer_read.php' class='btn btn-danger'>Back to read products</a>
                </td>
            </tr>
        </table>


    </div> <!-- end .container -->

</body>

</html>