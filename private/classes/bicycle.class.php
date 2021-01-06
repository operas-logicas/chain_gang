<?php

class Bicycle {

  public const CATEGORIES = ['road', 'mountain', 'hybrid', 'cruiser', 'city', 'BMX'];
  public const GENDERS = ['mens', 'womens', 'unixex']; 

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

  protected static $conditions = [
    1 => 'Beat Up',
    2 => 'Decent',
    3 => 'Good',
    4 => 'Great',
    5 => 'Like New'
  ];

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
