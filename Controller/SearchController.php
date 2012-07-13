<?php
App::uses('AppController', 'App.Controller');
class SearchController extends AppController {

	public $uses = array(
		'Search.SearchIndex'	
	);

	public $components = array(
		'Frontpage.Frontpage'
	);

	// only limit available with SearchIndex model
	// order is done by relevance
	public $paginate = array(
		'SearchIndex' => array(
			'limit' => 15 
		)
	);
	
	public function index() {
		$this->paginate['SearchIndex']['conditions'] = array(
			// query || SearchIndex.query field is here for magic
			'SearchIndex.query' => $this->request->query['q'],
		);
		$data = $this->paginate('SearchIndex');
		$this->set(compact('data'));
		$this->render();
	}
}
