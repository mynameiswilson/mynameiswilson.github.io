<?php 

if (isset($_GET['secret']) && $_GET['secret'] == 'some-secret-key') {
    // @todo integrate a sanity check against this:
    //       https://help.github.com/articles/what-ip-addresses-does-github-use-that-i-should-whitelist/
    `git pull`;  
}