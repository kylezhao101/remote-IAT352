<html>
  <head>
    <title>PHP Simple Form Processing</title>
  </head>

  <body>
    <?php
      if(isset($_POST['first_name']) && isset($_POST['last_name'])) {

        if(empty($_POST['first_name']) || empty($_POST['last_name'])) {
          echo '<p>You did not enter first name or last name</p>';
        } else {
          echo '<p>Your first name is <b>' . $_POST['first_name'] . '</b> and last name is <b>' . $_POST['last_name'] . '</b></p>';
        }
      }
    ?>
    <form method="post" action="name_input.php">
      <lable>First Name:</lable>
      <input size="20" name="first_name" type="text"/>
      <lable>Last Name:</lable>
      <input size="20" name="last_name" type="text"/>
      <input type="submit" value="submit" />
    </form>
  </body>

</html>