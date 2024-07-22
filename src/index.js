import { addFilter } from '@wordpress/hooks';

import { registerQuoteAPIEditorVariation } from './variations/quote-api-editor';
import { registerQuoteAPIFrontendVariation } from './variations/quote-api-frontend';

console.log('Hello from the index.js file!');

/**
 * Add the "namespace" attribute to "core/quote" block
 * @param {Object} settings
 */
function addAttributes( settings ) {
	if (
		'core/quote' !== settings.name &&
		'core/paragraph' !== settings.name
	) {
		return settings;
	}

	let extraAttributes = {
		namespace: {
			type: 'string',
		},
	};

	if ( 'core/paragraph' === settings.name ) {
		extraAttributes = {
			tags: {
				type: 'string',
			},
		};
	}
	
	const newSettings = {
		...settings,
		attributes: {
			...settings.attributes,
			...extraAttributes,
		},
	};
	
	return newSettings;
}

addFilter(
	'blocks.registerBlockType',
	'quote-api/add-attributes',
	addAttributes
);

registerQuoteAPIEditorVariation();
registerQuoteAPIFrontendVariation();
