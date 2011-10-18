<?php

class FilterComponent extends Object {

	function like($fields, $keywords) {
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
