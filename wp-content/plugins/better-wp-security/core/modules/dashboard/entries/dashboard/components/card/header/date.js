/**
 * External dependencies
 */
import { isString } from 'lodash';
import memize from 'memize';

/**
 * WordPress dependencies
 */
import { Fragment, useState } from '@wordpress/element';
import {
	Button,
	Dashicon,
	Modal,
	SelectControl,
	TextControl,
} from '@wordpress/components';
import { compose } from '@wordpress/compose';
import { withDispatch, withSelect } from '@wordpress/data';
import { __, sprintf } from '@wordpress/i18n';
import { dateI18n, format } from '@wordpress/date';

function getPeriod( queryArgs, config ) {
	if ( queryArgs.period ) {
		return queryArgs.period;
	}

	if ( config.query_args.period ) {
		return config.query_args.period.default;
	}
}

function getPeriodLabel( period ) {
	if ( ! period ) {
		return '';
	}

	const now = new window.Date();
	let start, end;

	switch ( period ) {
		case '24-hours':
			return __( '24 Hours', 'better-wp-security' );
		case '30-days':
			start = dateI18n( 'M j', now.setDate( now.getDate() - 30 ) );
			end = dateI18n( 'M j' );
			break;
		case 'week':
			start = dateI18n( 'M j', now.setDate( now.getDate() - 7 ) );
			end = dateI18n( 'M j' );
			break;
		default:
			start = dateI18n( 'M j', period.start );
			end = dateI18n( 'M j', period.end );
			break;
	}

	return sprintf(
		/* translators: 1. The start time, 2. The end time. */
		__( '%1$s - %2$s', 'better-wp-security' ),
		start,
		end
	);
}

const getDateOptions = memize( () => {
	return [
		{ value: '24-hours', label: __( '24 Hours', 'better-wp-security' ) },
		{ value: 'week', label: __( '7 Days', 'better-wp-security' ) },
		{ value: '30-days', label: __( '30 Days', 'better-wp-security' ) },
		{ value: 'custom', label: __( 'Custom', 'better-wp-security' ) },
	];
} );

const now = new window.Date();
const MIN = format( 'Y-m-d', now.setDate( now.getDate() - 60 ) );
const MAX = format( 'Y-m-d' );

function Date( {
	queryArgs,
	config,
	update,
} ) {
	const [ isOpen, setIsOpen ] = useState( false );
	const [ start, setStart ] = useState( undefined );
	const [ end, setEnd ] = useState( undefined );
	let [ periodOption, setPeriodOption ] = useState( undefined );
	const period = getPeriod( queryArgs, config );
	const periodLabel = getPeriodLabel( period );
	periodOption = periodOption || ( isString( period ) ? period : 'custom' );

	const onApply = ( e ) => {
		e.preventDefault();

		let newPeriod;

		if ( 'custom' === periodOption ) {
			newPeriod = { start, end };
		} else {
			newPeriod = periodOption;
		}

		update( { ...queryArgs, period: newPeriod } );
		setIsOpen( false );
	};

	return (
		<div className="itsec-card-header-date">
			<Button
				onClick={ () => setIsOpen( ! isOpen ) }
				title={ periodLabel }
				aria-expanded={ isOpen }
				aria-label={ sprintf(
					/* translators: 1. The current search period or interval Eg, 24 hours. */
					__( '%s (click to change)', 'better-wp-security' ),
					periodLabel
				) }
			>
				<span className="itsec-card-header-date__period">
					{ periodLabel }
				</span>
				<Dashicon
					icon="calendar"
					className="itsec-card-header-date__icon"
				/>
			</Button>
			{ isOpen && (
				<Modal
					title={ __( 'Change Date Period', 'better-wp-security' ) }
					onRequestClose={ () => setIsOpen( false ) }
				>
					<SelectControl
						options={ getDateOptions() }
						value={ periodOption }
						onChange={ ( newPeriod ) =>
							setPeriodOption( newPeriod )
						}
					/>
					{ periodOption === 'custom' && (
						<Fragment>
							<TextControl
								type="date"
								min={ MIN }
								max={ MAX }
								value={ start }
								onChange={ ( newStart ) =>
									setStart( newStart )
								}
								label={ __( 'Start Date', 'better-wp-security' ) }
								placeholder="YYYY-MM-DD"
							/>
							<TextControl
								type="date"
								min={ MIN }
								max={ MAX }
								value={ end }
								onChange={ ( newEnd ) =>
									setEnd( newEnd )
								}
								label={ __( 'End Date', 'better-wp-security' ) }
								placeholder="YYYY-MM-DD"
							/>
						</Fragment>
					) }
					<Button isPrimary onClick={ onApply }>
						{ __( 'Apply', 'better-wp-security' ) }
					</Button>
				</Modal>
			) }
		</div>
	);
}

export default compose( [
	withSelect( ( select, { card } ) => ( {
		queryArgs:
			select( 'ithemes-security/dashboard' ).getDashboardCardQueryArgs(
				card.id
			) || {},
	} ) ),
	withDispatch( ( dispatch, { card } ) => ( {
		update( queryArgs ) {
			return dispatch( 'ithemes-security/dashboard' ).queryDashboardCard(
				card.id,
				queryArgs
			);
		},
	} ) ),
] )( Date );
