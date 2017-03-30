<?php
require '../vendor/curl-master/src/CurlService.php';
$curlService = new CurlService();
require_once '../includes/db.php'; // The mysql database connection script
if(isset($_GET['subtaskId'])&&isset($_GET['subtaskTitle'])){
  print_r("ajax call");
$team=$_GET['team'];
$teamId=$_GET['teamId'];
$taskID=$_GET['taskId'];
$uId = $_GET['uId'];
$title = $_GET['title'];
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
$sql ="SELECT subTasks FROM task_".$team." WHERE task_id='$taskID'";
$resultSubTash = $mysqli->query($sql) or die($mysqli->error.__LINE__);

$subTasksArray=[];
$assignToArray=[];
if(!empty($_GET['assignTo'])){
    $assignToArray = json_encode($_GET['assignTo']);
}else {
  $assignToArray= NULL;
}
//print_r($subTasksArray);
if(count($resultSubTash) != 0){
  while($row = $resultSubTash->fetch_assoc()) {
    $subTasksArray = json_decode($row['subTasks'],true);
}
}
$subTasksArray[]=$myObj;
$subTasksArray=json_encode($subTasksArray);
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
$query="INSERT INTO task_".$team."(task_id)  VALUES ('$taskID') ON DUPLICATE KEY UPDATE subTasks ='$subTasksArray'";
$result = $mysqli->query($query) or die($mysqli->error.__LINE__);
$result = $mysqli->affected_rows;
echo $json_response = json_encode($result);
}
?>
