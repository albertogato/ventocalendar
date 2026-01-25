/**
 * VentoCalendar Calendar Initializer
 * Initializes all calendar instances on the page
 *
 * @package
 */

( function () {
	'use strict';

	/**
	 * Initialize a calendar instance
	 * @param {HTMLElement} element The DOM element
	 */
	function initCalendar( element ) {
		// Get configuration from data attributes.
		const viewType = element.getAttribute( 'data-view-type' ) || 'calendar';
		const firstDayOfWeek =
			element.getAttribute( 'data-first-day-of-week' ) || 'monday';
		const initialMonth = element.getAttribute( 'data-initial-month' ) || '';
		const layout = element.getAttribute( 'data-layout' ) || '';
		const showStartDate =
			element.getAttribute( 'data-show-start-date' ) === 'true';
		const showEndDate =
			element.getAttribute( 'data-show-end-date' ) === 'true';
		const showStartTime =
			element.getAttribute( 'data-show-start-time' ) === 'true';
		const showEndTime =
			element.getAttribute( 'data-show-end-time' ) === 'true';
		const showAddToCalendarGoogle =
			element.getAttribute( 'data-show-add-to-calendar-google' ) ===
			'true';
		const showAddToCalendarApple =
			element.getAttribute( 'data-show-add-to-calendar-apple' ) ===
			'true';

		// Parse format strings from JSON to preserve backslashes.
		let dateFormat = 'F j, Y';
		let timeFormat = 'g:i a';

		try {
			const dateFormatAttr = element.getAttribute( 'data-date-format' );
			if ( dateFormatAttr ) {
				dateFormat = dateFormatAttr;
			}
		} catch ( e ) {}

		try {
			const timeFormatAttr = element.getAttribute( 'data-time-format' );
			if ( timeFormatAttr ) {
				timeFormat = timeFormatAttr;
			}
		} catch ( e ) {}

		// Create Vue app.
		const { createApp } = window.Vue;
		const app = createApp( {
			components: {
				VentoCalendarCalendar: window.VentoCalendarCalendar,
			},
			template: `
	                <VentoCalendarCalendar
						:view-type="viewType"
	                    :first-day-of-week="firstDayOfWeek"
						:initial-month="initialMonth"
						:layout="layout"
	                    :show-start-date="showStartDate"
	                    :show-end-date="showEndDate"
	                    :show-start-time="showStartTime"
	                    :show-end-time="showEndTime"
						:show-add-to-calendar-google="showAddToCalendarGoogle"
						:show-add-to-calendar-apple="showAddToCalendarApple"
	                    :date-format="dateFormat"
	                    :time-format="timeFormat"
	                />
				`,
			data() {
				return {
					viewType,
					firstDayOfWeek,
					initialMonth,
					layout,
					showStartDate,
					showEndDate,
					showStartTime,
					showEndTime,
					showAddToCalendarGoogle,
					showAddToCalendarApple,
					dateFormat,
					timeFormat,
				};
			},
		} );

		// Mount the app.
		app.mount( element );
	}

	/**
	 * Initialize all calendars on the page
	 */
	function initAllCalendars() {
		const calendars = document.querySelectorAll(
			'.ventocalendar-calendar-wrapper'
		);
		calendars.forEach( function ( calendar ) {
			// Check if already initialized.
			if ( calendar.getAttribute( 'data-initialized' ) === 'true' ) {
				return;
			}

			initCalendar( calendar );
			calendar.setAttribute( 'data-initialized', 'true' );
		} );
	}

	// Initialize when DOM is ready.
	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', initAllCalendars );
	} else {
		// DOM is already ready.
		initAllCalendars();
	}

	// Also try on window load as a fallback.
	window.addEventListener( 'load', function () {
		// Check if there are any non-initialized calendars.
		const nonInitialized = document.querySelectorAll(
			'.ventocalendar-calendar-wrapper:not([data-initialized="true"])'
		);
		if ( nonInitialized.length > 0 ) {
			initAllCalendars();
		}
	} );
} )();
