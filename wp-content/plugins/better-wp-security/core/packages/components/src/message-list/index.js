/**
 * External dependencies
 */
import classnames from 'classnames';
import { castArray } from 'lodash';

/**
 * WordPress dependencies
 */
import { Button } from '@wordpress/components';

/**
 * Internal dependencies
 */
import './style.scss';

export default function MessageList( {
	type = 'info',
	title,
	messages = [],
	className,
	onDismiss,
	hasBorder,
	recommended,
} ) {
	messages = castArray( messages );

	if ( ! messages.length ) {
		return null;
	}

	return (
		<div
			className={ classnames(
				'itsec-message-list',
				className,
				`itsec-message-list--type-${ type }`,
				{
					'itsec-message-list--has-border': hasBorder,
					'itsec-message-list--recommended': recommended,
				}
			) }
		>
			<div>
				{ title && <h3>{ title }</h3> }
				<ul>
					{ messages.map( ( message, i ) => {
						return <li key={ i }>{ message }</li>;
					} ) }
				</ul>
			</div>
			{ onDismiss && <Button icon="dismiss" onClick={ onDismiss } /> }
		</div>
	);
}
