<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User registration</title>
    <link rel="stylesheet" type="text/css" href="/style.css">
</head>
<body>
<h1>User registration</h1>
<div class="container">
    <form name="registration" id="registration_form" method="post" action="/">
        <div class="field">
            <span class="label"><label for="email">Email</label></span>
            <input type="email" name="email" id="email" required value="<?php echo $user->getEmail();?>">
            <?php
            if ($user->getEmailError()):
            ?>
            <div class="error"><?php echo $user->getEmailError();?></div>
            <?php
            endif;
            ?>
        </div>
        <div class="field">
            <label for="name">Username</label>
            <input type="text" name="name" id="name" required value="<?php echo $user->getUsername();?>">
            <?php
            if ($user->getUsernameError()):
            ?>
            <div class="error"><?php echo $user->getUsernameError();?></div>
            <?php
            endif;
            ?>
        </div>
        <div class="field">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" min="6" max="32"
                   pattern="<?php echo trim(User::PATTERN_PASSWORD, '/'); ?>" required
                   title="<?php echo User::INSTRUCTION_PASSWORD ?>">
            <?php
            if ($user->getPasswordError()):
            ?>
            <div class="error"><?php echo $user->getPasswordError();?></div>
            <?php
            endif;
            ?>
        </div>
        <div class="field">
            <label for="password2">Repeat Password</label>
            <input type="password" name="password2" id="password2" min="6" max="32" required>
            <?php
            if ($user->getPasswordError()):
            ?>
            <div class="error"><?php echo $user->getPasswordError();?></div>
            <?php
            endif;
            ?>
        </div>
        <div class="submit">
            <input type="submit" value="Зарегистрировать">
        </div>
    </form>
</div>
</body>
<script>
    window.onload = function () {
        document.getElementById('password').onchange = doValidate;
        document.getElementById('password2').onchange = doValidate;
    };
    /**
     * Check is password is same as password repeat and set error message
     */
    var doValidate = function () {

        var password = document.getElementById('password');
        var password2 = document.getElementById('password2');

        if (password.value != password2.value) {
            password2.setCustomValidity('<?php echo User::INSTRUCTION_PASSWORD2?>');
        } else {
            password2.setCustomValidity('');
        }
    };
</script>
</html>