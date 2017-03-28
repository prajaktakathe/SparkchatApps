<?php
require_once '../includes/db.php'; // The mysql database connection script
$team=$_GET['team'];
$query="SELECT * FROM user_".$team." ORDER BY id";

$result = $mysqli->query($query) or die($mysqli->error.__LINE__);

$arr = array();
if($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		$arr[] = $row;
	}
}
echo $json_response = json_encode($arr);
?>
