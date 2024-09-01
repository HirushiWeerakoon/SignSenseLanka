<?php
session_start();
session_unset();
session_destroy();
header("Location: SignSence\sinhala_hand_sign_recognition-main\templates\index.html");
exit;
?>
