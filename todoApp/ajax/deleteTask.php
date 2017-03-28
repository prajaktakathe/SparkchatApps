<?php
require_once '../includes/db.php'; // The mysql database connection script

if(isset($_GET['taskId'])){
  echo ("inside delete");
$taskID = $_GET['taskId'];
$team=$_GET['team'];

$query="DELETE FROM task_".$team." WHERE task_id='$taskID'";
$result = $mysqli->query($query) or die($mysqli->error.__LINE__);
$query1="DELETE FROM mapping_".$team." WHERE task_id='$taskID'";
$result = $mysqli->query($query1) or die($mysqli->error.__LINE__);
$result = $mysqli->affected_rows;
echo $json_response = json_encode($result);
}
?>
