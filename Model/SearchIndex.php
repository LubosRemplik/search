<?php
App::uses('AppModel', 'App.Model');
class SearchIndex extends AppModel {
	
	public $actsAs = array(
		'Search.Searchable'
	);

    public $findMethods = array('results' =>  true);

	// fields for search with priority
	protected $_fields = array(
		'title' => 20,
		'description' => 10,
		'keywords' => 5,
		'content' => 3,
		//'body' => 1
	);

	protected $_keywords;

	protected $_limit;

	public function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = array()) {
		return $this->find('results', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'group'));
	}
	
	public function paginateCount($conditions = null, $recursive = 0, $extra = array()) {
		$results = $this->find('results', compact('conditions', 'recursive'));
		return count($results);
	}

	protected function _findResults($state, $query, $results = array()) {
        if ($state == 'before') {
			if (isset($query['conditions']["$this->alias.query"])) {
				$q = $query['conditions']["$this->alias.query"];
				unset($query['conditions']["$this->alias.query"]);
			}
			if (isset($query['conditions']['query'])) {
				$q = $query['conditions']['query'];
				unset($query['conditions']['query']);
			}
			if (isset($q)) {
				// saving keywords, amending conditinos
				$conditions = array();
				$this->_keywords = explode(' ', $q);
				foreach($this->_keywords as $keyword) {
					$parts = array();
					foreach($this->_fields as $field => $priority) {
						$partKey = sprintf('%s.%s LIKE', $this->alias, $field);
						$parts[] = array($partKey => "%{$keyword}%");
					}
					$conditions = am($conditions, $parts);
				}
				$query['conditions'] = am(
					$query['conditions'], 
					array('OR' => $conditions)
				);
				// saving limit, no limit for now.
				$this->_limit = $query['limit'];
				$query['limit'] = false;
			}
            return $query;
        }
		if (!empty($this->_keywords) && $this->_limit) {
			$data = $results;
			// counting priority
			foreach ($data as $key => $item) {
				$freqTotal = 0;
				$priorityTotal = 0;
				foreach($this->_fields as $field => $priority) {
					$freq = 0;
					foreach ($this->_keywords as $keyword) {
						$freq += substr_count(
							strtolower($item[$this->alias][$field]),
							strtolower($keyword)
						);
					}
					$freqTotal += $freq;
					$priorityTotal += $priority * $freq;
				}
				$priorityTotal *= $item[$this->alias]['priority'];
				$data[$key][$this->alias]['freq'] = $freqTotal;
				$data[$key][$this->alias]['priority'] = $priorityTotal;
			}
			// sort
			if (!empty($data)) {
				$data = Set::sort($data, "/$this->alias/priority", 'desc');
			}
			$counter = 0;
			$limitTo = $this->_limit * $query['page'];
			$limitFrom = $limitTo - $this->_limit + 1;
			$results = array();
			foreach ($data as $item) {
				$counter++;
				if ($counter >= $limitFrom) {
					$results[] = $item;
				}
				if (count($results) == $this->_limit) {
					break;
				}
			}
		}
        return $results;
	}
}
