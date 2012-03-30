<?php

	class extension_shorten extends Extension
	{
		public function getSubscribedDelegates()
		{
			return array(
				array (
					'page' =>'/frontend/',
					'delegate' => 'EventFinalSaveFilter',
					'callback' => 'cleanup'
				)
			);
		}

		public function uninstall()
		{
			Symphony::Database()->query("DROP TABLE `tbl_fields_shorten`");
		}

		public function install()
		{
			return Symphony::Database()->query(
				"CREATE TABLE `tbl_fields_shorten` (
					`id` int(11) unsigned NOT NULL auto_increment,
					`field_id` int(11) unsigned NOT NULL,
					`redirect` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
					`hide` ENUM('yes', 'no') DEFAULT 'no',
					PRIMARY KEY (`id`),
					KEY `field_id` (`field_id`)
				) ENGINE=MyISAM;"
			);
		}

		public function cleanup($context)
		{
			$section = SectionManager::fetch($context['entry']->get('section_id'));

			$field = $section->fetchFields('shorten');
			if (!$field) return;

			$field = current($field);
			$entry_id = $context['entry']->get('id');
			$field->update($entry_id);
		}
	}
