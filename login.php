<!DOCTYPE HTML>
<html>

<head>
    <title>Login</title>
    <meta charset="utf-8">
    <!-- Latest compiled and minified Bootstrap CSS (Apply your Bootstrap here -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link href="style.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@600&display=swap" rel="stylesheet">


</head>

<body>
    <!-- container -->
    <div class="container">
        <div class="cp-page-header">
            <h1>Login</h1>
        </div>

        <?php
        if ($_POST) {
            // include database connection
            include 'config/database.php';
            try { //if insert wrong will go to catch

                // posted values
                $username = htmlspecialchars(strip_tags($_POST['username']));
                $Password = htmlspecialchars(strip_tags($_POST['password']));

                // check if any field is empty
                if (empty($username)) {
                    $username_error = "Please enter Username";
                }
                if (empty($Password)) {
                    $Password_error = "Please enter Password";
                } elseif ($username != "" && $Password != "") {
                    $login_error = "Your account and/or password is incorrect, please try again";
                }

                // check if there are any errors
                if (!isset($username_error) && !isset($Password_error) && !isset($login_error)) {


                    // insert query
                    $query = "INSERT INTO customers SET username=:username, Password=:Password"; // info insert to blindParam

                    // prepare query for execution
                    $stmt = $con->prepare($query);

                    // bind the parameters
                    $stmt->bindParam(':username', $username);
                    $stmt->bindParam(':Password', $Password);


                    // Execute the query
                    if ($stmt->execute()) {
                        echo "<div class='alert alert-success'>Login Successfull.</div>";
                        $username = "";
                        $Password = "";
                    } else {
                        echo "<div class='alert alert-danger'>Your username and/or password is incorrect, please try again.</div>";
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
                    <td>Username</td>
                    <td><input type="text" name='username' class="form-control" value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>" />
                        <?php if (isset($username_error)) { ?><span class="text-danger"><?php echo $username_error; ?></span><?php } ?></td>
                </tr>

                <tr>
                    <td>Password</td>
                    <td><input type="password" name="password" class="form-control" value="<?php echo isset($Password) ? htmlspecialchars($Password) : ''; ?>" />
                        <?php if (isset($Password_error)) { ?><span class="text-danger"><?php echo $Password_error; ?></span><?php } ?></td>
                </tr>

                <tr>
                    <td></td>
                    <td>
                        <input type='submit' value='Login' class='btn btn-primary' />
                    </td>
                </tr>
            </table>
        </form>


    </div>
    <!-- end .container -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>

</body>

</html>