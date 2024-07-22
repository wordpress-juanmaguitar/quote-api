<?php

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

use QuoteAPI\WikiquoteAPI;

function qa__wikiquote_api_init()
{
	$wikiquoteAPI = new WikiquoteAPI();

	register_rest_route('wikiquote/v1', '/random-quote', [
		'methods' => 'GET',
		'callback' => function () use ($wikiquoteAPI) {
			$quoteData = $wikiquoteAPI->getRandomQuote();
			return rest_ensure_response($quoteData);
		},
		'permission_callback' => '__return_true',
	]);

	register_rest_route('wikiquote/v1', '/random-quote/author/(?P<author>[a-zA-Z0-9-]+)', [
		'methods' => 'GET',
		'callback' => function ($request) use ($wikiquoteAPI) {
			$quoteData = $wikiquoteAPI->getRandomQuoteByAuthor($request['author']);
			return rest_ensure_response($quoteData);
		},
		'permission_callback' => '__return_true',
	]);

	register_rest_route('wikiquote/v1', '/random-quote/tags', [
		'methods' => 'GET',
		'callback' => function ($request) use ($wikiquoteAPI) {
			$tags = explode(",", $request->get_param('tags'));
			$quoteData = $wikiquoteAPI->getRandomQuoteByTags($tags);
			return rest_ensure_response($quoteData);
		},
		'permission_callback' => '__return_true',
		'args' => [
			'tags' => [
				'required' => true,
				'validate_callback' => function ($param, $request, $key) {
					return is_string($param);
				},
			],
		],
	]);
}

add_action('rest_api_init', 'qa__wikiquote_api_init');
