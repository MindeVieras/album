<?php
  class PagesController {
    public function home() {
      $first_name = 'Jon';
      $last_name  = 'Snow';
      require_once('templates/home.php');
    }

    public function error() {
      require_once('templates/error.php');
    }
  }
?>