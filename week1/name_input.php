<html>

<head>
  <title>PHP Simple Form Processing</title>
</head>

<body>
  <?php
  if (isset($_POST['school_name']) && isset($_POST['gender']) && isset($_POST['year'])) {

    if (empty($_POST['school_name']) || empty($_POST['gender'])) {
      echo '<p>Form incomplete</p>';
    } else {
      echo '<p>Your school is <b>' . $_POST['school_name'] . '</b> and gender is <b>' . $_POST['gender'] . '</b></p>';
      echo '<p>Your year is <b> ' . $_POST['year'] . '</b></p>' ;
    }
  }
  ?>
  <form method="post" action="name_input.php">
    <lable>School Name</lable>
    <input size="20" name="school_name" type="text" />

    <lable>Gender</lable>
    <label for='male'>male</label>
    <input type="radio" id="male" name="gender" value="male">
    <label for='female'>female</label>
    <input type="radio" id="female" name="gender" value="female">
    <label for='other'>other</label>
    <input type="radio" id="other" name="gender" value="other">

    <label>School year</label>
    <select name="year" id="year">
      <option value="first">1</option>
      <option value="second">2</option>
      <option value="third">3</option>
      <option value="fourth">4</option>
      <option value="fifth and more">5+</option>
    </select>
    <input type="submit" value="submit" />
  </form>
</body>

</html>