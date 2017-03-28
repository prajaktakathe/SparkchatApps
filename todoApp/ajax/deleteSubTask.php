<?php
require_once '../includes/db.php'; // The mysql database connection script
// if(isset($_GET['taskID'])){
if(isset($_GET['taskId'])&&isset($_GET['subtask_id'])){
  $team=$_GET['team'];
  $taskID = $_GET['taskId'];
  $subtaskID = $_GET['subtask_id'];
  $sql ="SELECT subTasks FROM task_".$team." WHERE task_id='$taskID'";
  $resultSubTash = $mysqli->query($sql) or die($mysqli->error.__LINE__);

  $subTasksArray=[];
  if(count($resultSubTash) != 0){
    while($row = $resultSubTash->fetch_assoc()) {
      $subTasksArray = json_decode($row['subTasks'],true);
      //for($i=0;$i <= $length;$i++){
        foreach ($subTasksArray as $key => $value) {
          if(($subTasksArray[$key]['subtaskId']) == $subtaskID ){
            //$subTasksArray[$i]['subtaskStatus'] = $status;
            print_r("hello");
             unset($subTasksArray[$key]);
          }
        }
    //  }
    }
  }
  print_r($subTasksArray);
  $subTasksArray=json_encode($subTasksArray);
  $query="INSERT INTO task_".$team."(task_id)  VALUES ('$taskID') ON DUPLICATE KEY UPDATE subTasks ='$subTasksArray'";
  $result = $mysqli->query($query) or die($mysqli->error.__LINE__);
  $result = $mysqli->affected_rows;
  $json_response = json_encode($result);
}
?>
