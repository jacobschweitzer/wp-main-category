var WPMainCategoryGutenberg = ( function() {

	const { addFilter } = wp.hooks;
	const { createElement, Fragment } = wp.element;
	const { SelectControl } = wp.components;
	const { addQueryArgs } = wp.url;
	const apiFetch = wp.apiFetch;
	const { sortBy, invoke } = lodash;
	const { select } = wp.data;
	const { __ } = wp.i18n;

	const DEFAULT_QUERY = {
		per_page: -1,
		orderby:  'id',
		order:    'asc',
		_fields:  'id,name',
	};

	/**
	 * Initializes primary term selection for gutenberg.
	 *
	 * @function
	 * @return {undefined}
	 */
	const load = () => {

		const getMainCategoryHolder = () => document.getElementById( 'wpmc[_primary_category]' );
		const getMainCategoryId     = () => getMainCategoryHolder().value;
		const setMainCategoryId     = ( id ) => getMainCategoryHolder().value = id;

		var dataStores = {};
		class DataStore {
			constructor( slug ) {
				this.slug = slug;
				this.reset();
			}

			registeredData() {
				return !! Object.keys( this.data ).length;
			}

			reset() {
				this.data = {};
			}

			read() {
				return this.data;
			}

			get( what ) {
				return what in this.data && this.data[ what ] || null;
			}

			set( what, value ) {
				return this.data[ what ] = value;
			}
		}
		const createStore = () => dataStores.categories = new DataStore( 'categories' );
		const getStore    = () => dataStores.categories || createStore( 'categories' );

		class MainCategorySelectorHandler extends React.Component {
			constructor( props ) {
				super( props );

				this.state = {
					loading: true,
				};
			}

			componentDidMount() {
				this.updateData();
				if ( this.getDataStore().registeredData() ) {
					this.setState( {
						loading: false,
					} );
				}
			}

			componentWillUnmount() {
				invoke( this.fetchRequest, [ 'abort' ] );
				invoke( this.addRequest, [ 'abort' ] );
			}

			componentDidUpdate( prevProps, prevState ) {
				this.updateData();
				if ( this.props.terms.length > 2 && ! this.getDataStore().get( 'availableTerms' ) ) {
					this.fetchTerms();
				} else if ( this.props.terms !== prevProps.terms ) {
					let newID = this.props.terms.filter( termID => prevProps.terms.indexOf( termID ) === -1 );
					if ( newID.length && ! this.isTermAvailable( newID[0] ) ) {
						this.fetchTerms();
						this.saveMainCategory();
					}
				}

				const isSavingPost     = select( 'core/editor' ).isSavingPost();
				const isAutosavingPost = select( 'core/editor' ).isAutosavingPost();
				const isPreviewingPost = select( 'core/editor' ).isPreviewingPost();
				if ( isSavingPost && ! isAutosavingPost && ! isPreviewingPost ) {
					this.saveMainCategory();
				}
			}

			saveMainCategory() {
				const data = {
					value:   getMainCategoryId(),
					post_id: document.getElementById( 'post_ID' ).value,
					nonce:   wpmc.nonce,
				};
				wp.apiRequest( {
					path:   `/wpmc/v1/update-main-category`,
					method: 'POST',
					data:   data
				} ).then(
					( data ) => {
						return data;
					},
					( err ) => {
						return err;
					}
				);
			}

			updateData() {
				if ( ! this.getDataStore().registeredData() ) {
					this.getDataStore().set( 'availableTerms', [] );
					this.fetchTerms();
				}

				this.getDataStore().set( 'selectedTerms', this.props.terms );
			}

			getDataStore() {
				return getStore();
			}

			isTermAvailable( id ) {
				let availableTerms = this.getDataStore().get( 'availableTerms' );
				return availableTerms.some( term => term.id === id );
			}

			fetchTerms() {
				const { taxonomy } = this.props;
				if ( ! taxonomy ) {
					return;
				}

				this.setState( {
					selectedTerms: this.props.terms,
					loading: true,
				} );

				this.fetchRequest = apiFetch( {
					path: addQueryArgs( `/wp/v2/${ taxonomy.rest_base }`, DEFAULT_QUERY ),
				} );
				this.fetchRequest.then(
					( terms ) => { // resolve
						this.fetchRequest = null;
						this.setState( {
							loading: false,
						} );
						this.getDataStore().set( 'availableTerms', terms );
						this.forceUpdate();
					},
					( xhr ) => { // reject
						if ( xhr.statusText === 'abort' ) {
							return;
						}
						this.fetchRequest = null;
						this.setState( {
							loading: false,
						} );
						this.forceUpdate();
					}
				);
			}
		}

		class CategorySelector extends MainCategorySelectorHandler {
			constructor( props ) {
				super( props );

				this.onChange = this.onChange.bind( this );
			}

			getTermName( id ) {
				let availableTerms = this.getDataStore().get( 'availableTerms' );

				let term = availableTerms.find( term => term.id === id );
				return term && term.name || '';
			}

			getSelectOptions() {
				let options = this.getDataStore().get( 'selectedTerms' ).sort().map( id => {
					return { value: id, label: this.getTermName( id ) };
				} );
				return sortBy( options, 'label' );
			}

			onChange( value ) {
				this.setState( {
					options: this.getSelectOptions(),
					value:   setMainCategoryId( value )
				} );

				// @todo Utilize the Gutenberg preferred way to enable the Update button.
				$( '.edit-post-header__settings' ).find( '.components-button.editor-post-publish-button' ).attr( 'aria-disabled', false );
			}

			isDisabled() {
				return this.state.loading;
			}

			render() {
				this.updateData();
				const primaryTermId = getMainCategoryId();
				return createElement(
					SelectControl,
					{
						label: __( 'Primary Category', 'wp-main-category' ),
						value: primaryTermId,
						className: 'wpmc-select',
						onChange: this.onChange,
						options: this.state.loading ? '' : this.getSelectOptions(),
						disabled: this.isDisabled(),
					},
				);
			}
		}

		const mainCategorySelectorFilter = OriginalComponent => class extends MainCategorySelectorHandler {
			initSelectors() {
				const { slug, terms } = this.props;
				if ( 'category' !== slug ) {
					return;
				}

				if ( terms.length > 1 ) {
					return createElement(
						Fragment,
						{},
						createElement(
							CategorySelector,
							this.props,
						)
					);
				}
				return null;
			}

			render() {
				return createElement(
					Fragment,
					{},
					createElement(
						OriginalComponent,
						this.props,
					),
					this.initSelectors()
				);
			}
		};

		addFilter(
			'editor.PostTaxonomyType',
			'wpmc/select',
			mainCategorySelectorFilter,
			20
		);
	};

	return load;
}() );
$( window ).load( function() {
	WPMainCategoryGutenberg();
});
