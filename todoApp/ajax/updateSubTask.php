<?php
require_once '../includes/db.php'; // The mysql database connection script
// if(isset($_GET['taskID'])){
print_r("inside status");
if(isset($_GET['subtask_id']) && isset($_GET['status'])){
  print_r("status updated");
  $team=$_GET['team'];
  $status = $_GET['status'];
  $taskID = $_GET['taskId'];
  $subtaskID = $_GET['subtask_id'];
  $sql ="SELECT subTasks FROM task_".$team." WHERE task_id='$taskID'";
  $resultSubTash = $mysqli->query($sql) or die($mysqli->error.__LINE__);

  $subTasksArray=[];
  if(count($resultSubTash) != 0){
    while($row = $resultSubTash->fetch_assoc()) {
      $subTasksArray = json_decode($row['subTasks'],true);
      print_r($subTasksArray);
      $length = count($subTasksArray);
      for($i=0; $i < $length;$i++){
        if(($subTasksArray[$i]['subtaskId']) == $subtaskID ){
          $subTasksArray[$i]['subtaskStatus'] = $status;
        }
      }
    }
  }
  print_r($subTasksArray);
  $subTasksArray=json_encode($subTasksArray,true);
  $query="INSERT INTO task_".$team."(task_id)  VALUES ('$taskID') ON DUPLICATE KEY UPDATE subTasks ='$subTasksArray'";
  $result = $mysqli->query($query) or die($mysqli->error.__LINE__);
  $result = $mysqli->affected_rows;
  $json_response = json_encode($result,true);
}
if(isset($_GET['subtask_id']) && isset($_GET['priority'])){
  print_r("priority updated");
  $team=$_GET['team'];
  $priority = $_GET['priority'];
  $taskID = $_GET['taskId'];
  $subtaskID = $_GET['subtask_id'];
  $sql ="SELECT subTasks FROM task_".$team." WHERE task_id='$taskID'";
  $resultSubTash = $mysqli->query($sql) or die($mysqli->error.__LINE__);

  $subTasksArray=[];
  if(count($resultSubTash) != 0){
    while($row = $resultSubTash->fetch_assoc()) {
      $subTasksArray = json_decode($row['subTasks'],true);
      print_r($subTasksArray);
      $length = count($subTasksArray);
      for($i=0;$i < $length;$i++){
        if(($subTasksArray[$i]['subtaskId']) == $subtaskID ){
          $subTasksArray[$i]['subtaskPriority'] = $priority;
        }
      }
    }
  }
  print_r($subTasksArray);
  $subTasksArray=json_encode($subTasksArray,true);
  $query="INSERT INTO task_".$team."(task_id)  VALUES ('$taskID') ON DUPLICATE KEY UPDATE subTasks ='$subTasksArray'";
  $result = $mysqli->query($query) or die($mysqli->error.__LINE__);
  $result = $mysqli->affected_rows;
  $json_response = json_encode($result,true);
}
?>
