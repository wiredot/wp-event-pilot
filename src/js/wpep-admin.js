jQuery(document).ready(function($) {
	filtersInit();
});

function filtersInit() {
	newFilter();
	loadFilter();
	deleteFilter();
	saveFilter();
}

function newFilter() {
	jQuery('#save_new_filter_form').submit(function(event) {
		event.preventDefault();
		var filterValues = jQuery('#filters_form').serialize();
		var formValues = jQuery(this).serialize();
		var formAction = jQuery(this).attr('action');
		console.log(formValues);

		jQuery.ajax({
			type: "POST",
			url: formAction,
			data: filterValues + '&' + formValues,

			success: function(response) {
				console.log(response);
				if ( response.success ) {
					window.location.href = response.redirect;
				} else {
					alert(response.error)
				}
			}
		});
	});
}

function loadFilter() {
	jQuery('#load_filter').click(function(event) {
		event.preventDefault();

		var filterSelected = jQuery('#filters_list').val();
		if (filterSelected) {
			window.location.href = 'edit.php?post_type=wpep&page=filters&filter_id=' + filterSelected;
		}
	});
}

function deleteFilter() {
	jQuery('#delete_filter').click(function(event) {
		event.preventDefault();

		var formValues = jQuery('#filer_actions').serialize();
		var formAction = jQuery('#filer_actions').attr('action');

		console.log(formValues);

		jQuery.ajax({
			type: "POST",
			url: formAction,
			data: formValues + '&a=delete',

			success: function(response) {
				console.log(response);
				if ( response.success ) {
					window.location.href = response.redirect;
				} else {
					alert(response.error)
				}
			}
		});
	});
}

function saveFilter() {
	jQuery('#save_filter').click(function(event) {
		event.preventDefault();

		var formValues = jQuery('#filer_actions').serialize();
		var formAction = jQuery('#filer_actions').attr('action');
		var filterValues = jQuery('#filters_form').serialize();

		console.log(formValues);

		jQuery.ajax({
			type: "POST",
			url: formAction,
			data: filterValues + '&' + formValues + '&a=save',

			success: function(response) {
				console.log(response);
				if ( response.success ) {
					window.location.href = response.redirect;
				} else {
					alert(response.error)
				}
			}
		});
	});
}
