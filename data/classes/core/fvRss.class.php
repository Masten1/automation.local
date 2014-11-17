<?php

class fvRssBuilder {

	protected $collection;

	function __construct( fvPager $colection ){
		if( !$colection->getEntity() instanceof iRss )
			throw new Exception("Can't create rss feed from non iRssItem Entity");

		$this->collection = $colection;
	}

	function build( $information ){
		$xml = new simplexml_load_string( '<?xml version="1.0"?><rss version="2.0"/>' );

		$chanel = $xml->rss->addChild('chanel');

		$chanel->title = $information['title'];
		$chanel->link = $information['link'];
		$chanel->description = $information['description'];
		$chanel->language = $information['language'];
		$chanel->pubDate = $information['pubDate'];

		$chanel->lastBuildDate = $information['pubDate'];
		$chanel->link = $information['pubDate'];
		$chanel->docs = 'http://blogs.law.harvard.edu/tech/rss';
		$chanel->language = lang;
		$chanel->generator = 'fvRssBuilder';
		$chanel->managingEditor = fvSite::$fvConfig->get('mailer.email', 'webmaster@memo.ua');
		$chanel->webMaster = fvSite::$fvConfig->get('mailer.email', 'webmaster@memo.ua');

		foreach( $this->collection as $entity ){
			$item = $chanel->addChild('item');
			$item->title = $entity->getTitle();
			$item->link = $entity->getLink();
			$item->description = $entity->getDescription();
			$item->pubDate = $entity->getDate();
			$item->guid = $entity->getGuide();
		}

		print $xml->asXML();
		die;
	}



}