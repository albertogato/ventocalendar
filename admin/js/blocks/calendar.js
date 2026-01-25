/**
 * VentoCalendar Calendar Block
 * Gutenberg block for displaying the events calendar
 *
 * @param {Object} wp The WordPress global object.
 * @package
 */

( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { InspectorControls } = wp.blockEditor;
	const { PanelBody, SelectControl, ToggleControl, TextControl } =
		wp.components;
	const { __ } = wp.i18n;
	const { createElement: el } = wp.element;

	registerBlockType( 'ventocalendar/calendar', {
		title: __( 'Events Calendar', 'ventocalendar' ),
		description: __(
			'Display a monthly calendar view of your events',
			'ventocalendar'
		),
		icon: 'calendar-alt',
		category: 'widgets',
		attributes: {
			viewType: {
				type: 'string',
				default: 'calendar',
			},
			firstDayOfWeek: {
				type: 'string',
				default: 'monday',
			},
			initialMonth: {
				type: 'string',
				default: '',
			},
			layout: {
				type: 'string',
				default: 'basic',
			},
			showStartDate: {
				type: 'boolean',
				default: true,
			},
			showEndDate: {
				type: 'boolean',
				default: true,
			},
			showStartTime: {
				type: 'boolean',
				default: false,
			},
			showEndTime: {
				type: 'boolean',
				default: false,
			},
			showAddToCalendarGoogle: {
				type: 'boolean',
				default: false,
			},
			showAddToCalendarApple: {
				type: 'boolean',
				default: false,
			},
		},

		edit( props ) {
			const { attributes, setAttributes } = props;
			const {
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
			} = attributes;

			return el(
				'div',
				{},
				el(
					InspectorControls,
					{},
					el(
						PanelBody,
						{
							title: __( 'Calendar settings', 'ventocalendar' ),
							initialOpen: true,
						},
						el( SelectControl, {
							label: __( 'View type', 'ventocalendar' ),
							value: viewType,
							options: [
								{
									label: __( 'Calendar', 'ventocalendar' ),
									value: 'calendar',
								},
								{
									label: __( 'List', 'ventocalendar' ),
									value: 'list',
								},
							],
							onChange( value ) {
								setAttributes( { viewType: value } );
							},
						} ),
						el( SelectControl, {
							label: __( 'First day of week', 'ventocalendar' ),
							value: firstDayOfWeek,
							options: [
								{
									label: __( 'Monday', 'ventocalendar' ),
									value: 'monday',
								},
								{
									label: __( 'Sunday', 'ventocalendar' ),
									value: 'sunday',
								},
							],
							onChange( value ) {
								setAttributes( { firstDayOfWeek: value } );
							},
						} ),
						el( TextControl, {
							label: __( 'Initial month', 'ventocalendar' ),
							help: __(
								'Initial month to display (format: YYYY–MM, e.g., 2024–03). Leave empty to show current month.',
								'ventocalendar'
							),
							value: initialMonth,
							placeholder: __( 'Current month', 'ventocalendar' ),
							onChange( value ) {
								setAttributes( { initialMonth: value } );
							},
						} ),
						el( SelectControl, {
							label: __( 'Laoyut', 'ventocalendar' ),
							value: layout,
							options: [
								{
									label: __( 'Basic', 'ventocalendar' ),
									value: 'basic',
								},
								{
									label: __( 'Compact', 'ventocalendar' ),
									value: 'compact',
								},
								{
									label: __( 'Clean', 'ventocalendar' ),
									value: 'clean',
								},
							],
							onChange( value ) {
								setAttributes( { layout: value } );
							},
						} )
					),
					el(
						PanelBody,
						{
							title: __(
								'Event details display',
								'ventocalendar'
							),
							initialOpen: true,
						},
						el( ToggleControl, {
							label: __( 'Show start date', 'ventocalendar' ),
							help: __(
								'Display the start date in the event modal',
								'ventocalendar'
							),
							checked: showStartDate,
							onChange( value ) {
								setAttributes( { showStartDate: value } );
							},
						} ),
						el( ToggleControl, {
							label: __( 'Show end date', 'ventocalendar' ),
							help: __(
								'Display the end date in the event modal',
								'ventocalendar'
							),
							checked: showEndDate,
							onChange( value ) {
								setAttributes( { showEndDate: value } );
							},
						} ),
						el( ToggleControl, {
							label: __( 'Show start time', 'ventocalendar' ),
							help: __(
								'Display the start time in the event modal',
								'ventocalendar'
							),
							checked: showStartTime,
							onChange( value ) {
								setAttributes( { showStartTime: value } );
							},
						} ),
						el( ToggleControl, {
							label: __( 'Show end time', 'ventocalendar' ),
							help: __(
								'Display the end time in the event modal',
								'ventocalendar'
							),
							checked: showEndTime,
							onChange( value ) {
								setAttributes( { showEndTime: value } );
							},
						} ),
						el( ToggleControl, {
							label: __(
								'Show Add to Google Calendar button',
								'ventocalendar'
							),
							help: __(
								'Display the Google add to calendar button in the event modal',
								'ventocalendar'
							),
							checked: showAddToCalendarGoogle,
							onChange( value ) {
								setAttributes( {
									showAddToCalendarGoogle: value,
								} );
							},
						} ),
						el( ToggleControl, {
							label: __(
								'Show Add to Apple Calendar button',
								'ventocalendar'
							),
							help: __(
								'Display the Apple add to calendar button in the event modal',
								'ventocalendar'
							),
							checked: showAddToCalendarApple,
							onChange( value ) {
								setAttributes( {
									showAddToCalendarApple: value,
								} );
							},
						} )
					)
				),
				el(
					'div',
					{
						className: 'ventocalendar-calendar-block-preview',
						style: {
							padding: '20px',
							background: '#f0f0f1',
							borderRadius: '8px',
							textAlign: 'center',
						},
					},
					el( 'span', {
						className: 'dashicons dashicons-calendar-alt',
						style: {
							fontSize: '64px',
							width: '64px',
							height: '64px',
							color: '#2271b1',
						},
					} ),
					el(
						'p',
						{
							style: {
								marginTop: '10px',
								fontSize: '14px',
								color: '#50575e',
							},
						},
						__( 'Events Calendar', 'ventocalendar' )
					),
					el(
						'p',
						{
							style: {
								marginTop: '5px',
								fontSize: '12px',
								color: '#787c82',
							},
						},
						__(
							'The calendar will be displayed here',
							'ventocalendar'
						)
					)
				)
			);
		},

		save() {
			// Server-side rendering.
			return null;
		},
	} );
} )( window.wp );
