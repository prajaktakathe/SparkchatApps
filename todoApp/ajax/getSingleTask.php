<?php
require_once '../includes/db.php'; // The mysql database connection script
$team=$_GET['team'];
if(isset($_GET['taskId'])){
$task_id = $_GET['taskId'];
}
$query="SELECT * FROM task_".$team." WHERE task_id = '$task_id'";

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
