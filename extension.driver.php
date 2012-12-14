<?php

	class extension_shorten extends Extension
	{

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
					`hide` ENUM('yes', 'no') DEFAULT 'no',
					PRIMARY KEY (`id`),
					KEY `field_id` (`field_id`)
				) ENGINE=MyISAM;"
			);
		}
	}
