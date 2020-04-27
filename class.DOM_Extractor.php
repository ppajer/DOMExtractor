<?php

class DOM_Extractor {

	private $html;
	private $DOM;
	private $XPath;
	private $rules;

	public function __construct($rules = null, $html = null) {
		$this->DOM = new DOMDocument;
		if ($rules) {
			$this->setRules($rules);
		}
		if ($html) {
			$this->load($html);
		}
	}

	public function parse($rules = $this->rules, $context = false) {
		$result = array();
		foreach ($this->verifyRules($rules) as $key => $rule) {

			// Don't loop over instructions as data keys
			if (strpos($key, '@') !== false) {
				continue;
			}

			if ($context) {
				$nodes = $this->XPath->query($rule['@selector'], $context);
			} else {
				$nodes = $this->XPath->query($rule['@selector']);
			}
			
			$attr = isset($rule['@attr']) ? $rule['@attr'] : false;
			$subtree = isset($rule['@each']) ? $rule['@each'] : false;

			if ($subtree) {
				$result[$key] = array();
			}

			foreach ($nodes as $node) {
				if ($subtree) {
					$result[$key][] = $this->parse($subtree, $node);
				} else {
					$result[$key] = $attr ? $node->getAttribute($attr) : $node->nodeValue;
				}
			}
			return $result;
		}
	}

	private function verifyRules($rules) {
		if (!is_array($rules) OR empty($rules)) {
			throw new Exception("DOM_Extractor::rules must be a non-empty Array", 1);
		}
		return $rules;
	}

	public function setRules($rules) {
		if (is_string($rules)) {
			if (strpos($rules, '{') === false) {
				$rules = file_get_contents($rules);
			}
			$rules = json_decode($rules, true);
		}
		$this->rules = $this->verifyRules($rules);
		return $this;
	}

	public function load($html) {
		$this->DOM->loadHTML($html);
		$this->XPath = new DOMXpath($this->DOM);
		return $this;
	}
}

?>