<?php


	class fieldShorten extends Field
	{

		protected static $revalidate = '__must-revalidate';

		public function canShowTableColumn(){
			return false;
		}

		/**
		 * Test whether this field can be filtered. This default implementation
		 * prohibits filtering. Filtering allows the xml output results to be limited
		 * according to an input parameter. Subclasses should override this if
		 * filtering is supported.
		 *
		 * @return boolean
		 *	true if this can be filtered, false otherwise.
		 */
		public function canFilter(){
			return true;
		}

		/**
		 * Test whether this field must be unique in a section, that is, only one of
		 * this field's type is allowed per section. This default implementation
		 * always returns false.
		 *
		 * @return boolean
		 *	true if the content of this field must be unique, false otherwise.
		 */
		public function mustBeUnique(){
			return true;
		}

		/**
		 * Construct the html block to display a summary of this field. Any error messages
		 * generated are appended to the optional input error array. This function calls
		 * buildLocationSelect once it is completed
		 *
		 * @see buildLocationSelect()
		 * @param array $errors (optional)
		 *	an array to append html formatted error messages to. this defaults to null.
		 * @return XMLElement
		 *	the root xml element of the html display of this.
		 */
		public function buildSummaryBlock($errors = null){
			$div = new XMLElement('div');
			$div->setAttribute('class', 'group');

			$label = Widget::Label(__('Label'));
			$label->appendChild(Widget::Input('fields['.$this->get('sortorder').'][label]', $this->get('label')));
			if(isset($errors['label'])) $div->appendChild(Widget::wrapFormElementWithError($label, $errors['label']));
			else $div->appendChild($label);

			$label->appendChild(new XMLElement('span', __('The field won\'t be displayed.')));

			return $div;
		}

		/**
		 * Process the raw field data.
		 *
		 * @param mixed $data
		 *	post data from the entry form
		 * @param reference $status
		 *	the status code resultant from processing the data.
		 * @param boolean $simulate (optional)
		 *	true if this will tell the CF's to simulate data creation, false
		 *	otherwise. this defaults to false. this is important if clients
		 *	will be deleting or adding data outside of the main entry object
		 *	commit function.
		 * @param mixed $entry_id (optional)
		 *	the current entry. defaults to null.
		 * @return array[string]mixed
		 *	the processed field data.
		 */
		public function processRawFieldData($data, &$status, $simulate=false, $entry_id=null) {

			$status = self::__OK__;

			return array(
				'value' => self::$revalidate
			);
		}

		/**
		 * Display the default data-source filter panel.
		 *
		 * @param XMLElement $wrapper
		 *	the input XMLElement to which the display of this will be appended.
		 * @param mixed $data (optional)
		 *	the input data. this defaults to null.
		 * @param mixed errors (optional)
		 *	the input error collection. this defaults to null.
		 * @param string $fieldNamePrefix
		 *	the prefix to apply to the display of this.
		 * @param string $fieldNameSuffix
		 *	the suffix to apply to the display of this.
		 */
		public function displayDatasourceFilterPanel(XMLElement &$wrapper, $data = null, $errors = null, $fieldnamePrefix = null, $fieldnamePostfix = null){
			parent::displayDatasourceFilterPanel(&$wrapper, $data, $errors, $fieldnamePrefix, $fieldnamePostfix);

			$wrapper->appendChild(new XMLElement('span', __('$param url[xpath/expression]')));
		}

		/**
		 * Construct the SQL statement fragments to use to retrieve the data of this
		 * field when utilized as a data source.
		 *
		 * @param array $data
		 *	the supplied form data to use to construct the query from??
		 * @param string $joins
		 *	the join sql statement fragment to append the additional join sql to.
		 * @param string $where
		 *	the where condition sql statement fragment to which the additional
		 *	where conditions will be appended.
		 * @param boolean $andOperation (optional)
		 *	true if the values of the input data should be appended as part of
		 *	the where condition. this defaults to false.
		 * @return boolean
		 *	true if the construction of the sql was successful, false otherwise.
		 */
		public function buildDSRetrivalSQL($data, &$joins, &$where, $andOperation = false)
		{
			list($shorten, $url) = array_map('trim', explode(' ', $data[0]));
			if (!$shorten || !$url) return true;

			$entry_id = self::decode($shorten);
			// if the expression have already been compiled

			$query = 'select value from tbl_entries_data_'. $this->get('id').
						' where entry_id = '. $entry_id;

			$data  = Symphony::Database()->fetchVar(
				'value', 0, $query
			);

			if ($data && $data !== self::$revalidate) redirect($data);

			$where .= ' AND e.id = '. $entry_id;

			$this->shorten = $shorten;
			$this->url = $url;
			return true;
		}

		/**
		 * Append the formatted xml output of this field as utilized as a data source.
		 *
		 * @param XMLElement $wrapper
		 *	the xml element to append the xml representation of this to.
		 * @param array $data
		 *	the current set of values for this field. the values are structured as
		 *	for displayPublishPanel.
		 * @param boolean $encode (optional)
		 *	flag as to whether this should be html encoded prior to output. this
		 *	defaults to false.
		 * @param string $mode
		 *	 A field can provide ways to output this field's data. For instance a mode
		 *  could be 'items' or 'full' and then the function would display the data
		 *  in a different way depending on what was selected in the datasource
		 *  included elements.
		 * @param number $entry_id (optional)
		 *	the identifier of this field entry instance. defaults to null.
		 */
		public function appendFormattedElement(XMLElement &$wrapper, $data, $encode = false, $mode = null, $entry_id = null) {
			if ($this->shorten && $data['value'] && $data['value'] !== self::$revalidate)
				redirect($data['value']);

			$url  = $this->compile($entry_id);
			if ($url) redirect($url);

			$data = self::encode($entry_id);
			parent::appendFormattedElement($wrapper, $data, $encode);
		}

		/**
		 * The default method for constructing the example form markup containing this
		 * field when utilized as part of an event. This displays in the event documentation
		 * and serves as a basic guide for how markup should be constructed on the
		 * Frontend to save this field
		 *
		 * @return XMLElement
		 *	a label widget containing the formatted field element name of this.
		 */
		public function getExampleFormMarkup(){
			return null;
		}

		/**
		 * The default field table construction method. This constructs the bare
		 * minimum set of columns for a valid field table. Subclasses are expected
		 * to overload this method to create a table structure that contains
		 * additional columns to store the specific data created by the field.
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
				) ENGINE=MyISAM;"
			);
		}
		

		/*
		 * -----------------------------------------------------------------
		 * Stolen from: http://snipplr.com/view/22246/base62-encode--decode/
		 *
		 */
		public static function encode($val, $base=62, $chars='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
			// can't handle numbers larger than 2^31-1 = 2147483647
			$str = '';
			do {
				$i = $val % $base;
				$str = $chars[$i] . $str;
				$val = ($val - $i) / $base;
			} while($val > 0);
			return $str;
		}

		public static function decode($str, $base=62, $chars='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
			$len = strlen($str);
			$val = 0;
			$arr = array_flip(str_split($chars));
			for($i = 0; $i < $len; ++$i) {
				$val += $arr[$str[$i]] * pow($base, $len-$i-1);
			}
			return $val;
		}

		/*
		 * -----------------------------------------------------------------
		 */


		public function compile($entry_id)
		{
			require_once EXTENSIONS. '/shorten/lib/data.shorten.php';

			if (!$this->shorten || !$this->url) return null;

			$section_id = $this->get('parent_section');
			$ds = new datasource_Shorten(Frontend::Instance());

			$fields = Symphony::Database()->fetch(
				sprintf(
					"SELECT element_name FROM `tbl_fields` WHERE `parent_section` = %d",
					$section_id
				)
			);

			foreach($fields as $field)
				$ds->dsParamINCLUDEDELEMENTS[] = $field['element_name'];

			$ds->dsParamLIMIT = 1;
			$ds->dsParamSTARTPAGE = '1';
			$ds->dsParamROOTELEMENT = 'aaa';
			$ds->dsParamSORT = 'system:id';
			$ds->dsParamASSOCIATEDENTRYCOUNTS = 'no';
			$ds->_param_output_only = false;

			$ds->dsParamFILTERS = array(
				'id' => $entry_id
			);
			$ds->setSource($section_id);

			$xml = $ds->grab()->generate();
			$doc = new DomDocument;
			$doc->preserveWhiteSpace = false;

			$doc->loadXML($xml);

			$xpath  = new DOMXPath($doc);
			preg_match_all('/\[(.*?)\]/', $this->url, $matches);

			$full = $this->url;
			$search  = $matches[0];
			$replace = $matches[1];
			foreach ($search as $i => $str)
			{
				$query  = "//entry/". trim($replace[$i], '/');
				$result = $xpath->query($query);

				$new = array();
				foreach ($result as $r) $new[] = $r->nodeValue;

				$full = str_replace($str, join('', $new), $full);
			}

			
			$this->update($entry_id, $full);
			return $full;
		}

		public function update($entry_id, $value = null)
		{
			if (!$value) $value = self::$revalidate;
			$this->entryDataCleanup($entry_id);

			$table = 'tbl_entries_data_'. $this->get('id');
			$data  = array(
				'value' => $value,
				'entry_id' => $entry_id
			);

			Symphony::Database()->insert($data, $table);
		}
	}
