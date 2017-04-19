<?php

	include_once ("rpn.php");

	class Parser
	{
		private static $_singleton;
		public $line_n = 1;
		public $rpndata = [];
		public $condition = [];
		public $affected = [];
		public $variablestates = [];
		public $search = [];

		public static function singleton () {
			if (self::$_singleton == null) {
				self::$_singleton = new Parser();
			}
			return self::$_singleton;
		}

		public static function ParseLine ($line) {
			self::singleton()->_ParseLine($line);
		}

		public function _ParseLine ($line) {
			$parsed = explode("#", $line);
			if (count($parsed) > 2) {
				error("Parse error", $this->line_n);
			}
			if (!empty($parsed[0])) {
				$real_line = $this->RemoveWhiteSpace($parsed[0]);
				if (!empty($real_line)) {
					$this->SendLine($real_line);
					$stack[] = $real_line;
				}
			}
			$this->line_n++;
		}

		public function SendLine($line) {
			if (preg_match("/([^A-Z\|\^\!\(\)\+]+)/", $line, $match)) {
				if (count($match) > 2)
					error("Syntax error", $this->line_n);
				/* Check if is a rules lines */
				if (preg_match("/=>/", $line)) {
					$side = preg_split( "/=>/", $line);
					$tmp = new RPN($side[0], $this->line_n);
					$this->rpndata[] = $tmp->sortie;
					new RPN($side[1], $this->line_n);
					$this->affected[] = $side[1];
					preg_match_all("/[A-Z]/", $side[0], $match);
					foreach ($match[0] as $value) {
						if (!array_key_exists($value, $this->variablestates))
							$this->variablestates[$value] = false;
						$this->condition[$value][] = count($this->rpndata) - 1;
					}
					return ;
				}
				/* Check if is initial fact */
				if (preg_match("/^\=/", $line)) {
					preg_match_all("/[A-Z]/", $line, $match);
					foreach ($match[0] as $value) {
						$this->variablestates[$value] = true;
					}
					return ;
				}
				/* Check if is a question */
				if (preg_match("/^\?/", $line)) {
					preg_match_all("/[A-Z]/", $line, $match);
					foreach ($match[0] as $value) {
						$this->search[] = $value;
					}
					return ;
				}
				error("Syntax error", $this->line_n);
			}
		}

		private function RemoveWhiteSpace($line) {
			$cleaned = preg_replace("/\s/", "", $line);
			return $cleaned;
		}
	}
