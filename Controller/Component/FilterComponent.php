<?php
/**
 * Filter Component 
 **/
class FilterComponent extends Component {
	
	public $components = array();

	function __construct(ComponentCollection $collection, $settings = array()) {
		parent::__construct($collection, $settings);
	}

	public function initialize($controller) {
	}

	public function startup($controller) {
	}

	public function beforeRender($controller) {
	}

	public function shutdown($controller) {
	}

	public function beforeRedirect($controller, $url, $status=null, $exit=true) {
	}
	
	public function like($fields, $keywords) {
		$conditions = array();
		$keywords = explode(' ', $keywords);
		foreach($keywords as $keyword) {
			$parts = array();
			foreach($fields as $field) {
				$parts[] = array(sprintf('%s LIKE', $field) => "%{$keyword}%");
			}
			$conditions = am($conditions, $parts);
		}
		return $conditions;
	}
}
