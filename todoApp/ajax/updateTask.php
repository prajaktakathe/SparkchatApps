<?php
require_once '../includes/db.php'; // The mysql database connection script
// if(isset($_GET['taskID'])){
require '../vendor/curl-master/src/CurlService.php';
$curlService = new CurlService();
if(isset($_GET['taskId'])&&isset($_GET['team'])){
  print_r("update");
  $team=$_GET['team'];
  $teamId=$_GET['teamId'];
  $taskID = $_GET['taskId'];
  $uId = $_GET['uId'];
  $title = $_GET['title'];
  $description = $_GET['description'];
  $duedate = $_GET['endTimeStamp'];
  //$duedate =$duedate/1000;
  $reminder = $_GET['reminder'];
  $subTasksArray=[];
  $assignToArray=[];
  if(!empty($_GET['assignTo'])){
      $assignToArray = json_encode($_GET['assignTo']);
  }else {
    $assignToArray= NULL;
  }

  if(!empty($_GET['subTasks'])){
    $subTasksArray = json_encode($_GET['subTasks']);
  }else {
    $subTasksArray= NULL;
  }


  //print_r($assignToArray);
  $query="update task_".$team." set title='$title',description='$description',endTimeStamp='$duedate',reminder='$reminder',subTasks='$subTasksArray',assign_to='$assignToArray' where task_id='$taskID'";
  $result = $mysqli->query($query) or die($mysqli->error.__LINE__);
  $assignToArray=[];
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
  $json_response = json_encode($result);
}
if(isset($_GET['task_id'])&&isset($_GET['team'])&&isset($_GET['status'])){
  $team=$_GET['team'];
  $teamId=$_GET['teamId'];
  $status = $_GET['status'];
  $taskID = $_GET['task_id'];
  $title = $_GET['title'];
  $uId = $_GET['uId'];

  $sql ="SELECT assign_to FROM task_".$team." WHERE task_id='$taskID'";
  $resultassign = $mysqli->query($sql) or die($mysqli->error.__LINE__);
  $assignToArray=[];
  if(count($resultassign) != 0){
    while($row = $resultassign->fetch_assoc()) {
        print_r($row);
      $assignToArray = json_decode($row['assign_to'],true);
      print_r($assignToArray);
      $length = count($assignToArray);
      for($i=0; $i < $length;$i++){
          $user = ($assignToArray[$i]['id']);
          $response=$curlService->to('http://107.170.130.230:8443/plugins/sparkchat/api/v1/push/'.$teamId)
                               ->withHeader('authorization:'.$uId)
                               ->withData(array('appHandle'=>'todo','content'=>$title,'title'=>$title,'data'=>'{"edit":"true","taskId":'.$taskID.'}','toUid'=>$user))
                               ->returnResponseObject()
                               ->post();
          print_r($response);
        }
    }
  }
  $query="update task_".$team." set isCompleted='$status' where task_id='$taskID'";
  $result = $mysqli->query($query) or die($mysqli->error.__LINE__);
  $result = $mysqli->affected_rows;
  $json_response = json_encode($result);
}
if(isset($_GET['task_id'])&&isset($_GET['team'])&&isset($_GET['priority'])){
  print_r("hello");
  $team=$_GET['team'];
  $teamId=$_GET['teamId'];
  $priority = $_GET['priority'];
  $taskID = $_GET['task_id'];
  $title = $_GET['title'];
  $uId = $_GET['uId'];
  $sql ="SELECT assign_to FROM task_".$team." WHERE task_id='$taskID'";
  $resultassign = $mysqli->query($sql) or die($mysqli->error.__LINE__);
  $assignToArray=[];
  if(count($resultassign) != 0){
    while($row = $resultassign->fetch_assoc()) {
      $assignToArray = json_decode($row['assign_to'],true);
      print_r($assignToArray);
      $length = count($assignToArray);
      for($i=0; $i < $length;$i++){
          $user = ($assignToArray[$i]['id']);
          $response=$curlService->to('http://107.170.130.230:8443/plugins/sparkchat/api/v1/push/'.$teamId)
                               ->withHeader('authorization:'.$uId)
                               ->withData(array('appHandle'=>'todo','content'=>$title,'title'=>$title,'data'=>'{"edit":"true","taskId":'.$taskID.'}','toUid'=>$user))
                               ->returnResponseObject()
                               ->post();
          print_r($response);
        }
    }
  }
  $query="update task_".$team." set isImportant='$priority' where task_id='$taskID'";
  $result = $mysqli->query($query) or die($mysqli->error.__LINE__);
  $result = $mysqli->affected_rows;
  $json_response = json_encode($result);
}
?>
