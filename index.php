<?php


//helpers
include ('helper/Debughelper.php');

// unsere Klassen einbinden
include('controller/controller.php');
include('model/XMLFileModel.php');
include('view/view.php');

// $_GET und $_POST zusammenfasen, $_COOKIE interessiert uns nicht.
$request = array_merge($_GET, $_POST);
// Controller erstellen
$controller = new Controller($request);
// Inhalt der Webanwendung ausgeben.
echo $controller->display();

?>