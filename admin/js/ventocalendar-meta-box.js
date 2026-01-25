/**
 * VentoCalendar Meta Box JavaScript
 * Handles the dynamic behavior of event date/time fields
 *
 * @param {jQuery} $ The jQuery object.
 * @package
 */

( function ( $ ) {
	'use strict';

	// Import translation function.
	const { __ } = wp.i18n;

	/**
	 * Calculate the next 00 or 30 minute multiple from now.
	 *
	 * @return {string} Time in HH:MM format
	 */
	function getNextHalfHourTime() {
		const now = new Date();
		let minutes = now.getMinutes();
		let hours = now.getHours();

		// Round up to next 00 or 30.
		if ( minutes < 30 ) {
			minutes = 30;
		} else {
			minutes = 0;
			hours += 1;
		}

		// Wrap around if hours reach 24.
		if ( hours >= 24 ) {
			hours = 0;
		}

		const hoursStr = String( hours ).padStart( 2, '0' );
		const minutesStr = String( minutes ).padStart( 2, '0' );

		return `${ hoursStr }:${ minutesStr }`;
	}

	/**
	 * Calculate end time (start time + 1 hour).
	 * If result goes to next day, return same as start time.
	 *
	 * @param {string} startTime - Start time in HH:MM format
	 * @return {string} End time in HH:MM format
	 */
	function calculateEndTime( startTime ) {
		if ( ! startTime ) {
			return '';
		}

		const [ hours, minutes ] = startTime.split( ':' ).map( Number );
		let endHours = hours + 1;
		const endMinutes = minutes;

		// If adding 1 hour goes to next day, wrap to 0.
		if ( endHours >= 24 ) {
			endHours = 0;
		}

		// If the hour wrapped past the original hour, return start time (same day logic).
		if ( endHours < hours ) {
			return startTime;
		}

		return `${ String( endHours ).padStart( 2, '0' ) }:${ String(
			endMinutes
		).padStart( 2, '0' ) }`;
	}

	/**
	 * Get today's date in YYYY-MM-DD format.
	 *
	 * @return {string} Today's date
	 */
	function getTodayDate() {
		const today = new Date();
		const year = today.getFullYear();
		const month = String( today.getMonth() + 1 ).padStart( 2, '0' );
		const day = String( today.getDate() ).padStart( 2, '0' );

		return `${ year }-${ month }-${ day }`;
	}

	/**
	 * Initialize default values for new events.
	 */
	function initializeDefaults() {
		const $startDate = $( '#ventocalendar-start-date' );
		const $startTime = $( '#ventocalendar-start-time' );
		const $endTime = $( '#ventocalendar-end-time' );
		const $allDay = $( '#ventocalendar-all-day' );

		// Check if this is a new event (empty start_date).
		const isNewEvent = ! $startDate.val() || $startDate.val() === '';

		if ( isNewEvent ) {
			// Set default start_date to today.
			$startDate.val( getTodayDate() );

			// Set default times only if "All day" is not checked.
			if ( ! $allDay.is( ':checked' ) ) {
				const defaultStartTime = getNextHalfHourTime();
				$startTime.val( defaultStartTime );

				const defaultEndTime = calculateEndTime( defaultStartTime );
				$endTime.val( defaultEndTime );
			}
		}
	}

	/**
	 * Toggle visibility of time and end date fields based on "All day" checkbox.
	 */
	function toggleAllDayFields() {
		const $allDay = $( '#ventocalendar-all-day' );
		const isAllDay = $allDay.is( ':checked' );

		const $timeFields = $( '.ventocalendar-time-field' );
		const $startTime = $( '#ventocalendar-start-time' );
		const $endTime = $( '#ventocalendar-end-time' );
		const $endDateField = $( '.ventocalendar-end-date-field' );
		const $endDate = $( '#ventocalendar-end-date' );
		const $startDate = $( '#ventocalendar-start-date' );

		if ( isAllDay ) {
			// Hide time fields and clear values.
			$timeFields.hide();
			$startTime.val( '' );
			$endTime.val( '' );

			// Show end date field.
			$endDateField.show();

			// If end date is empty, copy start date value.
			if ( ! $endDate.val() && $startDate.val() ) {
				$endDate.val( $startDate.val() );
			}
		} else {
			// Show time fields.
			$timeFields.show();

			// Hide end date field (but DO NOT clear its value).
			$endDateField.hide();

			// Set default times if empty.
			if ( ! $startTime.val() ) {
				const defaultStartTime = getNextHalfHourTime();
				$startTime.val( defaultStartTime );
				const defaultEndTime = calculateEndTime( defaultStartTime );
				$endTime.val( defaultEndTime );
			}
		}
	}

	/**
	 * Update end time when start time changes.
	 */
	function updateEndTimeOnStartChange() {
		const $startTime = $( '#ventocalendar-start-time' );
		const $endTime = $( '#ventocalendar-end-time' );

		// Only auto-update if end time is empty or user hasn't manually changed it.
		if ( ! $endTime.val() || ! $endTime.data( 'manually-changed' ) ) {
			const newEndTime = calculateEndTime( $startTime.val() );
			$endTime.val( newEndTime );
		}
	}

	/**
	 * Validate the form before submission.
	 *
	 * @return {boolean} True if valid, false otherwise
	 */
	function validateForm() {
		const $startDate = $( '#ventocalendar-start-date' );
		const $startTime = $( '#ventocalendar-start-time' );
		const $endTime = $( '#ventocalendar-end-time' );
		const $endDate = $( '#ventocalendar-end-date' );
		const $allDay = $( '#ventocalendar-all-day' );

		let isValid = true;
		const errors = [];

		// Clear previous errors.
		$( '.ventocalendar-error-message' ).text( '' ).hide();
		$( '.ventocalendar-meta-box-wrapper input' ).removeClass( 'error' );

		// Validate start_date (required).
		if ( ! $startDate.val() ) {
			isValid = false;
			errors.push( {
				field: $startDate,
				message: __( 'Start date is required.', 'ventocalendar' ),
			} );
		}

		// Validate times (if both present) - only if same day (no end_date or end_date = start_date).
		if ( $startTime.val() && $endTime.val() ) {
			// Only validate time order if event is on same day.
			const isSameDay =
				! $endDate.val() || $endDate.val() === $startDate.val();

			if ( isSameDay ) {
				// Combine date and time for comparison.
				const startDateTimeStr =
					$startDate.val() + 'T' + $startTime.val();
				const endDateTimeStr = $startDate.val() + 'T' + $endTime.val();

				const startDateTime = new Date( startDateTimeStr );
				const endDateTime = new Date( endDateTimeStr );

				if ( endDateTime < startDateTime ) {
					isValid = false;
					errors.push( {
						field: $endTime,
						message: __(
							'End time cannot be before the start time.',
							'ventocalendar'
						),
					} );
				}
			}
		}

		// Validate end_date (if all-day is checked and end_date has value).
		if ( $allDay.is( ':checked' ) && $endDate.val() && $startDate.val() ) {
			const startTimestamp = new Date( $startDate.val() ).getTime();
			const endTimestamp = new Date( $endDate.val() ).getTime();

			if ( endTimestamp < startTimestamp ) {
				isValid = false;
				errors.push( {
					field: $endDate,
					message: __(
						'End date must be on or after the start date.',
						'ventocalendar'
					),
				} );
			}
		}

		// Display errors.
		errors.forEach( function ( error ) {
			error.field.addClass( 'error' );
			error.field
				.siblings( '.ventocalendar-error-message' )
				.text( error.message )
				.show();
		} );

		// Block/unblock Gutenberg save buttons.
		const publishButton = document.querySelector(
			'.editor-post-publish-button, .editor-post-publish-panel__toggle'
		);
		const updateButton = document.querySelector(
			'.editor-post-publish-button__button'
		);

		if ( publishButton ) {
			publishButton.setAttribute( 'aria-disabled', ! isValid );
		}
		if ( updateButton ) {
			updateButton.setAttribute( 'aria-disabled', ! isValid );
		}

		// Lock/unlock post saving in Gutenberg.
		if ( wp && wp.data && wp.data.dispatch ) {
			const editor = wp.data.dispatch( 'core/editor' );
			const notices = wp.data.dispatch( 'core/notices' );

			if ( ! isValid ) {
				editor.lockPostSaving( 'ventocalendar-validation' );
				notices.createNotice(
					'error',
					__(
						'Please review the event dates and times.',
						'ventocalendar'
					),
					{
						id: 'ventocalendar-validation',
						isDismissible: false,
					}
				);
			} else {
				editor.unlockPostSaving( 'ventocalendar-validation' );
				notices.removeNotice( 'ventocalendar-validation' );
			}
		}

		return isValid;
	}

	/**
	 * Handle Gutenberg save attempts.
	 * @param {Event} event
	 */
	function handleGutenbergSaveAttempt( event ) {
		if ( ! validateForm() ) {
			event.preventDefault();
			event.stopImmediatePropagation();
			return false;
		}
	}

	/**
	 * Initialize all event listeners.
	 */
	function initializeEventListeners() {
		const $allDay = $( '#ventocalendar-all-day' );
		const $startDate = $( '#ventocalendar-start-date' );
		const $startTime = $( '#ventocalendar-start-time' );
		const $endTime = $( '#ventocalendar-end-time' );
		const $endDate = $( '#ventocalendar-end-date' );

		// All day checkbox toggle.
		$allDay.on( 'change', function () {
			toggleAllDayFields();
			validateForm();
		} );

		// Start time change → update end time.
		$startTime.on( 'change', function () {
			updateEndTimeOnStartChange();
			validateForm();
		} );

		// Mark end time as manually changed.
		$endTime.on( 'change', function () {
			$endTime.data( 'manually-changed', true );
			validateForm();
		} );

		// Validate on all field changes.
		$startDate.on( 'change', validateForm );
		$endDate.on( 'change', validateForm );

		// Classic editor form submission.
		$( '#post' ).on( 'submit', function ( e ) {
			if ( ! validateForm() ) {
				e.preventDefault();
				return false;
			}
		} );

		// Gutenberg editor save buttons.
		const initGutenbergListeners = setInterval( function () {
			const publishButton = document.querySelector(
				'.editor-post-publish-button, .editor-post-publish-panel__toggle'
			);
			const updateButton = document.querySelector(
				'.editor-post-publish-button__button'
			);

			if ( ! publishButton && ! updateButton ) {
				return;
			}

			clearInterval( initGutenbergListeners );

			if ( publishButton ) {
				publishButton.addEventListener(
					'click',
					handleGutenbergSaveAttempt
				);
			}

			if ( updateButton ) {
				updateButton.addEventListener(
					'click',
					handleGutenbergSaveAttempt
				);
			}
		}, 300 );
	}

	/**
	 * Document ready initialization.
	 */
	$( document ).ready( function () {
		// Check if we're on the event edit screen.
		if ( $( '#ventocalendar-start-date' ).length === 0 ) {
			return;
		}

		// Initialize color picker.
		if ( $.fn.wpColorPicker ) {
			$( '.ventocalendar-color-picker' ).wpColorPicker();
		}

		// Initialize defaults for new events.
		initializeDefaults();

		// Apply initial state of "All day" checkbox.
		toggleAllDayFields();

		// Initialize event listeners.
		initializeEventListeners();

		// Run initial validation (non-blocking).
		setTimeout( validateForm, 500 );
	} );
} )( jQuery );
