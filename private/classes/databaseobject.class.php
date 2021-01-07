<?php

class DatabaseObject {

  protected static $db;
  protected static $table_name = '';
  protected static $columns = [];
  public $errors = [];

  public static function set_database($db) {
    self::$db = $db;
  }

  public static function find_by_sql($sql) {
    $result = self::$db->query($sql);
    if(!$result) {
      exit("Database query failed.");
    }

    // Put results into objects
    $object_array = [];
    while($record = $result->fetch_assoc()) {
      $object_array[] = self::instantiate($record);
    }
    $result->free();

    return $object_array;
  }

  public static function find_all() {
    $sql = "SELECT * FROM " . static::$table_name;
    return self::find_by_sql($sql);
  }

  public static function find_by_id($id) {
    $sql = "SELECT * FROM " . static::$table_name . " ";
    $sql .= "WHERE id='" . self::$db->escape_string($id) . "'";
    $object_array = self::find_by_sql($sql);

    if(!empty($object_array)) {
      return array_shift($object_array);
    } else {
      return false;
    }
  }

  public static function instantiate($record) {
    $object = new static;
    foreach($record as $property => $value) {
      if(property_exists($object, $property)) {
        $object->$property = $value;
      }
    }
    return $object;
  }

  public function has_unique_field($key, $value) {
    if(property_exists($this, $key) && !is_null($value)) {
      $sql = "SELECT * FROM " . static::$table_name . " ";
      $sql .= "WHERE " . self::$db->escape_string($key) . " ='";
      $sql .= self::$db->escape_string($value). "' ";
      $sql .= "AND id!='" . self::$db->escape_string($this->id) . "'";
      $result = self::$db->query($sql);
      $count = $result->num_rows;
      $result->free();
      return $count === 0;
    } else {
      return false;
    }
  }

  public function validate() {
    $this->errors = [];

    // TODO custom validations

    return $this->errors;
  }

  protected function create() {
    $this->validate();
    if(!empty($this->errors)) { return false; }

    $attributes = $this->sanitized_attributes();

    $sql = "INSERT INTO " . static::$table_name . " (";
    $sql .= join(', ', array_keys($attributes));
    $sql .= ") VALUES ('";
    $sql .= join("', '", array_values($attributes));
    $sql .= "')";
    $result = self::$db->query($sql);

    if($result) {
      $this->id = self::$db->insert_id;
    }

    return $result; // Returns true or false
  }

  protected function update() {
    $this->validate();
    if(!empty($this->errors)) { return false; }

    $attributes = $this->sanitized_attributes();
    $attribute_pairs = [];
    foreach($attributes as $key => $value) {
      $attribute_pairs[] = "{$key}='{$value}'";
    }

    $sql = "UPDATE " . static::$table_name . " SET ";
    $sql .= join(', ', $attribute_pairs);
    $sql .= " WHERE id='" . self::$db->escape_string($this->id) . "' ";
    $sql .= "LIMIT 1";
    $result = self::$db->query($sql);
    return $result; // Returns true or false
  }

  public function save() {
    // A new record will not have an id yet
    if(isset($this->id)) {
      return $this->update();
    } else {
      return $this->create();
    }
  }

  public function merge_attributes($args) {
    foreach($args as $key => $value) {
      if(property_exists($this, $key) && !is_null($value)) {
        $this->$key = $value;
      }
    }
  }

  // Get properties using db column names (excluding id)
  public function attributes() {
    $attributes = [];
    foreach(static::$columns as $column) {
      if($column == 'id') { continue; }
      $attributes[$column] = $this->$column;
    }
    return $attributes;
  }

  protected function sanitized_attributes() {
    $sanitized = [];
    foreach($this->attributes() as $key => $value) {
      $sanitized[$key] = self::$db->escape_string($value);
    }
    return $sanitized;
  }

  public function delete() {
    $sql = "DELETE FROM " . static::$table_name . " ";
    $sql .= "WHERE id='" . self::$db->escape_string($this->id) . "' ";
    $sql .= "LIMIT 1";
    $result = self::$db->query($sql);
    return $result;
  }

}

?>
