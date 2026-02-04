/**
 * VentoCalendar Calendar Vue Component
 * Displays events in a monthly calendar view
 *
 * @package
 */

( function () {
	'use strict';

	const __ = wp.i18n.__;

	const days = [
		__( 'Sunday', 'ventocalendar' ),
		__( 'Monday', 'ventocalendar' ),
		__( 'Tuesday', 'ventocalendar' ),
		__( 'Wednesday', 'ventocalendar' ),
		__( 'Thursday', 'ventocalendar' ),
		__( 'Friday', 'ventocalendar' ),
		__( 'Saturday', 'ventocalendar' ),
	];

	const months = [
		__( 'January', 'ventocalendar' ),
		__( 'February', 'ventocalendar' ),
		__( 'March', 'ventocalendar' ),
		__( 'April', 'ventocalendar' ),
		__( 'May', 'ventocalendar' ),
		__( 'June', 'ventocalendar' ),
		__( 'July', 'ventocalendar' ),
		__( 'August', 'ventocalendar' ),
		__( 'September', 'ventocalendar' ),
		__( 'October', 'ventocalendar' ),
		__( 'November', 'ventocalendar' ),
		__( 'December', 'ventocalendar' ),
	];

	const monthsShort = [
		__( 'Jan', 'ventocalendar' ),
		__( 'Feb', 'ventocalendar' ),
		__( 'Mar', 'ventocalendar' ),
		__( 'Apr', 'ventocalendar' ),
		__( 'May', 'ventocalendar' ),
		__( 'Jun', 'ventocalendar' ),
		__( 'Jul', 'ventocalendar' ),
		__( 'Aug', 'ventocalendar' ),
		__( 'Sep', 'ventocalendar' ),
		__( 'Oct', 'ventocalendar' ),
		__( 'Nov', 'ventocalendar' ),
		__( 'Dec', 'ventocalendar' ),
	];

	const translations = {
		'Previous month': __( 'Previous month', 'ventocalendar' ),
		Today: __( 'Today', 'ventocalendar' ),
		'Next month': __( 'Next month', 'ventocalendar' ),
		'No events on this day': __( 'No events on this day', 'ventocalendar' ),
		'No events on this month': __(
			'No events on this month',
			'ventocalendar'
		),
		'View event': __( 'View event', 'ventocalendar' ),
		'Add to calendar': __( 'Add to calendar', 'ventocalendar' ),
	};

	// Calendar component.
	const VentoCalendarCalendar = {
		name: 'VentoCalendarCalendar',

		props: {
			viewType: {
				type: String,
				default: 'calendar', // 'calendar' or 'list'.
				validator( value ) {
					return [ 'calendar', 'list' ].indexOf( value ) !== -1;
				},
			},
			firstDayOfWeek: {
				type: String,
				default: 'monday', // 'monday' or 'sunday'.
				validator( value ) {
					return [ 'monday', 'sunday' ].indexOf( value ) !== -1;
				},
			},
			initialMonth: {
				type: String,
				default: '', // Format: 'YYYY-MM' (e.g., '2024-03').
				validator( value ) {
					if ( ! value ) {
						return true;
					}
					// Validate format YYYY-MM.
					return /^\d{4}-\d{2}$/.test( value );
				},
			},
			layout: {
				type: String,
				default: 'basic', // 'basic', 'compact' or 'clean'.
				validator( value ) {
					return (
						[ 'basic', 'compact', 'clean' ].indexOf( value ) !== -1
					);
				},
			},
			showStartDate: {
				type: Boolean,
				default: true,
			},
			showEndDate: {
				type: Boolean,
				default: true,
			},
			showStartTime: {
				type: Boolean,
				default: false,
			},
			showEndTime: {
				type: Boolean,
				default: false,
			},
			showAddToCalendarGoogle: {
				type: Boolean,
				default: false,
			},
			showAddToCalendarApple: {
				type: Boolean,
				default: false,
			},
			dateFormat: {
				type: String,
				default: 'F j, Y',
			},
			timeFormat: {
				type: String,
				default: 'g:i a',
			},
		},

		data() {
			// Determine initial date.
			let initialDate = new Date();

			// If initialMonth is provided and valid, use it.
			if ( this.initialMonth ) {
				const parts = this.initialMonth.split( '-' );
				const year = parseInt( parts[ 0 ], 10 );
				const month = parseInt( parts[ 1 ], 10 ) - 1; // JavaScript months are 0-indexed.

				// Validate year and month ranges.
				if (
					year >= 1900 &&
					year <= 2100 &&
					month >= 0 &&
					month <= 11
				) {
					initialDate = new Date( year, month, 1 );
				}
			}

			return {
				currentDate: initialDate,
				events: [],
				loading: true,
				showModal: false,
				selectedDate: null,
				selectedEvents: [],
				windowWidth:
					typeof window !== 'undefined' ? window.innerWidth : 1024,
				loadedStartDate: null, // Track the start of loaded events range.
				loadedEndDate: null, // Track the end of loaded events range.
			};
		},

		computed: {
			currentYear() {
				return this.currentDate.getFullYear();
			},

			currentMonth() {
				return this.currentDate.getMonth();
			},

			monthName() {
				return this.formatMonthName( this.currentDate );
			},

			barSpacing() {
				if ( this.windowWidth <= 480 ) {
					return 6 + 1;
				} else if ( this.windowWidth <= 768 ) {
					return 12 + 2;
				}
				return 20 + 2;
			},
			barsOffset() {
				if ( this.windowWidth <= 480 ) {
					return 4;
				} else if ( this.windowWidth <= 768 ) {
					return 10;
				}
				return 14;
			},

			weekDays() {
				if ( this.firstDayOfWeek === 'monday' ) {
					// Move Sunday to the end.
					days.push( days.shift() );
				}

				// Return only first letter of each day.
				return days.map( ( day ) => day.charAt( 0 ) );
			},

			calendarDays() {
				const year = this.currentYear;
				const month = this.currentMonth;
				const firstDay = new Date( year, month, 1 );
				const lastDay = new Date( year, month + 1, 0 );

				let startOffset = firstDay.getDay();

				// Adjust offset based on first day of week preference.
				if ( this.firstDayOfWeek === 'monday' ) {
					startOffset = startOffset === 0 ? 6 : startOffset - 1;
				}

				const computedDays = [];

				// Previous month days.
				const prevMonth = new Date( year, month, 0 );
				const prevMonthDays = prevMonth.getDate();

				for ( let i = startOffset - 1; i >= 0; i-- ) {
					computedDays.push( {
						date: new Date( year, month - 1, prevMonthDays - i ),
						isCurrentMonth: false,
					} );
				}

				// Current month days.
				for ( let i = 1; i <= lastDay.getDate(); i++ ) {
					computedDays.push( {
						date: new Date( year, month, i ),
						isCurrentMonth: true,
					} );
				}

				// Next month days to fill the grid.
				const remainingDays = 42 - computedDays.length; // 6 rows x 7 days.
				for ( let i = 1; i <= remainingDays; i++ ) {
					computedDays.push( {
						date: new Date( year, month + 1, i ),
						isCurrentMonth: false,
					} );
				}

				return computedDays;
			},

			weeks() {
				const weeks = [];
				for ( let i = 0; i < this.calendarDays.length; i += 7 ) {
					weeks.push( this.calendarDays.slice( i, i + 7 ) );
				}
				return weeks;
			},
		},

		methods: {
			translate( text ) {
				return translations[ text ];
			},
			formatMonthName( date ) {
				return months[ date.getMonth() ] + ' ' + date.getFullYear();
			},

			formatDate( date ) {
				// Use local date instead of UTC to avoid timezone issues.
				const year = date.getFullYear();
				const month = String( date.getMonth() + 1 ).padStart( 2, '0' );
				const day = String( date.getDate() ).padStart( 2, '0' );
				return `${ year }-${ month }-${ day }`;
			},

			parseDate( dateString ) {
				return new Date( dateString + 'T00:00:00' );
			},

			formatDateWithPhpFormat( date, format ) {
				if ( ! date || ! format ) {
					return '';
				}

				// PHP to JavaScript format conversion.
				// Step 1: Temporarily replace escaped characters with placeholders.
				const escapedChars = [];
				let result = format.replace(
					/\\(.)/g,
					function ( match, char ) {
						const placeholder =
							'\x00' + escapedChars.length + '\x00';
						escapedChars.push( char );
						return placeholder;
					}
				);

				// Step 2: Replace format characters.
				const replacements = {
					F: months[ date.getMonth() ],
					M: monthsShort[ date.getMonth() ],
					m: String( date.getMonth() + 1 ).padStart( 2, '0' ),
					n: String( date.getMonth() + 1 ),
					d: String( date.getDate() ).padStart( 2, '0' ),
					j: String( date.getDate() ),
					Y: String( date.getFullYear() ),
					y: String( date.getFullYear() ).slice( -2 ),
				};

				result = result.replace( /[FMmnddjYy]/g, function ( match ) {
					return replacements[ match ] || match;
				} );

				// Step 3: Restore escaped characters.
				result = result.replace(
					/\x00(\d+)\x00/g,
					function ( match, index ) {
						return escapedChars[ parseInt( index ) ];
					}
				);

				return result;
			},

			formatTimeWithPhpFormat( timeString, format ) {
				if ( ! timeString || ! format ) {
					return '';
				}

				const parts = timeString.split( ':' );
				const hours = parseInt( parts[ 0 ], 10 );
				const minutes = parts[ 1 ];
				const seconds = parts[ 2 ] || '00';

				const hours24 = hours;
				const hours12 = hours % 12 || 12;
				const ampm = hours >= 12 ? 'PM' : 'AM';
				const ampmLower = hours >= 12 ? 'pm' : 'am';

				// PHP to JavaScript format conversion.
				// Step 1: Temporarily replace escaped characters with placeholders.
				const escapedChars = [];
				let result = format.replace(
					/\\(.)/g,
					function ( match, char ) {
						const placeholder =
							'\x00' + escapedChars.length + '\x00';
						escapedChars.push( char );
						return placeholder;
					}
				);

				// Step 2: Replace format characters.
				const replacements = {
					H: String( hours24 ).padStart( 2, '0' ),
					G: String( hours24 ),
					h: String( hours12 ).padStart( 2, '0' ),
					g: String( hours12 ),
					i: String( minutes ).padStart( 2, '0' ),
					s: String( seconds ).padStart( 2, '0' ),
					A: ampm,
					a: ampmLower,
				};

				result = result.replace( /[HGhgisAa]/g, function ( match ) {
					return replacements[ match ] || match;
				} );

				// Step 3: Restore escaped characters.
				result = result.replace(
					/\x00(\d+)\x00/g,
					function ( match, index ) {
						return escapedChars[ parseInt( index ) ];
					}
				);

				return result;
			},

			formatDateDisplay( dateString ) {
				if ( ! dateString ) {
					return '';
				}
				const date = this.parseDate( dateString );
				return this.formatDateWithPhpFormat( date, this.dateFormat );
			},

			formatTimeDisplay( timeString ) {
				if ( ! timeString ) {
					return '';
				}
				return this.formatTimeWithPhpFormat(
					timeString,
					this.timeFormat
				);
			},

			async fetchEvents( forceRefresh = false ) {
				// Check if current month is within loaded range.
				if (
					! forceRefresh &&
					this.loadedStartDate &&
					this.loadedEndDate
				) {
					const currentMonthStart = new Date(
						this.currentYear,
						this.currentMonth,
						1
					);
					const currentMonthEnd = new Date(
						this.currentYear,
						this.currentMonth + 1,
						0
					);

					if (
						currentMonthStart >= this.loadedStartDate &&
						currentMonthEnd <= this.loadedEndDate
					) {
						// Current month is within loaded range, no need to fetch.
						return;
					}
				}

				this.loading = true;

				// Load 6 months before and 6 months after the current month.
				const startDate = new Date(
					this.currentYear,
					this.currentMonth - 6,
					1
				);
				const endDate = new Date(
					this.currentYear,
					this.currentMonth + 7,
					0
				); // Last day of 6 months ahead.

				const start = this.formatDate( startDate );
				const end = this.formatDate( endDate );

				try {
					const response = await fetch(
						`${ window.ventoCalendar.restUrl }ventocalendar/v1/events?start=${ start }&end=${ end }`,
						{
							headers: {
								'Content-Type': 'application/json',
							},
						}
					);

					if ( ! response.ok ) {
						throw new Error( 'Failed to fetch events' );
					}

					this.events = await response.json();

					// Store the loaded range.
					this.loadedStartDate = startDate;
					this.loadedEndDate = endDate;
				} catch ( error ) {
					this.events = [];
				} finally {
					this.loading = false;
				}
			},

			getEventsForDate( date ) {
				const dateStr = this.formatDate( date );
				return this.events.filter( ( event ) => {
					const startDate = event.start_date;
					const endDate = event.end_date;

					// If no end_date, event only appears on start_date.
					if ( ! endDate ) {
						return dateStr === startDate;
					}

					// If has end_date, check if date is within range.
					return dateStr >= startDate && dateStr <= endDate;
				} );
			},

			getEventBarsForWeek( week ) {
				// Get all events that span across this week.
				const weekStart = week[ 0 ].date;
				const weekEnd = week[ 6 ].date;
				const weekStartStr = this.formatDate( weekStart );
				const weekEndStr = this.formatDate( weekEnd );

				const multiDayEvents = this.events.filter( ( event ) => {
					const startDate = event.start_date;
					const endDate = event.end_date;

					// Show as bar if event has end_date (even if same as start_date).
					if ( ! endDate ) {
						return false;
					}

					// Events with end_date that intersect with this week.
					return startDate <= weekEndStr && endDate >= weekStartStr;
				} );

				// Sort by start date to maintain consistent positioning.
				multiDayEvents.sort( ( a, b ) => {
					if ( a.start_date !== b.start_date ) {
						return a.start_date.localeCompare( b.start_date );
					}
					return a.id - b.id;
				} );

				// Calculate bar positions and spans.
				const bars = multiDayEvents
					.map( ( event ) => {
						// Find first and last day of event within this week.
						let firstDayIndex = -1;
						let lastDayIndex = -1;

						week.forEach( ( day, index ) => {
							const dayDate = this.formatDate( day.date );
							if (
								dayDate >= event.start_date &&
								dayDate <= event.end_date
							) {
								if ( firstDayIndex === -1 ) {
									firstDayIndex = index;
								}
								lastDayIndex = index;
							}
						} );

						if ( firstDayIndex === -1 ) {
							return null;
						}

						return {
							event,
							startIndex: firstDayIndex,
							endIndex: lastDayIndex,
							span: lastDayIndex - firstDayIndex + 1,
						};
					} )
					.filter( ( bar ) => bar !== null );

				return bars;
			},

			getEventsForCurrentMonth() {
				// Get all events in the month.
				const startOfMonth = new Date(
					this.currentDate.getFullYear(),
					this.currentDate.getMonth(),
					1
				);

				const endOfMonth = new Date(
					this.currentDate.getFullYear(),
					this.currentDate.getMonth() + 1,
					0,
					23,
					59,
					59,
					999
				);

				return this.events.filter( ( event ) => {
					const eventStart = new Date( event.start_date );
					const eventEnd = event.end_date
						? new Date( event.end_date )
						: eventStart;

					return eventStart <= endOfMonth && eventEnd >= startOfMonth;
				} );
			},

			previousMonth() {
				this.currentDate = new Date(
					this.currentYear,
					this.currentMonth - 1,
					1
				);
				// fetchEvents will check if data is already loaded.
				this.fetchEvents();
			},

			nextMonth() {
				this.currentDate = new Date(
					this.currentYear,
					this.currentMonth + 1,
					1
				);
				// fetchEvents will check if data is already loaded.
				this.fetchEvents();
			},

			goToToday() {
				this.currentDate = new Date();
				// fetchEvents will check if data is already loaded.
				this.fetchEvents();
			},

			isToday( date ) {
				const today = new Date();
				return (
					date.getDate() === today.getDate() &&
					date.getMonth() === today.getMonth() &&
					date.getFullYear() === today.getFullYear()
				);
			},

			handleDayClick( date ) {
				this.selectedDate = date;
				this.selectedEvents = this.getEventsForDate( date );

				if ( this.selectedEvents.length > 0 ) {
					this.showModal = true;
				}
			},

			closeModal() {
				this.showModal = false;
				this.selectedDate = null;
				this.selectedEvents = [];
			},

			handleResize() {
				if ( typeof window !== 'undefined' ) {
					this.windowWidth = window.innerWidth;
				}
			},

			formatEventDateTime( event ) {
				const parts = [];

				// Format start date/time.
				if ( this.showStartDate || this.showStartTime ) {
					let startPart = '';

					if ( this.showStartDate ) {
						startPart = this.formatDateDisplay( event.start_date );
					}

					if ( this.showStartTime && event.start_time ) {
						if ( startPart ) {
							startPart +=
								' ' +
								this.formatTimeDisplay( event.start_time );
						} else {
							startPart = this.formatTimeDisplay(
								event.start_time
							);
						}
					}

					if ( startPart ) {
						parts.push( startPart );
					}
				}

				// Format end date/time (only if event has end_date).
				if (
					event.end_date &&
					( this.showEndDate || this.showEndTime )
				) {
					let endPart = '';

					// Event with end_date (could be same day or multi-day).
					if (
						this.showEndDate &&
						event.end_date !== event.start_date
					) {
						endPart = this.formatDateDisplay( event.end_date );
					}

					if ( this.showEndTime && event.end_time ) {
						if ( endPart ) {
							endPart +=
								' ' + this.formatTimeDisplay( event.end_time );
						} else {
							endPart = this.formatTimeDisplay( event.end_time );
						}
					}

					if ( endPart ) {
						parts.push( endPart );
					}
				}

				// For events without end_date, show end_time if configured.
				if ( ! event.end_date && this.showEndTime && event.end_time ) {
					parts.push( this.formatTimeDisplay( event.end_time ) );
				}

				return parts.join( ' - ' );
			},

			generateGoogleCalendarURL( {
				title,
				start_date,
				end_date = null,
				start_time = null,
				end_time = null,
			} ) {
				const baseURL =
					'https://calendar.google.com/calendar/render?action=TEMPLATE';
				const text = encodeURIComponent( title );
				const formatDateTime = ( date, time, isEndDate = false ) => {
					const [ year, month, day ] = date
						.split( '-' )
						.map( Number );
					const d = new Date( year, month - 1, day );

					if ( time ) {
						const [ hours, minutes ] = time.split( ':' );

						return `${ year }${ String( month ).padStart(
							2,
							'0'
						) }${ String( day ).padStart(
							2,
							'0'
						) }T${ hours }${ minutes }00`;
					}

					if ( isEndDate ) {
						d.setDate( d.getDate() + 1 );
						return `${ d.getFullYear() }${ String(
							d.getMonth() + 1
						).padStart( 2, '0' ) }${ String( d.getDate() ).padStart(
							2,
							'0'
						) }`;
					}

					return `${ year }${ String( month ).padStart(
						2,
						'0'
					) }${ String( day ).padStart( 2, '0' ) }`;
				};

				const start = formatDateTime( start_date, start_time );

				let end;
				if ( end_date ) {
					end = formatDateTime( end_date, end_time, ! end_time );
				} else if ( start_time && end_time ) {
					end = formatDateTime( start_date, end_time );
				} else {
					const nextDay = new Date( start_date );
					nextDay.setDate( nextDay.getDate() + 1 );
					end = formatDateTime( nextDay, null );
				}

				const dates = `${ start }/${ end }`;
				const url = `${ baseURL }&text=${ text }&dates=${ dates }`;

				return url;
			},

			generateAppleCalendarURL( {
				title,
				start_date,
				end_date = null,
				start_time = null,
				end_time = null,
			} ) {
				const formatICSDateTime = ( date, time ) => {
					const [ year, month, day ] = date
						.split( '-' )
						.map( Number );
					const d = new Date( year, month - 1, day );
					const formattedYear = d.getFullYear();
					const formattedMonth = String( d.getMonth() + 1 ).padStart(
						2,
						'0'
					);
					const formattedDay = String( d.getDate() ).padStart(
						2,
						'0'
					);

					if ( time ) {
						const [ hours, minutes ] = time.split( ':' );

						return `${ formattedYear }${ formattedMonth }${ formattedDay }T${ hours }${ minutes }00`;
					}
					return `${ formattedYear }${ formattedMonth }${ formattedDay }`;
				};

				const dtstart = formatICSDateTime( start_date, start_time );

				let dtend;
				if ( end_date ) {
					dtend = formatICSDateTime( end_date, end_time );
					if ( ! end_time ) {
						const d = new Date( end_date );
						d.setDate( d.getDate() + 1 );
						const year = d.getFullYear();
						const month = String( d.getMonth() + 1 ).padStart(
							2,
							'0'
						);
						const day = String( d.getDate() ).padStart( 2, '0' );
						dtend = `${ year }${ month }${ day }`;
					}
				} else if ( start_time && end_time ) {
					dtend = formatICSDateTime( start_date, end_time );
				} else {
					const nextDay = new Date( start_date );
					nextDay.setDate( nextDay.getDate() + 1 );
					dtend = formatICSDateTime( nextDay, null );
				}

				const isAllDay = ! start_time && ! end_time;

				const icsContent = [
					'BEGIN:VCALENDAR',
					'VERSION:2.0',
					'PRODID:-//Apple Inc.//iCal//EN',
					'CALSCALE:GREGORIAN',
					'BEGIN:VEVENT',
					`SUMMARY:${ title }`,
					isAllDay
						? `DTSTART;VALUE=DATE:${ dtstart }`
						: `DTSTART:${ dtstart }`,
					isAllDay
						? `DTEND;VALUE=DATE:${ dtend }`
						: `DTEND:${ dtend }`,
					`DTSTAMP:${
						new Date()
							.toISOString()
							.replace( /[-:]/g, '' )
							.split( '.' )[ 0 ]
					}Z`,
					`UID:${ Date.now() }@calendar`,
					'END:VEVENT',
					'END:VCALENDAR',
				].join( '\r\n' );

				const dataUri = `data:text/calendar;charset=utf-8,${ encodeURIComponent(
					icsContent
				) }`;

				return dataUri;
			},

			downloadAppleCalendarEvent( event ) {
				const dataUri = this.generateAppleCalendarURL( event );
				const link = document.createElement( 'a' );
				link.href = dataUri;
				link.download = `${ event.title.replace(
					/[^a-z0-9]/gi,
					'_'
				) }.ics`;
				document.body.appendChild( link );
				link.click();
				document.body.removeChild( link );
			},
		},

		mounted() {
			this.fetchEvents();

			// Add resize event listener.
			if ( typeof window !== 'undefined' ) {
				window.addEventListener( 'resize', this.handleResize );
			}
		},

		beforeUnmount() {
			// Clean up resize event listener.
			if ( typeof window !== 'undefined' ) {
				window.removeEventListener( 'resize', this.handleResize );
			}
		},

		template: `
            <div class="ventocalendar-calendar" :class="'layout-' + layout">
                <div class="calendar-header">
                    <button @click="previousMonth" class="nav-button" :title="translate('Previous month')">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 16L6 10L12 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <h2 class="month-year">{{ monthName }}</h2>
                    <button @click="goToToday" class="today-button">{{ translate('Today') }}</button>
                    <button @click="nextMonth" class="nav-button" :title="translate('Next month')">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8 4L14 10L8 16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>

                <div v-if="loading" class="calendar-loading">
                    <span class="spinner is-active"></span>
                </div>

                <div v-else>
					<div class="calendar-grid" v-if="viewType === 'calendar'">
	                    <div class="weekday-header">
	                        <div v-for="day in weekDays" :key="day" class="weekday">
	                            {{ day }}
	                        </div>
	                    </div>

	                    <div v-for="(week, weekIndex) in weeks" :key="weekIndex" class="calendar-week">
	                        <div class="week-days">
	                            <div
	                                v-for="(day, dayIndex) in week"
	                                :key="dayIndex"
	                                :class="[
	                                    'calendar-day',
	                                    { 'other-month': !day.isCurrentMonth },
	                                    { 'today': isToday(day.date) }
	                                ]"
	                                @click="handleDayClick(day.date)"
	                            >
	                                <div class="day-number">{{ day.date.getDate() }}</div>
	                                <div class="day-events">
	                                    <div
	                                        v-for="event in getEventsForDate(day.date).filter(e => !e.end_date)"
	                                        :key="event.id"
	                                        class="event-dot-with-time"
	                                        :title="event.title"
	                                    >
	                                        <span class="event-dot" :style="{ backgroundColor: event.color }"></span>
	                                        <span v-if="event.start_time" class="event-time">{{ formatTimeDisplay(event.start_time) }}</span>
	                                    </div>
	                                </div>
	                            </div>
	                        </div>

	                        <div class="week-event-bars" v-if="getEventBarsForWeek(week).length > 0">
	                            <div
	                                v-for="(bar, barIndex) in getEventBarsForWeek(week)"
	                                :key="bar.event.id + '-' + weekIndex"
	                                class="event-bar"
	                                :style="{
	                                    backgroundColor: bar.event.color,
	                                    left: 'calc(' + ((bar.startIndex * 100) / 7) + '%)',
	                                    width: 'calc(' + ((bar.span * 100) / 7) + '%)',
	                                    bottom: 'calc(' + ((barIndex % 3) * barSpacing) + 'px + ' + barsOffset + 'px)'
	                                }"
	                                :title="bar.event.title"
	                            >
	                                <span class="event-bar-title">{{ bar.event.title }}</span>
	                            </div>
	                        </div>
	                    </div>
					</div>
					<div class="eventslist" v-if="viewType === 'list'">
						<div v-if="getEventsForCurrentMonth().length === 0" class="no-events">
                            {{ translate('No events on this month') }}
                        </div>
                        <ul v-else class="events-list">
                            <li v-for="event in getEventsForCurrentMonth()" :key="event.id" class="event-item" :style="{ borderColor: event.color }">
                                <div class="event-block">
                                    <div class="event-details">
                                        <div class="event-title">{{ event.title }}</div>
                                        <div v-if="formatEventDateTime(event)" class="event-meta">
                                            <div class="meta-item">
                                                {{ formatEventDateTime(event) }}
                                            </div>
                                        </div>
                                    </div>
									<div class="event-actions">
										<div class="event-link">
											<a :href="event.permalink">{{ translate('View event') }}</a>
										</div>
										<div class="event-add-to-my-calendar-buttons" v-if="showAddToCalendarGoogle || showAddToCalendarApple">
											<span>{{ translate('Add to calendar') }}</span>
											<a :href="generateGoogleCalendarURL(event)" v-if="showAddToCalendarGoogle">Google</a>
											<a href="#" @click="downloadAppleCalendarEvent(event)" v-if="showAddToCalendarApple">Apple</a>
										</div>
									</div>
                                </div>
                            </li>
                        </ul>
					</div>
                </div>

                <div v-if="showModal" class="calendar-modal-overlay" @click="closeModal">
                    <div class="calendar-modal" @click.stop>
                        <div class="modal-header">
                            <h3>{{ formatDateDisplay(formatDate(selectedDate)) }}</h3>
                            <button @click="closeModal" class="modal-close">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div v-if="selectedEvents.length === 0" class="no-events">
                                {{ translate('No events on this day') }}
                            </div>
                            <ul v-else class="events-list">
                                <li v-for="event in selectedEvents" :key="event.id" class="event-item" :style="{ borderColor: event.color }">
                                    <div class="event-block">
                                        <div class="event-details">
                                            <div class="event-title">{{ event.title }}</div>
                                            <div v-if="formatEventDateTime(event)" class="event-meta">
                                                <div class="meta-item">
                                                    {{ formatEventDateTime(event) }}
                                                </div>
                                            </div>
                                        </div>
										<div class="event-actions">
											<div class="event-link">
												<a :href="event.permalink">{{ translate('View event') }}</a>
											</div>
											<div class="event-add-to-my-calendar-buttons" v-if="showAddToCalendarGoogle || showAddToCalendarApple">
												<span>{{ translate('Add to calendar') }}</span>
												<a :href="generateGoogleCalendarURL(event)" v-if="showAddToCalendarGoogle">Google</a>
												<a href="#" @click="downloadAppleCalendarEvent(event)" v-if="showAddToCalendarApple">Apple</a>
											</div>
										</div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        `,
	};

	window.VentoCalendarCalendar = VentoCalendarCalendar;
} )();
