<?php
#." > /dev/null 2>/dev/null &"
ini_set("display_errors",1);
error_reporting(E_ALL);
exec('pwd',$current_folder);
$behat_folder = "cd /home/gtarkalanov/behat/qa";
$execute_command = "bin/behat -f html -o ";
$profile = $_POST['profile'];
$features = $_POST['folder'];
$behat_start_command = $behat_folder."&& nohup ".$execute_command.$current_folder[0]."/automation_report -p ".$profile." features/".$features;
$smth = sprintf("%s > %s 2>&1 & echo $! > %s",$behat_start_command, "output.txt", "pid.txt");
var_dump($smth);
exec($smth);
shell_exec('nohup php execute_test.php  > /dev/null 2>&1 &');
?>
