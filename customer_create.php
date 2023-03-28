<!DOCTYPE HTML>
<html>

<head>
    <title>Create Product</title>
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
                    <li class="nav-item active">
                        <a class="nav-link" href="customer_create.php">Create Customer</a>
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
        <div class="cp-page-header">
            <h1>Create Customer</h1>
        </div>

        <!-- html form to create product will be here -->
        <!-- PHP insert code will be here -->

        <?php
        //$_get (appear in url) and $_post (didnt appear in url) 是传送（隐形）
        if ($_POST) {
            // include database connection
            include 'config/database.php';
            try { //if insert wrong will go to catch

                // posted values
                $username = htmlspecialchars(strip_tags($_POST['username']));
                $Password = htmlspecialchars(strip_tags($_POST['Password']));
                $CPassword = htmlspecialchars(strip_tags($_POST['CPassword']));
                $fname = htmlspecialchars(strip_tags($_POST['fname']));
                $lname = htmlspecialchars(strip_tags($_POST['lname']));
                if (isset($_POST['gender'])) $gender = ($_POST['gender']);
                $dob = htmlspecialchars(strip_tags($_POST['dob']));
                if (isset($_POST['status'])) $status = ($_POST['status']);

                $alphabet = preg_match('/[a-zA-Z]/', $Password);
                $alphabet = preg_match('/[a-zA-Z]/', $username);
                $number = preg_match('/[0-9]/', $Password);
                $number = preg_match('/[0-9]/', $username);

                // check if any field is empty
                if (empty($username)) {
                    $username_error = "Please enter Username";
                }
                if (strlen($username) < 6) {
                    $username_error = "Username must be at least 6 characters";
                } elseif (!$alphabet) {
                    $username_error = "Username with alphabet only";
                } elseif ($number) {
                    $username_error = "Username with no number";
                }

                if (empty($Password)) {
                    $Password_error = "Please enter Password";
                } elseif (strlen($Password) < 8) {
                    $Password_error = "Password should be at least 8 characters in length";
                } elseif (!$alphabet) {
                    $Password_error = "Password must contain at least one letter";
                } elseif (!$number) {
                    $Password_error = "Password must contain at least one number";
                }
                if (empty($CPassword)) {
                    $CPassword_error = "Please enter Confirm Password";
                } elseif ($CPassword != $Password) {
                    $CPassword_error = "Confirm Password must same with Password";
                }
                if (empty($fname)) {
                    $fname_error = "Please enter First Name";
                }
                if (empty($lname)) {
                    $lname_error = "Please enter Last Name";
                }
                if (empty($gender)) {
                    $gender_error = "Please select gender";
                }
                if (empty($dob)) {
                    $dob_error = "Please enter Date of Birth";
                }

                if (empty($status)) {
                    $status_error = "Please select your Status";
                }


                // check if there are any errors
                if (!isset($username_error) && !isset($Password_error) && !isset($CPassword_error) && !isset($fname_error) && !isset($lname_error) && !isset($gender_error) && !isset($dob_error) && !isset($status_error)) {


                    // insert query
                    $query = "INSERT INTO customers SET username=:username, Password=:Password, fname=:fname, lname=:lname, gender=:gender, dob=:dob, register=:register, status=:status "; // info insert to blindParam

                    // prepare query for execution
                    $stmt = $con->prepare($query);

                    // bind the parameters
                    $stmt->bindParam(':username', $username);
                    $stmt->bindParam(':Password', $Password);
                    $stmt->bindParam(':fname', $fname);
                    $stmt->bindParam(':lname', $lname);
                    $stmt->bindParam(':gender', $gender);
                    $stmt->bindParam(':dob', $dob);
                    $stmt->bindParam(':status', $status);


                    // specify when this record was inserted to the database
                    $created = date('Y-m-d H:i:s');
                    $stmt->bindParam(':register', $created);

                    // Execute the query
                    if ($stmt->execute()) {
                        echo "<div class='alert alert-success'>Record was saved.</div>";
                        $username = "";
                        $Password = "";
                        $CPassword = "";
                        $fname = "";
                        $lname = "";
                        $gender = "";
                        $dob = "";
                        $status = "";
                    } else {
                        echo "<div class='alert alert-danger'>Unable to save record. </div>";
                    }
                } else {
                    echo "<div class='alert alert-danger'>Unable to save record. Please fill in all required fields.</div>";
                }
            }

            // show error
            catch (PDOException $exception) {
                die('ERROR: ' . $exception->getMessage());
            }
        }
        ?>



        <!-- html form here where the customer information will be entered -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <table class='table table-hover table-responsive table-bordered'>
                <tr>
                    <td>Username</td>
                    <td><input type="text" name='username' class="form-control" value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>" />
                        <?php if (isset($username_error)) { ?><span class="text-danger"><?php echo $username_error; ?></span><?php } ?></td>
                </tr>

                <tr>
                    <td>First Name</td>
                    <td><input type="text" name="fname" class="form-control" value="<?php echo isset($fname) ? htmlspecialchars($fname) : ''; ?>" />
                        <?php if (isset($fname_error)) { ?><span class="text-danger"><?php echo $fname_error; ?></span><?php } ?></td>
                </tr>

                <tr>
                    <td>Last Name</td>
                    <td><input type="text" name="lname" class="form-control" value="<?php echo isset($lname) ? htmlspecialchars($lname) : ''; ?>" />
                        <?php if (isset($lname_error)) { ?><span class="text-danger"><?php echo $lname_error; ?></span><?php } ?></td>
                </tr>

                <tr>
                    <td>Password</td>
                    <td><input type="password" name='Password' class="form-control" value="<?php echo isset($Password) ? htmlspecialchars($Password) : ''; ?>" />
                        <?php if (isset($Password_error)) { ?><span class="text-danger"><?php echo $Password_error; ?></span><?php } ?></td>
                </tr>

                <tr>
                    <td>Confirm Password</td>
                    <td><input type="password" name='CPassword' class="form-control" value="<?php echo isset($CPassword) ? htmlspecialchars($Password) : ''; ?>" />
                        <?php if (isset($CPassword_error)) { ?><span class="text-danger"><?php echo $CPassword_error; ?></span><?php } ?></td>
                </tr>

                <tr>
                    <td>Gender</td>
                    <td>
                        <input type="radio" name="gender" <?php if (isset($gender) && $gender == "female") echo "checked"; ?> value="female">Female
                        <input type="radio" name="gender" <?php if (isset($gender) && $gender == "male") echo "checked"; ?> value="male">Male

                        <?php if (isset($gender_error)) { ?><span class="text-danger"><?php echo "<br> $gender_error"; ?></span><?php } ?>
                    </td>
                </tr>

                <tr>
                    <td>Date of Birth</td>
                    <td><input type="date" name="dob" class="form-control" value="<?php echo isset($dob) ? htmlspecialchars($dob) : ''; ?>" />
                        <?php if (isset($dob_error)) { ?><span class="text-danger"><?php echo $dob_error; ?></span><?php } ?></td>
                </tr>

                <tr>
                    <td>Status</td>
                    <td>
                        <input type="radio" name="status" <?php if (isset($status) && $status == "active") echo "checked"; ?> value="active">Active
                        <input type="radio" name="status" <?php if (isset($status) && $status == "inactive") echo "checked"; ?> value="inactive">Inactive

                        <?php if (isset($status_error)) { ?><span class="text-danger"><?php echo "<br> $status_error"; ?></span><?php } ?>
                    </td>
                </tr>


                <tr>
                    <td></td>
                    <td>
                        <input type='submit' value='Save' class='btn btn-primary' />
                        <a href='index.php' class='btn btn-danger'>Back to read products</a>
                    </td>
                </tr>
            </table>
        </form>


    </div>
    <!-- end .container -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>

</html>