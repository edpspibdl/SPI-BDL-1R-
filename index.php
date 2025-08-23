<?php
session_start();
if(isset($_SESSION['login'])){
  header('Location: landingPage/index.php');
}else{
  header('Location: ./login.php');
}