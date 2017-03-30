<?php
require_once '../includes/db.php'; // The mysql database connection script
require '../vendor/curl-master/src/CurlService.php';
$curlService = new CurlService();
print_r("inside status");
if(isset($_GET['subtask_id']) && isset($_GET['status'])){
  print_r("status updated");
  $team=$_GET['team'];
  $teamId=$_GET['teamId'];
  $status = $_GET['status'];
  $taskID = $_GET['taskId'];
  $uId = $_GET['uId'];
  $subtaskID = $_GET['subtask_id'];
  $assignToArray=[];
  if(!empty($_GET['assignTo'])){
      $assignToArray = json_encode($_GET['assignTo']);
  }else {
    $assignToArray= NULL;
  }
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
  $assignToArray = json_decode($assignToArray,true);
  if(!empty($assignToArray)){
  //  print_r($assignToArray);
    $length = count($assignToArray);
    for($i=0; $i < $length;$i++){
        $user = ($assignToArray[$i]['id']);
        $query1="INSERT IGNORE INTO mapping_".$team."(task_id,user_name)  VALUES ('$taskID','$user')";
        $result = $mysqli->query($query1) or die($mysqli->error.__LINE__);
        $response=$curlService->to('http://107.170.130.230:8443/plugins/sparkchat/api/v1/push/'.$teamId)
                             ->withHeader('authorization:'.$uId)
                             ->withData(array('appHandle'=>'todo','content'=>$title,'title'=>$title,'data'=>'{"edit":"true","taskId":'.$taskID.'}','toUid'=>$user))
                             ->returnResponseObject()
                             ->post();
        print_r($response);
      }
  }
  $result = $mysqli->affected_rows;
  $json_response = json_encode($result,true);
}

if(isset($_GET['subtask_id']) && isset($_GET['priority'])){
  print_r("priority updated");
  $team=$_GET['team'];
  $teamId=$_GET['teamId'];
  $priority = $_GET['priority'];
  $taskID = $_GET['taskId'];
  $uId = $_GET['uId'];
  $subtaskID = $_GET['subtask_id'];
  $assignToArray=[];
  if(!empty($_GET['assignTo'])){
      $assignToArray = json_encode($_GET['assignTo']);
  }else {
    $assignToArray= NULL;
  }
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
  $assignToArray = json_decode($assignToArray,true);
  if(!empty($assignToArray)){
  //  print_r($assignToArray);
    $length = count($assignToArray);
    for($i=0; $i < $length;$i++){
        $user = ($assignToArray[$i]['id']);
        $query1="INSERT IGNORE INTO mapping_".$team."(task_id,user_name)  VALUES ('$taskID','$user')";
        $result = $mysqli->query($query1) or die($mysqli->error.__LINE__);
        $response=$curlService->to('http://107.170.130.230:8443/plugins/sparkchat/api/v1/push/'.$teamId)
                             ->withHeader('authorization:'.$uId)
                             ->withData(array('appHandle'=>'todo','content'=>$title,'title'=>$title,'data'=>'{"edit":"true","taskId":'.$taskID.'}','toUid'=>$user))
                             ->returnResponseObject()
                             ->post();
        print_r($response);
      }
  }
  $result = $mysqli->affected_rows;
  $json_response = json_encode($result,true);
}
?>
