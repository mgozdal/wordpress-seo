const fetch = require( "node-fetch" );

/**
 * Calls the GitHub API
 *
 * @param {string} path The path to call.
 * @param {Object} body The body data to send.
 * @param {string} [method] Optional. The request method, "POST", "GET", "PATCH".
 *
 * @returns {Promise<Object>} Response object.
 */
async function githubApi( path, body, method = "GET" ) {
	const apiRoot = "https://api.github.com";
	const accesToken = process.env.GITHUB_ACCESS_TOKEN;
	const repository = process.env.GITHUB_REPOSITORY;
	const apiUrl = `${ apiRoot }/repos/${ repository }/${ path }`;

	return await fetch( apiUrl, {
		method: method,
		headers: {
			"Content-Type": "application/json",
			Authorization: `token ${ accesToken }`,
		},
		body: JSON.stringify( body ),
	} );
}

module.exports = githubApi;
