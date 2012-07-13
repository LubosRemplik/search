<?php
App::uses('AppShell', 'Console/Command');
App::uses('Router', 'Routing');
App::uses('Sanitize', 'Utility');
App::uses('Xml', 'Utility');
App::import('Vendor', 'Search.simplehtmldom/simple_html_dom');
class SearchIndexShell extends AppShell {
	
	public $uses = array(
		'Search.SearchIndex'
	);

	public function main() {
		$this->out($this->OptionParser->help());
	}
	
	public function generate() {
		// reading sitemap
		$sitemap = Xml::toArray(Xml::build($this->args[0]));
		// reading search index
		$searchIndex = $this->SearchIndex->find('list', array(
			'fields' => array('url', 'modified')
		));
		foreach ($sitemap['urlset']['url'] as $url) {
			if ($this->params['force'] || !isset($searchIndex[$url['loc']])) {
				$pages[$url['loc']] = $url['priority'];
			} else {
				$modified = strtotime($searchIndex[$url['loc']]);
				$lastmod = strtotime($url['lastmod']);
				if ($modified < $lastmod) {
					$pages[$url['loc']] = $url['priority'];
				}
			}
		}
		$this->out(sprintf('Total pages: %d', count($pages)));
		$counter = 0;
		if (count($pages)) {
			foreach ($pages as $url => $priority) {
				$counter++;
				$parsedUrl = parse_url($url);
				$path = $parsedUrl['path'];
				if (isset($this->params['base'])
				&& strpos($path, $this->params['base']) == 0) {
					$path = substr($path, strlen($this->params['base']));
				}
				$html = file_get_contents($url);
				$dom = str_get_html($html);
				$title = $dom->find('title', 0)->innertext;
				$keywords = null;
				if ($element = $dom->find('meta[name=keywords]', 0)) {
					$keywords = $element->getAttribute('content');
				}
				$description = null;
				if ($element = $dom->find('meta[name=description]', 0)) {
					$description = $element->getAttribute('content');
				}
				$dom->clear();
				$bodyHtml = $html;
				$body = str_replace(
					PHP_EOL, ' ', 
					strip_tags(Sanitize::stripAll($bodyHtml))
				);
				$contentHtml = $this->requestAction($path, array('return'));
				$content = str_replace(
					PHP_EOL, ' ', 
					strip_tags(Sanitize::stripAll($contentHtml))
				);
				$data = array(
					'url' => $url,
					'priority' => $priority,
					'title' => $title,
					'keywords' => $keywords,
					'description' => $description,
					'body_html' => $bodyHtml,
					'body' => $body,
					'content_html' => $contentHtml,
					'content' => $content
				);
				if ($found = $this->SearchIndex->findByUrl($url)) {
					$this->SearchIndex->id = $found['SearchIndex']['id'];
				} else {
					$this->SearchIndex->create();
				}
				$result = $this->SearchIndex->save($data);
				if ($result) {
					$this->out(sprintf('<success>%s. %s</success>', $counter, __('Generated search index for %s', $url)));
				} else {
					$this->out(sprintf('<error>%s. %s</error>', $counter, __('Failed to save search for %s', $url)));
				}
			}
				
		}
	}
	
	public function getOptionParser() {
		$parser = parent::getOptionParser();

		$parser->description(__('A console tool for generating search index'))
			->addSubcommand('generate', array(
				'help' => __('Generate a fresh set of search index'),
				'parser' => array(
					'arguments' => array(
						'sitemap' => array(
							'help' => __('Enter url of sitemap.xml you want to parse.'),
							'required' => true,
						)
					),
					'options' => array(
						'base' => array(
							'short' => 'b',
							'help' => __('Enter site base.'),
						),
						'force' => array(
							'short' => 'f',
							'help' => __('Forcing to regenerate whole index.') ,
							'boolean' => true,
						),
					)
				)
			))
			;

		return $parser;
	}
}
