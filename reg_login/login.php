<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$page_title = 'Login';
$page_header_title = 'Login Page';
include './includes/preferences.php';
include HEADER;
if(isset($_SESSION['user_id'])){
    header('Location:'.BASE_URL);
    exit();
}

if(filter_input(INPUT_POST, 'login')){
    require MYSQL;
    $errors = array();
    $safePOST = array_map('trim', filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING));
    $id = $dbcl->real_escape_string($safePOST['id']);
    $pass = $dbcl->real_escape_string($safePOST['password']);
    $valid = TRUE;
    $query = "SELECT AES_DECRYPT(pin,salt), active, user_level, user_id,"
            ." CONCAT_WS('', first_name, ' ', last_name) FROM users WHERE";
    if(filter_var($id, FILTER_VALIDATE_EMAIL) && strlen($id)<=80){
        $query .=" email = ? limit 1";
    }else if(between($id, 5, 50)) {
        $query .=" username = ? limit 1";
    }else{
        $errors['id'] = 'Please enter a valid username/e-mail address.';
        $valid = FALSE;
    }
    
    if(!between($pass, 8, 20)){
        $errors['password'] = 'Please enter a valid password.';
        $valid = FALSE;
    }
    
    if($valid){
        $stmt = $dbcl->prepare($query);
        $stmt->bind_param('s', $id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($realPass, $active, $user_level, $user_id, $full_name);
        $stmt->fetch();
        if($stmt->num_rows == 1){
            if($active==NULL){
                if($realPass == $pass){
                    $_SESSION = array(
                        'user_id' => $user_id,
                        'user_level' => $user_level,
                        'full_name' => $full_name
                    );
                    header('Location:'.BASE_URL);
                    exit();
                }else{
                    $msg = '<p>Incorrect credentials! please, try again.'
                            . 'if you forget your password click here:'
                            . '<a href="reset_password.php">Reset your password</a></p>';
                }
            }else{
                $msg = '<p>Ypur account is not active yet, please click here'
                            . 'to activate your account '
                            . '<a href="activate.php">Activate your account</a></p>';
            }
        }else{
            $msg = '<p>User does not exist!</p>';
        }
    }
}

?>

<fieldset>
    <legend>Login</legend>
    <?php echo isset($msg)?$msg:'';?>
    <form action="login.php" method="POST">
        <p>
            <label for="id">E-mail/username: </label>
            <input type="text" name="id" value="<?php echo isset($id)?$id:''; ?>" />
            <span>
                <?php echo isset($errors['id'])?$errors['id']:''; ?>
            </span>
        </p>
        <p>
            <label for="password">Password: </label>
            <input type="password" name="password" value="<?php echo isset($pass)?$pass:''; ?>" />
            <span>
                <?php echo isset($errors['password'])?$errors['password']:''; ?>
            </span>
        </p>
        <p>
            <input type="submit" name="login" value="Sign In" />
        </p>
    </form> 
</fieldset>
<?php include FOOTER; ?>