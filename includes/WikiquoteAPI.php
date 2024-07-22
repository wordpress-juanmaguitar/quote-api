<?php

namespace QuoteAPI;

use GuzzleHttp\Client;

class WikiquoteAPI
{
	private $client;

	public function __construct()
	{
		$this->client = new Client();
	}

	private function searchPages($query)
	{
		// Adding "famous" keyword to the search query
		$searchUrl = "https://en.wikiquote.org/w/api.php?action=query&list=search&srsearch=" . urlencode($query . " famous") . "&format=json";
		$response = $this->client->request('GET', $searchUrl);
		$data = json_decode($response->getBody(), true);
		return $data['query']['search'];
	}

	private function getRandomPage($pages)
	{
		return $pages[array_rand($pages)];
	}

	private function fetchPageContent($pageId)
	{
		$contentUrl = "https://en.wikiquote.org/w/api.php?action=parse&pageid=" . $pageId . "&format=json";
		$response = $this->client->request('GET', $contentUrl);
		$data = json_decode($response->getBody(), true);
		return $data['parse'];
	}

	private function extractQuotes($htmlContent)
	{
		$quotes = [];
		$dom = new \DOMDocument();
		@$dom->loadHTML($htmlContent);  // Suppress errors due to malformed HTML
		$listItems = $dom->getElementsByTagName('li');
		foreach ($listItems as $item) {
			$quotes[] = $item->textContent;
		}
		return $quotes;
	}

	private function createQuoteResponse($quote, $author)
	{
		return [
			'quote' => $quote,
			'author' => $author,
			'author_bio_link' => 'https://en.wikiquote.org/wiki/' . urlencode($author),
		];
	}

	public function getRandomQuote()
	{
		$pages = $this->searchPages("quote");
		$randomPage = $this->getRandomPage($pages);
		$pageContent = $this->fetchPageContent($randomPage['pageid']);
		$quotes = $this->extractQuotes($pageContent['text']['*']);

		if (empty($quotes)) {
			return [
				'quote' => 'No quotes found.',
				'author' => '',
				'author_bio_link' => ''
			];
		}

		$quote = $quotes[array_rand($quotes)];
		return $this->createQuoteResponse($quote, $randomPage['title']);
	}

	public function getRandomQuoteByAuthor($author)
	{
		$pages = $this->searchPages($author);
		$randomPage = $this->getRandomPage($pages);
		$pageContent = $this->fetchPageContent($randomPage['pageid']);
		$quotes = $this->extractQuotes($pageContent['text']['*']);

		if (empty($quotes)) {
			return [
				'quote' => "No quotes found for author: $author",
				'author' => $author,
				'author_bio_link' => 'https://en.wikiquote.org/wiki/' . urlencode($author),
			];
		}

		$quote = $quotes[array_rand($quotes)];
		return $this->createQuoteResponse($quote, $author);
	}

	public function getRandomQuoteByTags($tags)
	{
		$tagsQuery = implode(" ", $tags);
		$pages = $this->searchPages($tagsQuery);
		$randomPage = $this->getRandomPage($pages);
		$pageContent = $this->fetchPageContent($randomPage['pageid']);
		$quotes = $this->extractQuotes($pageContent['text']['*']);

		if (empty($quotes)) {
			return [
				'quote' => "No quotes found for tags: " . implode(", ", $tags),
				'author' => '',
				'author_bio_link' => ''
			];
		}

		$quote = $quotes[array_rand($quotes)];
		return $this->createQuoteResponse($quote, $randomPage['title']);
	}
}
