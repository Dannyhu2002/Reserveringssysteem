<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
require_once ("includes/database.php");
$sql = "SELECT * FROM reserveringssysteem";
$showresult = mysqli_query($db, $sql)
or die('Error: '.$sql);

//Loop through the result to create a custom array
$reserveringen = [];
while ($row = mysqli_fetch_assoc($showresult)) {
    $reserveringen[] = $row;
}
mysqli_close($db);


?>
<!doctype html>
<html lang="en">
<head>
    <title>Reserveringen</title>
    <meta charset="utf-8"/>
    <link rel="stylesheet" type="text/css" href="css/style.css"/>
</head>
<body>
<h1>Wok 'n Sushi Reserveringen</h1>
<a href="logout.php">Log out</a>

<table>
    <thead>
    <tr>
        <th>#</th>
        <th>Naam</th>
        <th>Telefoonnummer</th>
        <th>E-mail</th>
        <th>Datum</th>
        <th>Tijd</th>
        <th>Aantal Personen</th>
        <th>Opmerkingen</th>
        <th colspan="2"></th>
    </tr>
    </thead>
    <tfoot>
    <tr>
        <td colspan="9">&copy; Wok 'n Sushi Reserveringen</td>
    </tr>
    </tfoot>
    <tbody>
    <?php foreach ($reserveringen as $reservering) { ?>
        <tr>
            <td><?= $reservering['id'] ?></td>
            <td><?= $reservering['naam']; ?></td>
            <td><?= $reservering['telefoonnummer']; ?></td>
            <td><?= $reservering['mail']; ?></td>
            <td><?= $reservering['datum']; ?></td>
            <td><?= $reservering['tijd']; ?></td>
            <td><?= $reservering['personen']; ?></td>
            <td><?= $reservering['opmerkingen']; ?></td>
            <td><a href="edit.php?id=<?= $reservering['id'] ?>">Edit</a></td>
            <td><a href="delete.php?id=<?= $reservering['id'] ?>">Delete</a></td>
        </tr>
    <?php } ?>
    </tbody>
</table>
<div>
    <a href="overzicht.php">Refresh Reserveringslijst</a>
</div>
</body>
</html>