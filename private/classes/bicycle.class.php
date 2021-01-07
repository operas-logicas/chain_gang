<?php

class Bicycle extends DatabaseObject {

  protected static $table_name = 'bicycles';
  protected static $columns = ['id', 'brand', 'model', 'year', 'category', 'color', 'gender', 'price', 'weight_kg', 'condition_id', 'description'];

  public $id;
  public $brand;
  public $model;
  public $year;
  public $category;
  public $color;
  public $description;
  public $gender;
  public $price;
  public $weight_kg;
  public $condition_id;

  public static $conditions = [
    1 => 'Beat Up',
    2 => 'Decent',
    3 => 'Good',
    4 => 'Great',
    5 => 'Like New'
  ];

  public const CATEGORIES = ['Road', 'Mountain', 'Hybrid', 'Cruiser', 'City', 'BMX'];
  public const GENDERS = ['Mens', 'Womens', 'Unisex'];

  public function __construct($args=[]) {
    $this->brand = $args['brand'] ?? '';
    $this->model = $args['model'] ?? '';
    $this->year = $args['year'] ?? '';
    $this->category = $args['category'] ?? '';
    $this->color = $args['color'] ?? '';
    $this->description = $args['description'] ?? '';
    $this->gender = $args['gender'] ?? '';
    $this->price = $args['price'] ?? 0.00;
    $this->weight_kg = $args['weight_kg'] ?? 0.00;
    $this->condition_id = $args['condition_id'] ?? 3;
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

  public function validate() {
    $this->errors = [];

    // brand
    if(is_blank($this->brand)) {
      $this->errors[] = "Brand cannot be blank.";
    } elseif(!has_length($this->brand, ['min' => 2, 'max' => 255])) {
      $this->errors[] = "Brand must be between 2 and 255 characters.";
    }

    // model
    if(is_blank($this->model)) {
      $this->errors[] = "Model cannot be blank.";
    } elseif(!has_length($this->model, ['min' => 2, 'max' => 255])) {
      $this->errors[] = "Model must be between 2 and 255 characters.";
    }

    // year
    if(is_blank($this->year)) {
      $this->errors[] = "Year cannot be blank.";
    } elseif(!((int) $this->year  >= 0) && !has_length_exactly($this->year, 4)) {
      $this->errors[] = "Year must be 4 digits.";
    }

    // TODO additional custom validation

    return $this->errors;
  }

}

?>
