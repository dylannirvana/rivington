<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

?>

<div class="wrap-rich-snippets-events">
	<div class="seopress-notice">
		<p>
			<?php
				/* translators: %s: link documentation */
				echo wp_kses_post(sprintf(__('Learn more about the <strong>Events schema</strong> from the <a href="%s" target="_blank">Google official documentation website</a>', 'wp-seopress-pro'), 'https://developers.google.com/search/docs/data-types/event'));
			?>
            <span class="dashicons dashicons-external"></span>
		</p>
	</div>
	<p>
		<label for="seopress_pro_rich_snippets_events_type_meta"><?php esc_html_e('Select your event type', 'wp-seopress-pro'); ?></label>
		<?php echo seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_type', ['default', 'events']); ?>
		<span class="description"><?php echo wp_kses_post(__('<strong>Authorized values:</strong> "BusinessEvent", "ChildrensEvent", "ComedyEvent", "CourseInstance", "DanceEvent", "DeliveryEvent", "EducationEvent", "ExhibitionEvent", "Festival", "FoodEvent", "LiteraryEvent", "MusicEvent", "PublicationEvent", "SaleEvent", "ScreeningEvent", "SocialEvent", "SportsEvent", "TheaterEvent", "VisualArtsEvent"', 'wp-seopress-pro')); ?></span>
	</p>
	<p>
		<label for="seopress_pro_rich_snippets_events_name_meta">
			<?php esc_html_e('Event name', 'wp-seopress-pro'); ?>
		</label>
		<?php echo seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_name', ['default', 'events']); ?>
		<span class="description"><?php esc_html_e('The name of your event', 'wp-seopress-pro'); ?></span>
	</p>
	<p>
		<label for="seopress_pro_rich_snippets_events_desc_meta">
			<?php esc_html_e('Event description (default excerpt, or beginning of the content)', 'wp-seopress-pro'); ?>
		</label>
		<?php echo seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_desc', ['default', 'events']); ?>
		<span class="description"><?php esc_html_e('Enter your event description', 'wp-seopress-pro'); ?></span>
	</p>
	<p>
		<label for="seopress_pro_rich_snippets_events_img_meta"><?php esc_html_e('Image thumbnail', 'wp-seopress-pro'); ?></label>
		<?php echo seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_img', ['image', 'events']); ?>
		<span class="description"><?php esc_html_e('Minimum width: 720px - Recommended size: 1920px -  .jpg, .png, or. gif format - crawlable and indexable', 'wp-seopress-pro'); ?></span>
	</p>
	<p>
		<label for="seopress_pro_rich_snippets_events_start_date_meta">
			<?php esc_html_e('Start date', 'wp-seopress-pro'); ?>
		</label>
		<?php echo seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_start_date', ['date', 'events']); ?>
		<span class="description"><?php esc_html_e('e.g. YYYY-MM-DD', 'wp-seopress-pro'); ?></span>
	</p>
	<p>
		<label for="seopress_pro_rich_snippets_events_start_date_timezone_meta">
			<?php esc_html_e('Timezone start date', 'wp-seopress-pro'); ?>
		</label>
		<?php echo seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_start_date_timezone', ['default', 'events']); ?>
		<span class="description"><?php esc_html_e('e.g. -4:00', 'wp-seopress-pro'); ?></span>
	</p>
	<p>
		<label for="seopress_pro_rich_snippets_events_start_time_meta">
			<?php esc_html_e('Start time', 'wp-seopress-pro'); ?></label>
		<?php echo seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_start_time', ['time', 'events']); ?>
		<span class="description"><?php esc_html_e('e.g. HH:MM', 'wp-seopress-pro'); ?></span>
	</p>
	<p>
		<label for="seopress_pro_rich_snippets_events_end_date_meta">
			<?php esc_html_e('End date', 'wp-seopress-pro'); ?>
		</label>
		<?php echo seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_end_date', ['date', 'events']); ?>
		<span class="description"><?php esc_html_e('e.g. YYYY-MM-DD', 'wp-seopress-pro'); ?></span>
	</p>
	<p>
		<label for="seopress_pro_rich_snippets_events_end_time_meta">
			<?php esc_html_e('End time', 'wp-seopress-pro'); ?></label>
		<?php echo seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_end_time', ['time', 'events']); ?>
		<span class="description"><?php esc_html_e('e.g. HH:MM', 'wp-seopress-pro'); ?></span>
	</p>
	<p>
		<label for="seopress_pro_rich_snippets_events_previous_start_date_meta">
			<?php esc_html_e('Previous Start date', 'wp-seopress-pro'); ?>
		</label>
		<?php echo seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_previous_start_date', ['date', 'events']); ?>
		<span class="description"><?php esc_html_e('e.g. YYYY-MM-DD', 'wp-seopress-pro'); ?></span>
	</p>
	<p>
		<label for="seopress_pro_rich_snippets_events_previous_start_time_meta">
			<?php esc_html_e('Previous Start time', 'wp-seopress-pro'); ?>
		</label>
		<?php echo seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_previous_start_time', ['time', 'events']); ?>
		<span class="description"><?php esc_html_e('e.g. HH:MM', 'wp-seopress-pro'); ?></span>
	</p>
	<p>
		<label for="seopress_pro_rich_snippets_events_location_name_meta">
			<?php esc_html_e('Location name', 'wp-seopress-pro'); ?>
		</label>
		<?php echo seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_location_name', ['default', 'events']); ?>
		<span class="description"><?php esc_html_e('e.g. My Local Business name', 'wp-seopress-pro'); ?></span>
	</p>
	<p>
		<label for="seopress_pro_rich_snippets_events_location_url_meta">
			<?php esc_html_e('Event website', 'wp-seopress-pro'); ?></label>
		<?php echo seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_location_url', ['default', 'events']); ?>
		<span class="description"><?php esc_html_e('e.g. https://www.example.com', 'wp-seopress-pro'); ?></span>
	</p>
	<p>
		<label for="seopress_pro_rich_snippets_events_location_address_meta">
			<?php esc_html_e('Location Address', 'wp-seopress-pro'); ?>
		</label>
		<?php echo seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_location_address', ['default', 'events']); ?>
		<span class="description"><?php esc_html_e('e.g. 1 Avenue de l\'Imperatrice, 64200 Biarritz', 'wp-seopress-pro'); ?></span>
	</p>
	<p>
		<label for="seopress_pro_rich_snippets_events_offers_name_meta">
			<?php esc_html_e('Offer name', 'wp-seopress-pro'); ?>
		</label>
		<?php echo seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_offers_name', ['default', 'events']); ?>
		<span class="description"><?php esc_html_e('e.g. General admission', 'wp-seopress-pro'); ?></span>
	</p>
	<p>
		<label for="seopress_pro_rich_snippets_events_offers_cat_meta"><?php esc_html_e('Select your offer category', 'wp-seopress-pro'); ?></label>
		<?php echo seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_offers_cat', ['default', 'events']); ?>
		<span class="description"><?php echo wp_kses_post(__('<strong>Authorized values: </strong>"Primary", "Secondary", "Presale", "Premium"', 'wp-seopress-pro')); ?></span>
	</p>
	<p>
		<label for="seopress_pro_rich_snippets_events_offers_price_meta">
			<?php esc_html_e('Price', 'wp-seopress-pro'); ?>
		</label>
		<?php echo seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_offers_price', ['default', 'events']); ?>
		<span class="description"><?php esc_html_e('e.g. 10', 'wp-seopress-pro'); ?></span>
	</p>
	<p>
		<label for="seopress_pro_rich_snippets_events_offers_price_currency_meta"><?php esc_html_e('Select your currency', 'wp-seopress-pro'); ?></label>
		<?php echo seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_offers_price_currency', ['default', 'events']); ?>
		<span class="description"><?php esc_html_e('e.g. USD, EUR...', 'wp-seopress-pro'); ?></span>
	</p>
	<p>
		<label for="seopress_pro_rich_snippets_events_offers_availability_meta"><?php esc_html_e('Availability', 'wp-seopress-pro'); ?></label>
		<?php echo seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_offers_availability', ['default', 'events']); ?>
		<span class="description"><?php esc_html_e('e.g. InStock, SoldOut, PreOrder', 'wp-seopress-pro'); ?></span>
	</p>
	<p>
		<label for="seopress_rich_snippets_events_offers_valid_from_meta_date"><?php esc_html_e('Valid From', 'wp-seopress-pro'); ?></label>
		<?php echo seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_offers_valid_from_date', ['date', 'events']); ?>
		<span class="description"><?php esc_html_e('The date when tickets go on sale', 'wp-seopress-pro'); ?></span>
	</p>
	<p>
		<label for="seopress_rich_snippets_events_offers_valid_from_meta_time"><?php esc_html_e('Time', 'wp-seopress-pro'); ?></label>
		<?php echo seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_offers_valid_from_time', ['time', 'events']); ?>
		<span class="description"><?php esc_html_e('The time when tickets go on sale', 'wp-seopress-pro'); ?></span>
	</p>
	<p>
		<label for="seopress_pro_rich_snippets_events_offers_url_meta">
			<?php esc_html_e('Website to buy tickets', 'wp-seopress-pro'); ?></label>
		<?php echo seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_offers_url', ['default', 'events']); ?>
		<span class="description"><?php esc_html_e('e.g. https://www.example.com', 'wp-seopress-pro'); ?></span>
	</p>
	<p>
		<label for="seopress_pro_rich_snippets_events_performer_meta">
			<?php esc_html_e('Performer name', 'wp-seopress-pro'); ?></label>
		<?php echo seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_performer', ['default', 'events']); ?>
		<span class="description"><?php esc_html_e('e.g. Lana Del Rey', 'wp-seopress-pro'); ?></span>
	</p>
	<p>
		<label for="seopress_pro_rich_snippets_events_organizer_name_meta">
			<?php esc_html_e('Organizer name', 'wp-seopress-pro'); ?>
		</label>
		<?php echo seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_organizer_name', ['default', 'events']); ?>
		<span class="description"><?php esc_html_e('e.g. Apple', 'wp-seopress-pro'); ?></span>
	</p>
	<p>
		<label for="seopress_pro_rich_snippets_events_organizer_url_meta">
			<?php esc_html_e('Organizer URL', 'wp-seopress-pro'); ?>
		</label>
		<?php echo seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_organizer_url', ['default', 'events']); ?>
		<span class="description"><?php esc_html_e('e.g. https://www.example.com', 'wp-seopress-pro'); ?></span>
	</p>
	<p>
		<label for="seopress_pro_rich_snippets_events_status_meta">
			<?php esc_html_e('Event status', 'wp-seopress-pro'); ?>
		</label>
		<?php echo seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_status', ['default', 'events']); ?>
		<span class="description"><?php echo wp_kses_post(__('<strong>Authorized values:</strong> "EventCancelled", "EventMovedOnline", "EventPostponed", "EventRescheduled", "EventScheduled"', 'wp-seopress-pro')); ?></span>
	</p>
	<p>
		<label for="seopress_pro_rich_snippets_events_attendance_mode_meta">
			<?php esc_html_e('Event attendance mode', 'wp-seopress-pro'); ?>
		</label>
		<?php echo seopress_schemas_mapping_array('seopress_pro_rich_snippets_events_attendance_mode', ['default', 'events']); ?>
		<span class="description"><?php echo wp_kses_post(__('<strong>Authorized values:</strong> "OfflineEventAttendanceMode", "OnlineEventAttendanceMode", "MixedEventAttendanceMode"', 'wp-seopress-pro')); ?></span>
	</p>
</div>
