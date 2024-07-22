<?php

if (defined('WP_CLI') && WP_CLI) {
	class WP_CLI_Custom_Commands
	{
		// Create custom tables
		public function create_tables()
		{
			global $wpdb;
			$charset_collate = $wpdb->get_charset_collate();

			$authors_table = $wpdb->prefix . 'authors';
			$quotes_table = $wpdb->prefix . 'quotes';
			$tags_table = $wpdb->prefix . 'tags';
			$quote_tags_table = $wpdb->prefix . 'quote_tags';

			// Create authors table
			$sql = "CREATE TABLE $authors_table (
                id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                name VARCHAR(255) NOT NULL,
                link VARCHAR(255) NOT NULL,
                bio TEXT NOT NULL,
                description TEXT NOT NULL,
                PRIMARY KEY (id)
            ) $charset_collate;";
			dbDelta($sql);

			// Create tags table
			$sql = "CREATE TABLE $tags_table (
                id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                name VARCHAR(255) NOT NULL,
                PRIMARY KEY (id)
            ) $charset_collate;";
			dbDelta($sql);

			// Create quotes table
			$sql = "CREATE TABLE $quotes_table (
                id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                author_id BIGINT(20) UNSIGNED NOT NULL,
                quote TEXT NOT NULL,
                FOREIGN KEY (author_id) REFERENCES $authors_table(id),
                PRIMARY KEY (id)
            ) $charset_collate;";
			dbDelta($sql);

			// Create quote_tags table
			$sql = "CREATE TABLE $quote_tags_table (
                quote_id BIGINT(20) UNSIGNED NOT NULL,
                tag_id BIGINT(20) UNSIGNED NOT NULL,
                FOREIGN KEY (quote_id) REFERENCES $quotes_table(id),
                FOREIGN KEY (tag_id) REFERENCES $tags_table(id),
                PRIMARY KEY (quote_id, tag_id)
            ) $charset_collate;";
			dbDelta($sql);

			WP_CLI::success("Tables created successfully.");
		}

		// Insert data from JSON files
		public function insert_data($args, $assoc_args)
		{
			global $wpdb;

			$authorsJsonPath = $assoc_args['authors'];
			$quotesJsonPath = $assoc_args['quotes'];
			$tagsJsonPath = $assoc_args['tags'];

			$authorsData = json_decode(file_get_contents($authorsJsonPath), true);
			$quotesData = json_decode(file_get_contents($quotesJsonPath), true);
			$tagsData = json_decode(file_get_contents($tagsJsonPath), true);

			foreach ($authorsData as $author) {
				$wpdb->insert(
					$wpdb->prefix . 'authors',
					array(
						'name' => $author['name'],
						'link' => $author['link'],
						'bio' => $author['bio'],
						'description' => $author['description']
					)
				);
			}

			foreach ($tagsData as $tag) {
				$wpdb->insert(
					$wpdb->prefix . 'tags',
					array('name' => $tag['name'])
				);
			}

			foreach ($quotesData as $quote) {
				$author_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$wpdb->prefix}authors WHERE name = %s", $quote['author']));

				$wpdb->insert(
					$wpdb->prefix . 'quotes',
					array(
						'author_id' => $author_id,
						'quote' => $quote['quote']
					)
				);

				$quote_id = $wpdb->insert_id;

				foreach ($quote['tags'] as $tag_name) {
					$tag_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$wpdb->prefix}tags WHERE name = %s", $tag_name));

					$wpdb->insert(
						$wpdb->prefix . 'quote_tags',
						array(
							'quote_id' => $quote_id,
							'tag_id' => $tag_id
						)
					);
				}
			}

			WP_CLI::success("Data inserted successfully.");
		}
	}

	WP_CLI::add_command('custom_tables create', [ 'WP_CLI_Custom_Commands', 'create_tables' ]);
	WP_CLI::add_command('custom_tables insert', [ 'WP_CLI_Custom_Commands', 'insert_data' ], [
		'authors' => [
			'description' => 'Path to the authors JSON file.',
			'type' => 'file',
			'optional' => false,
		],
		'quotes' => [
			'description' => 'Path to the quotes JSON file.',
			'type' => 'file',
			'optional' => false,
		],
		'tags' => [
			'description' => 'Path to the tags JSON file.',
			'type' => 'file',
			'optional' => false,
		],
	]);
}
