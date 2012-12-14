<?php


	class fieldShorten extends Field
	{
		/**
		 * Construct a new instance of this field.
		 */
		public function __construct()
		{
			$this->_name = __('Shorten');
			parent::__construct();
		}

		/**
		 * Test whether this field can show the table column.
		 *
		 * @return boolean
		 *	true if this can, false otherwise.
		 */
		public function canShowTableColumn()
		{
			return true;
		}

		/**
		 * Test whether this field can be toggled using the With Selected menu
		 * on the Publish Index.
		 *
		 * @return boolean
		 *	true if it can be toggled, false otherwise.
		 */
		public function canToggle()
		{
			return false;
		}

		/**
		 * Test whether this field can be filtered. This default implementation
		 * prohibits filtering. Filtering allows the XML output results to be limited
		 * according to an input parameter. Subclasses should override this if
		 * filtering is supported.
		 *
		 * @return boolean
		 *	true if this can be filtered, false otherwise.
		 */
		public function canFilter()
		{
			return true;
		}

		/**
		 * Test whether this field can be prepopulated with data. This default
		 * implementation does not support pre-population and, thus, returns false.
		 *
		 * @return boolean
		 *	true if this can be pre-populated, false otherwise.
		 */
		public function canPrePopulate()
		{
			return false;
		}

		/**
		 * Test whether this field can be sorted. This default implementation
		 * returns false.
		 *
		 * @return boolean
		 *	true if this field is sortable, false otherwise.
		 */
		public function isSortable()
		{
			return false;
		}

		/**
		 * Test whether this field must be unique in a section, that is, only one of
		 * this field's type is allowed per section. This default implementation
		 * always returns false.
		 *
		 * @return boolean
		 *	true if the content of this field must be unique, false otherwise.
		 */
		public function mustBeUnique()
		{
			return true;
		}

		/**
		 * Test whether this field supports data-source output grouping. This
		 * default implementation prohibits grouping. Data-source grouping allows
		 * clients of this field to group the XML output according to this field.
		 * Subclasses should override this if grouping is supported.
		 *
		 * @return boolean
		 *	true if this field does support data-source grouping, false otherwise.
		 */
		public function allowDatasourceOutputGrouping()
		{
			return false;
		}

		/**
		 * Just prior to the field being deleted, this function allows
		 * Fields to cleanup any additional things before it is removed
		 * from the section. This may be useful to remove data from any
		 * custom field tables or the configuration.
		 *
		 * @since Symphony 2.2.1
		 * @return boolean
		 */
		public function tearDown()
		{
			return true;
		}

		/**
		 * Display the default settings panel, calls the `buildSummaryBlock`
		 * function after basic field settings are added to the wrapper.
		 *
		 * @see buildSummaryBlock()
		 * @param XMLElement $wrapper
		 *	the input XMLElement to which the display of this will be appended.
		 * @param mixed errors (optional)
		 *	the input error collection. this defaults to null.
		 */
		public function displaySettingsPanel(XMLElement &$wrapper, $errors = null)
		{

			parent::displaySettingsPanel(&$wrapper, $errors=NULL);

			$order = $this->get('sortorder');

			$label = Widget::Label();
			$input = Widget::Input("fields[{$order}][hide]", 'yes', 'checkbox');

			if ($this->get('hide') == 'yes') $input->setAttribute('checked', 'checked');
			$label->setValue($input->generate() .' '. __('Hide this field on publish page'));

			$wrapper->appendChild($label);
			$this->appendShowColumnCheckbox($wrapper);
		}

		/**
		 * Display the publish panel for this field. The display panel is the
		 * interface shown to Authors that allow them to input data into this
		 * field for an `Entry`.
		 *
		 * @param XMLElement $wrapper
		 *	the XML element to append the html defined user interface to this
		 *	field.
		 * @param array $data (optional)
		 *	any existing data that has been supplied for this field instance.
		 *	this is encoded as an array of columns, each column maps to an
		 *	array of row indexes to the contents of that column. this defaults
		 *	to null.
		 * @param mixed $flagWithError (optional)
		 *	flag with error defaults to null.
		 * @param string $fieldnamePrefix (optional)
		 *	the string to be prepended to the display of the name of this field.
		 *	this defaults to null.
		 * @param string $fieldnameSuffix (optional)
		 *	the string to be appended to the display of the name of this field.
		 *	this defaults to null.
		 * @param integer $entry_id (optional)
		 *	the entry id of this field. this defaults to null.
		 */
		public function displayPublishPanel(XMLElement &$wrapper, $data = null, $flagWithError = null, $fieldnamePrefix = null, $fieldnamePostfix = null, $entry_id = null)
		{
			if ($this->get('hide') == 'yes' || !$entry_id) return;

			$value = isset($data['value']) ? $data['value'] : $this->encode($entry_id);

			$label = Widget::Label($this->get('label'));
			$span  = new XMLElement('span', null, array('class' => 'frame'));
			$short = new XMLElement('div',
				__('This entry has been shortened to').
					' <strong>'. $value. '</strong>'
				);

			$span->appendChild($short);
			$label->appendChild($span);
			$wrapper->appendChild($label);
		}

		/**
		 * Process the raw field data.
		 *
		 * @param mixed $data
		 *	post data from the entry form
		 * @param integer $status
		 *	the status code resultant from processing the data.
		 * @param string $message
		 *	the place to set any generated error message. any previous value for
		 *	this variable will be overwritten.
		 * @param boolean $simulate (optional)
		 *	true if this will tell the CF's to simulate data creation, false
		 *	otherwise. this defaults to false. this is important if clients
		 *	will be deleting or adding data outside of the main entry object
		 *	commit function.
		 * @param mixed $entry_id (optional)
		 *	the current entry. defaults to null.
		 * @return array
		 *	the processed field data.
		 */
		public function processRawFieldData($data, &$status, &$message=null, $simulate=false, $entry_id=null) {

			$status = self::__OK__;

			if (!$entry_id) return array();

			return array(
				'value' => $this->encode($entry_id)
			);
		}

		/**
		 * The default method for constructing the example form markup containing this
		 * field when utilized as part of an event. This displays in the event documentation
		 * and serves as a basic guide for how markup should be constructed on the
		 * `Frontend` to save this field
		 *
		 * @return XMLElement
		 *  a label widget containing the formatted field element name of this.
		 */
		public function getExampleFormMarkup(){
			return '';
		}

		/**
		 * Commit the settings of this field from the section editor to
		 * create an instance of this field in a section.
		 *
		 * @return boolean
		 *  true if the commit was successful, false otherwise.
		 */
		public function commit() {
			if (!parent::commit()) return false;

			$id = $this->get('id');
			if ($id === false) return false;

			$fields = array(
				'hide' => $this->get('hide')
			);

			return FieldManager::saveSettings($id, $fields);
		}

		/**
		 * The default field table construction method. This constructs the bare
		 * minimum set of columns for a valid field table. Subclasses are expected
		 * to overload this method to create a table structure that contains
		 * additional columns to store the specific data created by the field.
		 *
		 * @return boolean
		 */
		public function createTable(){
			return Symphony::Database()->query(
				"CREATE TABLE IF NOT EXISTS `tbl_entries_data_" . $this->get('id') . "` (
				  `id` int(11) unsigned NOT NULL auto_increment,
				  `entry_id` int(11) unsigned NOT NULL,
				  `value` varchar(255) default NULL,
				  PRIMARY KEY  (`id`),
				  KEY `entry_id` (`entry_id`),
				  KEY `value` (`value`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
			);
		}

		/*
		 * -----------------------------------------------------------------
		 * Stolen from: http://snipplr.com/view/22246/base62-encode--decode/
		 *
		 */
		public function encode($val, $base=62, $chars='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
		{
			// can't handle numbers larger than 2^31-1 = 2147483647
			$str = '';

			do {
				$i = $val % $base;
				$str = $chars[$i] . $str;
				$val = ($val - $i) / $base;
			} while($val > 0);

			return $str;
		}

		public function decode($str, $base=62, $chars='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
		{
			$len = strlen($str);
			$val = 0;
			$arr = array_flip(str_split($chars));

			for($i = 0; $i < $len; ++$i)
				$val += $arr[$str[$i]] * pow($base, $len-$i-1);

			return $val;
		}
	}
