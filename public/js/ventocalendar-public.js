/**
 * VentoCalendar public Javascript.
 *
 * @package
 */

( function () {
	'use strict';

	/**
	 * Parse and validate coordinate value.
	 *
	 * @param {string} value Coordinate value.
	 * @param {number} min   Minimum accepted value.
	 * @param {number} max   Maximum accepted value.
	 * @return {number|null} Parsed coordinate or null when invalid.
	 */
	function parseCoordinate( value, min, max ) {
		const parsed = Number.parseFloat( value );

		if ( Number.isNaN( parsed ) || parsed < min || parsed > max ) {
			return null;
		}

		return parsed;
	}

	/**
	 * Initialize event map views in frontend.
	 */
	function initializeEventMaps() {
		if ( typeof maplibregl === 'undefined' ) {
			return;
		}

		const config = window.ventocalendarPublicMapConfig || {};
		const styleUrl =
			config.styleUrl || 'https://tiles.openfreemap.org/styles/liberty';

		document
			.querySelectorAll( '.ventocalendar-event-map-view' )
			.forEach( function ( mapElement ) {
				const latitude = parseCoordinate(
					mapElement.dataset.latitude,
					-90,
					90
				);
				const longitude = parseCoordinate(
					mapElement.dataset.longitude,
					-180,
					180
				);

				if ( latitude === null || longitude === null ) {
					return;
				}

				const map = new maplibregl.Map( {
					container: mapElement,
					style: styleUrl,
					center: [ longitude, latitude ],
					zoom: 13,
					attributionControl: true,
				} );

				map.addControl(
					new maplibregl.NavigationControl(),
					'top-right'
				);

				new maplibregl.Marker( { draggable: false } )
					.setLngLat( [ longitude, latitude ] )
					.addTo( map );
			} );
	}

	document.addEventListener( 'DOMContentLoaded', initializeEventMaps );
} )();
