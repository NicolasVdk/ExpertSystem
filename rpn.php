<?php

define("DEBUG", false);

class RPN
{
	private $operator = ["(", ")", "^", "|", "+", "!"];
	private $operand = [];
	public $sortie = [];
	private $expression;

	public function __construct($expression) {
		$this->expression = str_split($expression);
		$this->convert_rpn();
	}

	private function convert_rpn() {
		if (DEBUG) {
			echo "- RPN Parsing -" . PHP_EOL;
			echo "########################" . PHP_EOL;
		}
		foreach ($this->expression as $value) {
			if (in_array($value, $this->operator)) {
				$this->push_operand($value);
			} else if (preg_match("/[A-Z]/", $value)) {
				$this->sortie[] = $value;
			}
			if (DEBUG) {
				echo "Token : " . $value . PHP_EOL;
				echo "Operateurs : " . implode(" ", $this->operand) . PHP_EOL;
				echo "Sortie : " . implode(" ", $this->sortie) . PHP_EOL;
				echo "########################" . PHP_EOL;
			}
		}
		$this->push_all_operand();
		if (DEBUG) {
			echo "- FINAL RESULT -" . PHP_EOL;
			echo "Operateurs : " . implode(" ", $this->operand) . PHP_EOL;
			echo "Sortie : " . implode(" ", $this->sortie) . PHP_EOL;
			echo "########################" . PHP_EOL;
		}
	}

	private function push_all_operand() {
		foreach ($this->operand as $value) {
			if ($value != "(" && $value != ")")
				$this->sortie[] = $value;
		}
		$this->operand = [];
	}

	private function push_operand($ope) {
		if ($ope == ")") {
			foreach ($this->operand as $key => $value) {
				unset($this->operand[$key]);
				if ($value == "(")
					break ;
				else
					$this->sortie[] = $value;
			}
		} else {
			$priority = array_search($ope, $this->operator);
			if (count($this->operand) === 0 || $priority > array_search($this->operand[0], $this->operator)) {
				array_unshift($this->operand, $ope);
			} else if ($ope != "(" && $ope != ")") {
				$this->sortie[] = $ope;
			}
		}
	}
}
