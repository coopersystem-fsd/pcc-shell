/**
 * External dependencies
 */
import { sortBy, filter } from 'lodash';
import { useParams } from 'react-router-dom';

/**
 * WordPress dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import { Card, Button, Flex, FlexItem } from '@wordpress/components';
import { useSelect, useDispatch } from '@wordpress/data';
import {
	useMemo,
	useState,
	createInterpolateElement,
} from '@wordpress/element';

/**
 * Internal dependencies
 */
import { useSingletonEffect } from '@ithemes/security-hocs';
import { Accordion, Spinner } from '@ithemes/security-components';
import { MODULES_STORE_NAME } from '@ithemes/security.packages.data';
import { useGlobalNavigationUrl } from '@ithemes/security-utils';
import { PageHeader } from '../../components';
import { ONBOARD_STORE_NAME } from '../../stores';
import ToughGuy from './tough-guy.svg';
import './style.scss';

export default function SecureSite() {
	useCompletionSteps();
	const [ isEndScreen, setIsEndScreen ] = useState( false );

	if ( isEndScreen ) {
		return <EndScreen />;
	}

	return <OverviewScreen goToEnd={ () => setIsEndScreen( true ) } />;
}

function OverviewScreen( { goToEnd } ) {
	const { root } = useParams();
	const { completeOnboarding } = useDispatch( ONBOARD_STORE_NAME );
	const { steps, currentStep } = useSelect(
		( select ) => ( {
			steps: select( ONBOARD_STORE_NAME ).getCompletionSteps(),
			currentStep: select( ONBOARD_STORE_NAME ).getCompletionStep(),
		} ),
		[]
	);

	let subtitle;

	if ( currentStep === true ) {
		subtitle = __( 'Your site has been secured.', 'better-wp-security' );
	} else if ( currentStep === false ) {
		subtitle = __( 'Click finish to secure your site.', 'better-wp-security' );
	} else {
		subtitle = __( 'Your site is being secured.', 'better-wp-security' );
	}

	return (
		<>
			<PageHeader
				title={ __( 'Secure Site', 'better-wp-security' ) }
				subtitle={ subtitle }
				breadcrumbs={ false }
			/>

			<h2 className="itsec-secure-site-overview">
				{ __( 'Overview', 'better-wp-security' ) }
			</h2>

			<Steps steps={ steps } currentStep={ currentStep } />

			<Flex justify="right">
				<FlexItem>
					{ currentStep === true ? (
						<Button isPrimary onClick={ goToEnd }>
							{ __( 'Finish', 'better-wp-security' ) }
						</Button>
					) : (
						<Button
							isPrimary
							onClick={ () => completeOnboarding( { root } ) }
							disabled={ currentStep !== false }
						>
							{ __( 'Secure Site', 'better-wp-security' ) }
						</Button>
					) }
				</FlexItem>
			</Flex>
		</>
	);
}

function EndScreen() {
	const dashboardLink = useGlobalNavigationUrl( 'dashboard' ),
		settingsLink = useGlobalNavigationUrl( 'settings' );

	return (
		<>
			<PageHeader
				align="center"
				title={ __(
					'Good work! Your site is more secure than ever.',
					'better-wp-security'
				) }
				subtitle={ __(
					'You can now move on with other things in your life.',
					'better-wp-security'
				) }
				breadcrumbs={ false }
			/>

			<figure className="itsec-secure-site-end-graphic">
				<ToughGuy />
			</figure>

			<p className="itsec-secure-site-end-content">
				{ createInterpolateElement(
					__(
						'If you want to dig into your site’s security further, checkout your <dashboard>security dashboard</dashboard>, and make changes via <settings>settings</settings>.',
						'better-wp-security'
					),
					{
						// eslint-disable-next-line jsx-a11y/anchor-has-content
						dashboard: <a href={ dashboardLink } />,
						// eslint-disable-next-line jsx-a11y/anchor-has-content
						settings: <a href={ settingsLink } />,
					}
				) }
			</p>

			<Flex justify="center">
				<FlexItem>
					<Button isPrimary href={ dashboardLink }>
						{ __( 'Dashboard', 'better-wp-security' ) }
					</Button>
				</FlexItem>

				<FlexItem>
					<Button isPrimary href={ settingsLink }>
						{ __( 'Settings', 'better-wp-security' ) }
					</Button>
				</FlexItem>
			</Flex>
		</>
	);
}

function Steps( { steps, currentStep } ) {
	const { root } = useParams();
	const [ expanded, setExpanded ] = useState( false );
	const panels = useMemo(
		() =>
			sortBy(
				filter(
					steps,
					( { activeCallback } ) =>
						! activeCallback || activeCallback( { root } )
				),
				'priority'
			).map( ( { render: Component, ...step } ) => {
				const isCurrent = step.id === currentStep?.id;
				const isDone = step.priority < ( currentStep?.priority || 0 );
				const isPending =
					step.priority > ( currentStep?.priority || 0 );

				return {
					name: step.id,
					title: step.label,
					text: step.label,
					icon: 'yes-alt',
					render:
						Component &&
						( ( props ) => (
							<div { ...props }>
								<Component />
							</div>
						) ),
					showSpinner:
						currentStep !== true && ( isCurrent || isPending ) ? (
							<Spinner
								size={ 30 }
								color="--itsec-primary-theme-color"
								paused={ isPending }
							/>
						) : (
							false
						),
					className: isDone && 'itsec-secure-site-step--complete',
				};
			} ),
		[ steps, currentStep ]
	);

	return (
		<Card>
			<Accordion
				isStyled
				className="itsec-secure-site-steps"
				allowNone
				panels={ panels }
				expanded={ expanded }
				setExpanded={ setExpanded }
			/>
		</Card>
	);
}

function useCompletionSteps() {
	const { registerCompletionStep } = useDispatch( ONBOARD_STORE_NAME );
	const { saveModules, saveSettings } = useDispatch( MODULES_STORE_NAME );

	useSingletonEffect( useCompletionSteps, () => {
		registerCompletionStep( {
			id: 'savingModules',
			label: __( 'Enable Features', 'better-wp-security' ),
			priority: 5,
			callback() {
				return saveModules();
			},
			render: function SavingModules() {
				const modules = useSelect(
					( select ) =>
						select( MODULES_STORE_NAME ).getEditedModules(),
					[]
				).filter(
					( module ) =>
						module.status.selected === 'active' && module.onboard
				);

				if ( ! modules.length ) {
					return (
						<p>
							{ __(
								'No additional security features have been selected.',
								'better-wp-security'
							) }
						</p>
					);
				}

				return (
					<>
						<p>
							{ __(
								'The following security features will be enabled:',
								'better-wp-security'
							) }
						</p>
						<ul>
							{ modules.map( ( module ) => (
								<li key={ module.id }>{ module.title }</li>
							) ) }
						</ul>
					</>
				);
			},
		} );

		registerCompletionStep( {
			id: 'savingSettings',
			label: __( 'Configure Settings', 'better-wp-security' ),
			priority: 10,
			callback() {
				return saveSettings();
			},
			render: function SavingSettings() {
				const settings = useSelect( ( select ) => {
					return select( MODULES_STORE_NAME )
						.getEditedModules()
						.filter(
							( module ) =>
								module.status.selected === 'active' &&
								module.settings?.onboard?.length > 0
						)
						.flatMap( ( module ) => {
							const edits = select(
								MODULES_STORE_NAME
							).getSettingEdits( module.id );

							return module.settings.onboard.reduce(
								( acc, setting ) => {
									if ( ! edits || ! edits[ setting ] ) {
										return acc;
									}

									const title =
										module.settings.schema?.uiSchema?.[
											setting
										]?.[ 'ui:title' ] ||
										module.settings.schema.properties[
											setting
										].title;

									acc.push(
										sprintf(
											/* translators: 1. Module title, 2. Setting title. */
											__( '%1$s: %2$s', 'better-wp-security' ),
											module.title,
											title
										)
									);

									return acc;
								},
								[]
							);
						} );
				}, [] );

				if ( ! settings.length ) {
					return (
						<p>
							{ __(
								'No settings have been configured.',
								'better-wp-security'
							) }
						</p>
					);
				}

				return (
					<>
						<p>
							{ __(
								'The following settings will be configured:',
								'better-wp-security'
							) }
						</p>
						<ul>
							{ settings.map( ( setting, i ) => (
								<li key={ i }>{ setting }</li>
							) ) }
						</ul>
					</>
				);
			},
		} );
	} );
}
