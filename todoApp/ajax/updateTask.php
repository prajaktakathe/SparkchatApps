<?php
require_once '../includes/db.php'; // The mysql database connection script
// if(isset($_GET['taskID'])){
if(isset($_GET['taskId'])&&isset($_GET['team'])){
  print_r("update");
  $team=$_GET['team'];
  $taskID = $_GET['taskId'];
  $title = $_GET['title'];
  $description = $_GET['description'];
  $duedate = $_GET['endTimeStamp'];
  //$duedate =$duedate/1000;
  $reminder = $_GET['reminder'];
  $subTasksArray=[];
  $assignToArray=[];
  if(!empty($_GET['subTasks'])){
    $subTasksArray = json_encode($_GET['subTasks']);
  }else {
    $subTasksArray= NULL;
  }
  if(!empty($_GET['assignTo'])){
      $assignToArray = json_encode($_GET['assignTo']);
  }else {
    $assignToArray= NULL;
  }


  //print_r($assignToArray);
  $query="update task_".$team." set title='$title',description='$description',endTimeStamp='$duedate',reminder='$reminder',subTasks='$subTasksArray',assign_to='$assignToArray' where task_id='$taskID'";
  $result = $mysqli->query($query) or die($mysqli->error.__LINE__);

  $assignToArray = json_decode($assignToArray,true);
  if(!empty($assignToArray)){
    print_r($assignToArray);
    $length = count($assignToArray);
    for($i=0; $i < $length;$i++){
        $user = ($assignToArray[$i]['id']);
        $query1="INSERT IGNORE INTO mapping_".$team."(task_id,user_name)  VALUES ('$taskID','$user')";
        $result = $mysqli->query($query1) or die($mysqli->error.__LINE__);
      }
  }



  $result = $mysqli->affected_rows;
  $json_response = json_encode($result);
}
if(isset($_GET['task_id'])&&isset($_GET['team'])&&isset($_GET['status'])){
  $team=$_GET['team'];
  $status = $_GET['status'];
  $taskID = $_GET['task_id'];
  $query="update task_".$team." set isCompleted='$status' where task_id='$taskID'";
  $result = $mysqli->query($query) or die($mysqli->error.__LINE__);
  $result = $mysqli->affected_rows;
  $json_response = json_encode($result);
}

if(isset($_GET['task_id'])&&isset($_GET['team'])&&isset($_GET['priority'])){
  print_r("hello");
  $team=$_GET['team'];
  $priority = $_GET['priority'];
  $taskID = $_GET['task_id'];
  $query="update task_".$team." set isImportant='$priority' where task_id='$taskID'";
  $result = $mysqli->query($query) or die($mysqli->error.__LINE__);
  $result = $mysqli->affected_rows;
  $json_response = json_encode($result);
}
?>
