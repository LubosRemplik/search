<?php
/**
 * Filter Component
 **/
class FilterComponent extends Object {
    
    public $components = array();

    //called before Controller::beforeFilter()
    function initialize(&$controller, $settings = array()) {
        // saving the controller reference for later use
        $this->controller =& $controller;
    }

    //called after Controller::beforeFilter()
    function startup(&$controller) {
    }

    //called after Controller::beforeRender()
    function beforeRender(&$controller) {
    }

    //calledafter Controller::render()
    function shutdown(&$controller) {
    }

    //called before Controller::redirect()
    function beforeRedirect(&$controller, $url, $status=null, $exit=true) {
    }

    function redirectSomewhere($value) {
        // utilizing a controller method
        $this->controller->redirect($value);
    }
	
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
