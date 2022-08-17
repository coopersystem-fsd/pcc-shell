/**
 * Internal dependencies
 */
import { apiFetch } from './controls';

/**
 * Fetch the index.
 *
 * @param {boolean} breakCache Whether to break the cache or not.
 * @return {Object} The index.
 */
export function* fetchIndex( breakCache = false ) {
	let path = '/ithemes-security/v1?context=help';

	if ( breakCache ) {
		path += '&_=' + Date.now();
	}

	const index = yield apiFetch( { path } );
	yield receiveIndex( index );

	return index;
}

export function receiveIndex( index ) {
	return {
		type: RECEIVE_INDEX,
		index,
	};
}

export function receiveUser( user ) {
	return {
		type: RECEIVE_USER,
		user,
	};
}

export function receiveCurrentUserId( userId ) {
	return {
		type: RECEIVE_CURRENT_USER_ID,
		userId,
	};
}

export function receiveActorTypes( types ) {
	return {
		type: RECEIVE_ACTOR_TYPES,
		types,
	};
}

export function receiveActors( type, actors ) {
	return {
		type: RECEIVE_ACTORS,
		actorType: type,
		actors,
	};
}

export function receiveSiteInfo( siteInfo ) {
	return {
		type: RECEIVE_SITE_INFO,
		siteInfo,
	};
}

export function __unstableLoadInitialFeatureFlags( flags ) {
	return {
		type: LOAD_INITIAL_FEATURE_FLAGS,
		flags,
	};
}

export function receiveBatchMaxItems( maxItems ) {
	return {
		type: RECEIVE_BATCH_MAX_ITEMS,
		maxItems,
	};
}

export const RECEIVE_INDEX = 'RECEIVE_INDEX';
export const RECEIVE_USER = 'RECEIVE_USER';
export const RECEIVE_CURRENT_USER_ID = 'RECEIVE_CURRENT_USER_ID';
export const RECEIVE_ACTOR_TYPES = 'RECEIVE_ACTOR_TYPES';
export const RECEIVE_ACTORS = 'RECEIVE_ACTORS';
export const RECEIVE_SITE_INFO = 'RECEIVE_SITE_INFO';
export const LOAD_INITIAL_FEATURE_FLAGS = 'LOAD_INITIAL_FEATURE_FLAGS';
export const RECEIVE_BATCH_MAX_ITEMS = 'RECEIVE_BATCH_MAX_ITEMS';
