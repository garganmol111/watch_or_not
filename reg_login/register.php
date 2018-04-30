<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$page_title = 'Register';
$page_header_title = 'Register Page';
include './includes/preferences.php';
include HEADER;
if(isset($_SESSION['user_id'])){
    header('Location:'.BASE_URL);
    exit();
}
if(filter_input(INPUT_POST, 'register')) {
    require MYSQL;
    $fn = $ln = $un = $p = $e = FALSE;
    $errors = array();
    $safeData = array_map('trim', filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING));
    if(filter_var($safeData['email'], FILTER_VALIDATE_EMAIL)
            && strlen($safeData['email']) <= 80) {
        $e = $dbcl->real_escape_string($safeData['email']);
    }else{
        $errors['email'] = 'Please provide a valid email address!';
    }
    
    if(between($safeData['first_name'], 2, 20)) {
        $fn = $dbcl->real_escape_string($safeData['first_name']);
    }else{
        $errors['first_name'] = 'Please enter first name';
    }
    
    if(between($safeData['last_name'], 2, 40)) {
        $ln = $dbcl->real_escape_string($safeData['last_name']);
    }else{
        $errors['last_name'] = 'Please enter last name';
    }
    
    if(between($safeData['username'], 2, 40)) {
        $un = $dbcl->real_escape_string($safeData['username']);
    }else{
        $errors['username'] = 'Please enter username';
    }
    
    if(between($safeData['password'], 8, 20)) {
        if($safeData['password'] == $safeData['cpassword']) { 
            $p = $dbcl->real_escape_string($safeData['password']);
        }else{
            $errors['cpassword'] = 'Please make sure to enter the same password';
        }
    }else{
        $errors['password'] = 'Please enter password';
    }
    
    if($fn && $ln && $un && $p && $e) {
        $taken = FALSE;
        $query = "SELECT username, email FROM users"." WHERE username = ? || email = ?";
        $stmt = $dbcl->prepare($query);
        $stmt->bind_param('ss', $un, $e);
        $stmt->execute();
        $stmt->bind_result($cun, $ce);
        $stmt->fetch();
        if($un == $cun) {
            $taken = TRUE;
            $errors['username'] = 'Sorry, this username is already registered'
                    . ',if you forgot your password click here '
                    . '<a href="reset_password.php">Reset Password</a>';
        }
        if($e == $ce) {
            $taken = TRUE;
            $errors['email'] = 'Sorry, this email is already registered'
                    . ',if you forgot your password click here '
                    . '<a href="reset_password.php">Reset Password</a>';
        }
        
        if(!$taken) {
            $query = "INSERT INTO users"
                    . "(username, first_name, last_name, email, salt, active, pin, "
                    . "registration_date)"
                    . "VALUES"
                    . "(?,?,?,?,?,?,AES_ENCRYPT(?,?),UTC_TIMESTAMP())";
            $salt = substr(md5(uniqid(rand())),-20);
            $active = substr(sha1(uniqid(rand())), -32);
            $stmt = $dbcl->prepare($query);
            $stmt->bind_param("ssssssss", $un, $fn, $ln, $e, $salt, $active, $p, $salt);
            $stmt->execute();
            if($stmt->affected_rows == 1) {
                $body = "Thank you for registering at watchornot.com"
                        . "To activate your account, please click on this link:\n\n";
                $body .= BASE_URL.'activate.php?x='.urldecode($e).'&y='.$active;
                mail($e, 'Registration Confirmation', $body, 'From: '.ADMIN_EMAIL);
                $_SESSION['msg'] = '<p>Thank you for registering! '
                        . 'A confirmation email has been sent to your e-mail address. '
                        . 'Please click on the link in the email in order to activate your account.';
                $stmt->close();
                unset($stmt);
                $dbcl->close();
                unset($dbcl);
                header('Location:'.BASE_URL.'register.php');
                exit();
            }else{
                $general_msg = '<p>System error occured, '
                        . 'Your record coudn\'t be registered at this time, we apologize'
                        . ' for the inconvenience. '.$stmt->error;
            }
        } // already taken?
        $stmt->close();
        unset($stmt);
        $dbcl->close();
        unset($dbcl);
    }
  
}

?>
<fieldset>
    <legend>Register</legend>
    <?php if(isset($_SESSION['msg'])){
        echo $_SESSION['msg'];
        unset($_SESSION['msg']);
        session_destroy();
        setcookie(session_name(), '', time()-3600);
    }
    echo isset($general_msg)?$general_msg:''; ?>
    <form action="register.php" method="POST">
        <p>
            <label for="username">Username: </label>
            <input type="text" name="username" value="<?php
            echo isset($safeData['username'])?$safeData['username']:'';
            ?>" />
            <span>
                <?php echo isset($errors['username'])?$errors['username']:''; ?>
            </span>
        </p>
        <p>
            <label for="email">E-Mail: </label>
            <input type="text" name="email" value="<?php
            echo isset($safeData['email'])?$safeData['email']:'';
            ?>" />
            <span>
                <?php echo isset($errors['email'])?$errors['email']:''; ?>
            </span>
        </p>
        <p>
            <label for="first_name">First name: </label>
            <input type="text" name="first_name" value="<?php
            echo isset($safeData['first_name'])?$safeData['first_name']:'';
            ?>" />
            <span>
                <?php echo isset($errors['first_name'])?$errors['first_name']:''; ?>
            </span>
        </p>
        <p>
            <label for="last_name">Last name: </label>
            <input type="text" name="last_name" value="<?php
            echo isset($safeData['last_name'])?$safeData['last_name']:'';
            ?>" />
            <span>
                <?php echo isset($errors['last_name'])?$errors['last_name']:''; ?>
            </span>
        </p>
        <p>
            <label for="password">Password: </label>
            <input type="password" name="password" value="<?php
            echo isset($safeData['password'])?$safeData['password']:'';
            ?>" />
            <span>
                <?php echo isset($errors['password'])?$errors['password']:''; ?>
            </span>
        </p>
        <p>
            <label for="cpassword">Confirm Password: </label>
            <input type="password" name="cpassword" value="<?php
            echo isset($safeData['cpassword'])?$safeData['cpassword']:'';
            ?>" />
            <span>
                <?php echo isset($errors['cpassword'])?$errors['cpassword']:''; ?>
            </span>
        </p>
        <p>
            <input type="submit" name="register" value="Register" />
        </p>
    </form>
</fieldset>

<?php include FOOTER; ?>