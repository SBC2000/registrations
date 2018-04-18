<?php

function __autoload($class_name) {
    include "classes/$class_name.php";
}

$year = $_GET["year"];

if (!($year >= 2011 && $year <= 2050)) {
	die("Geef een geldig jaar op, bijvoorbeeld export.php?year=2020");
}

header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=inschrijvingen$year.csv");
// Disable caching
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

function outputCSV($data) {
    $output = fopen("php://output", "w");
    foreach ($data as $row) {
        fputcsv($output, $row); // here you can change delimiter/enclosure
    }
    fclose($output);
}

$dbconn = pg_connect(getenv("DATABASE_URL"))
    or die('Unable to connect to database: ' . pg_last_error());

$exporter = new Exporter($year);
outputCSV($exporter->getAllSubscriptionsWithTeams());

pg_close($dbconn);

?>
