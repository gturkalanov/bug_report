<?php
$file = fopen("pid.txt","r");
$pid = fgets($file);
$to      = 'g.turkalanov@gmail.com';
$subject = 'the subject';
$message = 'hello';
$headers = 'From: webmaster@example.com' . "\r\n" .
    'Reply-To: webmaster@example.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

while(1)
{
	if(!isRunning($pid)) {
		mail($to, $subject, $message, $headers); break;
	}
}
function isRunning($pid){
    try{
        $result = shell_exec(sprintf("ps %d", $pid));
        if( count(preg_split("/\n/", $result)) > 2){
            return true;
        }
    }catch(Exception $e){}

    return false;
}
?>