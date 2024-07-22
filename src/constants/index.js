// const QUOTABLE_API_URL = 'https://api.quotable.io';
const WIKIQUOTE_API_URL = '/wp-json/wikiquote/v1';
const URL_RANDOM_QUOTE = `${ WIKIQUOTE_API_URL }/random-quote`;
const URL_RANDOM_BY_AUTHOR = `${ URL_RANDOM_QUOTE }/author/<%AUTHOR%>`;

const AUTHOR_TAG = '<%AUTHOR%>';

const API_ENDPOINTS = {
	URL_RANDOM_QUOTE,
	URL_RANDOM_BY_AUTHOR,
	AUTHOR_TAG,
};

export { API_ENDPOINTS };
export { authors } from './authors';
export { tags } from './tags';
