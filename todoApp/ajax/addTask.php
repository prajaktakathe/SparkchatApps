<?php
require_once '../includes/db.php'; // The mysql database connection script
if(isset($_GET['task'])&&isset($_GET['team'])){
$team=$_GET['team'];
$task_id = $_GET['task_id'];
$title = $_GET['task'];
$start = time();
$end = 9999999999;
$priority= 0;
$status= 0;
$reminder= 0;
$created_by= $_GET['assign_by'];
$last_modified= time();
$isSynced= true;


$query="INSERT INTO task_".$team."(task_id,title,startTimeStamp,endTimeStamp,isImportant,isCompleted,reminder,subTasks,created_by,assign_to,last_modified,isSynced)  VALUES ('$task_id', '$title','$start','$end','$priority','$status','$reminder','','$created_by','','$last_modified','$isSynced')";

$result = $mysqli->query($query) or die($mysqli->error.__LINE__);

$result = $mysqli->affected_rows;
echo $json_response = json_encode($result);
}
?>
