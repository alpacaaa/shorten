<?php

	class datasource_Shorten extends Datasource
	{
		protected $source;

		public function setSource($source)
		{
			$this->source = $source;
		}

		public function getSource()
		{
			return $this->source;
		}

		public function grab(&$param_pool=NULL){
			$result = new XMLElement($this->dsParamROOTELEMENT);
				
			try{
				include(TOOLKIT . '/data-sources/datasource.section.php');
			}
			catch(Exception $e){
				$result->appendChild(new XMLElement('error', $e->getMessage()));
				return $result;
			}	

			if($this->_force_empty_result) $result = $this->emptyXMLSet();
			return $result;
		}
	}
