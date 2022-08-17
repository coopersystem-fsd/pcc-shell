/**
 * WordPress dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import { Component, Fragment } from '@wordpress/element';
import { dateI18n } from '@wordpress/date';

/**
 * Internal dependencies
 */
import lockoutController from './lockout-controller';

class Detail extends Component {
	static defaultProps = {
		master: {},
		isVisible: false,
	};

	state = {
		details: null,
		viewLog: 0,
	};

	componentDidMount() {
		if ( this.props.isVisible ) {
			this.fetchDetails( this.props.master.id );
		}
	}

	componentDidUpdate( prevProps ) {
		const fetch =
			( this.props.isVisible && ! prevProps.isVisible ) ||
			this.props.master.id !== prevProps.master.id;

		if ( fetch ) {
			this.fetchDetails( this.props.master.id );
		}
	}

	shouldComponentUpdate( nextProps, nextState ) {
		if ( this.props.master.id !== nextProps.master.id ) {
			return true;
		}

		if ( this.props.isVisible !== nextProps.isVisible ) {
			return true;
		}

		if ( ! this.state.details && nextState.details ) {
			return true;
		}

		if ( this.state.viewLog !== nextState.viewLog ) {
			return true;
		}

		return false;
	}

	fetchDetails = ( id ) => {
		if ( ! this.props.master.links.item ) {
			return;
		}

		const url = this.props.master.links.item[ 0 ].href.replace(
			'{lockout_id}',
			id
		);

		lockoutController.getDetails( url ).then( ( details ) => {
			if ( this.unmounted || this.props.master.id !== id ) {
				return;
			}

			this.setState( { details } );
		} );
	};

	componentWillUnmount() {
		this.unmounted = true;
	}

	render() {
		const { master } = this.props;
		const { details } = this.state;

		return (
			<div className="itsec-card-active-lockouts__detail-container">
				<time
					className="itsec-card-active-lockouts__start-time"
					dateTime={ master.start_gmt }
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

				{ details && details.history.length > 0 && (
					<Fragment>
						<hr />

						<div className="itsec-card-active-lockouts__history">
							<h4 className="itsec-card-active-lockouts__history-title">
								{ __( 'History', 'better-wp-security' ) }
							</h4>
							<ul>
								{ details.history.map( this.renderHistory ) }
							</ul>
						</div>
					</Fragment>
				) }
			</div>
		);
	}

	renderHistory = ( history ) => {
		if ( ! history.label ) {
			return;
		}

		const time = (
			<time
				dateTime={ history.time }
				title={ dateI18n( 'M d, Y g:s A', history.time ) }
			>
				{ sprintf(
					/* translators: 1. Relative time from human_time_diff(). */
					__( '%s ago', 'better-wp-security' ),
					history.time_relative
				) }
			</time>
		);

		return (
			<li key={ history.id }>
				<code>{ history.label }</code>
				{ ' – ' }
				{ time }
			</li>
		);
	};
}

export default Detail;
