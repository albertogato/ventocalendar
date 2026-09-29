/**
 * VentoCalendar Event Info Block
 * Gutenberg block for displaying the events calendar
 *
 * @param {Object} blocks      - WordPress blocks API
 * @param {Object} element     - WordPress element API
 * @param {Object} blockEditor - WordPress block editor API
 * @param {Object} components  - WordPress components API
 * @param {Object} i18n        - WordPress internationalization API
 * @param {Object} data        - WordPress data API
 * @package
 */

( function ( blocks, element, blockEditor, components, i18n, data ) {
	const el = element.createElement;
	const registerBlockType = blocks.registerBlockType;
	const InspectorControls = blockEditor.InspectorControls;
	const PanelBody = components.PanelBody;
	const CheckboxControl = components.CheckboxControl;
	const Placeholder = components.Placeholder;
	const __ = i18n.__;
	const useSelect = data.useSelect;

	registerBlockType( 'ventocalendar/event-info', {
		title: __( 'Event Info', 'ventocalendar' ),
		icon: 'calendar-alt',
		category: 'widgets',
		attributes: {
			showStartTime: {
				type: 'boolean',
				default: true,
			},
			showEndTime: {
				type: 'boolean',
				default: true,
			},
			showLocation: {
				type: 'boolean',
				default: true,
			},
			showAddress: {
				type: 'boolean',
				default: true,
			},
			showMap: {
				type: 'boolean',
				default: true,
			},
		},
		supports: {
			html: false,
		},

		edit: function Edit( props ) {
			const attributes = props.attributes;
			const setAttributes = props.setAttributes;

			// Get information about the current post and metadata.
			const postData = useSelect( function ( select ) {
				const editor = select( 'core/editor' );
				const currentPost = editor ? editor.getCurrentPost() : null;

				if ( ! currentPost ) {
					return {
						postType: null,
						startDate: null,
						endDate: null,
						startTime: null,
						endTime: null,
						location: null,
						address: null,
						showMap: false,
						color: null,
					};
				}

				const meta = editor.getEditedPostAttribute( 'meta' ) || {};

				return {
					postType: currentPost.type,
					startDate: meta._start_date || null,
					endDate: meta._end_date || null,
					startTime: meta._start_time || null,
					endTime: meta._end_time || null,
					location: meta._location || null,
					address: meta._address || null,
					showMap:
						meta._show_map === true ||
						meta._show_map === 1 ||
						meta._show_map === '1',
					color: meta._color || '#2271b1',
				};
			}, [] );

			// Controls on the side panel.
			const inspectorControls = el(
				InspectorControls,
				{},
				el(
					PanelBody,
					{
						title: __( 'Event info settings', 'ventocalendar' ),
						initialOpen: true,
					},
					el( CheckboxControl, {
						label: __( 'Show start time', 'ventocalendar' ),
						checked: attributes.showStartTime,
						onChange( value ) {
							setAttributes( { showStartTime: value } );
						},
					} ),
					el( CheckboxControl, {
						label: __( 'Show end time', 'ventocalendar' ),
						checked: attributes.showEndTime,
						onChange( value ) {
							setAttributes( { showEndTime: value } );
						},
					} ),
					el( CheckboxControl, {
						label: __( 'Show location', 'ventocalendar' ),
						checked: attributes.showLocation,
						onChange( value ) {
							setAttributes( { showLocation: value } );
						},
					} ),
					el( CheckboxControl, {
						label: __( 'Show address', 'ventocalendar' ),
						checked: attributes.showAddress,
						onChange( value ) {
							setAttributes( { showAddress: value } );
						},
					} ),
					el( CheckboxControl, {
						label: __( 'Show map', 'ventocalendar' ),
						checked: attributes.showMap,
						onChange( value ) {
							setAttributes( { showMap: value } );
						},
					} )
				)
			);

			// Check if we are in a post of type 'ventocalendar_event'.
			if ( postData.postType !== 'ventocalendar_event' ) {
				return el(
					'div',
					{},
					inspectorControls,
					el(
						Placeholder,
						{
							icon: 'calendar-alt',
							label: __( 'Event Info', 'ventocalendar' ),
						},
						el(
							'p',
							{},
							__(
								'This block can only be used in Event posts.',
								'ventocalendar'
							)
						)
					)
				);
			}

			// Check if there is no set start date.
			if ( ! postData.startDate ) {
				return el(
					'div',
					{},
					inspectorControls,
					el(
						Placeholder,
						{
							icon: 'calendar-alt',
							label: __( 'Event Info', 'ventocalendar' ),
						},
						el(
							'p',
							{},
							__(
								'Please set the event start date in the Event Details metabox.',
								'ventocalendar'
							)
						)
					)
				);
			}

			// Auxiliary function for formatting date and time (simplified for preview).
			function formatDateTime( dateString, timeString, includeTime ) {
				if ( ! dateString ) {
					return '';
				}

				// Build datetime string.
				let dateTimeString = dateString;
				if ( includeTime && timeString ) {
					dateTimeString += 'T' + timeString;
				}

				const date = new Date( dateTimeString );

				// Simplified format for preview.
				const dateOptions = {
					year: 'numeric',
					month: 'long',
					day: 'numeric',
				};
				const timeOptions = { hour: '2-digit', minute: '2-digit' };

				if ( includeTime && timeString ) {
					return (
						date.toLocaleDateString( undefined, dateOptions ) +
						' ' +
						date.toLocaleTimeString( undefined, timeOptions )
					);
				}
				return date.toLocaleDateString( undefined, dateOptions );
			}

			// Auxiliary function to format time only (without date).
			function formatTimeOnly( dateString, timeString ) {
				if ( ! dateString || ! timeString ) {
					return '';
				}

				const dateTimeString = dateString + 'T' + timeString;
				const date = new Date( dateTimeString );

				const timeOptions = { hour: '2-digit', minute: '2-digit' };
				return date.toLocaleTimeString( undefined, timeOptions );
			}

			// Format dates for preview.
			const startFormatted = formatDateTime(
				postData.startDate,
				postData.startTime,
				attributes.showStartTime
			);

			let endFormatted = '';
			// Check if end_date exists and is different from start_date (multi-day event).
			if ( postData.endDate && postData.endDate !== postData.startDate ) {
				// Multi-day event: display end_date with end_time if it exists.
				endFormatted = formatDateTime(
					postData.endDate,
					postData.endTime,
					attributes.showEndTime
				);
			} else if ( postData.endTime && attributes.showEndTime ) {
				// Evento del mismo día: solo mostrar la hora.
				endFormatted = formatTimeOnly(
					postData.startDate,
					postData.endTime
				);
			}

			const location =
				attributes.showLocation && postData.location
					? postData.location
					: '';
			const address =
				attributes.showAddress && postData.address
					? postData.address
					: '';
			const shouldShowMap = attributes.showMap && postData.showMap;

			// Render block preview.
			return el(
				'div',
				{},
				inspectorControls,
				el(
					'div',
					{
						className: 'ventocalendar-date-info',
						style: {
							borderLeft: '4px solid ' + postData.color,
							padding: '15px 20px',
							marginBottom: '20px',
							backgroundColor: '#f9f9f9',
						},
					},
					el(
						'div',
						{
							className: 'ventocalendar-date-container',
						},
						startFormatted &&
							el(
								'span',
								{
									className: 'ventocalendar-date-value',
									style: { fontWeight: 'bold' },
								},
								startFormatted
							),
						startFormatted &&
							endFormatted &&
							el(
								'span',
								{
									className: 'ventocalendar-date-separator',
								},
								' - '
							),
						endFormatted &&
							el(
								'span',
								{
									className: 'ventocalendar-date-value',
									style: { fontWeight: 'bold' },
								},
								endFormatted
							)
					),
					( location || address ) &&
						el(
							'div',
							{
								className:
									'ventocalendar-event-location-details',
							},
							location &&
								el(
									'p',
									{
										className:
											'ventocalendar-event-location-line',
									},
									location
								),
							address &&
								el(
									'p',
									{
										className:
											'ventocalendar-event-location-line',
									},
									address
								)
						),
					shouldShowMap &&
						el(
							'div',
							{
								className: 'ventocalendar-event-map-wrapper',
							},
							el(
								'div',
								{
									className: 'ventocalendar-event-map-view',
									style: {
										height: '220px',
										display: 'flex',
										alignItems: 'center',
										justifyContent: 'center',
										backgroundColor: '#eef3f8',
										color: '#334155',
										fontSize: '13px',
										border: '1px solid #d1d5db',
									},
								},
								__( 'Map preview', 'ventocalendar' )
							)
						)
				)
			);
		},

		save() {
			return null;
		},
	} );
} )(
	window.wp.blocks,
	window.wp.element,
	window.wp.blockEditor,
	window.wp.components,
	window.wp.i18n,
	window.wp.data
);
