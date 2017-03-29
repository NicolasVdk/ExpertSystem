<?php

  class RPN
  {
    public $operator = ["+", "|", "!", "^", "(", ")"];
    public $operand = [];
    public $sortie = [];
    public $expression;

    public function __construct($expression) {
      $this->expression = str_split($expression);
      $this->convert_rpn();
    }

    private function convert_rpn() {

    }
  }
