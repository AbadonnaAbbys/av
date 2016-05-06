<?php

require_once 'DB.php';

/**
 * Created by PhpStorm.
 * User: Abadonna
 * Date: 04.05.2016
 * Time: 23:56
 */
class User {

    const PATTERN_PASSWORD = '/[a-zA-Z\s\d]{6,32}/';

    const INSTRUCTION_PASSWORD = 'Password should be from 6 to 32 symbols, latin chars, spaces and digits only';

    const INSTRUCTION_PASSWORD2 = 'Second password entrance is not same as password';

    /**
     * @var integer
     */
    private $id;
    /**
     * @var string
     */
    private $username;
    /**
     * @var string
     */
    private $email;
    /**
     * @var string
     */
    private $password;
    /**
     * @var string
     */
    private $password2;
    /**
     * @var array
     */
    private $error;

    /**
     * User constructor.
     */
    public function __construct() {
        $this->username = '';
        $this->email    = '';
        $this->password = '';
        $this->error    = [ ];
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * @param string $username
     *
     * @return User
     */
    public function setUsername( $username ) {
        $this->username = trim( $username );

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return User
     */
    public function setEmail( $email ) {
        $this->email = trim( $email );

        return $this;
    }

    /**
     * @param string $password
     *
     * @return User
     */
    public function setPassword( $password ) {
        $this->password = $password;

        return $this;
    }

    /**
     * @param string $password2
     *
     * @return $this
     */
    public function setPasswordRepeat( $password2 ) {
        $this->password2 = $password2;

        return $this;
    }

    /**
     * If user ID is empty, create new record, update record otherwise
     * @return $this
     */
    public function save() {
        if ( $this->isValid() ) {
            if ( ! is_null( $this->id ) ) {
                $this->update();
            } else {
                $this->create();
            }
        }

        return $this;
    }

    /**
     * Return true if data in username, email and password fields is valid
     * @return bool
     */
    private function isValid() {
        $res = $this->isUsernameValid();
        $res &= $this->isEmailValid();
        $res &= $this->isPasswordValid();

        return $res;
    }

    /**
     * Return true if username is not empty and not registered yet, else return false
     * @return bool
     * @throws \Exception
     */
    private function isUsernameValid() {
        $res = true;
        if ( empty( $this->username ) ) {
            $res = false;
            $this->setUsernameError( 'Please, set User Name' );
        }
        if ( $res ) {
            try {
                User::getByUsername( $this->username );
                $res = false;
                $this->setUsernameError( 'User name "' . $this->username . '" is already exists.' );
            } catch ( AWNotFound $e ) {
                $res = true;
            }
        }

        return $res;
    }

    /**
     * @param string $message
     */
    private function setUsernameError( $message ) {
        $this->error['username'] = $message;
    }

    /**
     * If username is in database return User entity, throw exception otherwise
     * @param $username string
     *
     * @return \User
     * @throws \AWNotFound
     * @throws \Exception
     */
    private static function getByUsername( $username ) {
        $stmt = DB::getConnection()->prepare( 'SELECT `id`, `username`, `email` FROM `user` WHERE `username` = ?' );
        if ( $stmt ) {
            if ( $stmt->bind_param( 's', $username ) ) {
                if ( $stmt->execute() ) {
                    $user = new User();
                    if ( $stmt->bind_result( $user->id, $user->username, $user->email ) ) {
                        if ( $stmt->fetch() ) {
                            $stmt->close();

                            return $user;
                        } else {
                            throw new AWNotFound( 'User with Username "' . $username . '" is not found' );
                        }
                    }
                }
            }
        }

        throw new Exception();
    }

    /**
     * Return true if email address hase valid format and not registered yet, else return false
     * @return bool
     * @throws \Exception
     */
    private function isEmailValid() {
        $res = true;
        if ( empty( $this->email ) ) {
            $res = false;
            $this->setEmailError( 'Please, set Email address' );
        }
        if ( $res && ! filter_var( $this->email, FILTER_VALIDATE_EMAIL ) ) {
            $res = false;
            $this->setEmailError( 'Email address is invalid' );
        }
        if ( $res ) {
            try {
                User::getByEmail( $this->email );
                $res = false;
                $this->setEmailError( 'Email address "' . $this->email . '" is already exists.' );
            } catch ( AWNotFound $e ) {
                $res = true;
            }
        }

        return $res;
    }

    /**
     * @param string $message
     */
    private function setEmailError( $message ) {
        $this->error['email'] = $message;
    }

    /**
     * If email address is in database return User entity, throw exception otherwise
     * @param string $email
     *
     * @return \User
     * @throws \AWNotFound
     * @throws \Exception
     * @internal param string $username
     *
     */
    private static function getByEmail( $email ) {
        $stmt = DB::getConnection()->prepare( 'SELECT `id`, `username`, `email` FROM `user` WHERE `email` = ?' );
        if ( $stmt ) {
            if ( $stmt->bind_param( 's', $email ) ) {
                if ( $stmt->execute() ) {
                    $user = new User();
                    if ( $stmt->bind_result( $user->id, $user->username, $user->email ) ) {
                        if ( $stmt->fetch() ) {
                            $stmt->close();

                            return $user;
                        } else {
                            throw new AWNotFound( 'User with Email address "' . $email . '" is not found' );
                        }
                    }
                }
            }
        }

        throw new Exception();
    }

    /**
     * If password hase valid format and equal to another one return true, else return false
     * @return bool
     */
    public function isPasswordValid() {
        $res = true;

        if ( ! filter_var( $this->password, FILTER_VALIDATE_REGEXP, [ 'options' => [ 'regexp' => self::PATTERN_PASSWORD ] ] ) ) {
            $res = false;
            $this->setPasswordError( self::INSTRUCTION_PASSWORD );
        }

        if ( $res && $this->password != $this->password2 ) {
            $res = false;
            $this->setPassword2Error( self::INSTRUCTION_PASSWORD2 );
        }

        return $res;
    }

    /**
     * @param $message string
     */
    public function setPasswordError( $message ) {
        $this->error['password'] = $message;
    }

    /**
     * @param $message string
     */
    public function setPassword2Error($message) {
        $this->error['password2'] = $message;
    }

    /**
     * Update password for user
     * @return $this
     * @throws \Exception
     */
    private function update() {
        $stmt = DB::getConnection()->prepare( 'UPDATE `user` SET `password` = ? WHERE `id` = ?' );
        if ( $stmt ) {
            $password = md5($this->password);
            if ( $stmt->bind_param( 'si', $password, $this->id ) ) {
                if ( $stmt->execute() ) {
                    $stmt->close();

                    return $this;
                }
            }
        }

        throw new Exception();
    }

    /**
     * Store new user data in database
     * @return $this
     * @throws \Exception
     */
    private function create() {
        $stmt = DB::getConnection()->prepare( 'INSERT INTO `user` SET `username` = ?, `email` = ?, `password` = ?' );
        if ( $stmt ) {
            $password = md5( $this->password );
            if ( $stmt->bind_param( 'sss', $this->username, $this->email, $password ) ) {
                if ( $stmt->execute() ) {
                    $this->id = $stmt->insert_id;
                    var_dump( $stmt );
                    $stmt->close();

                    return $this;
                }
                var_dump( $stmt );

                throw new Exception( 'Execute error' );
            }
            throw new Exception( 'Bind params error' );

        }

        throw new Exception( 'Statement not create' );
    }

    /**
     * @return string
     */
    public function getEmailError() {
        return isset( $this->error['email'] ) ? $this->error['email'] : '';
    }

    /**
     * @return string
     */
    public function getUsernameError() {
        return isset( $this->error['username'] ) ? $this->error['username'] : '';
    }

    /**
     * @return string
     */
    public function getPasswordError() {
        return isset( $this->error['password'] ) ? $this->error['password'] : '';
    }

    /**
     * @return string
     */
    public function getPassword2Error() {
        return isset( $this->error['password2'] ) ? $this->error['password2'] : '';
    }
}