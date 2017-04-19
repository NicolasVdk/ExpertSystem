<?php
define("DEBUG", true);

class RPN
{
	private $operators = [
	    '^' => ['precedence' => 0, 'associativity' => 'left'],
	    '|' => ['precedence' => 1, 'associativity' => 'left'],
	    '+' => ['precedence' => 2, 'associativity' => 'left'],
	    '!' => ['precedence' => 3, 'associativity' => 'left'],
	];
	private $expression;
	private $line;
	public $sortie;

	public function __construct($expression, $line) {
		$this->expression = str_split($expression);
		$this->line = $line;
		$this->sortie = $this->shunting_yard();
	}

	private function shunting_yard()
	{
		$tokens = $this->expression;
	    $stack = new \SplStack();
	    $output = new \SplQueue();
	    foreach ($tokens as $token) {
	        if (preg_match('/[A-Z]/', ($token))) {
	            $output->enqueue($token);
	        } elseif (isset($this->operators[$token])) {
	            $o1 = $token;
	            while ($this->has_operator($stack) && ($o2 = $stack->top()) && $this->has_lower_precedence($o1, $o2)) {
	                $output->enqueue($stack->pop());
	            }
	            $stack->push($o1);
	        } elseif ('(' === $token) {
	            $stack->push($token);
	        } elseif (')' === $token) {
	            while (count($stack) > 0 && '(' !== $stack->top()) {
	                $output->enqueue($stack->pop());
	            }
	            if (count($stack) === 0) {
	                error('Nombre de parenthese incorrect:' . json_encode($tokens), $this->line);
	            }
	            // pop off '('
	            $stack->pop();
	        } else {
	            error('Operateurs inconnu :'.$token, $this->line);
	        }
	    }
	    while ($this->has_operator($stack, $this->operators)) {
	        $output->enqueue($stack->pop());
	    }
	    if (count($stack) > 0) {
	        error('Mauvais format:'. json_encode($tokens), $this->line);
	    }
	    return iterator_to_array($output);
	}

	private function has_operator(\SplStack $stack)
	{
	    return count($stack) > 0 && ($top = $stack->top()) && isset($this->operators[$top]);
	}

	private function has_lower_precedence($o1, $o2)
	{
	    $op1 = $this->operators[$o1];
	    $op2 = $this->operators[$o2];
	    return ('left' === $op1['associativity'] && $op1['precedence'] === $op2['precedence']) || $op1['precedence'] < $op2['precedence'];
	}
}