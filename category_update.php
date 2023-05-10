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
            <h1>Update Category</h1>
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
            $query = "SELECT * FROM category WHERE id = ? ";
            $stmt = $con->prepare($query);

            // this is the first question mark
            $stmt->bindParam(1, $id);

            // execute our query
            $stmt->execute();

            // store retrieved row to a variable
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // values to fill up our form
            $catname = $row['catname'];
            $descr = $row['descr'];
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
                $query = "UPDATE category SET catname=:catname, descr=:descr WHERE id = :id";

                // prepare query for excecution
                $stmt = $con->prepare($query);

                // posted values
                $catname = htmlspecialchars(strip_tags($_POST['catname']));
                $descr = htmlspecialchars(strip_tags($_POST['descr']));

                // check if any field is empty
                if (empty($catname)) {
                    $catname_error = "Please enter category name";
                }
                if (empty($descr)) {
                    $descr_error = "Please enter category description";
                }

                // check if there are any errors
                if (!isset($catname_error) && !isset($derc_error)) {



                    // bind the parameters
                    $stmt->bindParam(':catname', $catname);
                    $stmt->bindParam(':descr', $descr);
                    $stmt->bindParam(':id', $id);

                    // Execute the query
                    if ($stmt->execute()) {
                        echo "<div class='alert alert-success'>Record was updated.</div>";
                    } else {
                        echo "<div class='alert alert-danger'>Unable to update record. Please try again.</div>";
                    }
                }
            }
            // show errors
            catch (PDOException $exception) {
                die('ERROR: ' . $exception->getMessage());
            }
        } ?>


        <!--we have our html form here where new record information can be updated-->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id={$id}"); ?>" method="post">
            <table class='table table-hover table-responsive table-bordered'>
                <tr>
                    <td style="font-weight: bold;">Category Name</td>
                    <td><input type='text' name='catname' class="form-control" value="<?php echo isset($catname) ? htmlspecialchars($catname) : ''; ?>" />
                        <?php if (isset($catname_error)) { ?><span class="text-danger"><?php echo $catname_error; ?></span><?php } ?></td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Description</td>
                    <td><textarea name='descr' class="form-control"><?php echo isset($descr) ? htmlspecialchars($descr) : ''; ?></textarea>
                        <?php if (isset($descr_error)) { ?><span class="text-danger"><?php echo  $descr_error; ?></span><?php } ?></td>
                </tr>
                <td></td>


                <tr>
                    <td></td>
                    <td>
                        <input type='submit' value='Save Changes' class='btn btn-primary' />
                        <a href='product_read.php' class='btn btn-danger'>Back to read products</a>
                    </td>
                </tr>
            </table>
        </form>

    </div>
    <!-- end .container -->
</body>

</html>