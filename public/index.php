<?php
/**
 * Created by PhpStorm.
 * User: Abadonna
 * Date: 04.05.2016
 * Time: 23:56
 */

require_once '../source/DB.php';
require_once '../source/AWException.php';
require_once '../source/User.php';

$user = new User();

if ( isset( $_POST['name'] ) ) {
    $user->setUsername( $_POST['name'] )
         ->setEmail( $_POST['email'] )
         ->setPassword( $_POST['password'] )
         ->setPasswordRepeat( $_POST['password2'] );
    try {
        $user->save();
        if ( $user->getId() ) {
            header( 'Location: /?success=true' );
            exit();
        }
    } catch ( AWNotFound $e ) {
        // do nothing
    } catch ( AWException $e ) {
        // some error is in posted data
    } catch ( Exception $e ) {
        exit( "Sorry, something wrong happen. " );
    }
} elseif ( ! empty( $_GET['success'] ) ) {
    include_once '../source/success.tpl';
    exit();
}

include_once '../source/index.tpl';