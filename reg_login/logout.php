<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();
if(isset($_SESSION['user_id'])){
    $_SESSION = array();
    session_destroy();
    setcookie(session_name(), '', time()-3600);
    header('Location:http://localhost/');
    exit();
}else{
    header('Location:http://localhost/');
    exit();
}

