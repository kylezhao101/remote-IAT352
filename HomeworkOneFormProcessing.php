<html>

<head>
    <title>IAT352 Homework 1</title>
</head>

<style>
    .formCol {
        display: flex;
        gap: 0.5rem;
        flex-direction: column;
    }
</style>

<body>
    <form method="post" action="HomeworkOneFormProcessing.php">
        <div class='formContainer'>
            <div class='formCol'>
                <label>Type of Program</label>
                <select name='type' id='type'>
                    <option value='' disabled selected>Select your country of choice</option>
                    <option value='exchange'>Exchange</option>
                </select>

                <label>Country</label>
                <select name="country" id="country">
                    <option value='' disabled selected>Select your country of choice</option>

                    <option value='Canada'>Canada</option>
                </select>

                <label>Term</label>
                <select name="term" id="term">
                    <option value='' disabled selected>Select your term</option>
                    <option value="Fall">Fall</option>
                    <option value="Spring">Spring</option>
                    <option value="Summer">Summer</option>
                </select>
            </div>
            <div class='formCol'>
                <label>Language of Instruction</label>
                <select name="language" id="language">
                    <option value='' disabled selected>Select your language of choice</option>

                    <option value='english'>English</option>
                </select>

                <label>Level of Study (at SFU)</label>
                <select name="level" id="level">
                    <option value="Undergraduate">Undergraduate</option>
                    <option value="Graduate">Graduate</option>
                    <option value="PhdOrPostDoctoral">Phd or Post-Doctoral</option>
                    <option value="PDP">PDP</option>
                </select>
            </div>
            <div>
                <input type="submit" value="apply" />
    </form>
    <?php
    if (isset($_POST['type']) && isset($_POST['term']) && isset($_POST['level'])) {

        if (empty($_POST['type']) || empty($_POST['term']) || empty($_POST['level'])) {
            echo '<p>Form incomplete</p>';
        } else {
            echo '<p>Your application is successfuly submitted. The following information is listed in the application</p>';
            echo '<ul>';
            echo '<li>Type of Program: ' . $_POST['type'] . '</li>';
            echo '<li>Country: ' . $_POST['country'] . '</li>';
            echo '<li>Term: ' . $_POST['term'] . '</li>';
            echo '<li>Language of Instruction: ' . $_POST['language'] . '</li>';
            echo '<li>Level of Study (at SFU): ' . $_POST['level'] . '</li>';
            echo '</ul>';
        }
    }
    ?>
</body>

</html>