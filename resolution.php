<?php

class Resolution
{
	private $operator = ["^", "|", "+", "!"];
	private $rpndata;
	private $condition;
	private $affected;
	private $search;
	private $variablestates;
	private $listofpass;
	private $verbose;

	public function __construct($c, $a, $r, $v, $s, $verbose) {
		$this->condition = $c;
		$this->affected = $a;
		$this->rpndata = $r;
		$this->variablestates = $v;
		$this->search = $s;
		$this->verbose = $verbose;
		$this->listofpass = array_keys($this->rpndata);
		$this->processList();
	}

	public function processList() {
		while (!empty($this->listofpass)) {
			foreach ($this->listofpass as $key => $value) {
				unset($this->listofpass[$key]);
				if ($this->verbose)
					echo "Process RPN : " . implode(' ', $this->rpndata[$value]) . PHP_EOL;
				$this->applyResult($this->processRpn($this->rpndata[$value]), $value);
			}
		}
		$this->show_result();
	}

	public function show_result() {
		if (empty($this->search)) {
			foreach ($this->variablestates as $key => $value) {
				echo $key." = ".($value ? 'true':'false').PHP_EOL;
			}
		} else {
			foreach ($this->search as $value) {
				if (isset($this->variablestates[$value])) 
					echo $value." = ".($this->variablestates[$value] ? 'true':'false').PHP_EOL;
				else
					echo $value." = ".'false'.PHP_EOL;
			}
		}
	}

	public function applyResult($bool, $a_i) {
		preg_match_all('/!?[A-Z]/', $this->affected[$a_i], $match);
		foreach ($match[0] as $value) {
			preg_match('/[A-Z]/', $value, $index);
			if (preg_match('/!([A-Z])/', $value)) {
				$this->applyChange($index[0], !$bool);
			} else {
				$this->applyChange($index[0], $bool);
			}
		}
	}

	public function applyChange($index, $bool) {
		if (!isset($this->variablestates[$index])) {
			$this->variablestates[$index] = false;
		}
		if ($this->variablestates[$index] !== $bool && !$this->variablestates[$index]) {
			if ($this->verbose)
				echo $index . " move from ".($this->variablestates[$index] ? 'true':'false')." on " . ($bool ? 'true':'false'). PHP_EOL;
			if (isset($this->condition[$index]) && $this->variablestates[$index] !== $bool) {
				foreach ($this->condition[$index] as $value) {
					if (!in_array($value, $this->listofpass, true))
						$this->listofpass[] = $value;
				}
			}
			$this->variablestates[$index] = $bool;
		}
	}

	public function processRpn($pile) {
		$pile = $this->ConvertOnRealOperation($pile);
		$stack = [];
		foreach ($pile as $key => $value) {
			if (in_array($value, $this->operator, true)) {
				$tmp = array_slice($stack, 0, count($stack) - 2);
				$stack = $this->defineOperatorFunction($value, array_slice($stack, -2, 2));
				$stack = array_merge($tmp, $stack);

			} else {
				$stack[] = $value;
			}
		}
		return $stack[0];
	}

	public function ConvertOnRealOperation($pile) {
		for ($i = 0; $i < count($pile) ; $i++) {
			if (!in_array($pile[$i], $this->operator, true)) {
				if (!isset($this->variablestates[$pile[$i]])) {
					$this->variablestates[$pile[$i]] = false;
				}
				$pile[$i] = $this->variablestates[$pile[$i]];
			}
		}
		return $pile;
	}

	public function defineOperatorFunction($operator, $stack) {
		switch ($operator) {
			case '+':
				return [$this->AllTrue($stack)];
				break;
			
			case '|':
				return [$this->OneTrue($stack)];
				break;

			case '^':
				return [$this->OnlyOneTrue($stack)];
				break;

			case '!':
				return $this->NotValue($stack);
				break;

			default:
				return [];
				break;			
		}
	}

	public function NotValue($stack) {
		if (count($stack) < 1) {
			error('Too much operator Maybe ?');
		}
		$stack[count($stack) - 1] = !$stack[count($stack) - 1];
		return $stack;
	}

	public function AllTrue($stack) {
		if (count($stack) < 2) {
			error('Too much operator Maybe ?');
		}
		foreach ($stack as $key => $value) {
			if ($value === false)
				return false;
		}
		return true;
	}

	public function OneTrue($stack) {
		if (count($stack) < 2) {
			error('Too much operator Maybe ?');
		}
		foreach ($stack as $value) {
			if ($value === true)
				return true;
		}
		return false;
	}

	public function OnlyOneTrue($stack) {
		if (count($stack) < 2) {
			error('Too much operator Maybe ?');
		}
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