<?php
require_once '../includes/db.php';// The mysql database connection script
$myfile = fopen("testfile.txt", "w");
fwrite($myfile,$_POST["users"]);
if(isset($_POST["teamDomain"]) && isset($_POST["action"]) && isset($_POST["users"])){

switch ($_POST["action"]) {
  case 'add':{
      echo "TODO get info from data and create tables";

      $data=$_POST['teamDomain'];

      $userdata=json_decode($_POST['users'],true);
      $user = $userdata;
      $query="CREATE TABLE IF NOT EXISTS task_".$data." ( `id` INT(255) NOT NULL AUTO_INCREMENT , `task_id` VARCHAR(255) NOT NULL , `title` VARCHAR(255) NOT NULL , `description` VARCHAR(255) NOT NULL , `startTimeStamp` VARCHAR(255) NOT NULL , `endTimeStamp` VARCHAR(255) NOT NULL , `isImportant` INT(255) NOT NULL , `isCompleted` INT(255) NOT NULL , `reminder` INT(255) NOT NULL , `subTasks` TEXT NOT NULL , `created_by` VARCHAR(255) NOT NULL , `assign_to` TEXT NOT NULL , `last_modified` VARCHAR(255) NOT NULL , `isSynced` INT(255) NOT NULL , `task_body` TEXT NOT NULL , PRIMARY KEY (`task_id`), UNIQUE (`id`)) ENGINE = InnoDB";
      $result = $mysqli->query($query) or die($mysqli->error.__LINE__);

      $query1="CREATE TABLE IF NOT EXISTS user_".$data." ( `id` BIGINT(255) NOT NULL AUTO_INCREMENT , `creation_date` VARCHAR(255) NOT NULL , `user_name` VARCHAR(255) NOT NULL , `acl` TEXT NOT NULL , `status` INT(255) NOT NULL , `contact` VARCHAR(255) NOT NULL , `name` VARCHAR(255) NOT NULL , `role` INT(255) NOT NULL , PRIMARY KEY (`user_name`), UNIQUE (`id`)) ENGINE = InnoDB;";
      $result1 = $mysqli->query($query1) or die($mysqli->error.__LINE__);

      $query2="CREATE TABLE IF NOT EXISTS mapping_".$data."( `id` BIGINT(255) NOT NULL AUTO_INCREMENT , `task_id` VARCHAR(255) NOT NULL , `user_name` VARCHAR(255) NOT NULL , PRIMARY KEY (`task_id`, `user_name`), UNIQUE (`id`)) ENGINE = InnoDB;";
      $result2 = $mysqli->query($query2) or die($mysqli->error.__LINE__);

      foreach($user as $obj){
         $acl= (json_encode($obj['acl']));
         if(isset($obj['email'])){
           $query3="INSERT IGNORE INTO user_".$data."(creation_date, user_name, acl, status, contact, name, role) VALUES ('$obj[creation_date]', '$obj[user_name]', '$acl', '$obj[status]', '$obj[email]', '$obj[name]', '$obj[role]')";
         }elseif(isset($obj['phone'])){
           $query3="INSERT IGNORE INTO user_".$data."(creation_date, user_name, acl, status, contact, name, role) VALUES ('$obj[creation_date]', '$obj[user_name]', '$acl', '$obj[status]', '$obj[phone]', '$obj[name]', '$obj[role]')";
         }

         $result3 = $mysqli->query($query3) or die($mysqli->error.__LINE__);
      }
        print_r($result);
        print_r($result1);
        print_r($result2);
        print_r($result3);

    break;
}
case 'remove':{
  # code...
  break;
}
case 'message':{
  # code...
  break;
}
case 'notification':{
  # code...
  break;

}

   default:
    # code...
    break;
}
}
else{
echo "string";
}

 ?>
