<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$page_title = 'Activate';
$page_header_title = 'Activate page';

include './includes/preferences.php';
include HEADER;

if(isset($_SESSION['user_id'])) {
    header('Location:'.BASE_URL);
    exit();
}

$display_form = TRUE;

$x = filter_input(INPUT_GET, 'x', FILTER_VALIDATE_EMAIL);
$y = filter_input(INPUT_GET, 'y', FILTER_SANITIZE_STRING);
if($x && strlen($y) == 32) {
    require MYSQL;
    $email = $dbcl->real_escape_string($x);
    $query = "SELECT active FROM users"
            . " WHERE email = ? limit 1";
    $stmt = $dbcl->prepare($query);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    if($stmt->num_rows == 1) {
        $stmt->bind_result($active);
        $stmt->fetch();
        if($active == $y) {
            $query = "UPDATE users SET active = NULL WHERE email = ? limit 1";
            $stmt = $dbcl->prepare($query);
            $stmt->bind_param('s', $email);
            $stmt->execute();
            if($stmt->affected_rows == 1) {
                $msg = '<p>Your account is now active. You may now <a href="login.php">Log in.</a></p>';
                $display_form = FALSE; 
            }else{
                $msg = '<p>Your account culs not be activated.'
                        . 'Please re-check the link or contact the system administrator.</p>';
            }
        }else if($active == NULL) {
            $msg = '<p>Your account is already activated!</p>';
        }else{
            $msg = '<p>Your account culs not be activated.'
                        . 'Please re-check the link or contact the system administrator.</p>';
        }
    }else{
        $msg = '<p>Your account culs not be activated.'
                        . 'Please re-check the link or contact the system administrator.</p>';
    }
    $stmt->close();
    unset($stmt);
    $dbcl->close();
    unset($dbcl);
}//end of get request
else if(filter_input(INPUT_POST, 'active')){
    require MYSQL;
    
    function send($id, $dbcl) {
        $active = substr(sha1(uniqid(rand())), -32);
        $query = "UPDATE users SET active = ? "
                . "WHERE email = ? limit 1";
        $stmt = $dbcl->prepare($query);
        $stmt->bind_param('ss', $active, $id);
        $stmt->execute();
        if($stmt->affected_rows == 1) {
            $body = "To activate your accont, please click on this link:\n\n";
            $body .= BASE_URL.'activate.php?x='.urlencode($id).'&y='.$active;
            mail($id, 'Registration Confirmation', $body, 'From: '.ADMIN_EMAIL);
            return TRUE;
        }
        return FALSE;
    }
    function is_exist($id, $dbcl, $username) {
        $query = "SELECT email, active FROM users";
        if(!$username) {
            $query .= "WHERE email = ?";
        }else{
            $query .= "WHERE username = ?";
        }
        $query .= " limit 1";
        $stmt = $dbcl->prepare($query);
        $stmt->bind_param('s', $id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($email, $active);
        $stmt->fetch();
        if($stmt->num_rows == 1) {
            if($username) {
                return array(TRUE, $active, $email);
            }
            return array(TRUE, $active);
        }
        return FALSE;
    }
    function send_activation_link($id, $dbcl, $username) {
        $result = is_exist($id, $dbcl, $username);
        if($result[0]) {
            if($result[1]!=NULL) {
                $id = $username?$result[2]:$id;
                if(send($id, $dbcl)) {
                    $_SESSION['msg'] = '<p>A confirmation email has been sent to your e-mail address'
                            . 'Please click on the link in that eamil in order to activate your account'
                            . '</p>';
                    header('Location:'.BASE_URL.'activate.php');
                    exit();
                }else{
                    return '<p>System error occured, we apologoze for any inconvenience.</p>';
                }
            }else{
                return '<p>Your account is already activated!</p>';
            }
        }else{
            return '<p>Please try again. There is no record with this username/e-mail!<p>';
        }
    } 
    
    $id = $dbcl->real_escape_string(
            trim(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING)));
    
    if(filter_var($id, FILTER_VALIDATE_EMAIL) && strlen($id)<=80) {
        $msg = send_activation_link($id, $dbcl, FALSE);
    }else if(between($id, 5, 50)) {
        $msg = send_activation_link($id, $dbcl, TRUE);
    }else{
        $msg = '<p>Please enter a valid username/e-mail!</p>';
        $dbcl->close();
        unset($dbcl);
    }
}

if(isset($_SESSION['msg'])) {
    echo $_SESSION['msg'];
    unset($_SESSION['msg']);
    session_destroy();
    setcookie(session_name(), '', time()-3600);
}
echo isset($msg)?$msg:'';
if($display_form) {
?>

<form action="activate.php" method="POST">
    <p>
        <label for="id">E-mail/username: </label>
        <input type="text" name="id" value="<?php echo isset($id)?$id:''; ?>" />
    </p>
    <p>
        <input type="submit" name="activate" value="Send activation link" />
    </p>
</form>

<?php }

include FOOTER; ?>
