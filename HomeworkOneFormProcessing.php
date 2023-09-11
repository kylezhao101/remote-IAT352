<html>

<head>
    <title>IAT352 Homework 1</title>
</head>

<style>
    form{
        display:flex;
        gap: 5rem;
    }
    .formCol{
        display:flex;
        gap: 0.5rem;
        flex-direction: column;
    }
</style>

<body>
    <?php
    if (isset($_POST['school_name']) && isset($_POST['gender']) && isset($_POST['year'])) {

        if (empty($_POST['school_name']) || empty($_POST['gender'])) {
            echo '<p>Form incomplete</p>';
        } else {
            echo '<p>Your school is <b>' . $_POST['school_name'] . '</b> and gender is <b>' . $_POST['gender'] . '</b></p>';
            echo '<p>Your year is <b> ' . $_POST['year'] . '</b></p>';
        }
    }
    ?>
    <form method="post" action="HomeworkOneFormProcessing.php">
        <form>
            <div class='formCol'>
                <label>Type of Program</label>
                <select name='type' id='type'>
                    <option value='exchange'>Exchange</option>
                </select>

                <label>Country</label>
                <select name="country" id="country">

                </select>

                <label>Term</label>
                <select name="term" id="term">
                    <option value="Fall">Fall</option>
                    <option value="Spring">Spring</option>
                    <option value="Summer">Summer</option>
                </select>
            </div>
            <div class='formCol'>
                <label>Language of Instruction</label>
                <select name="language" id="language">

                </select>

                <label>Level of Study (at SFU)</label>
                <select name="level" id="level">
                    <option value="Undergraduate">Undergraduate</option>
                    <option value="Graduate">Graduate</option>
                    <option value="PhdOrPostDoctoral">Phd or Post-Doctoral</option>
                    <option value="PDP">PDP</option>
                </select>
            </div>
        </form>
        <input type="submit" value="apply" />
    </form>
</body>

</html>