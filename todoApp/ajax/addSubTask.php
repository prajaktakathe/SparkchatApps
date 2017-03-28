<?php
require_once '../includes/db.php'; // The mysql database connection script
if(isset($_GET['subtaskId'])&&isset($_GET['subtaskTitle'])){
  print_r("ajax call");
$team=$_GET['team'];
$taskId=$_GET['taskId'];
print_r($team);
$subtaskId = $_GET['subtaskId'];
$subtaskTitle = $_GET['subtaskTitle'];
$priority= 0;
$status= 0;

$myObj->subtaskId = $subtaskId;
$myObj->subtaskTitle = $subtaskTitle;
$myObj->subtaskStatus = $status;
$myObj->subtaskPriority = $priority;
//$myJSON = json_encode($myObj,true);
$sql ="SELECT subTasks FROM task_".$team." WHERE task_id='$taskId'";
$resultSubTash = $mysqli->query($sql) or die($mysqli->error.__LINE__);

$subTasksArray=[];
//print_r($subTasksArray);
if(count($resultSubTash) != 0){
  while($row = $resultSubTash->fetch_assoc()) {
    $subTasksArray = json_decode($row['subTasks'],true);
}
}
$subTasksArray[]=$myObj;
$subTasksArray=json_encode($subTasksArray);
$query="INSERT INTO task_".$team."(task_id)  VALUES ('$taskId') ON DUPLICATE KEY UPDATE subTasks ='$subTasksArray'";


$result = $mysqli->query($query) or die($mysqli->error.__LINE__);

$result = $mysqli->affected_rows;
echo $json_response = json_encode($result);
}
?>
