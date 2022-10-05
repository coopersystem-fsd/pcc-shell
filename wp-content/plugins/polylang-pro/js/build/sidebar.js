/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ 184:
/***/ ((module, exports) => {

var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
  Copyright (c) 2018 Jed Watson.
  Licensed under the MIT License (MIT), see
  http://jedwatson.github.io/classnames
*/
/* global define */

(function () {
	'use strict';

	var hasOwn = {}.hasOwnProperty;

	function classNames() {
		var classes = [];

		for (var i = 0; i < arguments.length; i++) {
			var arg = arguments[i];
			if (!arg) continue;

			var argType = typeof arg;

			if (argType === 'string' || argType === 'number') {
				classes.push(arg);
			} else if (Array.isArray(arg)) {
				if (arg.length) {
					var inner = classNames.apply(null, arg);
					if (inner) {
						classes.push(inner);
					}
				}
			} else if (argType === 'object') {
				if (arg.toString === Object.prototype.toString) {
					for (var key in arg) {
						if (hasOwn.call(arg, key) && arg[key]) {
							classes.push(key);
						}
					}
				} else {
					classes.push(arg.toString());
				}
			}
		}

		return classes.join(' ');
	}

	if ( true && module.exports) {
		classNames.default = classNames;
		module.exports = classNames;
	} else if (true) {
		// register as 'classnames', consistent with npm package name
		!(__WEBPACK_AMD_DEFINE_ARRAY__ = [], __WEBPACK_AMD_DEFINE_RESULT__ = (function () {
			return classNames;
		}).apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__),
		__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	} else {}
}());


/***/ }),

/***/ 991:
/***/ ((module) => {

module.exports = (function() { return this["lodash"]; }());

/***/ }),

/***/ 514:
/***/ ((module) => {

module.exports = (function() { return this["wp"]["apiFetch"]; }());

/***/ }),

/***/ 893:
/***/ ((module) => {

module.exports = (function() { return this["wp"]["components"]; }());

/***/ }),

/***/ 576:
/***/ ((module) => {

module.exports = (function() { return this["wp"]["compose"]; }());

/***/ }),

/***/ 15:
/***/ ((module) => {

module.exports = (function() { return this["wp"]["data"]; }());

/***/ }),

/***/ 197:
/***/ ((module) => {

module.exports = (function() { return this["wp"]["editPost"]; }());

/***/ }),

/***/ 353:
/***/ ((module) => {

module.exports = (function() { return this["wp"]["editSite"]; }());

/***/ }),

/***/ 293:
/***/ ((module) => {

module.exports = (function() { return this["wp"]["element"]; }());

/***/ }),

/***/ 638:
/***/ ((module) => {

module.exports = (function() { return this["wp"]["htmlEntities"]; }());

/***/ }),

/***/ 122:
/***/ ((module) => {

module.exports = (function() { return this["wp"]["i18n"]; }());

/***/ }),

/***/ 19:
/***/ ((module) => {

module.exports = (function() { return this["wp"]["keycodes"]; }());

/***/ }),

/***/ 571:
/***/ ((module) => {

module.exports = (function() { return this["wp"]["plugins"]; }());

/***/ }),

/***/ 776:
/***/ ((module) => {

module.exports = (function() { return this["wp"]["primitives"]; }());

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

// EXTERNAL MODULE: external {"this":["wp","element"]}
var external_this_wp_element_ = __webpack_require__(293);
// EXTERNAL MODULE: external {"this":["wp","plugins"]}
var external_this_wp_plugins_ = __webpack_require__(571);
// EXTERNAL MODULE: external {"this":["wp","data"]}
var external_this_wp_data_ = __webpack_require__(15);
// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__(991);
// EXTERNAL MODULE: external {"this":["wp","editPost"]}
var external_this_wp_editPost_ = __webpack_require__(197);
// EXTERNAL MODULE: external {"this":["wp","editSite"]}
var external_this_wp_editSite_ = __webpack_require__(353);
// EXTERNAL MODULE: external {"this":["wp","i18n"]}
var external_this_wp_i18n_ = __webpack_require__(122);
// EXTERNAL MODULE: external {"this":["wp","apiFetch"]}
var external_this_wp_apiFetch_ = __webpack_require__(514);
var external_this_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_this_wp_apiFetch_);
// EXTERNAL MODULE: external {"this":["wp","url"]}
var external_this_wp_url_ = __webpack_require__(470);
// EXTERNAL MODULE: external {"this":["wp","components"]}
var external_this_wp_components_ = __webpack_require__(893);
// EXTERNAL MODULE: external {"this":["wp","compose"]}
var external_this_wp_compose_ = __webpack_require__(576);
;// CONCATENATED MODULE: ./modules/block-editor/js/sidebar/components/confirmation-modal/index.js


/**
 * Wordpress dependencies
 *
 * @package Polylang-Pro
 */






class ConfirmationModal extends external_this_wp_element_.Component {
  constructor() {
    super(...arguments);
    this.confirmButton = (0,external_this_wp_element_.createRef)();
  }

  componentDidMount() {
    this.confirmButton.current.focus();
  }

  render() {
    const {
      idPrefix,
      title,
      updateState,
      handleChange,
      children
    } = this.props; // phpcs:disable WordPress.WhiteSpace.OperatorSpacing.NoSpaceBefore, WordPress.WhiteSpace.OperatorSpacing.NoSpaceAfter

    return (0,external_this_wp_element_.createElement)(external_this_wp_components_.Modal, {
      title: title,
      className: "confirmBox",
      onRequestClose: updateState,
      shouldCloseOnEsc: false,
      shouldCloseOnClickOutside: false,
      focusOnMount: false
    }, children, (0,external_this_wp_element_.createElement)(external_this_wp_components_.ButtonGroup, {
      className: "buttons"
    }, (0,external_this_wp_element_.createElement)(external_this_wp_components_.Button, {
      id: `${idPrefix}_confirm`,
      ref: this.confirmButton,
      isPrimary: true,
      onClick: event => {
        handleChange(event);
        updateState();
      }
    }, (0,external_this_wp_i18n_.__)('OK', 'polylang-pro')), (0,external_this_wp_element_.createElement)(external_this_wp_components_.Button, {
      id: `${idPrefix}_cancel`,
      isSecondary: true,
      onClick: () => updateState()
    }, (0,external_this_wp_i18n_.__)('Cancel', 'polylang-pro')))); // phpcs:enable WordPress.WhiteSpace.OperatorSpacing.NoSpaceBefore, WordPress.WhiteSpace.OperatorSpacing.NoSpaceAfter
  }

}
/**
 * Control the execution of a component's function with a confirmation modal.
 *
 * @param {string} idPrefix Used to identify the modal's buttons. {@see ConfirmationModal.render()}
 * @param {React.Component} ModalContent Component which contains the content displayed in the confirmation modal.
 * @param {handleChangeCallback} handleChangeCallback Action triggered when we valid the confirmation modal by clicking the confirmation button.
 *
 * @return {Function} Higher-order component.
 */


const withConfirmation = function (idPrefix, ModalContent, handleChangeCallback) {
  return (0,external_this_wp_compose_.createHigherOrderComponent)(
  /**
   * @function Higher-Order Component
   *
   * @param {React.Component} WrappedComponent The component which needs a confirmation to change to its new value.
   * @param {string} WrappedComponent.labelConfirmationModal Used for both WrappedComponent and ConfirmationModal titles.
   * @param {WrappedComponent.getChangeValueCallback} WrappedComponent.getChangeValue
   * @param {WrappedComponent.bypassConfirmationCallback} WrappedComponent.bypassConfirmation
   * @return {WPComponent}
   */
  WrappedComponent => {
    class enhanceComponent extends external_this_wp_element_.Component {
      // phpcs:ignore PEAR.Functions.FunctionCallSignature.Indent
      constructor() {
        super(...arguments);
        this.state = {
          isOpen: false,
          changeValue: null
        };
        this.handleChange = this.handleChange.bind(this);
      }

      handleChange(event) {
        let changeValue = WrappedComponent.getChangeValue(event); // Process specific case for the template part deletion confirmation.

        const currentPost = this.props.currentPost;

        if (!(0,external_lodash_.isNil)(currentPost)) {
          changeValue = {
            templateId: changeValue,
            currentPost: currentPost
          };
        }

        if (!(0,external_lodash_.isUndefined)(WrappedComponent.bypassConfirmation) && WrappedComponent.bypassConfirmation(this.props.translationData)) {
          handleChangeCallback(changeValue);
        } else {
          this.setState({
            isOpen: true,
            changeValue: changeValue
          });
        }
      }

      render() {
        var _this$props$translati;

        // isDefaultLang property is only available in translationData which comes from template post type.
        const isDefaultLang = (_this$props$translati = this.props.translationData) === null || _this$props$translati === void 0 ? void 0 : _this$props$translati.is_default_lang;
        const passThroughProps = this.props;
        const wrappedComponentProps = Object.assign({}, { ...passThroughProps
        }, {
          handleChange: this.handleChange
        }); // phpcs:disable WordPress.WhiteSpace.OperatorSpacing.NoSpaceBefore, WordPress.WhiteSpace.OperatorSpacing.NoSpaceAfter

        return (0,external_this_wp_element_.createElement)(external_this_wp_element_.Fragment, null, (0,external_this_wp_element_.createElement)(WrappedComponent, wrappedComponentProps), this.state.isOpen && (0,external_this_wp_element_.createElement)(ConfirmationModal, {
          title: WrappedComponent.labelConfirmationModal,
          idPrefix: idPrefix,
          handleChange: () => handleChangeCallback(this.state.changeValue),
          updateState: () => this.setState({
            isOpen: false,
            changeValue: null
          })
        }, (0,external_this_wp_element_.createElement)(ModalContent, !(0,external_lodash_.isNil)(isDefaultLang) ? {
          isDefaultLang: isDefaultLang
        } : {}))); // phpcs:enable WordPress.WhiteSpace.OperatorSpacing.NoSpaceBefore, WordPress.WhiteSpace.OperatorSpacing.NoSpaceAfter
      }

    }

    ; // phpcs:disable PEAR.Functions.FunctionCallSignature.Indent

    enhanceComponent.bypassConfirmation = WrappedComponent.bypassConfirmation;
    enhanceComponent.getChangeValue = WrappedComponent.getChangeValue;
    return enhanceComponent; // phpcs:enable PEAR.Functions.FunctionCallSignature.Indent
  }, 'withConfirmation');
};
/**
 * Callback to trigger the action to change the value in the Component wrapped by the withConfirmation HOC.
 *
 * @callback handleChangeCallback
 * @param {string|Object} changeValue The value computed by {@see WrappedComponent.getChangeValueCallback} and could be completed by the withConfirmation HOC handleChange function.
 */

/**
 * Callback to retrieve the value to change from the Component wrapped by the withConfirmation HOC.
 *
 * @callback WrappedComponent.getChangeValueCallback
 * @param {Event} event A DOM triggered by the wrapped component.
 */

/**
 * Optional callback to check whether the Component wrapped by the withConfirmation HOC need to open the confirmation modal or not.
 *
 * @callback WrappedComponent.bypassConfirmationCallback
 * @param {Object} [translationData] A entry which represents the translation of the current post in a language {@see PLL_REST_Post::get_translations_table()}.
 */


/* harmony default export */ const confirmation_modal = (withConfirmation);
;// CONCATENATED MODULE: ./modules/block-editor/js/sidebar/settings.js
/**
 * Module Constants
 *
 * @package Polylang-Pro
 */
const MODULE_KEY = 'pll/metabox';
const MODULE_CORE_EDITOR_KEY = 'core/editor';
const MODULE_SITE_EDITOR_KEY = 'core/edit-site';
const settings_MODULE_POST_EDITOR_KEY = 'core/edit-post';
const MODULE_CORE_KEY = 'core';
const DEFAULT_STATE = {
  languages: [],
  selectedLanguage: {},
  translatedPosts: {},
  fromPost: null,
  currentTemplatePart: {}
};
const UNTRANSLATABLE_POST_TYPE = ['wp_template'];
const TEMPLATE_PART_SLUG_SEPARATOR = '___'; // Its value must be synchronized with its equivalent in PHP @see PLL_FSE_Template_Slug::SEPARATOR

const TEMPLATE_PART_SLUG_CHECK_LANGUAGE_PATTERN = '[a-z_-]+'; // Its value must be synchronized with it equivalent in PHP @see PLL_FSE_Template_Slug::SEPARATOR


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

function convertArrayToMap(array, key) {
  const map = new Map();
  array.reduce(function (accumulator, currentValue) {
    accumulator.set(currentValue[key], currentValue);
    return accumulator;
  }, map);
  return map;
}
/**
 * Converts map to an associative array.
 *
 * @param {Map} map The map to convert.
 * @returns {Object}
 */

function convertMapToObject(map) {
  const object = {};
  map.forEach(function (value, key, map) {
    const obj = this;
    this[key] = (0,external_lodash_.isBoolean)(value) ? value.toString() : value;
  }, object);
  return object;
}
/**
 * Checks whether the current screen is block-based post type editor.
 *
 * @returns {boolean} True if block editor for post type; false otherwise.
 */

function isPostTypeBlockEditor() {
  return !!document.getElementById('editor');
}
/**
 * Checks whether the current screen is the block-based widgets editor.
 *
 * @returns {boolean} True if we are in the widgets block editor; false otherwise.
 */

function isWidgetsBlockEditor() {
  return !!document.getElementById('widgets-editor');
}
/**
 * Checks whether the current screen is the customizer widgets editor.
 *
 * @returns {boolean} True if we are in the customizer widgets editor; false otherwise.
 */

function isWidgetsCustomizerEditor() {
  return !!document.getElementById('customize-controls');
}
/**
 * Checks whether the current screen is the site editor.
 * Takes in account if Gutenberg is activated.
 *
 * @returns {boolean} True if site editor screen, false otherwise.
 */

function isSiteBlockEditor() {
  return !!(document.getElementById('site-editor') || document.getElementById('edit-site-editor'));
}
/**
 * Returns the post type URL for REST API calls or undefined if the user hasn't the rights.
 *
 * @param {string} name The post type name.
 * @returns {string|undefined}
 */

function getPostsUrl(name) {
  const postTypes = (0,external_this_wp_data_.select)('core').getEntitiesByKind('postType');
  const postType = (0,external_lodash_.find)(postTypes, {
    name
  });
  return postType === null || postType === void 0 ? void 0 : postType.baseURL;
}
/**
 * Gets all query string parameters and convert them in a URLSearchParams object.
 *
 * @returns {Object}
 */

function getSearchParams() {
  // Variable window.location.search is just read for creating and returning a URLSearchParams object to be able to manipulate it more easily.
  if (!(0,external_lodash_.isEmpty)(window.location.search)) {
    // phpcs:ignore WordPressVIPMinimum.JS.Window.location
    return new URLSearchParams(window.location.search); // phpcs:ignore WordPressVIPMinimum.JS.Window.location
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

function getSelectedLanguage(lang) {
  const languages = (0,external_this_wp_data_.select)(MODULE_KEY).getLanguages(); // Pick up this language as selected in languages list

  return languages.get(lang);
}
/**
 * Gets the default language.
 *
 * @returns {Object} The default Language.
 */

function getDefaultLanguage() {
  const languages = (0,external_this_wp_data_.select)(MODULE_KEY).getLanguages();
  return Array.from(languages.values()).find(lang => lang.is_default_lang);
}
/**
 * Checks if the given language is the default one.
 *
 * @param {string} lang The language code to compare with.
 * @returns {boolean} True if the given language is the default one.
 */

function isDefaultLanguage(lang) {
  return lang === getDefaultLanguage().slug;
}
/**
 * Gets translated posts.
 *
 * @param {Object}                  translations       The translated posts object with language codes as keys and ids as values.
 * @param {Object.<string, Object>} translations_table The translations table data with language codes as keys and data object as values.
 * @returns {Map}
 */

function getTranslatedPosts(translations, translations_table, lang) {
  const translationsTable = getTranslationsTable(translations_table, lang);
  const fromPost = (0,external_this_wp_data_.select)(MODULE_KEY).getFromPost();
  let translatedPosts = new Map(Object.entries([]));

  if (!(0,external_lodash_.isUndefined)(translations)) {
    translatedPosts = new Map(Object.entries(translations));
  } // phpcs:disable PEAR.Functions.FunctionCallSignature.Indent
  // If we come from another post for creating a new one, we have to update translated posts from the original post
  // to be able to update translations attribute of the post


  if (!(0,external_lodash_.isNil)(fromPost) && !(0,external_lodash_.isNil)(fromPost.id)) {
    translationsTable.forEach((translationData, lang) => {
      if (!(0,external_lodash_.isNil)(translationData.translated_post) && !(0,external_lodash_.isNil)(translationData.translated_post.id)) {
        translatedPosts.set(lang, translationData.translated_post.id);
      }
    });
  } // phpcs:enable PEAR.Functions.FunctionCallSignature.Indent


  return translatedPosts;
}
/**
 * Gets synchronized posts.
 *
 * @param {Object.<string, boolean>} pll_sync_post The synchronized posts object with language codes as keys and boolean values to say if the post is synchronized or not.
 * @returns {Map}
 */

function getSynchronizedPosts(pll_sync_post) {
  let synchronizedPosts = new Map(Object.entries([]));

  if (!(0,external_lodash_.isUndefined)(pll_sync_post)) {
    synchronizedPosts = new Map(Object.entries(pll_sync_post));
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

function getTranslationsTable(translationsTableDatas, lang) {
  let translationsTable = new Map(Object.entries([])); // get translations table datas from post

  if (!(0,external_lodash_.isUndefined)(translationsTableDatas)) {
    // Build translations table map with language slug as key
    translationsTable = new Map(Object.entries(translationsTableDatas));
  }

  return translationsTable;
}
/**
 * Checks if the given request is for saving.
 *
 * @param {Object} options The initial request.
 * @returns {Boolean} True if the request is for saving.
 */

function isSaveRequest(options) {
  // If data is defined we are in a PUT or POST request method otherwise a GET request method
  // Test options.method property isn't efficient because most of REST request which use fetch API doesn't pass this property.
  // So, test options.data is necessary to know if the REST request is to save datas.
  // However test if options.data is undefined isn't sufficient because some REST request pass a null value as the ServerSideRender Gutenberg component.
  if (!isNil(options.data)) {
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

function addIsBlockEditorToRequest(options) {
  options.path = addQueryArgs(options.path, {
    is_block_editor: true
  });
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

function isCurrentPostRequest(options) {
  // Saving translation data is needed only for all post types.
  // It's done by verifying options.path matches with one of baseURL of all post types
  // and compare current post id with this sent in the request.
  // List of post type baseURLs.
  const postTypeURLs = map(select('core').getEntitiesByKind('postType'), property('baseURL')); // Id from the post currently edited.

  const postId = select('core/editor').getCurrentPostId(); // Id from the REST request.
  // options.data never isNil here because it's already verified before in isSaveRequest() function.

  const id = options.data.id; // Return true
  // if REST request baseURL matches with one of the known post type baseURLs
  // and the id from the post currently edited corresponds on the id passed to the REST request
  // Return false otherwise

  return -1 !== postTypeURLs.findIndex(function (element) {
    return new RegExp(`${escapeRegExp(element)}`).test(options.path); // phpcs:ignore WordPress.WhiteSpace.OperatorSpacing.NoSpaceBefore, WordPress.WhiteSpace.OperatorSpacing.NoSpaceAfter
  }) && postId === id;
}
/**
 * Checks if the given REST request is for the creation of a new template part translation.
 *
 * @param {Object} options The initial request.
 * @returns {Boolean} True if the request concerns a template part creation.
 */

function isTemplatePartTranslationCreationRequest(options) {
  return 'POST' == options.method && options.path.match(/^\/wp\/v2\/template-parts(?:\/|\?|$)/) && !isNil(options.data.from_post) && !isNil(options.data.lang);
}
/**
 * Adds language as query string parameter to the given request.
 *
 * @param {Object} options         The initial request.
 * @param {string} currentLanguage The language code to add to the request.
 */

function addLanguageToRequest(options, currentLanguage) {
  const hasLangArg = hasQueryArg(options.path, 'lang');
  const filterLang = isUndefined(options.filterLang) || options.filterLang;

  if (filterLang && !hasLangArg) {
    options.path = addQueryArgs(options.path, {
      lang: currentLanguage
    });
  }
}
/**
 * Adds `include_untranslated` parameter to the request.
 *
 * @param {Object} options The initial request.
 * @returns {void}
 */

function addIncludeUntranslatedParam(options) {
  options.path = addQueryArgs(options.path, {
    include_untranslated: true
  });
}
/**
 * Use addIncludeUntranslatedParam if the given page is a template part page.
 * Or if the template editing mode is enabled inside post editing.
 *
 * @param {Object} options The initial request.
 * @returns {void}
 */

function maybeRequireIncludeUntranslatedTemplate(options) {
  var _select;

  const params = new URL(document.location).searchParams;
  const postType = params.get('postType');
  const postId = params.get('postId');

  if (isFunction((_select = select(MODULE_POST_EDITOR_KEY)) === null || _select === void 0 ? void 0 : _select.isEditingTemplate)) {
    var _select2;

    const isEditingTemplate = (_select2 = select(MODULE_POST_EDITOR_KEY)) === null || _select2 === void 0 ? void 0 : _select2.isEditingTemplate();

    if ("wp_template_part" === postType && !isNil(postId) || isEditingTemplate) {
      addIncludeUntranslatedParam(options);
    }
  }
}
/**
 * Returns true if the given post is a template part, false otherwise.
 *
 * @param {Object} post A post object.
 * @returns {boolean} Whether it is a template part or not.
 */

function isTemplatePart(post) {
  return 'wp_template_part' === post.type;
}
/**
 * Returns the current post type considering the Site Editor or Post Editor.
 *
 * @returns {string} The current post type.
 */

function getCurrentPostType() {
  if (isSiteBlockEditor()) {
    return (0,external_this_wp_data_.select)(MODULE_SITE_EDITOR_KEY).getEditedPostType();
  }

  return (0,external_this_wp_data_.select)(MODULE_CORE_EDITOR_KEY).getCurrentPostType();
}
/**
 * Gets the default language from a translations table.
 *
 * @param {Object} translationsTable The translations table data with language codes as keys and data object as values.
 * @returns {Object} The default language.
 */

function getDefaultLangFromTable(translationsTable) {
  let defaultLang = {};
  translationsTable.forEach(translation => {
    if (translation.is_default_lang) {
      defaultLang = translation.lang;
    }
  });
  return defaultLang;
}
/**
 * Returns a regular expression ready to use to perform search and replace.
 *
 * @returns {RegExp} The regular expression.
 */

function getLangSlugRegex() {
  let languageCheckPattern = TEMPLATE_PART_SLUG_CHECK_LANGUAGE_PATTERN;
  const languages = (0,external_this_wp_data_.select)(MODULE_KEY).getLanguages();
  const languageSlugs = Array.from(languages.keys());

  if (!(0,external_lodash_.isEmpty)(languageSlugs)) {
    languageCheckPattern = languageSlugs.join('|');
  }

  return new RegExp(`${TEMPLATE_PART_SLUG_SEPARATOR}(?:${languageCheckPattern})$`);
}
// EXTERNAL MODULE: external {"this":["wp","primitives"]}
var external_this_wp_primitives_ = __webpack_require__(776);
;// CONCATENATED MODULE: ./modules/block-editor/js/icons/library/duplication.js


/**
 * Duplication icon - admin-page Dashicon.
 *
 * @package Polylang-Pro
 */

/**
 * WordPress dependencies
 */


const isPrimitivesComponents = !(0,external_lodash_.isUndefined)(wp.primitives);
const duplication = isPrimitivesComponents ? (0,external_this_wp_element_.createElement)(external_this_wp_primitives_.SVG, {
  width: "20",
  height: "20",
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 20 20"
}, (0,external_this_wp_element_.createElement)(external_this_wp_primitives_.Path, {
  d: "M6 15v-13h10v13h-10zM5 16h8v2h-10v-13h2v11z"
})) : 'admin-page';
/* harmony default export */ const library_duplication = (duplication);
;// CONCATENATED MODULE: ./modules/block-editor/js/icons/library/pencil.js


/**
 * Pencil icon - edit Dashicon.
 *
 * @package Polylang-Pro
 */

/**
 * WordPress dependencies
 */


const pencil_isPrimitivesComponents = !(0,external_lodash_.isUndefined)(wp.primitives);
const pencil = pencil_isPrimitivesComponents ? (0,external_this_wp_element_.createElement)(external_this_wp_primitives_.SVG, {
  width: "20",
  height: "20",
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 20 20"
}, (0,external_this_wp_element_.createElement)(external_this_wp_primitives_.Path, {
  d: "M13.89 3.39l2.71 2.72c0.46 0.46 0.42 1.24 0.030 1.64l-8.010 8.020-5.56 1.16 1.16-5.58s7.6-7.63 7.99-8.030c0.39-0.39 1.22-0.39 1.68 0.070zM11.16 6.18l-5.59 5.61 1.11 1.11 5.54-5.65zM8.19 14.41l5.58-5.6-1.070-1.080-5.59 5.6z"
})) : 'edit';
/* harmony default export */ const library_pencil = (pencil);
;// CONCATENATED MODULE: ./modules/block-editor/js/icons/library/plus.js


/**
 * Plus icon - plus Dashicon.
 *
 * @package Polylang-Pro
 */

/**
 * WordPress dependencies
 */


const plus_isPrimitivesComponents = !(0,external_lodash_.isUndefined)(wp.primitives);
const plus = plus_isPrimitivesComponents ? (0,external_this_wp_element_.createElement)(external_this_wp_primitives_.SVG, {
  width: "20",
  height: "20",
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 20 20"
}, (0,external_this_wp_element_.createElement)(external_this_wp_primitives_.Path, {
  d: "M17 7v3h-5v5h-3v-5h-5v-3h5v-5h3v5h5z"
})) : 'plus';
/* harmony default export */ const library_plus = (plus);
;// CONCATENATED MODULE: ./modules/block-editor/js/icons/library/synchronization.js


/**
 * Synchronization icon - controls-repeat Dashicon.
 *
 * @package Polylang-Pro
 */

/**
 * WordPress dependencies
 */


const synchronization_isPrimitivesComponents = !(0,external_lodash_.isUndefined)(wp.primitives);
const synchronization = synchronization_isPrimitivesComponents ? (0,external_this_wp_element_.createElement)(external_this_wp_primitives_.SVG, {
  width: "20",
  height: "20",
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 20 20"
}, (0,external_this_wp_element_.createElement)(external_this_wp_primitives_.Path, {
  d: "M5 7v3l-2 1.5v-6.5h11v-2l4 3.010-4 2.99v-2h-9zM15 13v-3l2-1.5v6.5h-11v2l-4-3.010 4-2.99v2h9z"
})) : 'controls-repeat';
/* harmony default export */ const library_synchronization = (synchronization);
;// CONCATENATED MODULE: ./modules/block-editor/js/icons/library/translation.js


/**
 * Translation icon - translation Dashicon.
 *
 * @package Polylang-Pro
 */

/**
 * WordPress dependencies
 */


const translation_isPrimitivesComponents = !(0,external_lodash_.isUndefined)(wp.primitives);
const translation = translation_isPrimitivesComponents ? (0,external_this_wp_element_.createElement)(external_this_wp_primitives_.SVG, {
  width: "20",
  height: "20",
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 20 20"
}, (0,external_this_wp_element_.createElement)(external_this_wp_primitives_.Path, {
  d: "M11 7H9.49c-.63 0-1.25.3-1.59.7L7 5H4.13l-2.39 7h1.69l.74-2H7v4H2c-1.1 0-2-.9-2-2V5c0-1.1.9-2 2-2h7c1.1 0 2 .9 2 2v2zM6.51 9H4.49l1-2.93zM10 8h7c1.1 0 2 .9 2 2v7c0 1.1-.9 2-2 2h-7c-1.1 0-2-.9-2-2v-7c0-1.1.9-2 2-2zm7.25 5v-1.08h-3.17V9.75h-1.16v2.17H9.75V13h1.28c.11.85.56 1.85 1.28 2.62-.87.36-1.89.62-2.31.62-.01.02.22.97.2 1.46.84 0 2.21-.5 3.28-1.15 1.09.65 2.48 1.15 3.34 1.15-.02-.49.2-1.44.2-1.46-.43 0-1.49-.27-2.38-.63.7-.77 1.14-1.77 1.25-2.61h1.36zm-3.81 1.93c-.5-.46-.85-1.13-1.01-1.93h2.09c-.17.8-.51 1.47-1 1.93l-.04.03s-.03-.02-.04-.03z"
})) : 'translation';
/* harmony default export */ const library_translation = (translation);
;// CONCATENATED MODULE: ./modules/block-editor/js/icons/library/trash.js


/**
 * Trash icon - trash Dashicon.
 *
 * @package Polylang-Pro
 */

/**
 * WordPress dependencies
 */


const trash_isPrimitivesComponents = !(0,external_lodash_.isUndefined)(wp.primitives);
const trash = trash_isPrimitivesComponents ? (0,external_this_wp_element_.createElement)(external_this_wp_primitives_.SVG, {
  width: "20",
  height: "20",
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 20 20"
}, (0,external_this_wp_element_.createElement)(external_this_wp_primitives_.Path, {
  d: "M12 4h3c.6 0 1 .4 1 1v1H3V5c0-.6.5-1 1-1h3c.2-1.1 1.3-2 2.5-2s2.3.9 2.5 2zM8 4h3c-.2-.6-.9-1-1.5-1S8.2 3.4 8 4zM4 7h11l-.9 10.1c0 .5-.5.9-1 .9H5.9c-.5 0-.9-.4-1-.9L4 7z"
})) : 'trash';
/* harmony default export */ const library_trash = (trash);
;// CONCATENATED MODULE: ./modules/block-editor/js/icons/library/star.js


/**
 * Star icon - star-filled Dashicon.
 *
 * @package Polylang-Pro
 */

/**
 * WordPress dependencies
 */


const star_isPrimitivesComponents = !(0,external_lodash_.isUndefined)(wp.primitives);
const star = star_isPrimitivesComponents ? (0,external_this_wp_element_.createElement)(external_this_wp_primitives_.SVG, {
  width: "20",
  height: "20",
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 20 20"
}, (0,external_this_wp_element_.createElement)(external_this_wp_primitives_.Path, {
  d: "m10 1 3 6 6 .75-4.12 4.62L16 19l-6-3-6 3 1.13-6.63L1 7.75 7 7z"
})) : 'star-filled';
/* harmony default export */ const library_star = (star);
;// CONCATENATED MODULE: ./modules/block-editor/js/icons/index.js
/**
 * Icons library
 *
 * @package Polylang-Pro
 */







;// CONCATENATED MODULE: ./modules/block-editor/js/components/language-flag.js


/**
 * @package Polylang-Pro
 */

/**
 * External dependencies.
 */

/**
 * Internal dependencies.
 */


/**
 * Display a flag icon for a given language.
 *
 * @since 3.1
 * @since 3.2 Now its own component.
 *
 * @param {Object} A language object.
 *
 * @return {Object}
 */

function LanguageFlag(_ref) {
  let {
    language
  } = _ref;
  return !(0,external_lodash_.isNil)(language) ? !(0,external_lodash_.isEmpty)(language.flag_url) ? (0,external_this_wp_element_.createElement)("span", {
    className: "pll-select-flag"
  }, (0,external_this_wp_element_.createElement)("img", {
    src: language.flag_url,
    alt: language.name,
    title: language.name,
    className: "flag"
  })) : (0,external_this_wp_element_.createElement)("abbr", null, language.slug, (0,external_this_wp_element_.createElement)("span", {
    className: "screen-reader-text"
  }, language.name)) : (0,external_this_wp_element_.createElement)("span", {
    className: "pll-translation-icon"
  }, library_translation);
}

/* harmony default export */ const language_flag = (LanguageFlag);
;// CONCATENATED MODULE: ./modules/block-editor/js/components/language-dropdown.js


/**
 * @package Polylang-Pro
 */
// External dependencies

/**
 * Displays a dropdown to select a language.
 *
 * @since 3.1
 *
 * @param {Function} handleChange Callback to be executed when language changes.
 * @param {mixed} children Child components to be used as select options.
 * @param {Object} selectedLanguage An object representing a Polylang Language. Default to null.
 * @param {string} Default value to be selected if the selected language is not provided. Default to an empty string.
 *
 * @return {Object} A dropdown selector for languages.
 */

function LanguageDropdown(_ref) {
  let {
    handleChange,
    children,
    selectedLanguage = null,
    defaultValue = ''
  } = _ref;
  const selectedLanguageSlug = selectedLanguage !== null && selectedLanguage !== void 0 && selectedLanguage.slug ? selectedLanguage.slug : defaultValue;
  return (0,external_this_wp_element_.createElement)("div", {
    id: "select-post-language"
  }, (0,external_this_wp_element_.createElement)(language_flag, {
    language: selectedLanguage
  }), children && (0,external_this_wp_element_.createElement)("select", {
    value: selectedLanguageSlug,
    onChange: event => handleChange(event),
    id: "pll_post_lang_choice",
    name: "pll_post_lang_choice",
    className: "post_lang_choice"
  }, children));
}
/**
 * Map languages objects as options for a <select> tag.
 *
 * @since 3.1
 *
 * @param {mixed} languages An iterable object containing languages objects.
 *
 * @return {Object} A list of <option> tags to be used in a <select> tag.
 */


function LanguagesOptionsList(_ref2) {
  let {
    languages
  } = _ref2;
  return Array.from(languages.values()).map(_ref3 => {
    let {
      slug,
      name,
      w3c
    } = _ref3;
    return (0,external_this_wp_element_.createElement)("option", {
      value: slug,
      lang: w3c,
      key: slug
    }, name);
  });
}


;// CONCATENATED MODULE: ./modules/block-editor/js/sidebar/components/switcher/index.js


/**
 * WordPress dependencies
 *
 * @package Polylang-Pro
 */






/**
 * Internal dependencies
 */






class Switcher extends external_this_wp_element_.Component {
  static bypassConfirmation() {
    const editor = (0,external_this_wp_data_.select)(MODULE_CORE_EDITOR_KEY);
    return !editor.getEditedPostAttribute('title') && !editor.getEditedPostContent() && !editor.getEditedPostAttribute('excerpt');
  }

  static getChangeValue(event) {
    return event.target.value;
  }
  /**
   * Manage language choice in the dropdown list
   *
   * @param language New language slug.
   */


  static handleLanguageChange(language) {
    const oldLanguageSlug = (0,external_this_wp_data_.select)(MODULE_CORE_EDITOR_KEY).getEditedPostAttribute('lang');
    const postId = (0,external_this_wp_data_.select)(MODULE_CORE_EDITOR_KEY).getCurrentPostId();
    const languages = (0,external_this_wp_data_.select)(MODULE_KEY).getLanguages();
    const newLanguage = languages.get(language);
    const oldSelectedLanguage = getSelectedLanguage(oldLanguageSlug);
    const pll_sync_post = (0,external_this_wp_data_.select)(MODULE_CORE_EDITOR_KEY).getEditedPostAttribute('pll_sync_post');
    const synchronizedPosts = getSynchronizedPosts(pll_sync_post);
    const translations_table = (0,external_this_wp_data_.select)(MODULE_CORE_EDITOR_KEY).getEditedPostAttribute('translations_table');
    const translations = (0,external_this_wp_data_.select)(MODULE_CORE_EDITOR_KEY).getEditedPostAttribute('translations');
    const translatedPosts = getTranslatedPosts(translations, translations_table, oldSelectedLanguage.slug);
    const translationsTable = getTranslationsTable(translations_table, oldSelectedLanguage.slug); // The translated post of the previous selected language must be deleted

    translatedPosts.delete(oldSelectedLanguage.slug); // Replace translated post for the new language

    translatedPosts.set(newLanguage.slug, postId); // The current post is synchronized itself and synchronization must be deleted for the previous language
    // to ensure it will be not synchronized with the new language

    synchronizedPosts.delete(oldSelectedLanguage.slug); // Update translations table
    // Add old selected language datas - only datas needed just to update visually the metabox

    const oldTranslationData = translationsTable.get(oldSelectedLanguage.slug);
    translationsTable.set(oldSelectedLanguage.slug, {
      can_synchronize: oldTranslationData.can_synchronize,
      lang: oldTranslationData.lang,
      links: {
        add_link: oldTranslationData.links.add_link
      }
    }); // Update some new language datas from the old selected language datas

    const newTranslationData = translationsTable.get(newLanguage.slug);
    translationsTable.set(newLanguage.slug, {
      can_synchronize: newTranslationData.can_synchronize,
      lang: newTranslationData.lang,
      links: oldTranslationData.links,
      translated_post: oldTranslationData.translated_post
    }); // Update the global javascript variable for maintaining it updated outside block editor context

    pll_block_editor_plugin_settings = newLanguage; // And save changes in store

    (0,external_this_wp_data_.dispatch)(MODULE_CORE_EDITOR_KEY).editPost({
      lang: newLanguage.slug
    });
    (0,external_this_wp_data_.dispatch)(MODULE_CORE_EDITOR_KEY).editPost({
      pll_sync_post: convertMapToObject(synchronizedPosts)
    });
    (0,external_this_wp_data_.dispatch)(MODULE_CORE_EDITOR_KEY).editPost({
      translations: convertMapToObject(translatedPosts)
    });
    (0,external_this_wp_data_.dispatch)(MODULE_CORE_EDITOR_KEY).editPost({
      translations_table: convertMapToObject(translationsTable)
    }); // Need to save post for recalculating permalink

    (0,external_this_wp_data_.dispatch)(MODULE_CORE_EDITOR_KEY).savePost();
    Switcher.forceLanguageSave(oldSelectedLanguage.slug);
    Switcher.invalidateParentPagesStoredInCore();
  }
  /**
   * Even if no content has been written, Polylang back-end code needs the correct language to send back the correct metadatas. (e.g.: Attachable Medias).
   *
   * @since 3.0
   *
   * @param {string} lang A language slug.
   */


  static forceLanguageSave(lang) {
    const editor = (0,external_this_wp_data_.select)(MODULE_CORE_EDITOR_KEY);

    if (!editor.getEditedPostAttribute('title') && !editor.getEditedPostContent() && !editor.getEditedPostAttribute('excerpt')) {
      external_this_wp_apiFetch_default()({
        path: (0,external_this_wp_url_.addQueryArgs)(`wp/v2/posts/${editor.getCurrentPostId()}`, // phpcs:ignore WordPress.WhiteSpace.OperatorSpacing
        {
          lang: lang
        }),
        method: 'POST'
      });
    }
  }
  /**
   * Invalidate resolution of parent page attribute request to redo it
   * and refresh the list of pages filtered with the right language
   */


  static invalidateParentPagesStoredInCore() {
    // invalidate cache on parent pages attribute
    // arguments must be exactly the same as the getEntityRecords done in the parent pages component of the editor
    const postId = (0,external_this_wp_data_.select)(MODULE_CORE_EDITOR_KEY).getCurrentPostId();
    const postTypeSlug = (0,external_this_wp_data_.select)(MODULE_CORE_EDITOR_KEY).getEditedPostAttribute('type');
    const query = {
      per_page: -1,
      exclude: postId,
      parent_exclude: postId,
      orderby: 'menu_order',
      order: 'asc'
    };
    (0,external_this_wp_data_.dispatch)('core/data').invalidateResolution('core', 'getEntityRecords', ['postType', postTypeSlug, query]);
  }

  render() {
    const languages = (0,external_this_wp_data_.select)(MODULE_KEY).getLanguages();
    const lang = (0,external_this_wp_data_.select)(MODULE_CORE_EDITOR_KEY).getEditedPostAttribute('lang');
    const selectedLanguage = getSelectedLanguage(lang); // phpcs:disable WordPress.WhiteSpace.OperatorSpacing.NoSpaceBefore, WordPress.WhiteSpace.OperatorSpacing.NoSpaceAfter, PEAR.Functions.FunctionCallSignature.Indent

    return (0,external_this_wp_element_.createElement)(external_this_wp_element_.Fragment, null, (0,external_this_wp_element_.createElement)("p", null, (0,external_this_wp_element_.createElement)("strong", null, (0,external_this_wp_i18n_.__)("Language", "polylang-pro"))), (0,external_this_wp_element_.createElement)("label", {
      className: "screen-reader-text",
      htmlFor: "pll_post_lang_choice"
    }, (0,external_this_wp_i18n_.__)("Language", "polylang-pro")), (0,external_this_wp_element_.createElement)(LanguageDropdown, {
      selectedLanguage: selectedLanguage,
      handleChange: this.props.handleChange
    }, (0,external_this_wp_element_.createElement)(LanguagesOptionsList, {
      languages: languages
    }))); // phpcs:enable WordPress.WhiteSpace.OperatorSpacing.NoSpaceBefore, WordPress.WhiteSpace.OperatorSpacing.NoSpaceAfter, PEAR.Functions.FunctionCallSignature.Indent
  }

}

Switcher.labelConfirmationModal = (0,external_this_wp_i18n_.__)('Change language', 'polylang-pro');

const ModalContent = function () {
  // phpcs:disable WordPress.WhiteSpace.OperatorSpacing.NoSpaceBefore, WordPress.WhiteSpace.OperatorSpacing.NoSpaceAfter
  return (0,external_this_wp_element_.createElement)("p", null, (0,external_this_wp_i18n_.__)('Are you sure you want to change the language of the current content?', 'polylang-pro')); // phpcs:enable WordPress.WhiteSpace.OperatorSpacing.NoSpaceBefore, WordPress.WhiteSpace.OperatorSpacing.NoSpaceAfter
};

const SwitcherWithConfirmation = confirmation_modal('pll_change_lang', ModalContent, Switcher.handleLanguageChange)(Switcher);
/* harmony default export */ const switcher = (SwitcherWithConfirmation);
;// CONCATENATED MODULE: ./modules/block-editor/js/sidebar/components/default-lang-icon/index.js


/**
 * WordPress dependencies
 *
 * @package Polylang-Pro
 */



/**
 * Internal dependencies
 */



const DefaultLangIcon = () => (0,external_this_wp_element_.createElement)(external_this_wp_element_.Fragment, null, (0,external_this_wp_element_.createElement)(external_this_wp_components_.Icon, {
  icon: library_star,
  className: "pll-defaut-lang-icon"
}), (0,external_this_wp_element_.createElement)("span", {
  className: "screen-reader-text"
}, (0,external_this_wp_i18n_.__)('Default language.', 'polylang-pro')));

/* harmony default export */ const default_lang_icon = (DefaultLangIcon);
;// CONCATENATED MODULE: ./modules/block-editor/js/sidebar/components/language-item/index.js


/**
 * WordPress dependencies
 *
 * @package Polylang-Pro
 */



/**
 * Internal dependencies
 */





class LanguageItem extends external_this_wp_element_.Component {
  constructor() {
    super(...arguments);
    this.state = {
      isDefaultLanguage: this.props.language.slug === getDefaultLangFromTable(this.props.translationsTable).slug
    };
  }

  render() {
    return (0,external_this_wp_element_.createElement)(external_this_wp_element_.Fragment, null, (0,external_this_wp_element_.createElement)("p", null, (0,external_this_wp_element_.createElement)("strong", null, (0,external_this_wp_i18n_.__)("Language", "polylang-pro"))), (0,external_this_wp_element_.createElement)("div", {
      className: "pll-language-item"
    }, (0,external_this_wp_element_.createElement)(language_flag, {
      language: this.props.language
    }), (0,external_this_wp_element_.createElement)("span", {
      className: "pll-language-name"
    }, (0,external_this_wp_i18n_.__)(this.props.language.name, 'polylang-pro')), this.state.isDefaultLanguage && (0,external_this_wp_element_.createElement)(default_lang_icon, null)), this.state.isDefaultLanguage && (0,external_this_wp_element_.createElement)("div", null, (0,external_this_wp_element_.createElement)("span", {
      className: "pll-metabox-info"
    }, (0,external_this_wp_i18n_.__)('This template part is used for languages that have not yet been translated.', 'polylang-pro'))));
  }

}

/* harmony default export */ const language_item = (LanguageItem);
;// CONCATENATED MODULE: ./modules/block-editor/js/sidebar/components/duplicate-button/index.js


/**
 * WordPress dependencies
 *
 * @package Polylang-Pro
 */





/**
 * Internal dependencies
 */





class DuplicateButton extends external_this_wp_element_.Component {
  constructor() {
    super(...arguments);
    const currentUser = (0,external_this_wp_data_.select)(MODULE_KEY).getCurrentUser();
    this.postType = getCurrentPostType();
    this.state = {
      isDuplicateActive: this.isDuplicateActive(currentUser),
      currentUser
    };
    this.handleDuplicateContentChange = this.handleDuplicateContentChange.bind(this);
    this.setState = this.setState.bind(this);
  }
  /**
   * Read if content duplicate tool is active or not
   *
   * @param {type} user
   * @returns {Boolean}
   */


  isDuplicateActive(user) {
    if ((0,external_lodash_.isUndefined)(user.pll_duplicate_content) || (0,external_lodash_.isUndefined)(user.pll_duplicate_content[this.postType])) {
      return false;
    }

    return user.pll_duplicate_content[this.postType];
  }
  /**
   * Manage Duplicate content change by clicking on the icon
   *
   * @param {type} event
   */


  handleDuplicateContentChange(event) {
    const currentUser = this.state.currentUser; // If pll_duplicate_content user meta is a string, it have never been created
    // So we initialize it as an object

    if ((0,external_lodash_.isUndefined)(currentUser.pll_duplicate_content) || (0,external_lodash_.isString)(currentUser.pll_duplicate_content)) {
      currentUser.pll_duplicate_content = {};
    }

    currentUser.pll_duplicate_content[this.postType] = !this.state.isDuplicateActive; // update component state

    this.setState({
      currentUser: currentUser,
      isDuplicateActive: !this.state.isDuplicateActive
    }); // and update currentUser in store

    (0,external_this_wp_data_.dispatch)(MODULE_KEY).setCurrentUser({
      pll_duplicate_content: currentUser.pll_duplicate_content
    }, true);
  }

  render() {
    const isDuplicateActive = this.state.isDuplicateActive;
    /* translators: accessibility text */

    const duplicateButtonText = this.state.isDuplicateActive ? (0,external_this_wp_i18n_.__)('Deactivate the content duplication', 'polylang-pro') : (0,external_this_wp_i18n_.__)('Activate the content duplication', 'polylang-pro'); // phpcs:disable WordPress.WhiteSpace.OperatorSpacing.NoSpaceBefore, WordPress.WhiteSpace.OperatorSpacing.NoSpaceAfter

    return (0,external_this_wp_element_.createElement)(external_this_wp_components_.IconButton, {
      id: "pll-duplicate",
      className: `pll-button ${isDuplicateActive && `wp-ui-text-highlight`}`,
      onClick: this.handleDuplicateContentChange,
      icon: library_duplication,
      label: duplicateButtonText
    }, (0,external_this_wp_element_.createElement)("span", {
      className: "screen-reader-text"
    }, duplicateButtonText)); // phpcs:enable WordPress.WhiteSpace.OperatorSpacing.NoSpaceBefore, WordPress.WhiteSpace.OperatorSpacing.NoSpaceAfter
  }

}

/* harmony default export */ const duplicate_button = (DuplicateButton);
;// CONCATENATED MODULE: ./modules/block-editor/js/sidebar/components/synchronization-button/index.js


/**
 * WordPress dependencies
 *
 * @package Polylang-Pro
 */





/**
 * Internal dependencies
 */






class SynchronizationButton extends external_this_wp_element_.Component {
  constructor() {
    super(...arguments);
  }
  /**
   * Manage synchronziation with translated posts
   *
   * @param {type} event
   */


  static handleSynchronizationChange(language) {
    const pll_sync_post = (0,external_this_wp_data_.select)(MODULE_CORE_EDITOR_KEY).getEditedPostAttribute('pll_sync_post');
    const synchronizedPosts = getSynchronizedPosts(pll_sync_post);

    if (synchronizedPosts.has(language)) {
      synchronizedPosts.delete(language);
    } else {
      synchronizedPosts.set(language, true);
    } // and store the new value


    (0,external_this_wp_data_.dispatch)(MODULE_CORE_EDITOR_KEY).editPost({
      pll_sync_post: convertMapToObject(synchronizedPosts)
    }); // simulate a post modification to change status of the publish/update button

    (0,external_this_wp_data_.dispatch)(MODULE_CORE_EDITOR_KEY).editPost({
      title: (0,external_this_wp_data_.select)(MODULE_CORE_EDITOR_KEY).getEditedPostAttribute('title')
    });
  }

  static bypassConfirmation(translationData) {
    const pll_sync_post = (0,external_this_wp_data_.select)(MODULE_CORE_EDITOR_KEY).getEditedPostAttribute('pll_sync_post');
    const synchronizedPosts = getSynchronizedPosts(pll_sync_post);
    const isSynchronized = !(0,external_lodash_.isEmpty)(synchronizedPosts) && synchronizedPosts.has(translationData.lang.slug);
    const isTranslated = !(0,external_lodash_.isUndefined)(translationData.translated_post) && !(0,external_lodash_.isNil)(translationData.translated_post.id);
    return isSynchronized || !isTranslated;
  }

  static getChangeValue(event) {
    return event.currentTarget.id.match(/\[(.[^[]+)\]/i)[1];
  }

  render() {
    const pll_sync_post = (0,external_this_wp_data_.select)(MODULE_CORE_EDITOR_KEY).getEditedPostAttribute('pll_sync_post');
    const synchronizedPosts = getSynchronizedPosts(pll_sync_post);
    const translationData = this.props.translationData;
    const isSynchronized = !(0,external_lodash_.isEmpty)(synchronizedPosts) && synchronizedPosts.has(translationData.lang.slug);
    const highlightButtonClass = isSynchronized && 'wp-ui-text-highlight';
    const synchronizeButtonText = isSynchronized ? (0,external_this_wp_i18n_.__)("Don't synchronize this post", 'polylang-pro') : (0,external_this_wp_i18n_.__)('Synchronize this post', 'polylang-pro'); // phpcs:disable WordPress.WhiteSpace.OperatorSpacing.NoSpaceBefore, WordPress.WhiteSpace.OperatorSpacing.NoSpaceAfter

    return (0,external_this_wp_element_.createElement)(external_this_wp_components_.IconButton, {
      icon: library_synchronization,
      label: synchronizeButtonText,
      id: `pll_sync_post[${translationData.lang.slug}]`,
      className: `pll-button ${highlightButtonClass}`,
      onClick: event => {
        this.props.handleChange(event);
      }
    }, (0,external_this_wp_element_.createElement)("span", {
      className: "screen-reader-text"
    }, synchronizeButtonText)); // phpcs:enable WordPress.WhiteSpace.OperatorSpacing.NoSpaceBefore, WordPress.WhiteSpace.OperatorSpacing.NoSpaceAfter
  }

}

SynchronizationButton.labelConfirmationModal = (0,external_this_wp_i18n_.__)('Synchronize this post', 'polylang-pro');

const synchronization_button_ModalContent = function () {
  // phpcs:disable WordPress.WhiteSpace.OperatorSpacing.NoSpaceBefore, WordPress.WhiteSpace.OperatorSpacing.NoSpaceAfter
  return (0,external_this_wp_element_.createElement)("p", null, (0,external_this_wp_i18n_.__)('You are about to overwrite an existing translation. Are you sure you want to proceed?', 'polylang-pro')); // phpcs:enable WordPress.WhiteSpace.OperatorSpacing.NoSpaceBefore, WordPress.WhiteSpace.OperatorSpacing.NoSpaceAfter
};

const SynchronizationButtonWithConfirmation = confirmation_modal('pll_sync_post', synchronization_button_ModalContent, SynchronizationButton.handleSynchronizationChange)(SynchronizationButton); // phpcs:enable PEAR.Functions.FunctionCallSignature.Indent, PEAR.Functions.FunctionCallSignature.EmptyLine

/* harmony default export */ const synchronization_button = (SynchronizationButtonWithConfirmation);
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
  setLanguages(languages) {
    return {
      type: 'SET_LANGUAGES',
      languages
    };
  },

  setCurrentUser(currentUser) {
    let save = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
    return {
      type: 'SET_CURRENT_USER',
      currentUser,
      save
    };
  },

  setFromPost(fromPost) {
    return {
      type: 'SET_FROM_POST',
      fromPost
    };
  },

  fetchFromAPI(options) {
    return {
      type: 'FETCH_FROM_API',
      options
    };
  },

  setCurrentTemplatePart(currentTemplatePart) {
    return {
      type: 'SET_CURRENT_TEMPLATE_PART',
      currentTemplatePart
    };
  }

};
const store = (0,external_this_wp_data_.registerStore)(MODULE_KEY, {
  reducer() {
    let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : DEFAULT_STATE;
    let action = arguments.length > 1 ? arguments[1] : undefined;

    switch (action.type) {
      case 'SET_LANGUAGES':
        return { ...state,
          languages: action.languages
        };

      case 'SET_CURRENT_USER':
        if (action.save) {
          updateCurrentUser(action.currentUser);
        }

        return { ...state,
          currentUser: action.currentUser
        };

      case 'SET_FROM_POST':
        return { ...state,
          fromPost: action.fromPost
        };

      case 'SET_CURRENT_TEMPLATE_PART':
        return { ...state,
          currentTemplatePart: action.currentTemplatePart
        };

      default:
        return state;
    }
  },

  selectors: {
    getLanguages(state) {
      return state.languages;
    },

    getCurrentUser(state) {
      return state.currentUser;
    },

    getFromPost(state) {
      return state.fromPost;
    },

    getCurrentTemplatePart(state) {
      return state.currentTemplatePart;
    }

  },
  actions,
  controls: {
    FETCH_FROM_API(action) {
      return external_this_wp_apiFetch_default()({ ...action.options
      });
    }

  },
  resolvers: {
    *getLanguages() {
      const path = '/pll/v1/languages';
      const languages = yield actions.fetchFromAPI({
        path,
        filterLang: false
      });
      return actions.setLanguages(convertArrayToMap(languages, 'slug'));
    },

    *getCurrentUser() {
      const path = '/wp/v2/users/me';
      const currentUser = yield actions.fetchFromAPI({
        path,
        filterLang: true
      });
      return actions.setCurrentUser(currentUser);
    },

    *getCurrentTemplatePart() {
      const currentTemplatePart = getCurrentTemplateFromDataStore();
      return actions.setCurrentTemplatePart(currentTemplatePart);
    }

  }
});
/**
 * Wait for the whole post block editor context has been initialized: current post loaded and languages list initialized.
 */

const isBlockPostEditorContextInitialized = () => {
  if ((0,external_lodash_.isNil)((0,external_this_wp_data_.select)(MODULE_CORE_EDITOR_KEY))) {
    return Promise.reject("Polylang languages panel can't be initialized because block editor isn't fully initialized.");
  } // save url params espacially when a new translation is creating


  saveURLParams(); // call to getCurrentUser to force call to resolvers and initialize state

  const currentUser = (0,external_this_wp_data_.select)(MODULE_KEY).getCurrentUser();
  /**
   * Set a promise for waiting for the current post has been fully loaded before making other processes.
   */

  const isCurrentPostLoaded = new Promise(function (resolve) {
    let unsubscribe = (0,external_this_wp_data_.subscribe)(function () {
      const currentPost = (0,external_this_wp_data_.select)(MODULE_CORE_EDITOR_KEY).getCurrentPost();

      if (!(0,external_lodash_.isEmpty)(currentPost)) {
        unsubscribe();
        resolve();
      }
    });
  }); // Wait for current post has been loaded and languages list initialized.

  return Promise.all([isCurrentPostLoaded, isLanguagesinitialized]).then(function () {
    // If we come from another post for creating a new one, we have to update translations from the original post.
    const fromPost = (0,external_this_wp_data_.select)(MODULE_KEY).getFromPost();

    if (!(0,external_lodash_.isNil)(fromPost) && !(0,external_lodash_.isNil)(fromPost.id)) {
      const lang = (0,external_this_wp_data_.select)(MODULE_CORE_EDITOR_KEY).getEditedPostAttribute('lang');
      const translations = (0,external_this_wp_data_.select)(MODULE_CORE_EDITOR_KEY).getEditedPostAttribute('translations');
      const translations_table = (0,external_this_wp_data_.select)(MODULE_CORE_EDITOR_KEY).getEditedPostAttribute('translations_table');
      const translatedPosts = getTranslatedPosts(translations, translations_table, lang);
      (0,external_this_wp_data_.dispatch)(MODULE_CORE_EDITOR_KEY).editPost({
        translations: convertMapToObject(translatedPosts)
      });
    }
  });
};
/**
 * Wait for the whole site editor context to be initialized: current template loaded and languages list initialized.
 */

const isSiteEditorContextInitialized = () => {
  // save url params espacially when a new translation is creating
  saveURLParams();
  /**
   * Set a promise to wait for the current user to be fully loaded before making other processes.
   */

  const isCurrentUserLoaded = new Promise(function (resolve) {
    let unsubscribe = (0,external_this_wp_data_.subscribe)(function () {
      const currentUser = (0,external_this_wp_data_.select)(MODULE_KEY).getCurrentUser();

      if (!(0,external_lodash_.isNil)(currentUser) && !(0,external_lodash_.isEmpty)(currentUser)) {
        unsubscribe();
        resolve();
      }
    });
  });
  /**
   * Set a promise to wait for the current template to be fully loaded before making other processes.
   * It allows to see if both Site Editor and Core stores are available (@see getCurrentTemplateFromDataStore()).
   */

  const isTemplatePartLoaded = new Promise(function (resolve) {
    let unsubscribe = (0,external_this_wp_data_.subscribe)(function () {
      const currentTemplatePart = getCurrentTemplateFromDataStore();

      if (!(0,external_lodash_.isNil)(currentTemplatePart) && !(0,external_lodash_.isEmpty)(currentTemplatePart)) {
        unsubscribe();
        resolve();
      }
    });
  });
  return Promise.all([isCurrentUserLoaded, isTemplatePartLoaded, isLanguagesinitialized]).then(
  /**
   * Sets the duplication of template part to true as default behavior.
   */
  () => {
    const currentUser = (0,external_this_wp_data_.select)(MODULE_KEY).getCurrentUser(); // If pll_duplicate_content user meta is a string, it have never been created
    // So we initialize it as an object

    if ((0,external_lodash_.isString)(currentUser.pll_duplicate_content)) {
      currentUser.pll_duplicate_content = {};
    }

    currentUser.pll_duplicate_content['wp_template_part'] = true;
    (0,external_this_wp_data_.dispatch)(MODULE_KEY).setCurrentUser({
      pll_duplicate_content: currentUser.pll_duplicate_content
    }, true);
  });
};
/**
 * Set a promise for waiting for the languages list is correctly initialized before making other processes.
 */

const isLanguagesinitialized = new Promise(function (resolve) {
  let unsubscribe = (0,external_this_wp_data_.subscribe)(function () {
    const languages = (0,external_this_wp_data_.select)(MODULE_KEY).getLanguages();

    if (languages.size > 0) {
      unsubscribe();
      resolve();
    }
  });
});
/**
 * Save query string parameters from URL. They could be needed after
 * They could be null if they does not exist
 */

function saveURLParams() {
  // Variable window.location.search isn't use directly
  // Function getSearchParams return an URLSearchParams object for manipulating each parameter
  // Each of them are sanitized below
  const searchParams = getSearchParams(window.location.search); // phpcs:ignore WordPressVIPMinimum.JS.Window.location

  if (null !== searchParams) {
    (0,external_this_wp_data_.dispatch)(MODULE_KEY).setFromPost({
      id: wp.sanitize.stripTagsAndEncodeText(searchParams.get('from_post')),
      postType: wp.sanitize.stripTagsAndEncodeText(searchParams.get('post_type')),
      newLanguage: wp.sanitize.stripTagsAndEncodeText(searchParams.get('new_lang'))
    });
  }
}
/**
 * Save current user when it is wondered
 *
 * @param {object} currentUser
 */


function updateCurrentUser(currentUser) {
  external_this_wp_apiFetch_default()({
    path: '/wp/v2/users/me',
    data: currentUser,
    method: 'POST'
  });
}
/**
 * Gets the current template using the Site Editor store and the Core store.
 *
 * @returns {object} The current template object.
 */


function getCurrentTemplateFromDataStore() {
  var _select, _select2;

  const currentTemplateId = (_select = (0,external_this_wp_data_.select)(MODULE_SITE_EDITOR_KEY)) === null || _select === void 0 ? void 0 : _select.getEditedPostId();
  const currentTemplateType = (_select2 = (0,external_this_wp_data_.select)(MODULE_SITE_EDITOR_KEY)) === null || _select2 === void 0 ? void 0 : _select2.getEditedPostType();
  return (0,external_this_wp_data_.select)(MODULE_CORE_KEY).getEntityRecord('postType', currentTemplateType, currentTemplateId);
}
/* harmony default export */ const sidebar_store = ((/* unused pure expression or super */ null && (store)));
;// CONCATENATED MODULE: ./modules/block-editor/js/sidebar/components/add-edit-link/index.js


/**
 * WordPress dependencies
 *
 * @package Polylang-Pro
 */





/**
 * Internal dependencies
 */






const AddEditLink = _ref => {
  let {
    translationData
  } = _ref;
  const isTranslated = !(0,external_lodash_.isUndefined)(translationData.translated_post) && !(0,external_lodash_.isNil)(translationData.translated_post.id);
  const currentUserCanEdit = !(0,external_lodash_.isUndefined)(translationData.links) && !(0,external_lodash_.isNil)(translationData.links.edit_link);
  const currentUSerCanCreate = !(0,external_lodash_.isUndefined)(translationData.links) && !(0,external_lodash_.isEmpty)(translationData.links.add_link);
  let translationIcon = library_plus;
  /* translators: accessibility text, %s is a native language name. For example Deutsch for German or Français for french. */

  let translationScreenReaderText = (0,external_this_wp_i18n_.sprintf)((0,external_this_wp_i18n_.__)('Add a translation in %s', 'polylang-pro'), translationData.lang.name);
  let translationUrl = decodeURI(translationData.links.add_link);

  if (isTranslated) {
    translationIcon = library_pencil;
    /* translators: accessibility text, %s is a native language name. For example Deutsch for German or Français for french. */

    translationScreenReaderText = (0,external_this_wp_i18n_.sprintf)((0,external_this_wp_i18n_.__)('Edit the translation in %s', 'polylang-pro'), translationData.lang.name);
    translationUrl = decodeURI(translationData.links.edit_link);
  } // if the current user can't create or can't edit return nothing


  if (!currentUSerCanCreate && !isTranslated || !currentUserCanEdit && isTranslated) {
    return null;
  } // phpcs:disable WordPress.WhiteSpace.OperatorSpacing.NoSpaceBefore, WordPress.WhiteSpace.OperatorSpacing.NoSpaceAfter


  if (isSiteBlockEditor() && !isTranslated) {
    return (0,external_this_wp_element_.createElement)(external_this_wp_components_.Button, {
      href: '#',
      icon: translationIcon,
      label: translationScreenReaderText,
      className: `pll-button`,
      onClick: handleAddClick,
      "data-target-language": translationData.lang.slug // Store the target language to retrieve it through the click event.

    }, (0,external_this_wp_element_.createElement)("span", {
      className: "screen-reader-text"
    }, translationScreenReaderText));
  }

  return (0,external_this_wp_element_.createElement)(external_this_wp_components_.Button, {
    href: translationUrl,
    icon: translationIcon,
    label: translationScreenReaderText,
    className: `pll-button`
  }, (0,external_this_wp_element_.createElement)("span", {
    className: "screen-reader-text"
  }, translationScreenReaderText)); // phpcs:enable WordPress.WhiteSpace.OperatorSpacing.NoSpaceBefore, WordPress.WhiteSpace.OperatorSpacing.NoSpaceAfter
};
/**
 * Handle the template part translation creation when the "add" button is clicked.
 * Indeed, the Site Editor now create template through REST request.
 *
 * @param {object} event
 */


const handleAddClick = event => {
  const targetLanguage = event.target.closest('a.pll-button').getAttribute('data-target-language');
  const currentTemplate = getCurrentTemplateFromDataStore();
  const translationsData = {
    [currentTemplate.lang]: currentTemplate.wp_id
  };
  const isDuplicateActive = (0,external_this_wp_data_.select)(MODULE_KEY).getCurrentUser().pll_duplicate_content.wp_template_part;
  const content = isDuplicateActive ? currentTemplate.content.raw : "";
  const langSlugRegex = getLangSlugRegex();
  const newSlug = currentTemplate.slug.replace(langSlugRegex, '');
  external_this_wp_apiFetch_default()({
    path: '/wp/v2/template-parts',
    method: 'POST',
    data: {
      slug: newSlug,
      title: currentTemplate.title.raw,
      content: content,
      area: currentTemplate.area,
      lang: targetLanguage,
      from_post: currentTemplate.wp_id,
      translations: translationsData
    }
  }).then(createdTemplate => {
    var _get;

    const editLinkToRedirect = (_get = (0,external_lodash_.get)(createdTemplate.translations_table, targetLanguage)) === null || _get === void 0 ? void 0 : _get.links.edit_link;

    if (!(0,external_lodash_.isNil)(editLinkToRedirect)) {
      location.href = editLinkToRedirect;
    }
  });
};

/* harmony default export */ const add_edit_link = (AddEditLink);
// EXTERNAL MODULE: ./node_modules/classnames/index.js
var classnames = __webpack_require__(184);
var classnames_default = /*#__PURE__*/__webpack_require__.n(classnames);
;// CONCATENATED MODULE: ./node_modules/dom-scroll-into-view/dist-web/index.js
function _typeof(obj) {
  if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
    _typeof = function (obj) {
      return typeof obj;
    };
  } else {
    _typeof = function (obj) {
      return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
    };
  }

  return _typeof(obj);
}

function _defineProperty(obj, key, value) {
  if (key in obj) {
    Object.defineProperty(obj, key, {
      value: value,
      enumerable: true,
      configurable: true,
      writable: true
    });
  } else {
    obj[key] = value;
  }

  return obj;
}

function ownKeys(object, enumerableOnly) {
  var keys = Object.keys(object);

  if (Object.getOwnPropertySymbols) {
    var symbols = Object.getOwnPropertySymbols(object);
    if (enumerableOnly) symbols = symbols.filter(function (sym) {
      return Object.getOwnPropertyDescriptor(object, sym).enumerable;
    });
    keys.push.apply(keys, symbols);
  }

  return keys;
}

function _objectSpread2(target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i] != null ? arguments[i] : {};

    if (i % 2) {
      ownKeys(source, true).forEach(function (key) {
        _defineProperty(target, key, source[key]);
      });
    } else if (Object.getOwnPropertyDescriptors) {
      Object.defineProperties(target, Object.getOwnPropertyDescriptors(source));
    } else {
      ownKeys(source).forEach(function (key) {
        Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key));
      });
    }
  }

  return target;
}

var RE_NUM = /[\-+]?(?:\d*\.|)\d+(?:[eE][\-+]?\d+|)/.source;

function getClientPosition(elem) {
  var box;
  var x;
  var y;
  var doc = elem.ownerDocument;
  var body = doc.body;
  var docElem = doc && doc.documentElement; // 根据 GBS 最新数据，A-Grade Browsers 都已支持 getBoundingClientRect 方法，不用再考虑传统的实现方式

  box = elem.getBoundingClientRect(); // 注：jQuery 还考虑减去 docElem.clientLeft/clientTop
  // 但测试发现，这样反而会导致当 html 和 body 有边距/边框样式时，获取的值不正确
  // 此外，ie6 会忽略 html 的 margin 值，幸运地是没有谁会去设置 html 的 margin

  x = box.left;
  y = box.top; // In IE, most of the time, 2 extra pixels are added to the top and left
  // due to the implicit 2-pixel inset border.  In IE6/7 quirks mode and
  // IE6 standards mode, this border can be overridden by setting the
  // document element's border to zero -- thus, we cannot rely on the
  // offset always being 2 pixels.
  // In quirks mode, the offset can be determined by querying the body's
  // clientLeft/clientTop, but in standards mode, it is found by querying
  // the document element's clientLeft/clientTop.  Since we already called
  // getClientBoundingRect we have already forced a reflow, so it is not
  // too expensive just to query them all.
  // ie 下应该减去窗口的边框吧，毕竟默认 absolute 都是相对窗口定位的
  // 窗口边框标准是设 documentElement ,quirks 时设置 body
  // 最好禁止在 body 和 html 上边框 ，但 ie < 9 html 默认有 2px ，减去
  // 但是非 ie 不可能设置窗口边框，body html 也不是窗口 ,ie 可以通过 html,body 设置
  // 标准 ie 下 docElem.clientTop 就是 border-top
  // ie7 html 即窗口边框改变不了。永远为 2
  // 但标准 firefox/chrome/ie9 下 docElem.clientTop 是窗口边框，即使设了 border-top 也为 0

  x -= docElem.clientLeft || body.clientLeft || 0;
  y -= docElem.clientTop || body.clientTop || 0;
  return {
    left: x,
    top: y
  };
}

function getScroll(w, top) {
  var ret = w["page".concat(top ? 'Y' : 'X', "Offset")];
  var method = "scroll".concat(top ? 'Top' : 'Left');

  if (typeof ret !== 'number') {
    var d = w.document; // ie6,7,8 standard mode

    ret = d.documentElement[method];

    if (typeof ret !== 'number') {
      // quirks mode
      ret = d.body[method];
    }
  }

  return ret;
}

function getScrollLeft(w) {
  return getScroll(w);
}

function getScrollTop(w) {
  return getScroll(w, true);
}

function getOffset(el) {
  var pos = getClientPosition(el);
  var doc = el.ownerDocument;
  var w = doc.defaultView || doc.parentWindow;
  pos.left += getScrollLeft(w);
  pos.top += getScrollTop(w);
  return pos;
}

function _getComputedStyle(elem, name, computedStyle_) {
  var val = '';
  var d = elem.ownerDocument;
  var computedStyle = computedStyle_ || d.defaultView.getComputedStyle(elem, null); // https://github.com/kissyteam/kissy/issues/61

  if (computedStyle) {
    val = computedStyle.getPropertyValue(name) || computedStyle[name];
  }

  return val;
}

var _RE_NUM_NO_PX = new RegExp("^(".concat(RE_NUM, ")(?!px)[a-z%]+$"), 'i');

var RE_POS = /^(top|right|bottom|left)$/;
var CURRENT_STYLE = 'currentStyle';
var RUNTIME_STYLE = 'runtimeStyle';
var LEFT = 'left';
var PX = 'px';

function _getComputedStyleIE(elem, name) {
  // currentStyle maybe null
  // http://msdn.microsoft.com/en-us/library/ms535231.aspx
  var ret = elem[CURRENT_STYLE] && elem[CURRENT_STYLE][name]; // 当 width/height 设置为百分比时，通过 pixelLeft 方式转换的 width/height 值
  // 一开始就处理了! CUSTOM_STYLE.height,CUSTOM_STYLE.width ,cssHook 解决@2011-08-19
  // 在 ie 下不对，需要直接用 offset 方式
  // borderWidth 等值也有问题，但考虑到 borderWidth 设为百分比的概率很小，这里就不考虑了
  // From the awesome hack by Dean Edwards
  // http://erik.eae.net/archives/2007/07/27/18.54.15/#comment-102291
  // If we're not dealing with a regular pixel number
  // but a number that has a weird ending, we need to convert it to pixels
  // exclude left right for relativity

  if (_RE_NUM_NO_PX.test(ret) && !RE_POS.test(name)) {
    // Remember the original values
    var style = elem.style;
    var left = style[LEFT];
    var rsLeft = elem[RUNTIME_STYLE][LEFT]; // prevent flashing of content

    elem[RUNTIME_STYLE][LEFT] = elem[CURRENT_STYLE][LEFT]; // Put in the new values to get a computed value out

    style[LEFT] = name === 'fontSize' ? '1em' : ret || 0;
    ret = style.pixelLeft + PX; // Revert the changed values

    style[LEFT] = left;
    elem[RUNTIME_STYLE][LEFT] = rsLeft;
  }

  return ret === '' ? 'auto' : ret;
}

var getComputedStyleX;

if (typeof window !== 'undefined') {
  getComputedStyleX = window.getComputedStyle ? _getComputedStyle : _getComputedStyleIE;
}

function each(arr, fn) {
  for (var i = 0; i < arr.length; i++) {
    fn(arr[i]);
  }
}

function isBorderBoxFn(elem) {
  return getComputedStyleX(elem, 'boxSizing') === 'border-box';
}

var BOX_MODELS = ['margin', 'border', 'padding'];
var CONTENT_INDEX = -1;
var PADDING_INDEX = 2;
var BORDER_INDEX = 1;
var MARGIN_INDEX = 0;

function swap(elem, options, callback) {
  var old = {};
  var style = elem.style;
  var name; // Remember the old values, and insert the new ones

  for (name in options) {
    if (options.hasOwnProperty(name)) {
      old[name] = style[name];
      style[name] = options[name];
    }
  }

  callback.call(elem); // Revert the old values

  for (name in options) {
    if (options.hasOwnProperty(name)) {
      style[name] = old[name];
    }
  }
}

function getPBMWidth(elem, props, which) {
  var value = 0;
  var prop;
  var j;
  var i;

  for (j = 0; j < props.length; j++) {
    prop = props[j];

    if (prop) {
      for (i = 0; i < which.length; i++) {
        var cssProp = void 0;

        if (prop === 'border') {
          cssProp = "".concat(prop + which[i], "Width");
        } else {
          cssProp = prop + which[i];
        }

        value += parseFloat(getComputedStyleX(elem, cssProp)) || 0;
      }
    }
  }

  return value;
}
/**
 * A crude way of determining if an object is a window
 * @member util
 */


function isWindow(obj) {
  // must use == for ie8

  /* eslint eqeqeq:0 */
  return obj != null && obj == obj.window;
}

var domUtils = {};
each(['Width', 'Height'], function (name) {
  domUtils["doc".concat(name)] = function (refWin) {
    var d = refWin.document;
    return Math.max( // firefox chrome documentElement.scrollHeight< body.scrollHeight
    // ie standard mode : documentElement.scrollHeight> body.scrollHeight
    d.documentElement["scroll".concat(name)], // quirks : documentElement.scrollHeight 最大等于可视窗口多一点？
    d.body["scroll".concat(name)], domUtils["viewport".concat(name)](d));
  };

  domUtils["viewport".concat(name)] = function (win) {
    // pc browser includes scrollbar in window.innerWidth
    var prop = "client".concat(name);
    var doc = win.document;
    var body = doc.body;
    var documentElement = doc.documentElement;
    var documentElementProp = documentElement[prop]; // 标准模式取 documentElement
    // backcompat 取 body

    return doc.compatMode === 'CSS1Compat' && documentElementProp || body && body[prop] || documentElementProp;
  };
});
/*
 得到元素的大小信息
 @param elem
 @param name
 @param {String} [extra]  'padding' : (css width) + padding
 'border' : (css width) + padding + border
 'margin' : (css width) + padding + border + margin
 */

function getWH(elem, name, extra) {
  if (isWindow(elem)) {
    return name === 'width' ? domUtils.viewportWidth(elem) : domUtils.viewportHeight(elem);
  } else if (elem.nodeType === 9) {
    return name === 'width' ? domUtils.docWidth(elem) : domUtils.docHeight(elem);
  }

  var which = name === 'width' ? ['Left', 'Right'] : ['Top', 'Bottom'];
  var borderBoxValue = name === 'width' ? elem.offsetWidth : elem.offsetHeight;
  var computedStyle = getComputedStyleX(elem);
  var isBorderBox = isBorderBoxFn(elem);
  var cssBoxValue = 0;

  if (borderBoxValue == null || borderBoxValue <= 0) {
    borderBoxValue = undefined; // Fall back to computed then un computed css if necessary

    cssBoxValue = getComputedStyleX(elem, name);

    if (cssBoxValue == null || Number(cssBoxValue) < 0) {
      cssBoxValue = elem.style[name] || 0;
    } // Normalize '', auto, and prepare for extra


    cssBoxValue = parseFloat(cssBoxValue) || 0;
  }

  if (extra === undefined) {
    extra = isBorderBox ? BORDER_INDEX : CONTENT_INDEX;
  }

  var borderBoxValueOrIsBorderBox = borderBoxValue !== undefined || isBorderBox;
  var val = borderBoxValue || cssBoxValue;

  if (extra === CONTENT_INDEX) {
    if (borderBoxValueOrIsBorderBox) {
      return val - getPBMWidth(elem, ['border', 'padding'], which);
    }

    return cssBoxValue;
  }

  if (borderBoxValueOrIsBorderBox) {
    var padding = extra === PADDING_INDEX ? -getPBMWidth(elem, ['border'], which) : getPBMWidth(elem, ['margin'], which);
    return val + (extra === BORDER_INDEX ? 0 : padding);
  }

  return cssBoxValue + getPBMWidth(elem, BOX_MODELS.slice(extra), which);
}

var cssShow = {
  position: 'absolute',
  visibility: 'hidden',
  display: 'block'
}; // fix #119 : https://github.com/kissyteam/kissy/issues/119

function getWHIgnoreDisplay(elem) {
  var val;
  var args = arguments; // in case elem is window
  // elem.offsetWidth === undefined

  if (elem.offsetWidth !== 0) {
    val = getWH.apply(undefined, args);
  } else {
    swap(elem, cssShow, function () {
      val = getWH.apply(undefined, args);
    });
  }

  return val;
}

function css(el, name, v) {
  var value = v;

  if (_typeof(name) === 'object') {
    for (var i in name) {
      if (name.hasOwnProperty(i)) {
        css(el, i, name[i]);
      }
    }

    return undefined;
  }

  if (typeof value !== 'undefined') {
    if (typeof value === 'number') {
      value += 'px';
    }

    el.style[name] = value;
    return undefined;
  }

  return getComputedStyleX(el, name);
}

each(['width', 'height'], function (name) {
  var first = name.charAt(0).toUpperCase() + name.slice(1);

  domUtils["outer".concat(first)] = function (el, includeMargin) {
    return el && getWHIgnoreDisplay(el, name, includeMargin ? MARGIN_INDEX : BORDER_INDEX);
  };

  var which = name === 'width' ? ['Left', 'Right'] : ['Top', 'Bottom'];

  domUtils[name] = function (elem, val) {
    if (val !== undefined) {
      if (elem) {
        var computedStyle = getComputedStyleX(elem);
        var isBorderBox = isBorderBoxFn(elem);

        if (isBorderBox) {
          val += getPBMWidth(elem, ['padding', 'border'], which);
        }

        return css(elem, name, val);
      }

      return undefined;
    }

    return elem && getWHIgnoreDisplay(elem, name, CONTENT_INDEX);
  };
}); // 设置 elem 相对 elem.ownerDocument 的坐标

function setOffset(elem, offset) {
  // set position first, in-case top/left are set even on static elem
  if (css(elem, 'position') === 'static') {
    elem.style.position = 'relative';
  }

  var old = getOffset(elem);
  var ret = {};
  var current;
  var key;

  for (key in offset) {
    if (offset.hasOwnProperty(key)) {
      current = parseFloat(css(elem, key)) || 0;
      ret[key] = current + offset[key] - old[key];
    }
  }

  css(elem, ret);
}

var util = _objectSpread2({
  getWindow: function getWindow(node) {
    var doc = node.ownerDocument || node;
    return doc.defaultView || doc.parentWindow;
  },
  offset: function offset(el, value) {
    if (typeof value !== 'undefined') {
      setOffset(el, value);
    } else {
      return getOffset(el);
    }
  },
  isWindow: isWindow,
  each: each,
  css: css,
  clone: function clone(obj) {
    var ret = {};

    for (var i in obj) {
      if (obj.hasOwnProperty(i)) {
        ret[i] = obj[i];
      }
    }

    var overflow = obj.overflow;

    if (overflow) {
      for (var _i in obj) {
        if (obj.hasOwnProperty(_i)) {
          ret.overflow[_i] = obj.overflow[_i];
        }
      }
    }

    return ret;
  },
  scrollLeft: function scrollLeft(w, v) {
    if (isWindow(w)) {
      if (v === undefined) {
        return getScrollLeft(w);
      }

      window.scrollTo(v, getScrollTop(w));
    } else {
      if (v === undefined) {
        return w.scrollLeft;
      }

      w.scrollLeft = v;
    }
  },
  scrollTop: function scrollTop(w, v) {
    if (isWindow(w)) {
      if (v === undefined) {
        return getScrollTop(w);
      }

      window.scrollTo(getScrollLeft(w), v);
    } else {
      if (v === undefined) {
        return w.scrollTop;
      }

      w.scrollTop = v;
    }
  },
  viewportWidth: 0,
  viewportHeight: 0
}, domUtils);

function scrollIntoView(elem, container, config) {
  config = config || {}; // document 归一化到 window

  if (container.nodeType === 9) {
    container = util.getWindow(container);
  }

  var allowHorizontalScroll = config.allowHorizontalScroll;
  var onlyScrollIfNeeded = config.onlyScrollIfNeeded;
  var alignWithTop = config.alignWithTop;
  var alignWithLeft = config.alignWithLeft;
  var offsetTop = config.offsetTop || 0;
  var offsetLeft = config.offsetLeft || 0;
  var offsetBottom = config.offsetBottom || 0;
  var offsetRight = config.offsetRight || 0;
  allowHorizontalScroll = allowHorizontalScroll === undefined ? true : allowHorizontalScroll;
  var isWin = util.isWindow(container);
  var elemOffset = util.offset(elem);
  var eh = util.outerHeight(elem);
  var ew = util.outerWidth(elem);
  var containerOffset;
  var ch;
  var cw;
  var containerScroll;
  var diffTop;
  var diffBottom;
  var win;
  var winScroll;
  var ww;
  var wh;

  if (isWin) {
    win = container;
    wh = util.height(win);
    ww = util.width(win);
    winScroll = {
      left: util.scrollLeft(win),
      top: util.scrollTop(win)
    }; // elem 相对 container 可视视窗的距离

    diffTop = {
      left: elemOffset.left - winScroll.left - offsetLeft,
      top: elemOffset.top - winScroll.top - offsetTop
    };
    diffBottom = {
      left: elemOffset.left + ew - (winScroll.left + ww) + offsetRight,
      top: elemOffset.top + eh - (winScroll.top + wh) + offsetBottom
    };
    containerScroll = winScroll;
  } else {
    containerOffset = util.offset(container);
    ch = container.clientHeight;
    cw = container.clientWidth;
    containerScroll = {
      left: container.scrollLeft,
      top: container.scrollTop
    }; // elem 相对 container 可视视窗的距离
    // 注意边框, offset 是边框到根节点

    diffTop = {
      left: elemOffset.left - (containerOffset.left + (parseFloat(util.css(container, 'borderLeftWidth')) || 0)) - offsetLeft,
      top: elemOffset.top - (containerOffset.top + (parseFloat(util.css(container, 'borderTopWidth')) || 0)) - offsetTop
    };
    diffBottom = {
      left: elemOffset.left + ew - (containerOffset.left + cw + (parseFloat(util.css(container, 'borderRightWidth')) || 0)) + offsetRight,
      top: elemOffset.top + eh - (containerOffset.top + ch + (parseFloat(util.css(container, 'borderBottomWidth')) || 0)) + offsetBottom
    };
  }

  if (diffTop.top < 0 || diffBottom.top > 0) {
    // 强制向上
    if (alignWithTop === true) {
      util.scrollTop(container, containerScroll.top + diffTop.top);
    } else if (alignWithTop === false) {
      util.scrollTop(container, containerScroll.top + diffBottom.top);
    } else {
      // 自动调整
      if (diffTop.top < 0) {
        util.scrollTop(container, containerScroll.top + diffTop.top);
      } else {
        util.scrollTop(container, containerScroll.top + diffBottom.top);
      }
    }
  } else {
    if (!onlyScrollIfNeeded) {
      alignWithTop = alignWithTop === undefined ? true : !!alignWithTop;

      if (alignWithTop) {
        util.scrollTop(container, containerScroll.top + diffTop.top);
      } else {
        util.scrollTop(container, containerScroll.top + diffBottom.top);
      }
    }
  }

  if (allowHorizontalScroll) {
    if (diffTop.left < 0 || diffBottom.left > 0) {
      // 强制向上
      if (alignWithLeft === true) {
        util.scrollLeft(container, containerScroll.left + diffTop.left);
      } else if (alignWithLeft === false) {
        util.scrollLeft(container, containerScroll.left + diffBottom.left);
      } else {
        // 自动调整
        if (diffTop.left < 0) {
          util.scrollLeft(container, containerScroll.left + diffTop.left);
        } else {
          util.scrollLeft(container, containerScroll.left + diffBottom.left);
        }
      }
    } else {
      if (!onlyScrollIfNeeded) {
        alignWithLeft = alignWithLeft === undefined ? true : !!alignWithLeft;

        if (alignWithLeft) {
          util.scrollLeft(container, containerScroll.left + diffTop.left);
        } else {
          util.scrollLeft(container, containerScroll.left + diffBottom.left);
        }
      }
    }
  }
}

/* harmony default export */ const dist_web = (scrollIntoView);
//# sourceMappingURL=index.js.map

// EXTERNAL MODULE: external {"this":["wp","htmlEntities"]}
var external_this_wp_htmlEntities_ = __webpack_require__(638);
// EXTERNAL MODULE: external {"this":["wp","keycodes"]}
var external_this_wp_keycodes_ = __webpack_require__(19);
;// CONCATENATED MODULE: ./modules/block-editor/js/sidebar/components/translation-input/index.js


/**
 * External dependencies
 *
 * @package Polylang-Pro
 */



/**
 * WordPress dependencies
 */










/**
 * Internal dependencies
 */

 // Since TranslationInput is rendered in the context of other inputs, but should be
// considered a separate modal node, prevent keyboard events from propagating
// as being considered from the input.

const stopEventPropagation = event => event.stopPropagation();

class TranslationInput extends external_this_wp_element_.Component {
  constructor() {
    super(...arguments);
    this.onChange = this.onChange.bind(this);
    this.onKeyDown = this.onKeyDown.bind(this);
    this.bindListNode = this.bindListNode.bind(this);
    this.updateSuggestions = (0,external_lodash_.throttle)(this.updateSuggestions.bind(this), 200);
    this.suggestionNodes = [];
    this.state = {
      posts: [],
      showSuggestions: false,
      selectedSuggestion: null
    };
  }

  componentDidUpdate() {
    const {
      showSuggestions,
      selectedSuggestion
    } = this.state; // only have to worry about scrolling selected suggestion into view
    // when already expanded

    if (showSuggestions && selectedSuggestion !== null && !this.scrollingIntoView) {
      this.scrollingIntoView = true;
      dist_web(this.suggestionNodes[selectedSuggestion], this.listNode, {
        onlyScrollIfNeeded: true
      });
      setTimeout(() => {
        this.scrollingIntoView = false; // phpcs:ignore PEAR.Functions.FunctionCallSignature.Indent
      }, 100);
    }
  }

  componentWillUnmount() {
    delete this.suggestionsRequest;
  }

  bindListNode(ref) {
    this.listNode = ref;
  }

  bindSuggestionNode(index) {
    return ref => {
      this.suggestionNodes[index] = ref;
    };
  }

  updateSuggestions(value) {
    let noControl = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;

    // Show the suggestions after typing at least 2 characters
    // and also for URLs
    if (value.length < 2 && !noControl) {
      this.setState({
        showSuggestions: false,
        selectedSuggestion: null,
        loading: false
      });
      return;
    }

    this.setState({
      selectedSuggestion: null,
      loading: true
    });
    const postId = (0,external_this_wp_data_.select)(MODULE_CORE_EDITOR_KEY).getCurrentPostId();
    const postType = (0,external_this_wp_data_.select)(MODULE_CORE_EDITOR_KEY).getCurrentPostType();
    const postLanguageSlug = (0,external_this_wp_data_.select)(MODULE_CORE_EDITOR_KEY).getEditedPostAttribute('lang');
    const translationLanguageSlug = this.props.translationData.lang.slug; // language for the suggestion

    const request = external_this_wp_apiFetch_default()({
      path: (0,external_this_wp_url_.addQueryArgs)('/pll/v1/untranslated-posts', {
        search: value,
        include: postId,
        untranslated_in: postLanguageSlug,
        lang: translationLanguageSlug,
        type: postType,
        is_block_editor: true
      })
    }); // phpcs:disable PEAR.Functions.FunctionCallSignature.Indent, PEAR.Functions.FunctionCallSignature.EmptyLine

    request.then(posts => {
      // A fetch Promise doesn't have an abort option. It's mimicked by
      // comparing the request reference in on the instance, which is
      // reset or deleted on subsequent requests or unmounting.
      if (this.suggestionsRequest !== request) {
        return;
      }

      this.setState({
        posts,
        showSuggestions: true,
        loading: false
      });

      if (!!posts.length) {
        // phpcs:ignore Generic.WhiteSpace.ScopeIndent.IncorrectExact, WordPress.WhiteSpace.OperatorSpacing.NoSpaceBefore, WordPress.WhiteSpace.OperatorSpacing.NoSpaceAfter
        this.props.debouncedSpeak((0,external_this_wp_i18n_.sprintf)(
        /* translators: accessibility text. %d is a number of posts. */
        (0,external_this_wp_i18n_._n)('%d result found, use up and down arrow keys to navigate.', '%d results found, use up and down arrow keys to navigate.', posts.length, 'polylang-pro'), posts.length), 'assertive');
      } else {
        // phpcs:ignore Generic.WhiteSpace.ScopeIndent.IncorrectExact

        /* translators: accessibility text */
        this.props.debouncedSpeak((0,external_this_wp_i18n_.__)('No results.', 'polylang-pro'), 'assertive');
      } // phpcs:ignore Generic.WhiteSpace.ScopeIndent.IncorrectExact

    }).catch(() => {
      if (this.suggestionsRequest === request) {
        this.setState({
          loading: false
        });
      }
    }); // phpcs:enable PEAR.Functions.FunctionCallSignature.Indent, PEAR.Functions.FunctionCallSignature.EmptyLine

    this.suggestionsRequest = request;
  }

  onChange(event) {
    const inputValue = event.target.value;
    const translatedPosts = this.props.translatedPosts;
    const translationsTable = this.props.translationsTable;
    const language = this.props.translationData.lang;
    this.props.onChange({
      value: inputValue,
      translatedPosts,
      translationsTable,
      language
    });
    this.updateSuggestions(inputValue);
  }

  onKeyDown(event) {
    const {
      showSuggestions,
      selectedSuggestion,
      posts,
      loading
    } = this.state;
    let inputValue = event.target.value;
    let doUpdateSuggestions = false; // If the suggestions are not shown or loading, we shouldn't handle the arrow keys
    // We shouldn't preventDefault to allow block arrow keys navigation

    if (!showSuggestions || !posts.length || loading) {
      switch (event.keyCode) {
        case external_this_wp_keycodes_.SPACE:
          const {
            ctrlKey,
            shiftKey,
            altKey,
            metaKey
          } = event;

          if (ctrlKey && !(shiftKey || altKey || metaKey)) {
            inputValue = '';
            doUpdateSuggestions = true;
          }

          break;

        case external_this_wp_keycodes_.BACKSPACE:
          if ((0,external_lodash_.isEmpty)(inputValue)) {
            doUpdateSuggestions = true;
          }

          break;
      }

      if (doUpdateSuggestions) {
        this.updateSuggestions(inputValue, true);
      }

      return;
    }

    switch (event.keyCode) {
      case external_this_wp_keycodes_.UP:
        {
          event.stopPropagation();
          event.preventDefault();
          const previousIndex = !selectedSuggestion ? posts.length - 1 : selectedSuggestion - 1;
          this.setState({
            selectedSuggestion: previousIndex
          });
          break;
        }

      case external_this_wp_keycodes_.DOWN:
        {
          event.stopPropagation();
          event.preventDefault();
          const nextIndex = selectedSuggestion === null || selectedSuggestion === posts.length - 1 ? 0 : selectedSuggestion + 1;
          this.setState({
            selectedSuggestion: nextIndex
          });
          break;
        }

      case external_this_wp_keycodes_.ENTER:
        {
          if (this.state.selectedSuggestion !== null) {
            event.stopPropagation();
            const post = this.state.posts[this.state.selectedSuggestion];
            this.selectLink(post);
          }

          break;
        }

      case external_this_wp_keycodes_.ESCAPE:
        {
          event.stopPropagation();
          this.setState({
            selectedSuggestion: null,
            showSuggestions: false
          });
          break;
        }
    }
  }

  selectLink(post) {
    const translationsTable = this.props.translationsTable;
    const translatedPosts = this.props.translatedPosts;
    const language = this.props.translationData.lang;
    this.props.onChange({
      value: post.title.rendered,
      post,
      translatedPosts,
      translationsTable,
      language
    });
    this.setState({
      selectedSuggestion: null,
      showSuggestions: false
    });
  }

  render() {
    const {
      value = '',
      autoFocus = true,
      instanceId,
      translationData
    } = this.props;
    const language = translationData.lang;
    const {
      showSuggestions,
      posts,
      selectedSuggestion,
      loading
    } = this.state;
    const currentUserCanEdit = !(0,external_lodash_.isUndefined)(translationData.links) && ((0,external_lodash_.isUndefined)(translationData.links.edit_link) || !(0,external_lodash_.isUndefined)(translationData.links.edit_link) && !(0,external_lodash_.isNull)(translationData.links.edit_link)); // phpcs:disable WordPress.WhiteSpace.OperatorSpacing.NoSpaceBefore, WordPress.WhiteSpace.OperatorSpacing.NoSpaceAfter

    return (0,external_this_wp_element_.createElement)(external_this_wp_element_.Fragment, null, (0,external_this_wp_element_.createElement)("div", {
      className: "translation-input"
    }, (0,external_this_wp_element_.createElement)("input", {
      lang: language.w3c,
      dir: language.is_rtl ? 'rtl' : 'ltr',
      style: {
        direction: language.is_rtl ? 'rtl' : 'ltr'
      },
      autoFocus: autoFocus,
      disabled: !currentUserCanEdit,
      type: "text",
      "aria-label":
      /* translators: accessibility text */
      (0,external_this_wp_i18n_.__)('URL', 'polylang-pro'),
      required: true,
      value: value,
      onChange: this.onChange,
      onInput: stopEventPropagation,
      placeholder: (0,external_this_wp_i18n_.__)('Start typing the post title', 'polylang-pro'),
      onKeyDown: this.onKeyDown,
      role: "combobox",
      "aria-expanded": showSuggestions,
      "aria-autocomplete": "list",
      "aria-owns": `translation-input-suggestions-${instanceId}`,
      "aria-activedescendant": selectedSuggestion !== null ? `translation-input-suggestion-${instanceId}-${selectedSuggestion}` : undefined
    }), loading && (0,external_this_wp_element_.createElement)(external_this_wp_components_.Spinner, null)), showSuggestions && !!posts.length && (0,external_this_wp_element_.createElement)(external_this_wp_components_.Popover, {
      position: "bottom",
      noArrow: true,
      focusOnMount: false
    }, (0,external_this_wp_element_.createElement)("div", {
      className: "translation-input__suggestions",
      id: `translation-input-suggestions-${instanceId}`,
      ref: this.bindListNode,
      role: "listbox"
    }, posts.map((post, index) => (0,external_this_wp_element_.createElement)("button", {
      key: post.id,
      role: "option",
      tabIndex: "-1",
      id: `translation-input-suggestion-${instanceId}-${index}`,
      ref: this.bindSuggestionNode(index),
      className: classnames_default()('translation-input__suggestion', {
        'is-selected': index === selectedSuggestion
      }),
      onClick: () => this.selectLink(post),
      "aria-selected": index === selectedSuggestion
    }, (0,external_this_wp_htmlEntities_.decodeEntities)(post.title.rendered) || (0,external_this_wp_i18n_.__)('(no title)', 'polylang-pro')))))); // phpcs:enable WordPress.WhiteSpace.OperatorSpacing.NoSpaceBefore, WordPress.WhiteSpace.OperatorSpacing.NoSpaceAfter
  }

}

/* harmony default export */ const translation_input = ((0,external_this_wp_components_.withSpokenMessages)((0,external_this_wp_compose_.withInstanceId)(TranslationInput)));
;// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/extends.js
function _extends() {
  _extends = Object.assign || function (target) {
    for (var i = 1; i < arguments.length; i++) {
      var source = arguments[i];

      for (var key in source) {
        if (Object.prototype.hasOwnProperty.call(source, key)) {
          target[key] = source[key];
        }
      }
    }

    return target;
  };

  return _extends.apply(this, arguments);
}
;// CONCATENATED MODULE: ./modules/block-editor/js/sidebar/components/delete-button/index.js



/**
 * WordPress dependencies
 *
 * @package Polylang-Pro
 */







/**
 * Internal dependencies
 */






class DeleteButton extends external_this_wp_element_.Component {
  /**
   * Handles the deletion of an item after confirmation.
   * Used as the handleChangeCallback for the confirmation modal (3rd param of withConfirmation()).
   *
   * @param {Object} deleteData             The delete data required to manage a template deletion.
   * @param {string} deleteData.templateId  The id of the template to delete.
   * @param {Object} deleteData.currentPost The current post we need to refresh after the template deletion.
   */
  static handleDelete(_ref) {
    let {
      templateId,
      currentPost
    } = _ref;

    if (!(0,external_lodash_.isEmpty)(templateId)) {
      const restBaseUrl = getPostsUrl(currentPost.type);
      external_this_wp_apiFetch_default()({
        path: `${restBaseUrl}/${templateId}`,
        method: 'GET'
      }).then(template => {
        if (!(0,external_lodash_.isEmpty)(template)) {
          const {
            translations_table,
            translations
          } = currentPost;
          const translationsTable = getTranslationsTable(translations_table); // Gets the removed translation from the translations table to update it.

          const removedTranslation = translationsTable.get(template.lang); // Modifies the template title rendered to change the confirmation's message when deleting the template.

          template.title.rendered = template.title.rendered + ' in ' + removedTranslation.lang.name; // Deletes the template in the corresponding language

          (0,external_this_wp_data_.dispatch)(MODULE_SITE_EDITOR_KEY).removeTemplate(template); // Needs to update the translated posts in the store to refresh the metabox after the template deletion.
          // Removes the translated_post property to say that it doesn't exist a translation anymore for the deleted language.

          delete removedTranslation.translated_post; // Needs to update the translations group of the currentPost accordingly.

          const translatedPosts = getTranslatedPosts(translations); // Removes the removed translation from the translations group.

          translatedPosts.delete(template.lang); // Updates the translations group.

          currentPost.translations = convertMapToObject(translatedPosts); // Updates the current template in store to refresh the metabox UI.

          (0,external_this_wp_data_.dispatch)(MODULE_SITE_EDITOR_KEY).setTemplatePart(currentPost.id); // When the current language is the default one,
          // always returns back to the template parts list because user's custom template parts doesn't exist anymore after deletion.
          // For template parts provided by the theme as a file, template parts deletion remove user's customisation.
          // So it's consistent to redirect to the template parts list too.

          if (isDefaultLanguage(removedTranslation.lang.slug)) {
            // Simply remove the postId query string parameter from the URL to redirect to the template parts list.
            location.href = (0,external_this_wp_url_.removeQueryArgs)(location.href, 'postId');
          }
        }
      });
    }
  }
  /**
   * Returns the templateId of the template by clicking the delete link button component.
   * Used internally in the HOC withConfirmation as the getChangeValueCallback of the wrapped component.
   *
   * @param {object} event The onClick event from the Button component.
   * @returns {string} The link to delete the current item.
   */


  static getChangeValue(event) {
    const id = event.currentTarget.id;

    if ((0,external_lodash_.isEmpty)(id)) {
      return '';
    }

    return id.match(/\[(.[^[]+)\]/i)[1];
  }

  render() {
    const {
      lang: language,
      translated_post,
      links,
      template
    } = this.props.translationData; // translators: %s is a native language name.

    const translationScreenReaderText = (0,external_this_wp_i18n_.sprintf)((0,external_this_wp_i18n_.__)('Delete the translation in %s', 'polylang-pro'), language.name);
    const currentUserCanDelete = !(0,external_lodash_.isUndefined)(links.delete_link);
    const isTranslated = !(0,external_lodash_.isUndefined)(translated_post) && !(0,external_lodash_.isNil)(translated_post.id); // If the current user can't delete return nothing.

    if (!currentUserCanDelete) {
      return null;
    }

    return (0,external_this_wp_element_.createElement)(external_this_wp_components_.Button, _extends({
      icon: library_trash,
      label: translationScreenReaderText,
      disabled: !isTranslated
    }, template ? {
      id: `templateId[${template.id}]`
    } : {}, {
      className: "pll-button",
      onClick: this.props.handleChange
    }), (0,external_this_wp_element_.createElement)("span", {
      className: "screen-reader-text"
    }, translationScreenReaderText));
  }

}

DeleteButton.labelConfirmationModal = (0,external_this_wp_i18n_.__)('Delete template part confirmation', 'polylang-pro');

const delete_button_ModalContent = _ref2 => {
  let {
    isDefaultLang
  } = _ref2;
  return (0,external_this_wp_element_.createElement)(external_this_wp_element_.Fragment, null, isDefaultLang && (0,external_this_wp_element_.createElement)("p", null, (0,external_this_wp_i18n_.__)('You are about to delete the template part in the default language.', 'polylang-pro'), (0,external_this_wp_element_.createElement)("br", null), (0,external_this_wp_i18n_.__)('This will delete its customization and all its corresponding translations.', 'polylang-pro')), (0,external_this_wp_element_.createElement)("p", null, (0,external_this_wp_i18n_.__)('Are you sure you want to delete this template part?', 'polylang-pro')));
};

const DeleteWithConfirmation = confirmation_modal('pll_delete_translation', delete_button_ModalContent, DeleteButton.handleDelete)(DeleteButton);
/* harmony default export */ const delete_button = (DeleteWithConfirmation);
;// CONCATENATED MODULE: ./modules/block-editor/js/sidebar/components/translations-table/index.js


/**
 * WordPress dependencies
 *
 * @package Polylang-Pro
 */



/**
 * Internal dependencies
 */









const onChange = _ref => {
  let {
    value,
    post = null,
    translatedPosts,
    translationsTable,
    language
  } = _ref;
  const translationData = translationsTable.get(language.slug);

  if ((0,external_lodash_.isEmpty)(post)) {
    translationData.translated_post = {
      id: null,
      title: value
    };
    translationData.links = {
      add_link: translationData.links.add_link
    }; // unlink translation

    translatedPosts.delete(language.slug);
  } else {
    translatedPosts.set(language.slug, post.id);
    translationData.translated_post = {
      id: post.id,
      title: post.title.rendered
    };
    translationData.links.edit_link = post.edit_link;
  } // update translations table in store


  translationsTable.set(language.slug, translationData);
  (0,external_this_wp_data_.dispatch)(MODULE_CORE_EDITOR_KEY).editPost({
    translations: convertMapToObject(translatedPosts)
  }); // simulate a post modification to change status of the publish/update button

  (0,external_this_wp_data_.dispatch)(MODULE_CORE_EDITOR_KEY).editPost({
    title: (0,external_this_wp_data_.select)(MODULE_CORE_EDITOR_KEY).getEditedPostAttribute('title')
  });
};

const TranslationsTable = _ref2 => {
  let {
    selectedLanguage,
    translationsTable,
    translatedPosts,
    currentPost
  } = _ref2;
  // phpcs:disable WordPress.WhiteSpace.OperatorSpacing.NoSpaceBefore, WordPress.WhiteSpace.OperatorSpacing.NoSpaceAfter, Generic.Formatting.MultipleStatementAlignment.IncorrectWarning
  return (0,external_this_wp_element_.createElement)("div", {
    id: "post-translations",
    className: "translations"
  }, (0,external_this_wp_element_.createElement)("p", null, (0,external_this_wp_element_.createElement)("strong", null, (0,external_this_wp_i18n_.__)("Translations", "polylang-pro"))), (0,external_this_wp_element_.createElement)("table", null, (0,external_this_wp_element_.createElement)("tbody", null, Array.from(translationsTable.values()).map( // phpcs:disable PEAR.Functions.FunctionCallSignature.Indent, PEAR.Functions.FunctionCallSignature.EmptyLine
  translationData => {
    return selectedLanguage.slug !== translationData.lang.slug && (0,external_this_wp_element_.createElement)("tr", {
      key: translationData.lang.slug
    }, (0,external_this_wp_element_.createElement)("th", {
      className: "pll-language-column"
    }, !(0,external_lodash_.isEmpty)(translationData.lang.flag) ? (0,external_this_wp_element_.createElement)("span", {
      className: "pll-select-flag flag"
    }, (0,external_this_wp_element_.createElement)("img", {
      src: translationData.lang.flag_url,
      alt: translationData.lang.name,
      title: translationData.lang.name
    })) : (0,external_this_wp_element_.createElement)("abbr", null, translationData.lang.slug, (0,external_this_wp_element_.createElement)("span", {
      className: "screen-reader-text"
    }, translationData.lang.name))), isSiteBlockEditor() && (0,external_this_wp_element_.createElement)(external_this_wp_element_.Fragment, null, (0,external_this_wp_element_.createElement)("td", {
      className: "pll-translation-column"
    }, (0,external_this_wp_element_.createElement)("span", {
      className: "pll-translation-language"
    }, translationData.lang.name)), (0,external_this_wp_element_.createElement)("td", {
      className: "pll-edit-column pll-column-icon"
    }, (0,external_this_wp_element_.createElement)(add_edit_link, {
      translationData: translationData
    })), (0,external_this_wp_element_.createElement)("td", {
      className: "pll-delete-column pll-column-icon"
    }, (0,external_this_wp_element_.createElement)(delete_button, {
      translationData: translationData,
      currentPost: currentPost
    })), (0,external_this_wp_element_.createElement)("td", {
      className: "pll-default-lang-column pll-column-icon"
    }, translationData.is_default_lang && (0,external_this_wp_element_.createElement)(default_lang_icon, null))), !isSiteBlockEditor() && (0,external_this_wp_element_.createElement)(external_this_wp_element_.Fragment, null, (0,external_this_wp_element_.createElement)("td", {
      className: "pll-edit-column pll-column-icon"
    }, (0,external_this_wp_element_.createElement)(add_edit_link, {
      translationData: translationData
    })), (0,external_this_wp_element_.createElement)("td", {
      className: "pll-sync-column pll-column-icon"
    }, translationData.can_synchronize && (0,external_this_wp_element_.createElement)(synchronization_button, {
      translationData: translationData
    })), (0,external_this_wp_element_.createElement)("td", {
      className: "pll-translation-column"
    }, (0,external_this_wp_element_.createElement)("label", {
      className: "screen-reader-text",
      htmlFor: `tr_lang_${translationData.lang.slug}`
    },
    /* translators: accessibility text */
    (0,external_this_wp_i18n_.__)('Translation', 'polylang-pro')), (0,external_this_wp_element_.createElement)(translation_input, {
      id: `htr_lang_${translationData.lang.slug}`,
      autoFocus: false,
      translationsTable: translationsTable,
      translatedPosts: translatedPosts,
      translationData: translationData,
      value: !(0,external_lodash_.isUndefined)(translationData.translated_post) ? translationData.translated_post.title : '',
      onChange: onChange
    })))); // phpcs:enable PEAR.Functions.FunctionCallSignature.Indent, PEAR.Functions.FunctionCallSignature.EmptyLine
  })))); // phpcs:enable WordPress.WhiteSpace.OperatorSpacing.NoSpaceBefore, WordPress.WhiteSpace.OperatorSpacing.NoSpaceAfter, Generic.Formatting.MultipleStatementAlignment.IncorrectWarning
};

/* harmony default export */ const translations_table = (TranslationsTable);
;// CONCATENATED MODULE: ./modules/block-editor/js/sidebar/components/metabox/index.js


/**
 * WordPress dependencies
 *
 * @package Polylang-Pro
 */




/**
 * Internal dependencies
 */









class MetaBox extends external_this_wp_element_.Component {
  /**
   * Render the language metabox
   */
  render() {
    if (!this.props.isAllowedPostType) {
      return (0,external_this_wp_element_.createElement)("div", {
        className: "components-panel__body is-opened"
      }, (0,external_this_wp_element_.createElement)("div", {
        className: "pll-metabox-error components-notice is-warning"
      }, (0,external_this_wp_i18n_.__)('Templates are not translatable, only template parts are.', 'polylang-pro')));
    }

    return (0,external_this_wp_element_.createElement)("div", {
      className: "components-panel__body is-opened"
    }, !(0,external_lodash_.isNil)(this.props.selectedLanguage) ? (0,external_this_wp_element_.createElement)("form", {
      className: "pll-metabox-location"
    }, isTemplatePart(this.props.currentPost) ? (0,external_this_wp_element_.createElement)(external_this_wp_element_.Fragment, null, (0,external_this_wp_element_.createElement)(language_item, {
      language: this.props.selectedLanguage,
      translationsTable: this.props.translationsTable
    })) : (0,external_this_wp_element_.createElement)(external_this_wp_element_.Fragment, null, (0,external_this_wp_element_.createElement)(switcher, {
      selectedLanguage: this.props.selectedLanguage
    }), (0,external_this_wp_element_.createElement)(duplicate_button, null)), (0,external_this_wp_element_.createElement)(translations_table, {
      selectedLanguage: this.props.selectedLanguage,
      translationsTable: this.props.translationsTable,
      translatedPosts: this.props.translatedPosts,
      currentPost: this.props.currentPost
    })) : (0,external_this_wp_element_.createElement)("div", {
      className: "pll-metabox-error components-notice is-error"
    }, (0,external_this_wp_i18n_.__)('Unable to retrieve the content language', 'polylang-pro'))); // phpcs:enable WordPress.WhiteSpace.OperatorSpacing.NoSpaceBefore, WordPress.WhiteSpace.OperatorSpacing.NoSpaceAfter
  }

} // phpcs:disable PEAR.Functions.FunctionCallSignature.Indent, PEAR.Functions.FunctionCallSignature.EmptyLine

/**
 * High Order Component to wrap polylang sidebar component
 */


const wrapLanguagesPanel = select => {
  let lang = '';
  let translations_table = [];
  let translations = [];
  let currentPost = {};
  let isAllowedPostType = false;

  if (isSiteBlockEditor()) {
    currentPost = getCurrentTemplateFromDataStore();
  } else {
    currentPost = select(MODULE_CORE_EDITOR_KEY).getCurrentPost();
  }

  isAllowedPostType = !(0,external_lodash_.isNil)(currentPost) && !UNTRANSLATABLE_POST_TYPE.includes(currentPost.type);

  if (!(0,external_lodash_.isNil)(currentPost)) {
    lang = currentPost.lang;
    translations_table = currentPost.translations_table;
    translations = currentPost.translations;
  }

  const selectedLanguage = getSelectedLanguage(lang);
  const translationsTable = getTranslationsTable(translations_table, lang);
  const translatedPosts = getTranslatedPosts(translations, translations_table, lang);
  return {
    currentPost,
    selectedLanguage,
    translationsTable,
    translatedPosts,
    isAllowedPostType
  };
};

const MetaBoxWatch = (0,external_this_wp_data_.withSelect)(wrapLanguagesPanel)(MetaBox);
/* harmony default export */ const metabox = (MetaBoxWatch);
;// CONCATENATED MODULE: ./modules/block-editor/js/sidebar/components/sidebar/index.js


/**
 * WordPress dependencies
 *
 * @package Polylang-Pro
 */



/**
 * Internal Dependencies
 */


 // phpcs:disable WordPress.WhiteSpace.OperatorSpacing.NoSpaceBefore, WordPress.WhiteSpace.OperatorSpacing.NoSpaceAfter

const Sidebar = () => {
  if (isSiteBlockEditor()) {
    return (0,external_this_wp_element_.createElement)(external_this_wp_editSite_.PluginSidebar, {
      name: "polylang-sidebar",
      title: (0,external_this_wp_i18n_.__)('Languages', 'polylang-pro')
    }, (0,external_this_wp_element_.createElement)(metabox, null));
  } else {
    return (0,external_this_wp_element_.createElement)(external_this_wp_editPost_.PluginSidebar, {
      name: "polylang-sidebar",
      title: (0,external_this_wp_i18n_.__)('Languages', 'polylang-pro')
    }, (0,external_this_wp_element_.createElement)(metabox, null));
  }
}; // phpcs:enable WordPress.WhiteSpace.OperatorSpacing.NoSpaceBefore, WordPress.WhiteSpace.OperatorSpacing.NoSpaceAfter


/* harmony default export */ const sidebar = (Sidebar);
;// CONCATENATED MODULE: ./modules/block-editor/js/sidebar/components/menu-item/index.js


/**
 * @package Polylang-Pro
 */

 // phpcs:disable WordPress.WhiteSpace.OperatorSpacing.NoSpaceBefore, WordPress.WhiteSpace.OperatorSpacing.NoSpaceAfter

const MenuItem = () => (0,external_this_wp_element_.createElement)(external_this_wp_editPost_.PluginSidebarMoreMenuItem, {
  target: "polylang-sidebar"
}, (0,external_this_wp_i18n_.__)("Languages", "polylang-pro")); // phpcs:enable WordPress.WhiteSpace.OperatorSpacing.NoSpaceBefore, WordPress.WhiteSpace.OperatorSpacing.NoSpaceAfter


/* harmony default export */ const menu_item = (MenuItem);
;// CONCATENATED MODULE: ./modules/block-editor/js/sidebar/index.js


/**
 * Import styles
 *
 * @package Polylang-Pro
 */

/**
 * WordPress Dependencies
 */





/**
 * Internal dependencies
 */







/**
 * Polylang plugin sidebar component definition.
 */

const PolylangSidebar = () => {
  return (0,external_this_wp_element_.createElement)(external_this_wp_element_.Fragment, null, (0,external_this_wp_element_.createElement)(sidebar, null), (0,external_this_wp_element_.createElement)(menu_item, null));
};
/**
 * Call initialization of pll/metabox store for getting ready some datas
 */


if (isSiteBlockEditor()) {
  /**
   * Allows to refresh store data if the current language is not the default one
   * and if the given URL is for a template or template part list.
   *
   * @param {string} nextLocation The URL to check.
   * @return {void}
   */
  const maybeRefreshData = nextLocation => {
    const params = new URL(nextLocation).searchParams;
    const postType = params.get('postType');
    const postId = params.get('postId');

    if (!pll_block_editor_plugin_settings.lang.is_default_lang && (0,external_lodash_.isNil)(postId) && !(0,external_lodash_.isNil)(postType) && !(0,external_lodash_.isEmpty)((0,external_lodash_.intersection)([postType], ['wp_template', 'wp_template_part']))) {
      (0,external_this_wp_data_.dispatch)(MODULE_CORE_KEY).invalidateResolutionForStore();
    }
  }; // Let's refresh data when the client go back in history.


  window.onpopstate = event => {
    maybeRefreshData(document.location.href);
  }; // Overrides pushState and replaceState to call maybeRefreshData() when the client navigates through the Site Editor.


  (history => {
    const originalPushState = history.pushState;
    const originalReplaceState = history.replaceState;

    history.pushState = (state, key, path) => {
      maybeRefreshData(path);
      return originalPushState.apply(history, [state, key, path]);
    };

    history.replaceState = (state, key, path) => {
      maybeRefreshData(path);
      return originalReplaceState.apply(history, [state, key, path]);
    };
  })(window.history);

  isSiteEditorContextInitialized().then(function (result) {
    (0,external_this_wp_plugins_.registerPlugin)("polylang-sidebar", {
      icon: library_translation,
      render: PolylangSidebar
    });
  });
} else {
  isBlockPostEditorContextInitialized().then(function (result) {
    (0,external_this_wp_plugins_.registerPlugin)("polylang-sidebar", {
      icon: library_translation,
      render: PolylangSidebar
    });
  }, function (reason) {
    console.info(reason);
  });
}
})();

this["polylang-pro"] = __webpack_exports__;
/******/ })()
;