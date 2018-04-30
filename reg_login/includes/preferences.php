<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define('BASE_URL', 'http://localhost/reg_login/');
define('MYSQL', './includes/mysql_connection_link.php');
define('HEADER', './includes/header.php');
define('FOOTER', './includes/footer.php');
define('ADMIN_EMAIL', 'info@watchornot.com');

function between($val, $x, $y) {
    $val_len = strlen($val);
    return ($val_len >= $x && $val_len <= $y)?TRUE:FALSE;
}