<?php

$apiKey = "4dc20a34e8efccbcf851eb7d6cf3288d";
$cityId = "2758011";
$googleApiUrl = "http://api.openweathermap.org/data/2.5/weather?id=" . $cityId . "&lang=en&units=metric&APPID=" . $apiKey;

$ch = curl_init();

curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_URL, $googleApiUrl);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_VERBOSE, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);

curl_close($ch);
$data = json_decode($response);
$currentTime = time();

require_once ("includes/database.php");


//Check if Post isset, else do nothing
if (isset($_POST['submit'])) {
    //Postback with the data showed to the user, first retrieve data from 'Super global'
    $name = mysqli_escape_string($db, $_POST['naam']);
    $telnr = mysqli_escape_string($db, $_POST['telefoonnummer']);
    $mail = mysqli_escape_string($db, $_POST['mail']);
    $datum = mysqli_escape_string($db, $_POST['datum']);
    $time = mysqli_escape_string($db, $_POST['tijd']);
    $personen = mysqli_escape_string($db, $_POST['personen']);
    $opmerkingen = mysqli_escape_string($db, $_POST['opmerkingen']);



    function getErrorsForFields($name, $telnr, $mail, $datum, $time, $personen, $opmerkingen) {
//Check if data is valid & generate error if not so
    $errors = [];
    if ($name == "") {
        $errors[] = 'Uw Naam cannot be empty';
    }
    if ($telnr == "") {
        $errors[] = 'Uw Telefoonnummer cannot be empty';
    }
    if ($mail == "") {
        $errors[] = ' Uw E-mail cannot be empty';
    }
    if ($datum == "") {
        $errors[] = 'dd-mm-jjjj cannot be empty';
    }
    if ($time == "") {
        $errors[] = 'Tijd cannot be empty';
    }
    if (!is_numeric($personen) || strlen($personen) != 1 || strlen($personen) != 2) {
        $errors[] = ' Aantal Personen needs to be a number with the length of 2';
    }
    if ($opmerkingen == "") {
        $errors[] = 'Opmerkingen cannot be empty';
    }
    return $errors;
    }
    $errors = getErrorsForFields($name, $telnr, $mail, $datum, $time, $personen, $opmerkingen);

    $hasErrors = !empty($errors);

    if (!$hasErrors) {
        insertIntoDatabase($name, $telnr, $mail, $datum, $time, $personen, $opmerkingen);
    }


//Save the reservering to the database
        $query = "INSERT INTO `reserveringssysteem` (`naam`,`telefoonnummer`, `mail`, `datum`, `tijd`, `personen`, `opmerkingen`)
                  VALUES ('$name', '$telnr', '$mail', '$datum', '$time', '$personen', '$opmerkingen')";
        $result = mysqli_query($db, $query)
        or die('Error: ' . $query);

        if ($result) {
          // header('Location: index.php');
            echo"Reservering gelukt!";

            $subject = "Wok 'n Sushi Reservering";
            $body = "Uw reservering is gelukt op $datum, $time, op de naam van $name, voor $personen personen!";
            $headers = [
                'From' => 'dannyhu2002@gmail.com'
            ];
            //for($i=0;$i<50;$i++){
                if (mail($mail, $subject, $body, $headers)) {
                    echo " Email successfully sent to $mail...";
                } else {
                    echo " Email sending failed...";
                }
            //}

//            exit;
        } else {
            $errors[] = 'Something went wrong in your database query: ' . mysqli_error($db);
        }

mysqli_close($db);
}
?>

<?php
require_once ("weather.php");
?>

<!doctype html>
<html>
<head>
    <title> &copy; Wok 'n Sushi Reserveringen</title>
    <meta charset="utf-8"/>
    <link rel="stylesheet" type="text/css" href="css/style.css"/>
</head>
<header>
    <a href="https://www.woknsushi-koperwiek.nl/" class="logo">
        <img src="//www.woknsushi-koperwiek.nl/wp-content/uploads/2017/11/logo.png" alt="" title="" class="img-responsive logo-header-transparent">
    </a>
    <a href= "https://www.woknsushi-koperwiek.nl/">Go to official website</a>
</header>
<body>
<h1>Tafel Reserveren</h1>

<form action="" method="post">
    <div class="data-field">
        <label for="Uw Naam">Uw Naam</label>
        <input id="Uw Naam" type="text" placeholder="Uw Naam" name="naam" value="<?= (isset($name) ? $name : ''); ?>" required/>
        <span><?= (isset($errors['Uw Naam']) ? $errors['Uw Naam'] : '') ?></span>
    </div>
    <div class="data-field">
        <label for="Uw Telefoonnummer">Uw Telefoonnummer</label>
        <input id="Uw Telefoonnummer" type="text" placeholder="Uw Telefoonnummer" name="telefoonnummer" value="<?= (isset($telnr) ? $telnr : ''); ?>" required/>
    </div>
    <div class="data-field">
        <label for="Uw Email">Uw E-mail</label>
        <input id="Uw Email" type="email" placeholder="Uw E-mail" name="mail" value="<?= (isset($mail) ? $mail : ''); ?>" required/>
    </div>
    <div class="data-field">
        <label for="dd-mm-jjjj">Datum</label>
        <input id="dd-mm-jjjj" type="date" placeholder="Datum" name="datum" value="<?= (isset($datum) ? $datum : ''); ?>" required/>
    </div>
    <div class="data-field">
        <label for="Tijd">Tijd</label>
        <input id="Tijd" type="time" placeholder="Tijd" name="tijd" value="<?= (isset($time) ? $time : ''); ?>" required/>
    </div>
    <div class="data-field">
        <label for="Aantal Personen">Aantal Personen</label>
        <input id="Aantal Personen" type="number" placeholder="Aantal Personen" name="personen" value="<?= (isset($personen) ? $personen : ''); ?>" required/>
    </div>
    <div class="data-field">
        <label for="Opmerkingen">Opmerkingen</label>
        <input id="Opmerkingen" type="text" placeholder="Opmerkingen" name="opmerkingen" value="<?= (isset($opmerkingen) ? $opmerkingen : ''); ?>"/>
    </div>
    <div class="data-submit">
        <input type="submit" name="submit" value="Save"/>
    </div>
</form>
<div>
    <a href="index.php">Ga terug naar Home page</a>
    <p> <a href="login.php">Login here</a>.</p>
</div>
</body>
</html>
