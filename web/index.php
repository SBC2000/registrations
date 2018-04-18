<?php

function __autoload($class_name) {
    include "classes/$class_name.php";
}

// Constants
$year = 2018;
$fullPrice = 90;
$juniorPrice = 35;

// Lambda to pass to objects that need to calculate prices
$getTeamPrice = function($team) use ($fullPrice, $juniorPrice) {
  switch ($team->getType()) {
    case 'Heren':
    case 'Dames':
      return $fullPrice;
    case 'Jongens B':
    case 'Jongens C':
    case 'Meisjes B/C':
    case 'Gemengd D':
    case 'Gemengd E':
      return $juniorPrice;
    default:
      throw new Exception('Kan de prijs niet bepalen van een onbekend team type: ' . $team->getType());
  }
};

$dbconn = pg_connect(getenv("DATABASE_URL"))
  or die('Unable to connect to database: ' . pg_last_error());

$subscriptionManager = new SubscriptionManager($year, $getTeamPrice);

// handle post requests to this page
switch ($_POST["formname"]) {
  case "Confirmations":
    $subscriptionManager->handleConfirmations($_POST);

    // refresh the page
    header("Location: https://sbc2000-inschrijvingen.herokuapp.com/");
    break;
  case "Payments":
    $subscriptionManager->handlePayments($_POST);

    // refresh the page
    header("Location: https://sbc2000-inschrijvingen.herokuapp.com/");
    break;
}

$teamOverview = new TeamOverview($subscriptionManager, $getTeamPrice);

pg_close($dbconn);

?>

<html>
  <head>
    <meta name="viewport" content="user-scalable=0;"/>
    <title>Teamoverzicht</title>
    <link rel='stylesheet' type='text/css' href='css/font-awesome.min.css' />
    <link rel='stylesheet' type='text/css' href='css/style.css' />
  </head>
  <body>
    <div id="wrapper">
      <span class="export"><a href="export.php?year=<?php print($year) ?>">Exporteer CSV</a></span>

      <div id="subscribed">
        <div class="fit">
          <h1>Ingeschreven</h1>
          <?php if ($teamOverview->hasRegisteredSubscriptions()) : ?>
            <form method="POST">
              <?php print($teamOverview->getRegisteredSubscriptions()); ?>
              <input type="hidden" name="formname" value="Confirmations" />
              <input type="submit" value="Teams bevestigen/afwijzen" />
            </form>
          <?php else : ?>
            <p>Er zijn momenteel geen openstaande inschrijvingen.</p>
          <?php endif; ?>
        </div>
      </div>

      <div id="confirmed">
        <div class="fit">
          <h1>Bevestigd</h1>
          <?php if ($teamOverview->hasConfirmedSubscriptions()) : ?>
            <form method="POST">
              <?php print($teamOverview->getConfirmedSubscriptions()); ?>
              <input type="hidden" name="formname" value="Payments" />
              <input type="submit" value="Betalingen opslaan" />
            </form>
          <?php else : ?>
            <p>Er zijn momenteel geen openstaande inschrijvingen.</p>
          <?php endif; ?>
        </div>
      </div>

      <div id="paid">
        <div class="fit">
          <h1>Betaald</h1>
          <?php if ($teamOverview->hasPaidSubscriptions()) : ?>
            <?php print($teamOverview->getPaidSubscriptions($getTeamPrice)); ?>
          <?php else : ?>
            <p>Er zijn momenteel geen betaalde inschrijvingen.</p>
          <?php endif; ?>
        </div>
      </div>

      <div id="rejected">
        <div class="fit">
          <h1>Afgewezen</h1>
          <?php if ($teamOverview->hasRejectedSubscriptions()) : ?>
            <?php print($teamOverview->getRejectedSubscriptions()); ?>
          <?php else : ?>
            <p>Er zijn momenteel geen afgewezen inschrijvingen.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>

  </body>
</html>
