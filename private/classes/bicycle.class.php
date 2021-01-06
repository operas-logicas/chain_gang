<?php

class Bicycle {

  /* ------- START OF ACTIVE RECORD CODE -------- */

  protected static $db;

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
    $sql = "SELECT * FROM bicycles";
    return self::find_by_sql($sql);
  }

  public static function find_by_id($id) {
    $sql = "SELECT * FROM bicycles ";
    $sql .= "WHERE id='" . self::$db->escape_string($id) . "'";
    $object_array = self::find_by_sql($sql);
    if(!empty($object_array)) {
      return array_shift($object_array);
    } else {
      return false;
    }
  }

  public static function instantiate($record) {
    $object = new self;
    foreach($record as $property => $value) {
      if(property_exists($object, $property)) {
        $object->$property = $value;
      }
    }
    return $object;
  }

  public function create() {
    $sql = "INSERT INTO bicycles (";
    $sql .= "brand, model, year, category, color, gender, price, weight_kg, condition_id, description";
    $sql .= ") VALUES (";
    $sql .= "'" . $this->brand . "', ";
    $sql .= "'" . $this->model . "', ";
    $sql .= "'" . $this->year . "', ";
    $sql .= "'" . $this->category . "', ";
    $sql .= "'" . $this->color . "', ";
    $sql .= "'" . $this->gender . "', ";
    $sql .= "'" . $this->price . "', ";
    $sql .= "'" . $this->weight_kg . "', ";
    $sql .= "'" . $this->condition_id . "', ";
    $sql .= "'" . $this->description . "'";
    $sql .= ")";
    $result = self::$db->query($sql);

    if($result) {
      $this->id = self::$db->insert_id;
    }
    return $result; // Returns true or false
  }

  /* ------- END OF ACTIVE RECORD CODE -------- */

  public const CATEGORIES = ['Road', 'Mountain', 'Hybrid', 'Cruiser', 'City', 'BMX'];
  public const GENDERS = ['Mens', 'Womens', 'Unixex'];

  public static $conditions = [
    1 => 'Beat Up',
    2 => 'Decent',
    3 => 'Good',
    4 => 'Great',
    5 => 'Like New'
  ];

  public $id;
  public $brand;
  public $model;
  public $year;
  public $category;
  public $color;
  public $description;
  public $gender;
  public $price;

  protected $weight_kg;
  protected $condition_id;

  public function __construct($args=[]) {
    $this->brand = $args['brand'] ?? '';
    $this->model = $args['model'] ?? '';
    $this->year = $args['year'] ?? '';
    $this->category = $args['category'] ?? '';
    $this->color = $args['color'] ?? '';
    $this->description = $args['description'] ?? '';
    $this->gender = $args['gender'] ?? '';
    $this->price = $args['price'] ?? '';
    $this->weight_kg = $args['weight_kg'] ?? 0.00;
    $this->condition_id = $args['condition_id'] ?? 3;

    // Caution: allows private/protected props to be set dynamically
    // foreach($args as $k => $v) {
    //   if(property_exists($this, $k)) {
    //     $this->$k = $v;
    //   }
    // }

  }

  public function name() {
    return "{$this->brand} {$this->model} {$this->year}";
  }

  public function weight_kg() {
    return number_format($this->weight_kg, 2) . ' kg';
  }

  public function weight_lbs() {
    return number_format(floatval($this->weight_kg) * 2.20462262185, 2) . ' lbs';
  }

  public function set_weight_kg($weight_kg) {
    $this->weight_kg = floatval($weight_kg);
  }

  public function set_weight_lbs($weight_lbs) {
    $this->weight_kg = floatval($weight_lbs) / 2.20462262185;
  }

  public function condition() {
    return self::$conditions[$this->condition_id] ?? 'Unknown';
  }

}

?>
