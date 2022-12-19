/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ 991:
/***/ ((module) => {

module.exports = (function() { return this["lodash"]; }());

/***/ }),

/***/ 514:
/***/ ((module) => {

module.exports = (function() { return this["wp"]["apiFetch"]; }());

/***/ }),

/***/ 15:
/***/ ((module) => {

module.exports = (function() { return this["wp"]["data"]; }());

/***/ }),

/***/ 470:
/***/ ((module) => {

module.exports = (function() { return this["wp"]["url"]; }());

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be in strict mode.
(() => {
"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXTERNAL MODULE: external {"this":["wp","apiFetch"]}
var external_this_wp_apiFetch_ = __webpack_require__(514);
var external_this_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_this_wp_apiFetch_);
// EXTERNAL MODULE: external {"this":["wp","data"]}
var external_this_wp_data_ = __webpack_require__(15);
// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__(991);
// EXTERNAL MODULE: external {"this":["wp","url"]}
var external_this_wp_url_ = __webpack_require__(470);
;// CONCATENATED MODULE: ./modules/block-editor/js/sidebar/settings.js
/**
 * Module Constants
 *
 * @package Polylang-Pro
 */

const settings_MODULE_KEY = 'pll/metabox';
const settings_MODULE_CORE_EDITOR_KEY = 'core/editor';
const settings_MODULE_SITE_EDITOR_KEY = 'core/edit-site';
const MODULE_POST_EDITOR_KEY = 'core/edit-post';
const MODULE_CORE_KEY = 'core';
const DEFAULT_STATE = {
	languages: [],
	selectedLanguage: {},
	translatedPosts: {},
	fromPost: null,
	currentTemplatePart: {}
};
const UNTRANSLATABLE_POST_TYPE = (/* unused pure expression or super */ null && (['wp_template']));
const settings_TEMPLATE_PART_SLUG_SEPARATOR = '___'; // Its value must be synchronized with its equivalent in PHP @see PLL_FSE_Template_Slug::SEPARATOR
const settings_TEMPLATE_PART_SLUG_CHECK_LANGUAGE_PATTERN = '[a-z_-]+'; // Its value must be synchronized with it equivalent in PHP @see PLL_FSE_Template_Slug::SEPARATOR


;// CONCATENATED MODULE: ./modules/block-editor/js/sidebar/utils.js
/**
 * WordPress Dependencies
 *
 * @package Polylang-Pro
 */





/**
 * Internal dependencies
 */


/**
 * Converts array of object to a map.
 *
 * @param {array} array Array to convert.
 * @param {*}     key   The key in the object used as key to build the map.
 * @returns {Map}
 */
function convertArrayToMap( array, key ){
	const map = new Map();
	array.reduce(
		function(accumulator, currentValue){
			accumulator.set( currentValue[key], currentValue );
			return accumulator;
		},
		map
	);
	return map;
}

/**
 * Converts map to an associative array.
 *
 * @param {Map} map The map to convert.
 * @returns {Object}
 */
function utils_convertMapToObject( map ){
	const object = {};
	map.forEach(
		function ( value, key, map ) {
			const obj = this;
			this[key] = isBoolean( value ) ? value.toString() : value;
		},
		object
	);
	return object;
}

/**
 * Checks whether the current screen is block-based post type editor.
 *
 * @returns {boolean} True if block editor for post type; false otherwise.
 */
function isPostTypeBlockEditor() {
	return !! document.getElementById( 'editor' );
}

/**
 * Checks whether the current screen is the block-based widgets editor.
 *
 * @returns {boolean} True if we are in the widgets block editor; false otherwise.
 */
function isWidgetsBlockEditor() {
	return !! document.getElementById( 'widgets-editor' );
}

/**
 * Checks whether the current screen is the customizer widgets editor.
 *
 * @returns {boolean} True if we are in the customizer widgets editor; false otherwise.
 */
function isWidgetsCustomizerEditor() {
	return !! document.getElementById( 'customize-controls' );
}


/**
 * Checks whether the current screen is the site editor.
 * Takes in account if Gutenberg is activated.
 *
 * @returns {boolean} True if site editor screen, false otherwise.
 */
function isSiteBlockEditor() {
	return !! ( document.getElementById( 'site-editor' ) || document.getElementById( 'edit-site-editor' ) );
}

/**
 * Returns the post type URL for REST API calls or undefined if the user hasn't the rights.
 *
 * @param {string} name The post type name.
 * @returns {string|undefined}
 */
function getPostsUrl( name ) {
	const postTypes = (0,external_this_wp_data_.select)( 'core' ).getEntitiesByKind( 'postType' );
	const postType = (0,external_lodash_.find)( postTypes, { name } );
	return postType?.baseURL;
}

/**
 * Gets all query string parameters and convert them in a URLSearchParams object.
 *
 * @returns {Object}
 */
function	utils_getSearchParams() {
	// Variable window.location.search is just read for creating and returning a URLSearchParams object to be able to manipulate it more easily.
	if ( ! isEmpty( window.location.search ) ) { // phpcs:ignore WordPressVIPMinimum.JS.Window.location
		return new URLSearchParams( window.location.search ); // phpcs:ignore WordPressVIPMinimum.JS.Window.location
	} else {
		return null;
	}
}

/**
 * Gets selected language.
 *
 * @param {string} lang The post language code.
 * @returns {Object} The selected language.
 */
function getSelectedLanguage( lang ) {
	const languages = select( MODULE_KEY ).getLanguages();
	// Pick up this language as selected in languages list
	return languages.get( lang );
}

/**
 * Gets the default language.
 *
 * @returns {Object} The default Language.
 */
function getDefaultLanguage() {
	const languages = select( MODULE_KEY ).getLanguages();
	return Array.from( languages.values() ).find( lang => lang.is_default_lang );
}

/**
 * Checks if the given language is the default one.
 *
 * @param {string} lang The language code to compare with.
 * @returns {boolean} True if the given language is the default one.
 */
function isDefaultLanguage( lang ) {
	return lang === getDefaultLanguage().slug;
}

/**
 * Gets translated posts.
 *
 * @param {Object}                  translations       The translated posts object with language codes as keys and ids as values.
 * @param {Object.<string, Object>} translations_table The translations table data with language codes as keys and data object as values.
 * @returns {Map}
 */
function utils_getTranslatedPosts( translations, translations_table, lang ) {
	const translationsTable = getTranslationsTable( translations_table, lang );
	const fromPost = select( MODULE_KEY ).getFromPost();
	let translatedPosts = new Map( Object.entries( [] ) );
	if ( ! isUndefined( translations ) ) {
		translatedPosts = new Map( Object.entries( translations ) );
	}
	// phpcs:disable PEAR.Functions.FunctionCallSignature.Indent
	// If we come from another post for creating a new one, we have to update translated posts from the original post
	// to be able to update translations attribute of the post
	if ( ! isNil( fromPost ) && ! isNil( fromPost.id ) ) {
		translationsTable.forEach(
			( translationData, lang ) => {
				if ( ! isNil( translationData.translated_post ) && ! isNil( translationData.translated_post.id ) ) {
					translatedPosts.set( lang, translationData.translated_post.id );
				}
			}
		);
	}
	// phpcs:enable PEAR.Functions.FunctionCallSignature.Indent
	return translatedPosts;
}

/**
 * Gets synchronized posts.
 *
 * @param {Object.<string, boolean>} pll_sync_post The synchronized posts object with language codes as keys and boolean values to say if the post is synchronized or not.
 * @returns {Map}
 */
function getSynchronizedPosts( pll_sync_post ){
	let synchronizedPosts = new Map( Object.entries( [] ) );
	if ( ! isUndefined( pll_sync_post ) ) {
		synchronizedPosts = new Map( Object.entries( pll_sync_post ) );
	}
	return synchronizedPosts;
}

/**
 * Gets translations table.
 *
 * @param {Object.<string, Object>} translationsTableDatas The translations table data object with language codes as keys and data object as values.
 * @param {string} lang The language code.
 * @returns {Map}
 */
function getTranslationsTable( translationsTableDatas, lang ){
	let translationsTable = new Map( Object.entries( [] ) );
	// get translations table datas from post
	if ( ! isUndefined( translationsTableDatas ) ) {
		// Build translations table map with language slug as key
		translationsTable = new Map( Object.entries( translationsTableDatas ) );
	}
	return translationsTable;
}

/**
 * Checks if the given request is for saving.
 *
 * @param {Object} options The initial request.
 * @returns {Boolean} True if the request is for saving.
 */
function isSaveRequest( options ){
	// If data is defined we are in a PUT or POST request method otherwise a GET request method
	// Test options.method property isn't efficient because most of REST request which use fetch API doesn't pass this property.
	// So, test options.data is necessary to know if the REST request is to save datas.
	// However test if options.data is undefined isn't sufficient because some REST request pass a null value as the ServerSideRender Gutenberg component.
	if ( ! (0,external_lodash_.isNil)( options.data ) ) {
		return true;
	} else {
		return false;
	}
}

/**
 * Adds `is_block_editor` parameter to the request in a block editor context.
 *
 * @param {Object} options The initial request.
 */
function addIsBlockEditorToRequest( options ){
	options.path = (0,external_this_wp_url_.addQueryArgs)(
		options.path,
		{
			is_block_editor: true
		}
	);
}

/**
 * Checks if the given request concerns the current post type.
 *
 * Useful when saving a reusable block contained in another post type.
 * Indeed a reusable block is also a post, but its saving request doesn't concern the post currently edited.
 * As we don't know the language of the reusable block when the user triggers the reusable block saving action,
 * we need to pass the current post language to be sure that the reusable block will have a language.
 *
 * @see https://github.com/polylang/polylang/issues/437 - Reusable block has no language when it's saved from another post type editing.
 *
 * @param {Object} options the initial request
 * @returns {boolean} True if the request concerns the current post.
 */
function isCurrentPostRequest( options ){
	// Saving translation data is needed only for all post types.
	// It's done by verifying options.path matches with one of baseURL of all post types
	// and compare current post id with this sent in the request.

	// List of post type baseURLs.
	const postTypeURLs = (0,external_lodash_.map)( (0,external_this_wp_data_.select)( 'core' ).getEntitiesByKind( 'postType' ), (0,external_lodash_.property)( 'baseURL' ) );

	// Id from the post currently edited.
	const postId = (0,external_this_wp_data_.select)( 'core/editor' ).getCurrentPostId();

	// Id from the REST request.
	// options.data never isNil here because it's already verified before in isSaveRequest() function.
	const id = options.data.id;

	// Return true
	// if REST request baseURL matches with one of the known post type baseURLs
	// and the id from the post currently edited corresponds on the id passed to the REST request
	// Return false otherwise
	return -1 !== postTypeURLs.findIndex(
		function( element ) {
			return new RegExp( `${ (0,external_lodash_.escapeRegExp)( element ) }` ).test( options.path ); // phpcs:ignore WordPress.WhiteSpace.OperatorSpacing.NoSpaceBefore, WordPress.WhiteSpace.OperatorSpacing.NoSpaceAfter
		}
	) && postId === id;
}

/**
 * Checks if the given REST request is for the creation of a new template part translation.
 *
 * @param {Object} options The initial request.
 * @returns {Boolean} True if the request concerns a template part creation.
 */
function isTemplatePartTranslationCreationRequest( options ) {
	return 'POST' == options.method
		&&  options.path.match( /^\/wp\/v2\/template-parts(?:\/|\?|$)/ )
		&& ! (0,external_lodash_.isNil)( options.data.from_post )
		&& ! (0,external_lodash_.isNil)( options.data.lang );}

/**
 * Adds language as query string parameter to the given request.
 *
 * @param {Object} options         The initial request.
 * @param {string} currentLanguage The language code to add to the request.
 */
function addLanguageToRequest( options, currentLanguage ){
	const hasLangArg= (0,external_this_wp_url_.hasQueryArg)( options.path, 'lang' );
	const filterLang = (0,external_lodash_.isUndefined)( options.filterLang ) || options.filterLang;
	if ( filterLang && ! hasLangArg ) {
		options.path = (0,external_this_wp_url_.addQueryArgs)(
			options.path,
			{
				lang: currentLanguage
			}
		);
	}
}

/**
 * Adds `include_untranslated` parameter to the request.
 *
 * @param {Object} options The initial request.
 * @returns {void}
 */
function addIncludeUntranslatedParam( options ) {
	options.path = (0,external_this_wp_url_.addQueryArgs)(
		options.path,
		{
			include_untranslated: true
		}
	);
}

/**
 * Use addIncludeUntranslatedParam if the given page is a template part page.
 * Or if the template editing mode is enabled inside post editing.
 *
 * @param {Object} options The initial request.
 * @returns {void}
 */
function maybeRequireIncludeUntranslatedTemplate( options ) {
	const params = ( new URL( document.location ) ).searchParams;
	const postType = params.get( 'postType' );
	const postId = params.get( 'postId' );
	if ( (0,external_lodash_.isFunction)( (0,external_this_wp_data_.select)( MODULE_POST_EDITOR_KEY )?.isEditingTemplate ) ) {
		const isEditingTemplate = (0,external_this_wp_data_.select)( MODULE_POST_EDITOR_KEY )?.isEditingTemplate();
		if ( ( "wp_template_part" === postType && ! (0,external_lodash_.isNil)( postId ) ) || isEditingTemplate ) {
			addIncludeUntranslatedParam( options );
		}
	}
}

/**
 * Returns true if the given post is a template part, false otherwise.
 *
 * @param {Object} post A post object.
 * @returns {boolean} Whether it is a template part or not.
 */
function isTemplatePart( post ) {
	return 'wp_template_part' === post.type;
}

/**
 * Returns the current post type considering the Site Editor or Post Editor.
 *
 * @returns {string} The current post type.
 */
function getCurrentPostType() {
	if ( isSiteBlockEditor() ) {
		return select( MODULE_SITE_EDITOR_KEY ).getEditedPostType();
	}

	return select( MODULE_CORE_EDITOR_KEY ).getCurrentPostType();
}

/**
 * Gets the default language from a translations table.
 *
 * @param {Object} translationsTable The translations table data with language codes as keys and data object as values.
 * @returns {Object} The default language.
 */
function getDefaultLangFromTable( translationsTable ) {
	let defaultLang = {}
	translationsTable.forEach( ( translation ) => {
		if ( translation.is_default_lang ) {
				defaultLang = translation.lang;
			}
		}
	);

	return defaultLang;
}

/**
 * Returns a regular expression ready to use to perform search and replace.
 *
 * @returns {RegExp} The regular expression.
 */
function getLangSlugRegex() {
	let languageCheckPattern = TEMPLATE_PART_SLUG_CHECK_LANGUAGE_PATTERN;
	const languages = select( MODULE_KEY ).getLanguages();
	const languageSlugs = Array.from( languages.keys() );
	if ( ! isEmpty( languageSlugs ) ) {
		languageCheckPattern = languageSlugs.join( '|' );
	}

	return new RegExp( `${TEMPLATE_PART_SLUG_SEPARATOR}(?:${languageCheckPattern})$` );
}

;// CONCATENATED MODULE: ./modules/block-editor/js/sidebar/store/index.js
/**
 * WordPress Dependencies
 *
 * @package Polylang-Pro
 */





/**
 * Internal dependencies
 */



const actions = {
	setLanguages( languages ) {
		return {
			type: 'SET_LANGUAGES',
			languages
		};
	},
	setCurrentUser( currentUser, save = false ) {
		return {
			type: 'SET_CURRENT_USER',
			currentUser,
			save
		};
	},
	setFromPost( fromPost ) {
		return {
			type: 'SET_FROM_POST',
			fromPost,
		};
	},
	fetchFromAPI( options ) {
		return {
			type: 'FETCH_FROM_API',
			options,
		};
	},
	setCurrentTemplatePart( currentTemplatePart ) {
		return {
			type: 'SET_CURRENT_TEMPLATE_PART',
			currentTemplatePart,
		}
	}
};

const store = (0,external_this_wp_data_.registerStore)(
	settings_MODULE_KEY,
	{
		reducer( state = DEFAULT_STATE, action ) {
			switch ( action.type ) {
				case 'SET_LANGUAGES':
					return {
						...state,
						languages: action.languages
					};
				case 'SET_CURRENT_USER':
					if ( action.save ) {
						updateCurrentUser( action.currentUser );
					}
					return {
						...state,
						currentUser: action.currentUser
					};
				case 'SET_FROM_POST':
					return {
						...state,
						fromPost: action.fromPost
					};
				case 'SET_CURRENT_TEMPLATE_PART':
					return {
						...state,
						currentTemplatePart: action.currentTemplatePart
					};
				default:
					return state;
			}
		},
		selectors: {
			getLanguages( state ){
				return state.languages;
			},
			getCurrentUser( state ){
				return state.currentUser;
			},
			getFromPost( state ){
				return state.fromPost;
			},
			getCurrentTemplatePart( state ){
				return state.currentTemplatePart;
			}
		},
		actions,
		controls: {
			FETCH_FROM_API( action ) {
				return external_this_wp_apiFetch_default()( { ...action.options } );
			},
		},
		resolvers: {
			* getLanguages(){
				const path = '/pll/v1/languages';
				const languages = yield actions.fetchFromAPI( { path, filterLang: false } );
				return actions.setLanguages( convertArrayToMap( languages, 'slug' ) );
			},
			* getCurrentUser() {
				const path = '/wp/v2/users/me';
				const currentUser = yield actions.fetchFromAPI( { path, filterLang: true } );
				return actions.setCurrentUser( currentUser );
			},
			* getCurrentTemplatePart() {
				const currentTemplatePart = getCurrentTemplateFromDataStore();
				return actions.setCurrentTemplatePart( currentTemplatePart );
			}
		}
	}
);

/**
 * Wait for the whole post block editor context has been initialized: current post loaded and languages list initialized.
 */
const isBlockPostEditorContextInitialized = () => {
	if ( isNil( select( MODULE_CORE_EDITOR_KEY ) ) ) {
		return Promise.reject( "Polylang languages panel can't be initialized because block editor isn't fully initialized." );
	}

	// save url params espacially when a new translation is creating
	saveURLParams();
	// call to getCurrentUser to force call to resolvers and initialize state
	const currentUser = select( MODULE_KEY ).getCurrentUser();

	/**
	 * Set a promise for waiting for the current post has been fully loaded before making other processes.
	 */
	const isCurrentPostLoaded = new Promise(
		function( resolve ) {
			let unsubscribe = subscribe(
				function() {
					const currentPost = select( MODULE_CORE_EDITOR_KEY ).getCurrentPost();
					if ( ! isEmpty( currentPost ) ) {
						unsubscribe();
						resolve();
					}
				}
			);
		}
	);

	// Wait for current post has been loaded and languages list initialized.
	return Promise.all( [ isCurrentPostLoaded, isLanguagesinitialized ] ).then(
		function() {
			// If we come from another post for creating a new one, we have to update translations from the original post.
			const fromPost = select( MODULE_KEY ).getFromPost();
			if ( ! isNil( fromPost ) && ! isNil( fromPost.id ) ) {
				const lang = select( MODULE_CORE_EDITOR_KEY ).getEditedPostAttribute( 'lang' );
				const translations = select( MODULE_CORE_EDITOR_KEY ).getEditedPostAttribute( 'translations' );
				const translations_table = select( MODULE_CORE_EDITOR_KEY ).getEditedPostAttribute( 'translations_table' );
				const translatedPosts = getTranslatedPosts( translations, translations_table, lang );
				dispatch( MODULE_CORE_EDITOR_KEY ).editPost( { translations: convertMapToObject( translatedPosts ) } );
			}
		}
	);
}

/**
 * Wait for the whole site editor context to be initialized: current template loaded and languages list initialized.
 */
const isSiteEditorContextInitialized = () => {
	// save url params espacially when a new translation is creating
	saveURLParams();

	/**
	 * Set a promise to wait for the current user to be fully loaded before making other processes.
	 */
	const isCurrentUserLoaded = new Promise(
		function( resolve ) {
			let unsubscribe = subscribe(
				function() {
					const currentUser = select( MODULE_KEY ).getCurrentUser();
					if ( ! isNil( currentUser ) && ! isEmpty( currentUser ) ) {
						unsubscribe();
						resolve();
					}
				}
			);
		}
	);

	/**
	 * Set a promise to wait for the current template to be fully loaded before making other processes.
	 * It allows to see if both Site Editor and Core stores are available (@see getCurrentTemplateFromDataStore()).
	 */
	const isTemplatePartLoaded = new Promise(
		function( resolve ) {
			let unsubscribe = subscribe(
				function() {
					const currentTemplatePart = getCurrentTemplateFromDataStore();
					if ( ! isNil( currentTemplatePart ) && ! isEmpty( currentTemplatePart ) ) {
						unsubscribe();
						resolve();
					}
				}
			);
		}
	);

	return Promise.all( [ isCurrentUserLoaded, isTemplatePartLoaded, isLanguagesinitialized ] ).then(
		/**
		 * Sets the duplication of template part to true as default behavior.
		 */
		() => {
			const currentUser = select( MODULE_KEY ).getCurrentUser();

			// If pll_duplicate_content user meta is a string, it have never been created
			// So we initialize it as an object
			if ( isString( currentUser.pll_duplicate_content ) ) {
				currentUser.pll_duplicate_content = {};
			}

			currentUser.pll_duplicate_content['wp_template_part'] = true;

			dispatch( MODULE_KEY ).setCurrentUser(
				{
					pll_duplicate_content: currentUser.pll_duplicate_content
				},
				true
			);
		}
	);
}

/**
 * Set a promise for waiting for the languages list is correctly initialized before making other processes.
 */
const isLanguagesinitialized = new Promise(
	function( resolve ) {
		let unsubscribe = (0,external_this_wp_data_.subscribe)(
			function() {
				const languages = (0,external_this_wp_data_.select)( settings_MODULE_KEY ).getLanguages();
				if ( languages.size > 0 ) {
					unsubscribe();
					resolve();
				}
			}
		);
	}
);

/**
 * Save query string parameters from URL. They could be needed after
 * They could be null if they does not exist
 */
function saveURLParams(){
	// Variable window.location.search isn't use directly
	// Function getSearchParams return an URLSearchParams object for manipulating each parameter
	// Each of them are sanitized below
	const searchParams = getSearchParams( window.location.search ); // phpcs:ignore WordPressVIPMinimum.JS.Window.location
	if ( null !== searchParams ) {
		dispatch( MODULE_KEY ).setFromPost(
			{
				id: wp.sanitize.stripTagsAndEncodeText( searchParams.get( 'from_post' ) ),
				postType: wp.sanitize.stripTagsAndEncodeText( searchParams.get( 'post_type' ) ),
				newLanguage: wp.sanitize.stripTagsAndEncodeText( searchParams.get( 'new_lang' ) )
			}
		);
	}
}

/**
 * Save current user when it is wondered
 *
 * @param {object} currentUser
 */
function updateCurrentUser( currentUser ) {
	external_this_wp_apiFetch_default()(
		{
			path: '/wp/v2/users/me',
			data: currentUser,
			method: 'POST'
		}
	);
}

/**
 * Gets the current template using the Site Editor store and the Core store.
 *
 * @returns {object} The current template object.
 */
function getCurrentTemplateFromDataStore() {
	const currentTemplateId = (0,external_this_wp_data_.select)( settings_MODULE_SITE_EDITOR_KEY )?.getEditedPostId();
	const currentTemplateType = (0,external_this_wp_data_.select)( settings_MODULE_SITE_EDITOR_KEY )?.getEditedPostType();
	return (0,external_this_wp_data_.select)( MODULE_CORE_KEY ).getEntityRecord(
		'postType',
		currentTemplateType,
		currentTemplateId
	);
}

/* harmony default export */ const sidebar_store = ((/* unused pure expression or super */ null && (store)));

;// CONCATENATED MODULE: ./modules/block-editor/js/block-editor-plugin.js
/**
 * WordPress dependencies
 *
 * @package Polylang-Pro
 */




/**
 * External dependencies
 */


/**
 * Internal dependencies
 */




/*
 * Initializes a block editor apiFetch middleware to be able to inject the language in the REST API requests.
 */
external_this_wp_apiFetch_default().use(
	// phpcs:disable PEAR.Functions.FunctionCallSignature.Indent
	( options, next ) => {
		// If options.url is defined, this is not a REST request but a direct call to post.php for legacy metaboxes, for example.
		if ( (0,external_lodash_.isUndefined)( options.url ) ) {
			const currentLangSlug = getCurrentLanguage();
			if ( isSaveRequest( options ) ) {
				options.data.is_block_editor = true;
				if ( ! isCurrentPostRequest( options ) && ! isTemplatePartTranslationCreationRequest( options ) ) {
					options.data.lang = currentLangSlug;
				}
				maybeAddLangSuffixToTemplatePart( options, currentLangSlug );
			} else {
				addLanguageToRequest( options, getCurrentLanguage() );
				maybeRequireIncludeUntranslatedTemplate( options );
				addIsBlockEditorToRequest( options )
			}
		}
		return next( options );
	}
	// phpcs:enable PEAR.Functions.FunctionCallSignature.Indent
);

/**
 * Gets language from store or a fallback javascript global variable.
 *
 * @returns {string}
 */
function getCurrentLanguage(){
	// Post block editor case.
	const postLanguage = (0,external_this_wp_data_.select)( settings_MODULE_CORE_EDITOR_KEY ).getEditedPostAttribute( 'lang' );
	if ( ! (0,external_lodash_.isUndefined)( postLanguage ) && postLanguage ) {
		return postLanguage;
	}

	// Returns the default lang if the current location is a template part list
	// and update pll_block_editor_plugin_settings at the same time.
	const params = ( new URL( document.location ) ).searchParams;
	const postType = params.get( 'postType' );
	const postId = params.get( 'postId' );
	if ( "wp_template_part" === postType && (0,external_lodash_.isNil)( postId ) ) {
		const languages = (0,external_this_wp_data_.select)( settings_MODULE_KEY ).getLanguages();
		if ( languages instanceof Map && languages.size > 0 ) {
			pll_block_editor_plugin_settings.lang = Array.from( languages.values() ).find( lang => lang.is_default_lang );

			return pll_block_editor_plugin_settings.lang.slug;
		}
	}

	// FSE template editor case.
	const template = getCurrentTemplateFromDataStore();
	const templateLanguage = template?.lang;
	if ( ! (0,external_lodash_.isUndefined)( templateLanguage ) && templateLanguage ) {
		return templateLanguage;
	}

	// For the first requests block editor isn't initialized yet.
	// So language is retrieved from a javascript global variable initialized server-side.
	return pll_block_editor_plugin_settings.lang.slug;
}

/**
 * Adds the language suffix to a template part only during creation.
 *
 * @param {object} options Object representing a REST request.
 * @param {string} langSlug The Language slug to add.
 * @return {void}
 */
function maybeAddLangSuffixToTemplatePart( options, langSlug ){
	const restBaseUrl = getPostsUrl( 'wp_template_part' );
	if ( (0,external_lodash_.isUndefined)( restBaseUrl ) ) {
		// The user hasn't the rights to edit template part.
		return;
	}
	const templatePartURLRegExp = new RegExp( (0,external_lodash_.escapeRegExp)( restBaseUrl ) );
	if ( 'POST' == options.method && templatePartURLRegExp.test( options.path ) ) {
		const languages = (0,external_this_wp_data_.select)( settings_MODULE_KEY ).getLanguages();
		const language = languages.get( langSlug );

		if ( ! language.is_default_lang ) {
			// No suffix for default language.
			const langSuffix = settings_TEMPLATE_PART_SLUG_SEPARATOR + langSlug;
			options.data.slug += langSuffix;
		}
	}
}

// Duplicate code of PLL_Admin_Base::admin_print_footer_scripts() to add lang parameter in admin ajax requests in FSE.
if ( typeof jQuery != 'undefined' ) {
	jQuery(
		function( $ ){
			$.ajaxPrefilter( function ( options, originalOptions, jqXHR ) {
				if ( -1 != options.url.indexOf( ajaxurl ) || -1 != ajaxurl.indexOf( options.url ) ) {

				const currentLanguage = getCurrentLanguage();
				const arr = { 'lang' : currentLanguage };

				function addPolylangParametersAsString() {
						const str = 'lang=' + currentLanguage;
						if ( 'undefined' === typeof options.data || '' === options.data.trim() ) {
							// Only Polylang data need to be send. So it could be as a simple query string.
							options.data = str;
						} else {
							/*
							 * In some cases data could be a JSON string like in third party plugins.
							 * So we need not to break their process by adding polylang parameters as valid JSON datas.
							 */
							try {
								options.data = JSON.stringify( Object.assign( JSON.parse( options.data ), arr ) );
							} catch ( exception ) {
								// Add Polylang data to the existing query string.
								options.data = options.data + '&' + str;
							}
						}
					}

					/*
					 * options.processData set to true is the default jQuery process where the data is converted in a query string by using jQuery.param().
					 * This step is done before applying filters. Thus here the options.data is already a string in this case.
					 * @See https://github.com/jquery/jquery/blob/3.5.1/src/ajax.js#L563-L569 jQuery ajax function.
					 * It is the most case WordPress send ajax request this way however third party plugins or themes could be send JSON string.
					 * Use JSON format is recommended in jQuery.param() documentation to be able to send complex data structures.
					 * @See https://api.jquery.com/jquery.param/ jQuery param function.
					 */
					if ( options.processData ) {
						addPolylangParametersAsString();
					} else {
						/*
						 * If options.processData is set to false data could be undefined or pass as a string.
						 * So data as to be processed as if options.processData is set to true.
						 */
						if ( 'undefined' === typeof options.data || 'string' === typeof options.data ) {
							addPolylangParametersAsString();
						} else {
							// Otherwise options.data is probably an object.
							options.data = Object.assign( options.data || {} , arr );
						}
					}
				}
			});
		}
	);
}


})();

this["polylang-pro"] = __webpack_exports__;
/******/ })()
;