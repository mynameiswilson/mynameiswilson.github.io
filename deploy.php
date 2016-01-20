<?php 

if (isset($_GET['secret']) && hash("sha256",$_GET['secret']) == '766cf657353421f70937fd1cbf22412d92997d02a15bb755c1a21090776de189') {
    // @todo integrate a sanity check against this:
    //       https://help.github.com/articles/what-ip-addresses-does-github-use-that-i-should-whitelist/
    `git pull origin master`;  
} else {
  die('Your secret was wrong!');
}
?>