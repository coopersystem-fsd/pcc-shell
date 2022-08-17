/**
 * External dependencies
 */
import { Redirect, Route, Switch, useRouteMatch } from 'react-router-dom';

/**
 * Internal dependencies
 */
import { NoticeList } from '@ithemes/security-components';
import { usePages } from '../../page-registration';
import {
	Navigation,
	AdvancedNavigation,
	Main,
	Sidebar,
} from '../../components';

export default function Settings() {
	const pages = usePages();
	const { url, path } = useRouteMatch();

	return (
		<Switch>
			{ pages.map( ( { id, render: Component } ) => (
				<Route path={ `${ path }/:page(${ id })` } key={ id }>
					<Sidebar>
						<Navigation />
						<AdvancedNavigation />
					</Sidebar>
					<Main>
						<NoticeList />
						<Component />
					</Main>
				</Route>
			) ) }

			<Route path={ url }>
				{ pages.length > 0 && (
					<Redirect to={ `${ url }/${ pages[ 0 ].id }` } />
				) }
				<Sidebar>
					<Navigation />
					<AdvancedNavigation />
				</Sidebar>
				<Main />
			</Route>
		</Switch>
	);
}
