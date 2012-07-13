<?php
$output = '';
$output .= $this->element('Frontpage.content', array('category' => 'hero'));
$results = '';
$results .= $this->Html->tag('h1', __('Search Results'));
$results .= $this->Html->tag('h2', __(
	'Your search for "%s" found %s results.', 
	$this->request->query['q'],
	$this->Paginator->counter('{:count}')
)); 
foreach ($data as $item) {
	$resultItem = '';
	$resultItem .= $this->Html->tag('h3', $item['SearchIndex']['title']);
	$resultItem .= $this->Html->link($item['SearchIndex']['url']);
	$resultItem .= $this->Html->tag('p', $item['SearchIndex']['description']);
	$results .= $this->Html->div('result-item', $resultItem);
}
$output .= $this->element('Frontpage.content', array(
	'category' => 'article',
	'class' => 'article results',
	'after' => $results
));
$output .= $this->Pager->display();
echo $output;
