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
    <title>Update</title>
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
        <div class="page-header">
            <h1>Update Customer</h1>
        </div>
        <!-- PHP read record by ID will be here -->
        <?php
        // get passed parameter value, in this case, the record ID
        // isset() is a PHP function used to verify if a value is there or not
        $id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Record ID not found.');

        //include database connection
        include 'config/database.php';

        // read current record's data
        try {

            // prepare select query
            $query = "SELECT * FROM customers WHERE id = ? ";
            $stmt = $con->prepare($query);

            // this is the first question mark
            $stmt->bindParam(1, $id);

            // execute our query
            $stmt->execute();

            // store retrieved row to a variable
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // values to fill up our form
            $username = $row['username'];
            $old_password = $row['Password'];
            $fname = $row['fname'];
            $lname = $row['lname'];
            $gender = $row['gender'];
            $dob = $row['dob'];
            $status = $row['status'];
        }

        // show error
        catch (PDOException $exception) {
            die('ERROR: ' . $exception->getMessage());
        }
        ?>

        <!-- HTML form to update record will be here -->
        <!-- PHP post to update record will be here -->
        <?php
        // check if form was submitted
        if ($_POST) {
            try {
                // write update query
                // in this case, it seemed like we have so many fields to pass and
                // it is better to label them and not use question marks
                $query = "UPDATE customers SET username=:username, Password=:new_password, fname=:fname, lname=:lname, gender=:gender, dob=:dob, `status`=:status WHERE id = :id";

                // prepare query for excecution
                $stmt = $con->prepare($query);

                // posted values
                $username = htmlspecialchars(strip_tags($_POST['username']));
                $Password = htmlspecialchars(strip_tags($_POST['Password']));
                $new_password = $_POST['new_password'];
                $confirm_new_password = $_POST['confirm_new_password'];
                $fname = htmlspecialchars(strip_tags($_POST['fname']));
                $lname = htmlspecialchars(strip_tags($_POST['lname']));
                if (isset($_POST['gender'])) $gender = ($_POST['gender']);
                //isset是要看箱子有没有，没有存在就不会交上去（error)，有的话才把选择放进去$gender里面交上去
                $dob = htmlspecialchars(strip_tags($_POST['dob']));
                if (isset($_POST['status'])) $status = ($_POST['status']);


                $alphabet = preg_match('/[a-zA-Z]/', $new_password);
                $u_alphabet = preg_match('/[a-zA-Z]/', $username);
                $number = preg_match('/[0-9]/', $new_password);
                $u_number = preg_match('/[0-9]/', $username);

                // check if any field is empty
                if (strlen($username) < 6) {
                    $username_error = "Username must be at least 6 characters";
                } elseif (!$u_alphabet) {
                    $username_error = "Username with alphabet only";
                } elseif ($u_number) {
                    $username_error = "Username  no number";
                }
                // check if oldpassword is same as password
                if ($Password != $_POST['Password']) {
                    $Opassword_error = "Old Password is incorrect";
                }
                // check if newpassword is same as confirm new password
                if (!empty($Password) && empty($new_password)) {
                    $Password_error = "Please enter New Password";
                }
                if (strlen($new_password) < 8) {
                    $Password_error = "Password should be at least 8 characters in length";
                } elseif (!$alphabet) {
                    $Password_error = "Password must contain at least one letter";
                } elseif (!$number) {
                    $Password_error = "Password must contain at least one number";
                } elseif ($new_password != $confirm_new_password) {
                    $CPassword_error = "Confirm New Password must same with New Password";
                } else {
                    $new_password = md5($new_password);
                }

                // check if there are any errors
                if (!isset($username_error) && !isset($Password_error) && !isset($CPassword_error)) {

                    // prepare query for execution
                    $stmt = $con->prepare($query);

                    // bind the parameters
                    $stmt->bindParam(':username', $username);
                    $stmt->bindParam(':new_password', $new_password);
                    $stmt->bindParam(':fname', $fname);
                    $stmt->bindParam(':lname', $lname);
                    $stmt->bindParam(':gender', $gender);
                    $stmt->bindParam(':dob', $dob);
                    $stmt->bindParam(':status', $status);
                    $stmt->bindParam(':id', $id);

                    // Execute the query
                    if ($stmt->execute()) {
                        echo "<div class='alert alert-success'>Record was saved.</div>";
                        $username = "";
                        $Password = "";
                        $new_password = "";
                        $confirm_new_password = "";
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
            // show errors
            catch (PDOException $exception) {
                die('ERROR: ' . $exception->getMessage());
            }
        }
        ?>


        <!--we have our html form here where new record information can be updated-->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id={$id}"); ?>" method="post">
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
                    <td style="font-weight: bold;">Old Password</td>
                    <td><input type="password" name="Password" class="form-control" value="<?php echo isset($Password) ? htmlspecialchars($Password) : ''; ?>" />
                        <?php if (isset($Opassword_error)) { ?><span class="text-danger"><?php echo $Opassword_error; ?></span><?php } ?>
                    </td>
                </tr>

                <tr>
                    <td style="font-weight: bold;">New Password</td>
                    <td><input type="password" name="new_password" class="form-control" value="<?php echo isset($new_password) ? htmlspecialchars($new_password) : ''; ?>" />
                        <?php if (isset($Password_error)) { ?><span class="text-danger"><?php echo $Password_error; ?></span><?php } ?></td>
                </tr>

                <tr>
                    <td style="font-weight: bold;">Confirm New Password</td>
                    <td><input type="password" name="confirm_new_password" class="form-control" value="<?php echo isset($confirm_new_password) ? htmlspecialchars($confirm_new_password) : ''; ?>" />
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
                        <input type='submit' value='Save Changes' class='btn btn-primary' />
                        <a href='customer_read.php' class='btn btn-danger'>Back to read customers</a>
                    </td>
                </tr>
            </table>
        </form>

    </div>
    <!-- end .container -->
</body>

</html>