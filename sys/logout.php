<?php
session_start();

if(isset($_SESSION['nicname'])) {
    session_destroy();
    unset($_SESSION);
    header("Location: index.php");
} else {
    header("Location: index.php");
}
?>