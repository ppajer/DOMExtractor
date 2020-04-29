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

	public function parse($rules = null, $context = false) {
		$result = array();
		if (is_null($rules)) {
			$rules = $this->rules;
		}
		foreach ($this->verifyRules($rules) as $key => $rule) {

			// Don't loop over instructions as data keys
			if (strpos($key, '@') !== false) {
				continue;
			}

			$nodes = $this->getNodes($rule['@selector'], $context);
			$result[$key] = $this->processNodes($nodes, $rule);
			
		}
		return $result;
	}

	private function getNodes($selector, $context = false) {
		if ($context) {
			return $this->XPath->query($selector, $context);
		}
		return $this->XPath->query($selector);
	}

	private function processNodes($nodes, $rule) {
		$result = array();

		foreach ($nodes as $node) {
			if (isset($rule['@each'])) {
				$result[] = $this->parse($rule['@each'], $node);
			} else {
				$result[] = $node->nodeValue;
			}
		}

		// Return string if single value
		return (count($result) > 1) ? $result : $result[0];
	}

	private function verifyRules($rules) {
		if (!is_array($rules) OR empty($rules)) {
			throw new Exception("DOM_Extractor::rules must be a non-empty Array", 1);
		}
		return $rules;
	}
}

?>