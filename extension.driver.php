<?php

	class extension_shorten extends Extension
	{
		protected static $sm;

		public function about()
		{
			return array(
				'name' => 'Shorten',
				'version' => '0.3',
				'release-date' => '2012-02-14',
				'author' => array(
					'name' => 'Marco Sampellegrini',
					'email' => 'm@rcosa.mp'
				)
			);
		}

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
			if (!self::$sm)
			{
				$sm = new SectionManager(Symphony::Engine());
				self::$sm = $sm;
			}

			$section = self::$sm->fetch($context['entry']->get('section_id'));
			$field = $section->fetchFields('shorten');
			if (!$field) return;

			$field = current($field);

			$entry_id = $context['entry']->get('id');
			$field->update($entry_id);
		}
	}
