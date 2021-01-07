<?php

class Admin extends DatabaseObject {

  protected static $table_name = 'admins';
  protected static $columns = ['id', 'first_name', 'last_name', 'email', 'username', 'hashed_password'];

  public $id;
  public $first_name;
  public $last_name;
  public $email;
  public $username;
  public $password;
  public $confirm_password;
  protected $hashed_password;
  protected $password_required = true;

  public function __construct($args=[]) {
    $this->first_name = $args['first_name'] ?? '';
    $this->last_name = $args['last_name'] ?? '';
    $this->email = $args['email'] ?? '';
    $this->username = $args['username'] ?? '';
    $this->password = $args['password'] ?? '';
    $this->confirm_password = $args['confirm_password'] ?? '';
  }

  public function name() {
    return "{$this->first_name} {$this->last_name}";
  }

  public function verify_password($password) {
    return password_verify($password, $this->hashed_password);
  }

  protected function set_hashed_password() {
    $this->hashed_password = password_hash($this->password, PASSWORD_BCRYPT);
  }

  protected function create() {
    $this->set_hashed_password();
    return parent::create();
  }

  protected function update() {
    // If both password and confirm_password were blank,
    // assume user has chosen not to update their password
    // so skip hashing and validation
    if(is_blank($this->password) && is_blank($this->confirm_password)) {
      $this->password_required = false;
    } else {
      $this->set_hashed_password();
    }
    return parent::update();
  }

  public function validate() {
    $this->errors = [];

    // first_name
    if(is_blank($this->first_name)) {
        $this->errors[] = "First name cannot be blank.";
    } elseif(!has_length($this->first_name, ['min' => 2, 'max' => 255])) {
        $this->errors[] = "First name must be between 2 and 255 characters.";
    }

    // last_name
    if(is_blank($this->last_name)) {
        $this->errors[] = "Last name cannot be blank.";
    } elseif(!has_length($this->last_name, ['min' => 2, 'max' => 255])) {
        $this->errors[] = "Last name must be between 2 and 255 characters.";
    }

    // email
    if(is_blank($this->email)) {
        $this->errors[] = "Email cannot be blank.";
    } elseif(!has_length($this->email, ['max' => 255])) {
        $this->errors[] = "Email must be less than 255 characters.";
    } elseif(!has_valid_email_format($this->email)) {
        $this->errors[] = "Email must be valid format (i.e. 'nobody@nowhere.com').";
    } 

    // username
    if(is_blank($this->username)) {
        $this->errors[] = "Username cannot be blank.";
    } elseif(!has_length($this->username, ['min' => 8, 'max' => 255])) {
        $this->errors[] = "Username must be between 8 and 255 characters.";
    } elseif(!has_unique_username($this->username, $this->id ?? '0')) {
        $this->errors[] = "Username must be unique.";
    }

    // password
    if($this->password_required) {
      if(is_blank($this->password)) {
          $this->errors[] = "Password cannot be blank.";
      } elseif(!has_length($this->password, ['min' => 12])) {
          $this->errors[] = "Password must be 12 or more characters.";
      } elseif(!is_valid_password($this->password)) {
          $this->errors[] = "Password must contain at least 1 uppercase letter, 1 lowercase letter, 1 number, and 1 symbol.";
      }

      if(is_blank($this->confirm_password)) {
          $this->errors[] = "Confirm password cannot be blank.";
      } elseif($this->password !== $this->confirm_password) {
          $this->errors[] = "Confirm password must match password.";
      }
    }

    return $this->errors;

  }

  public static function find_by_username($username) {
    $sql = "SELECT * FROM " . static::$table_name . " ";
    $sql .= "WHERE username='" . self::$db->escape_string($username) . "'";
    $object_array = self::find_by_sql($sql);

    if(!empty($object_array)) {
      return array_shift($object_array);
    } else {
      return false;
    }
  }

}

?>
