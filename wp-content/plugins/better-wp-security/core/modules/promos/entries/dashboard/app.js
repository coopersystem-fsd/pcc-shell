/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { Button } from '@wordpress/components';

/**
 * Internal dependencies
 */
import {
	BelowToolbarFill,
	EditCardsFill,
} from '@ithemes/security.dashboard.api';
import {
	useConfigContext,
	PromoCard,
} from '@ithemes/security.dashboard.dashboard';
import { LogoProWhite } from '@ithemes/security-style-guide';
import { FlexSpacer } from '@ithemes/security-components';
import { useLocalStorage } from '@ithemes/security-hocs';
import './style.scss';

export default function App() {
	const { installType } = useConfigContext();

	if ( installType === 'pro' ) {
		return null;
	}

	return (
		<>
			<BelowToolbarFill>
				{ ( { page, dashboardId } ) =>
					dashboardId > 0 && page === 'view-dashboard' && <Footer />
				}
			</BelowToolbarFill>
			<EditCardsFill>
				<PromoCard title={ __( 'Trusted Devices', 'better-wp-security' ) } />
				<PromoCard title={ __( 'Updates Summary', 'better-wp-security' ) } />
				<PromoCard title={ __( 'User Security Profiles', 'better-wp-security' ) } />
			</EditCardsFill>
		</>
	);
}

function Footer() {
	const [ isDismissed, setIsDismiss ] = useLocalStorage(
		'itsecPromoProUpgrade'
	);

	if ( isDismissed ) {
		return null;
	}

	return (
		<aside className="itsec-promo-pro-upgrade">
			<LogoProWhite />
			<section>
				<h2>
					{ __( 'Unlock More Security Features with Pro', 'better-wp-security' ) }
				</h2>
				<p>
					{ __(
						'Go beyond the basics with premium features & support.',
						'better-wp-security'
					) }
				</p>
			</section>
			<FlexSpacer />
			<a
				href="https://ithem.es/included-with-pro"
				className="itsec-promo-pro-upgrade__details"
			>
				{ __( 'What’s included with Pro?', 'better-wp-security' ) }
			</a>
			<Button
				className="itsec-promo-pro-upgrade__button"
				href="https://ithem.es/go-security-pro-now"
			>
				{ __( 'Go Pro Now', 'better-wp-security' ) }
			</Button>
			<Button
				icon="dismiss"
				className="itsec-promo-pro-upgrade__close"
				label={ __( 'Dismiss', 'better-wp-security' ) }
				onClick={ () => setIsDismiss( true ) }
			/>
		</aside>
	);
}
