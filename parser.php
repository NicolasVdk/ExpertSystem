<?php

	class Parser
	{
		private static $_singleton;
		public $line_n = 1;
		public $condition = [];
		public $affected = [];
		public $variable = [];
		public $expression_lists = [];

		public function __construct() {
			$expression_lists = ["+", "|", "!", "^", "<=>", "=>", "(", ")"];
		}

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
			$line_n++;
		}

		public function SendLine($line) {
			if (preg_match("([^A-Z\|\^\!\(\)\+]+)", $line, $match)) {
				var_dump($match);
				if (count($match) > 2)
					error("Syntax error", $this->line_n);
				/* Check if is a rules lines */
				if (preg_match("/<=>|=>/", $line)) {
					$side = preg_split( "/<=>|=>/", $line);
					var_dump($line);
					var_dump($side);
					if ($line [1] === "=>") {

					} else {

					}
					return ;
				}
				/* Check if is initial fact */
				if (preg_match("/^\=/", $line)) {
					return ;
				}
				/* Check if is a question */
				if (preg_match("/^\?/", $line)) {
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