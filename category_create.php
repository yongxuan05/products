<!DOCTYPE HTML>
<html>

<head>
    <title>Add Category</title>
    <meta charset="utf-8">
    <!-- Latest compiled and minified Bootstrap CSS (Apply your Bootstrap here -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link href="style.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@600&display=swap" rel="stylesheet">


</head>

<body>
    <?php include 'nav.php' ?>

    <!-- container -->
    <div class="container">
        <div class="cp-page-header">
            <h1>Add Category</h1>
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
                if (!isset($catname_error) && !isset($descr_error)) {
                    // specify when this record was inserted to the database     

                    // get all categories from database
                    $query = "INSERT INTO category SET catname=:catname, descr=:descr, created=:created"; // info insert to blindParam
                    $stmt = $con->prepare($query);

                    // bind the parameters
                    $stmt->bindParam(':catname', $catname);
                    $stmt->bindParam(':descr', $descr);

                    // specify when this record was inserted to the database
                    $created = date('Y-m-d H:i:s');
                    $stmt->bindParam(':created', $created);

                    // Execute the query
                    if ($stmt->execute()) {
                        echo "<div class='alert alert-success'>Record was saved.</div>";
                        $catname = "";
                        $descr = "";
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
                    <td>Category Name</td>
                    <td><input type='text' name='catname' class="form-control" value="<?php echo isset($catname) ? htmlspecialchars($catname) : ''; ?>" />
                        <?php if (isset($catname_error)) { ?><span class="text-danger"><?php echo $catname_error; ?></span><?php } ?></td>
                </tr>
                <tr>
                    <td>Description</td>
                    <td><textarea name='descr' class="form-control"><?php echo isset($descr) ? htmlspecialchars($descr) : ''; ?></textarea>
                        <?php if (isset($descr_error)) { ?><span class="text-danger"><?php echo  $descr_error; ?></span><?php } ?></td>
                </tr>
                <td></td>
                <td>
                    <input type='submit' value='Create' class='btn btn-primary' />
                    <a href='index.php' class='btn btn-danger'>Back to category</a>
                </td>
                </tr>
            </table>
        </form>


    </div>
    <!-- end .container -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>

</html>