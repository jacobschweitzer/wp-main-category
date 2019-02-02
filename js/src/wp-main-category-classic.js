/* globals wpmc */
document.addEventListener( 'DOMContentLoaded', function() {
	let mainCategoryRadio = document.createElement( 'input' );
	mainCategoryRadio.setAttribute( 'type', 'radio' );
	mainCategoryRadio.setAttribute( 'name', 'wpmc[]' );
	mainCategoryRadio.style.float = 'right';
	mainCategoryRadio.setAttribute( 'title', 'Set Main Category' );
	mainCategoryRadio.setAttribute( 'aria-label', 'Set Main Category' );

	const categoryList = document.getElementById( 'categorychecklist' );
	const allCategoryInputs = categoryList.querySelectorAll( 'input[type="checkbox"]' );
	const mainCategory = wpmc.mainCategory;

	const addRadios = () => {
		const categoryList = document.getElementById( 'categorychecklist' );
		const categories = categoryList.querySelectorAll( 'input[type="checkbox"]' );
		categories.forEach( function( category ) {
			let hasRadio = 0 < category.parentElement.querySelectorAll( 'input[name="wpmc[]"]' ).length;
			if ( category.checked && ! hasRadio ) {
				let clonedRadio = mainCategoryRadio.cloneNode( true );
				clonedRadio.value = category.value;
				if ( mainCategory === category.value ) {
					clonedRadio.checked = 'checked';
					clonedRadio.setAttribute( 'title', 'Main Category' );
					clonedRadio.setAttribute( 'aria-label', 'Main Category' );

				}
				category.parentElement.append( clonedRadio );
			} else if ( hasRadio && ! category.checked ) {
				let radioToRemove = category.parentElement.querySelector( 'input[name="wpmc[]"]' );
				category.parentElement.removeChild( radioToRemove );
			}
		});
	};
	addRadios();
	allCategoryInputs.forEach( function( categoryInput ) {
		categoryInput.addEventListener( 'change', addRadios );
	});
});