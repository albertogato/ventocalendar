/**
 * VentoCalendar admin settings page functionality.
 *
 * @param {jQuery} $ The jQuery object.
 * @package
 */
( function ( $ ) {
	'use strict';

	$( document ).ready( function () {
		$( '.ventocalendar-color-picker' ).wpColorPicker();

		function showAutomaticInfoParameters() {
			$( '#date_format' ).closest( 'tr' ).show();
			$( '#time_format' ).closest( 'tr' ).show();
			$( '#show_start_time' ).closest( 'tr' ).show();
			$( '#show_end_time' ).closest( 'tr' ).show();
		}

		function hideAutomaticInfoParameters() {
			$( '#date_format' ).closest( 'tr' ).hide();
			$( '#time_format' ).closest( 'tr' ).hide();
			$( '#show_start_time' ).closest( 'tr' ).hide();
			$( '#show_end_time' ).closest( 'tr' ).hide();
		}

		if ( $( '#show_event_info_automatically' ).is( ':checked' ) ) {
			showAutomaticInfoParameters();
		} else {
			hideAutomaticInfoParameters();
		}

		$( '#show_event_info_automatically' ).change( function () {
			if ( $( this ).is( ':checked' ) ) {
				showAutomaticInfoParameters();
			} else {
				hideAutomaticInfoParameters();
			}
		} );
	} );
} )( jQuery );
