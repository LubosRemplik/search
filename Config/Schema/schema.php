<?php
class SearchSchema extends CakeSchema {

	public $search_indices = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'url' => array('type' => 'string'),
		'priority' => array('type' => 'float'),
		'title' => array('type' => 'string'),
		'keywords' => array('type' => 'text'),
		'description' => array('type' => 'text'),
		'body_html' => array('type' => 'text'),
		'body' => array('type' => 'text'),
		'content_html' => array('type' => 'text'),
		'content' => array('type' => 'text'),
		'created' => array('type' => 'datetime'),
		'modified' => array('type' => 'datetime'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
}
