<?php
require_once '../includes/db.php'; // The mysql database connection script

if(isset($_GET['taskId']) && isset($_GET['userId'])){
$taskID = $_GET['taskId'];
$userId = $_GET['userId'];
$team=$_GET['team'];

$sql ="SELECT assign_to FROM task_".$team." WHERE task_id='$taskID'";
$resultSubTash = $mysqli->query($sql) or die($mysqli->error.__LINE__);

$assignToArray=[];
if(count($resultSubTash) != 0){
  while($row = $resultSubTash->fetch_assoc()) {
    $assignToArray = json_decode($row['assign_to'],true);
    //print_r($assignToArray);
    foreach ($assignToArray as $key => $value) {
      if(($assignToArray[$key]['id']) == $userId ){
         unset($assignToArray[$key]);
      }
    }
  }
}
print_r($assignToArray);
$assignToArray=json_encode($assignToArray,true);
$query="INSERT INTO task_".$team."(task_id)  VALUES ('$taskID') ON DUPLICATE KEY UPDATE assign_to ='$assignToArray'";
$result = $mysqli->query($query) or die($mysqli->error.__LINE__);

$query1="DELETE FROM mapping_".$team." WHERE task_id = '$taskID' AND user_name = '$userId'";
$result = $mysqli->query($query1) or die($mysqli->error.__LINE__);
$result = $mysqli->affected_rows;
echo $json_response = json_encode($result,true);
}
?>
