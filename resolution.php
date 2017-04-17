<?php

class Resolution
{
	private $operator = ["^", "|", "+", "!"];
	private $rpndata;
	private $condition;
	private $affected;
	private $variablestates;
	private $listofpass;
	private $nextlistofpass;

	public function __construct($c, $a, $r, $v) {
		$this->condition = $c;
		$this->affected = $a;
		$this->rpndata = $r;
		$this->variablestates = $v;
		$this->listofpass = array_keys($this->rpndata);
		$this->nextlistofpass = [];
		$this->processList();
	}

	public function processList() {
		foreach ($this->listofpass as $key => $value) {
			echo "Process line : " . $key . PHP_EOL;
			$this->processRpn($this->rpndata[$value]);
		}
	}

	public function processRpn($pile) {
		var_dump($pile);
		$pile = $this->ConvertOnRealOperation($pile);
		$stack = [];
		while (count($pile) > 1) {
			foreach ($pile as $key => $value) {
				echo "\r".implode(" ", $pile).'                  ';
				unset($pile[$key]);
				if (in_array($value, $this->operator)) {
					array_unshift($pile, $this->defineOperatorFunction($value, $stack));
					$stack = [];
				} else {
					$stack[] = $value;
				}
			}
			//echo "\r".count($pile);
		}
		echo PHP_EOL;
		//var_dump($pile);
	}

	public function ConvertOnRealOperation($pile) {
		for ($i = 0; $i < count($pile) ; $i++) {
			if (!in_array($pile[$i], $this->operator)) {
				$pile[$i] = $this->variablestates[$pile[$i]];
			}
		}
		return $pile;
	}

	public function defineOperatorFunction($operator, $stack) {
		switch ($operator) {
			case '+':
				return ($this->AllTrue($stack));
				break;
			
			case '|':
				return ($this->OneTrue($stack));
				break;

			case '^':
				return ($this->OnlyOneTrue($stack));
				break;

			case '!':
				return ($this->NotValue($stack));
				break;
		}
	}

	public function NotValue($stack) {
		return !$stack[count($stack - 1)];
	}

	public function AllTrue($stack) {
		foreach ($stack as $key => $value) {
			if ($value === false)
				return false;
		}
		return true;
	}

	public function OneTrue($stack) {
		foreach ($stack as $value) {
			if ($value === true)
				return true;
		}
		return false;
	}

	public function OnlyOneTrue($stack) {
		$states = false;
		foreach ($stack as $value) {
			if ($value === true) {
				if ($states === true)
					return false;
				$states = true;
			}
		}
		return $states;
	}
}