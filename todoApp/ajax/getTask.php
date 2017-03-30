<?php

require_once '../includes/db.php'; // The mysql database connection script
$team=$_GET['team'];
$uid=$_GET['uId'];

$query="SELECT * FROM task_".$team." WHERE created_by='$uid' OR assign_to LIKE '%".$uid."%' ORDER BY isCompleted,id desc";
$result = $mysqli->query($query) or die($mysqli->error.__LINE__);
$arr = array();
if($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		$arr[] = $row;
	}
}

# JSON-encode the response
echo $json_response = json_encode($arr);
?>
