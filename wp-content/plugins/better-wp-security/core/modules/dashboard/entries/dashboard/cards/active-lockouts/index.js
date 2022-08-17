/**
 * External dependencies
 */
import { isEmpty } from 'lodash';
import memize from 'memize';
/**
 * WordPress dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import { compose, pure } from '@wordpress/compose';
import { Fragment, useState } from '@wordpress/element';
import { useSelect, useDispatch } from '@wordpress/data';
import { dateI18n } from '@wordpress/date';
import { Button } from '@wordpress/components';

/**
 * iThemes dependencies
 */
import { SearchControl } from '@ithemes/ui';

/**
 * Internal dependencies
 */
import { withDebounceHandler } from '@ithemes/security-hocs';
import Header, { Title } from '../../components/card/header';
import Footer from '../../components/card/footer';
import MasterDetail, { Back } from '../../components/master-detail';
import { CardHappy } from '../../components/empty-states';
import Detail from './Detail';
import lockoutController from './lockout-controller';
import './style.scss';

function MasterRender( { master } ) {
	return (
		<Fragment>
			<time
				className="itsec-card-active-lockouts__start-time"
				dateTime={ master.start_gmt }
				title={ dateI18n( 'M d, Y g:s A', master.start_gmt ) }
			>
				{ sprintf(
					/* translators: 1. Relative time from human_time_diff(). */
					__( '%s ago', 'better-wp-security' ),
					master.start_gmt_relative
				) }
			</time>
			<h3 className="itsec-card-active-lockouts__label">
				{ master.label }
			</h3>
			<p className="itsec-card-active-lockouts__description">
				{ master.description }
			</p>
		</Fragment>
	);
}

const withLinks = memize( function( lockouts, links ) {
	return lockouts.map( ( lockout ) => ( {
		...lockout,
		links,
	} ) );
} );

function ActiveLockouts( {
	card,
	config,
} ) {
	const [ selectedId, setSelectedId ] = useState( 0 );
	const [ releasingIds, setReleasingIds ] = useState( [] );
	const [ searchTerm, setSearchTerm ] = useState( '' );

	const { isQuerying } = useSelect(
		( select ) => ( {
			isQuerying: select( 'ithemes-security/dashboard' ).isQueryingDashboardCard( card.id ),
		} ),
		[ card.id ]
	);
	const { queryDashboardCard: query, refreshDashboardCard } = useDispatch( 'ithemes-security/dashboard' );
	const select = ( id ) => {
		return setSelectedId( id );
	};

	const onRelease = async ( e ) => {
		e.preventDefault();
		setReleasingIds( [ ...releasingIds, selectedId ] );

		try {
			await lockoutController.release(
				card._links[
					'ithemes-security:release-lockout'
				][ 0 ].href.replace( '{lockout_id}', selectedId )
			);
		} catch ( error ) {
			// eslint-disable-next-line no-console
			console.warn( error );
		}

		await refreshDashboardCard( card.id );

		setSelectedId( 0 );
		setReleasingIds( releasingIds.filter( ( id ) => id !== selectedId ) );
	};

	const isSmall = true;

	return (
		<div className="itsec-card--type-active-lockouts">
			<Header>
				<Back
					isSmall={ isSmall }
					select={ select }
					selectedId={ selectedId }
				/>
				<Title card={ card } config={ config } />
			</Header>
			{ selectedId === 0 && (
				<div className="itsec-card-active-lockouts__search-container">
					<SearchControl
						value={ searchTerm }
						onChange={ ( next ) => {
							setSearchTerm( next );
							query( card.id, next ? { search: next } : {} );
						} }
						placeholder={ __( 'Search Lockouts', 'better-wp-security' ) }
						isSearching={ isQuerying }
						surfaceVariant="secondary"
					/>
				</div>
			) }
			{ isEmpty( card.data.lockouts ) ? (
				<CardHappy
					title={ __( 'All Clear!', 'better-wp-security' ) }
					text={ __(
						'No users are currently locked out of your site.',
						'better-wp-security'
					) }
				/>
			) : (
				<MasterDetail
					masters={ withLinks( card.data.lockouts, card._links ) }
					detailRender={ Detail }
					masterRender={ MasterRender }
					mode="list"
					selectedId={ selectedId }
					select={ select }
					isSmall={ isSmall }
				/>
			) }
			{ selectedId > 0 &&
				card._links[ 'ithemes-security:release-lockout' ] && (
				<Footer>
					<span className="itsec-card-footer__action">
						<Button
							isPrimary
							isSmall
							aria-disabled={ releasingIds.includes(
								selectedId
							) }
							isBusy={ releasingIds.includes( selectedId ) }
							onClick={ onRelease }
						>
							{ __( 'Release Lockout', 'better-wp-security' ) }
						</Button>
					</span>
				</Footer>
			) }
		</div>
	);
}

export const slug = 'active-lockouts';
export const settings = {
	render: compose( [
		withDebounceHandler( 'query', 500, { leading: true } ),
		pure,
	] )( ActiveLockouts ),
	elementQueries: [
		{
			type: 'width',
			dir: 'max',
			px: 500,
		},
	],
};
