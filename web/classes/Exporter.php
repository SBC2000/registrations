<?php

class Exporter {
    private $year;

	 function __construct($year) {
        $this->year = $year;
    }

	public function getAllSubscriptionsWithTeams() {
		$query = "
		  SELECT *
		  FROM inschrijving AS I JOIN team AS T ON I.id = T.inschrijvingsId
		  WHERE jaar = '$this->year'
		";
		$result = pg_query($query) or die('Query failed: ' . pg_last_error());

		$header = array();
		for ($i = 0; $i < pg_num_fields($result); $i++) {
			$header[] = pg_field_name($result, $i);
		}
		$table = array($header);
		while ($row = pg_fetch_row($result)) {
			$table[] = $row;
		}
		pg_free_result($result);

		return $table;
	}
}
?>
