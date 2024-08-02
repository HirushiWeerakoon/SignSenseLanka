<?php
session_start();
session_unset();
session_destroy();
header("Location: /SignSence/Login.Register/index.html");
exit;
?>
