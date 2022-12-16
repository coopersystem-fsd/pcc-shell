/**
 * External dependencies
 */
import { ErrorBoundary } from 'react-error-boundary';
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import { pure } from '@wordpress/compose';
import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { withProps } from '@ithemes/security-hocs';
import { useCardElementQueries, useCardRenderer } from '../../cards';
import CardUnknown from '../empty-states/card-unknown';
import CardCrash from '../empty-states/card-crash';
import './style.scss';

function Card( { id, dashboardId, className, gridWidth, children, ...rest } ) {
	const { card, config } = useSelect(
		( select ) => ( {
			card: select( 'ithemes-security/dashboard' ).getDashboardCard( id ),
			config:
				select( 'ithemes-security/dashboard' ).getDashboardCardConfig(
					id
				) || {},
		} ),
		[ id ]
	);
	const CardRender = useCardRenderer( config );
	const eqProps = useCardElementQueries( config, rest.style, gridWidth );

	if ( card.card === 'unknown' ) {
		return (
			<article
				className={ classnames(
					className,
					'itsec-card',
					'itsec-card--unknown'
				) }
				{ ...rest }
			>
				<CardUnknown card={ card } dashboardId={ dashboardId } />
			</article>
		);
	}

	if ( ! CardRender ) {
		return (
			<article
				className={ classnames(
					className,
					'itsec-card',
					'itsec-card--no-rendered'
				) }
				{ ...rest }
			>
				<CardCrash card={ card } config={ config } />
			</article>
		);
	}

	return (
		<article
			className={ classnames( className, 'itsec-card' ) }
			id={ `itsec-card-${ card.id }` }
			{ ...rest }
			{ ...eqProps }
		>
			<ErrorBoundary
				FallbackComponent={ withProps( { card, config } )( CardCrash ) }
			>
				<CardRender
					card={ card }
					config={ config }
					dashboardId={ dashboardId }
					eqProps={ eqProps }
				/>
			</ErrorBoundary>
			{ children }
		</article>
	);
}

export default pure( Card );
