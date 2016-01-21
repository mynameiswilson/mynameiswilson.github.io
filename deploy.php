<?php 

$github_lower_ip = ip2long('192.30.252.0');
$github_upper_ip = ip2long('192.30.255.255');

if (ip2long($_SERVER['REMOTE_ADDR']) >= $github_lower_ip && ip2long($_SERVER['REMOTE_ADDR']) <= $github_upper_ip) {
	if ( hash("sha256",$_GET['secret']) == '766cf657353421f70937fd1cbf22412d92997d02a15bb755c1a21090776de189') {
		$output = `git pull origin master`;  
		echo "Success";
} else {
		die('The secret you provided is incorrect');
	}
} else {
  die('This request did not come from Github\'s IP range');
}
?>
