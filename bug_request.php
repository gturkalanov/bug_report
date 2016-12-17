<?php
	$fields = "&fields=issue type,key,summary,priority,created,description,status";
	$url = $_POST['jira_url']."/rest/api/2/search?jql=".$_POST['jira_query'].$fields;
	$url = str_replace(" ", "%20", $url);
	$credentials = $_POST['username'].":".$_POST["password"];
	$headers = array(
		"Authorization: Basic ". base64_encode($credentials),
		"Content-Type: application/json",
		"Accept: application/json"
	);

	function get_web_page($url,$headers){
	// Get cURL resource
	$curl = curl_init($url);
	// Set some options - we are passing in a useragent too here
	curl_setopt_array($curl, array(
	    CURLOPT_HTTPHEADER => $headers,
	    CURLOPT_URL => $url
	));
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	// Send the request & save response to $resp
	$content = curl_exec($curl);
	return $content;
	}

	$response = get_web_page($url,$headers);
	$json = json_decode($response);
	
	
	function generate_report($json)
	{
			$handle = fopen("bug_report.html","w") or die("Unable to open file!");
			$head = 
"
<html>
<head>
<style type='text/css'>
table {
    mso-displayed-decimal-separator:'\.';
    mso-displayed-thousand-separator:'\,'	;
}
body
{
    margin: 0px;
    font-size: 12px;
    font-family: Arial, sans-serif;
    color:black;
}
</style>
</head>";

$counter =0;
$issues = $json->issues;
foreach ($issues as $issue) {
	$issue_key = $issue->key;
	$tbody .="<tr id='issuerow'".$counter." rel=".$counter." data-issuekey=".$issue_key." class='issuerow'>";
	$tbody .="<td class='issuetype'>Bug</td>
	  <td class='issuekey'>
   				<a class='issue-link' data-issue-key=".$issue_key." href='https://ffwagency.atlassian.net/browse/".$issue_key."'>".$issue_key."</a>
      </td>";

	$fields = $issue->fields;
	foreach ($fields as $key => $value) {
		if($key=="summary")
		{
			$summary = $value;
			
		}
		if($key=="priority")
		{
			$priority = $value->name;
			
			
		}
		if ($key=="description")
		{
			$description = $value;
			
		}
		if($key=="status")
		{
			$status = $value->name;	
		}
		if($key=="created")
		{
			$created = $value;
			$created = date('d/m/Y g:i a', strtotime(substr($created,0,10)));
		}
		
	}
	$tbody .= "<td class='summary'>
         			   	<p>
        				".$summary."	
         				</p>
     				   </td>";
        $tbody .= "<td class='priority'>".$priority."</td>";
        $tbody .= "<td class='created'>".$created."</td>";
        $tbody .= "<td class='description'>".$description."</td>";
        $tbody .= "<td class='status'>".$status."</td>";
	$counter++;
}
$body = "
<body>
<table border='1'>
    <tbody><tr height='30' bgcolor='#205081'>
        <td colspan='14'>
            <img src='https://ffwagency.atlassian.net/jira-logo-scaled.png' alt='FFW JIRA'
             width='108' height='30' border='0'>
        </td>
    </tr>
    <tr>
        <td colspan='14'>
            <a href='https://ffwagency.atlassian.net/issues/?jql=status+not+in+%28done%29+AND+reporter+%3D+currentuser%28%29+AND+type+%3D+bug+AND+priority+%3D+high'>FFW JIRA</a>
        </td>
    </tr>
    <tr>
        <td colspan='14'>
            Displaying <strong>".$json->total."</strong> issues at <strong>".date('d/m/Y g:i a')."</strong>.
        </td>
    </tr>
</tbody></table>

<table id='issuetable' width='100%' cellspacing='1' cellpadding='3' border='1'>
<thead>
   <tr class='rowHeader'>
      <th class='colHeaderLink headerrow-issuetype' data-id='issuetype'>
         Issue Type
      </th>
      <th class='colHeaderLink headerrow-issuekey' data-id='issuekey'>
         Key
      </th>
      <th class='colHeaderLink headerrow-summary' data-id='summary'>
         Summary
      </th>
      <th class='colHeaderLink headerrow-priority' data-id='priority'>
         Priority
      </th>
      <th class='colHeaderLink headerrow-created' data-id='created'>
         Created
      </th>
      <th class='colHeaderLink headerrow-description' data-id='description'>
         Description
      </th>
      <th class='colHeaderLink headerrow-status' data-id='status'>
         Status
      </th>
   </tr>
</thead>
<tbody>
".$tbody."
</tbody>
</body>
</html>
";
$html = $head.$body;

		    fwrite($handle,$html);
		 	fclose($handle);

		 	$file="bug_report.html";

if(isset($_POST['send_email']))
{
	mail_attachment($file);
}
if(!$file){ // file does not exist
    die('file not found');
} else {
    header("Cache-Control: public");
    header("Content-Description: File Transfer");
    header("Content-Disposition: attachment; filename=$file");
    header("Content-Type: application/html");
    header("Content-Transfer-Encoding: binary");

    // read the file from disk
    readfile($file);
}
		 	echo "<h1>Report successfully generated</h1>";
	}
generate_report($json);


/*

Under masive development

 function mail_attachment($file) {
  // $file should include path and filename
  $filename = basename($file);
  $file_size = filesize($file);
  $to = $_POST['email_address'];
  $subject = 'Bug Report';
  $message = $_POST['message'];
  $from = "boga_na_reportite@gmail.com";
  $send = mail($to, $subject, $file);
  if($send)
  {
  	var_dump("YES");
  }
  else var_dump("NO");
 }
*/
	
?>