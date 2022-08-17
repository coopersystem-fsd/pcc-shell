/**
 * WordPress dependencies
 */
import { createRegistrySelector } from '@wordpress/data';
import { MODULES_STORE_NAME } from './';

/**
 * Get a WP User by its ID.
 *
 * @param {Object} state
 * @param {number} userId
 * @return {Object} User data.
 */
export function getUser( state, userId ) {
	return state.users.byId[ userId ];
}

/**
 * Get the current user.
 *
 * @param {Object} state The store state.
 * @return {Object} The current user object.
 */
export function getCurrentUser( state ) {
	return state.users.byId[ state.users.currentId ];
}

export function getIndex( state ) {
	return state.index;
}

/**
 * Get a schema from the root index.
 *
 * @param {Object} state
 * @param {string} schemaId The full schema ID like ithemes-security-user-group
 * @return {Object|null} The schema.
 */
export function getSchema( state, schemaId ) {
	const index = state.index;

	if ( ! index ) {
		return null;
	}

	for ( const route in index.routes ) {
		if ( ! index.routes.hasOwnProperty( route ) ) {
			continue;
		}

		const schema = index.routes[ route ].schema;

		if ( schema && schema.title === schemaId ) {
			return schema;
		}
	}

	return null;
}

export function getRoles( state ) {
	return state.index?.roles || null;
}

export function getActorTypes( state ) {
	return state.actors.types;
}

export function getActors( state, type ) {
	return state.actors.byType[ type ];
}

export function getSiteInfo( state ) {
	return state.siteInfo;
}

export const getFeatureFlags = createRegistrySelector(
	( select ) => ( state ) => {
		const setting = select( MODULES_STORE_NAME ).getSetting(
			'feature-flags',
			'enabled'
		);

		return setting || state.featureFlags;
	}
);

export function getBatchMaxItems( state ) {
	return state.batchMaxItems;
}
