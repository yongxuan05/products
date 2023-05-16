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
    <title>Add Customer</title>
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
    <div class="container" style="margin-top: 90px;">
        <div class="cp-page-header">
            <h1>Add Customer</h1>
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
                $Password = $_POST['Password'];
                $CPassword = $_POST['CPassword'];
                $fname = htmlspecialchars(strip_tags($_POST['fname']));
                $lname = htmlspecialchars(strip_tags($_POST['lname']));
                if (isset($_POST['gender'])) $gender = ($_POST['gender']);
                //isset是要看箱子有没有，没有存在就不会交上去（error)，有的话才把选择放进去$gender里面交上去
                $dob = htmlspecialchars(strip_tags($_POST['dob']));
                if (isset($_POST['status'])) $status = ($_POST['status']);

                $alphabet = preg_match('/[a-zA-Z]/', $Password);
                $u_alphabet = preg_match('/[a-zA-Z]/', $username);
                $number = preg_match('/[0-9]/', $Password);
                $u_number = preg_match('/[0-9]/', $username);

                // check if any field is empty
                if (empty($username)) {
                    $username_error = "Please enter Username";
                }
                if (strlen($username) < 6) {
                    $username_error = "Username must be at least 6 characters";
                } elseif (!$u_alphabet) {
                    $username_error = "Username with alphabet only";
                } elseif ($u_number) {
                    $username_error = "Username  no number";
                }

                if (empty($Password)) {
                    $Password_error = "Please enter Password";
                } elseif (strlen($Password) < 8) {
                    $Password_error = "Password should be at least 8 characters in length";
                } elseif (!$alphabet) {
                    $Password_error = "Password must contain at least one letter";
                } elseif (!$number) {
                    $Password_error = "Password must contain at least one number";
                } elseif (empty($CPassword)) {
                    $CPassword_error = "Please enter Confirm Password";
                } elseif ($CPassword != $Password) {
                    $CPassword_error = "Confirm Password must same with Password";
                } else {
                    $Password = md5($Password);
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
                } elseif (strtotime($dob) > time()) {
                    $dob_error = "Date of Birth cannot be in the future";
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
                        echo "<div class='alert alert-danger'>Unable to save record. Please fill in all required fields.</div>";
                    }
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
                    <td style="font-weight: bold;">Username</td>
                    <td><input type="text" name='username' class="form-control" value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>" />
                        <?php if (isset($username_error)) { ?><span class="text-danger"><?php echo $username_error; ?></span><?php } ?></td>
                </tr>

                <tr>
                    <td style="font-weight: bold;">First Name</td>
                    <td><input type="text" name="fname" class="form-control" value="<?php echo isset($fname) ? htmlspecialchars($fname) : ''; ?>" />
                        <?php if (isset($fname_error)) { ?><span class="text-danger"><?php echo $fname_error; ?></span><?php } ?></td>
                </tr>

                <tr>
                    <td style="font-weight: bold;">Last Name</td>
                    <td><input type="text" name="lname" class="form-control" value="<?php echo isset($lname) ? htmlspecialchars($lname) : ''; ?>" />
                        <?php if (isset($lname_error)) { ?><span class="text-danger"><?php echo $lname_error; ?></span><?php } ?></td>
                </tr>

                <tr>
                    <td style="font-weight: bold;">Password</td>
                    <td><input type="password" name='Password' class="form-control" value="<?php echo isset($Password) ? htmlspecialchars($Password) : ''; ?>" />
                        <?php if (isset($Password_error)) { ?><span class="text-danger"><?php echo $Password_error; ?></span><?php } ?></td>
                </tr>

                <tr>
                    <td style="font-weight: bold;">Confirm Password</td>
                    <td><input type="password" name='CPassword' class="form-control" value="<?php echo isset($CPassword) ? htmlspecialchars($Password) : ''; ?>" />
                        <?php if (isset($CPassword_error)) { ?><span class="text-danger"><?php echo $CPassword_error; ?></span><?php } ?></td>
                </tr>

                <tr>
                    <td style="font-weight: bold;">Gender</td>
                    <td>
                        <input type="radio" name="gender" <?php if (isset($gender) && $gender == "female") echo "checked"; ?> value="female">Female
                        <input type="radio" name="gender" <?php if (isset($gender) && $gender == "male") echo "checked"; ?> value="male">Male

                        <?php if (isset($gender_error)) { ?><span class="text-danger"><?php echo "<br> $gender_error"; ?></span><?php } ?>
                    </td>
                </tr>

                <tr>
                    <td style="font-weight: bold;">Date of Birth</td>
                    <td><input type="date" name="dob" class="form-control" value="<?php echo isset($dob) ? htmlspecialchars($dob) : ''; ?>" />
                        <?php if (isset($dob_error)) { ?><span class="text-danger"><?php echo $dob_error; ?></span><?php } ?></td>
                </tr>

                <tr>
                    <td style="font-weight: bold;">Status</td>
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
                        <a href='customer_read.php' class='btn btn-danger'>Back to read customers</a>
                    </td>
                </tr>
            </table>
        </form>


    </div>
    <!-- end .container -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>

</html>