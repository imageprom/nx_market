<?php
/**
 * Imageprom
 * @package    nx
 * @subpackage nx_market
 * @copyright  2017 Imageprom
 */

namespace NXMarket;

interface INXApiFormat {

    /**
     * @param $data
     * @return mixed
     */
    public function Format($data);

    /**
     * @param $data
     * @return mixed
     */
    public function Show($data);
}

class CNXJsonView implements INXApiFormat {

	public $Root;
	public $NodeName;

    /**
     * CNXJsonView constructor.
     * @param string $root
     * @param string $nodeName
     */
    public function __construct($root = 'Data', $nodeName = 'Item') {
		if($root) $this->root = $root;
		if($nodeName) $this->nodeName = $nodeName;
	}

    /**
     * @param $data
     * @return bool|false|mixed|string
     */
    public function Format($data) {
		try {
			if (!$data) return false;
			elseif(!is_array($data)) throw new \Exception('Data not array');
			else return json_encode(array($this->root => $data));
		}

		catch (\Exception $e) {
	 		var_dump($e->getMessage());
	    }	
	}

    /**
     * @param $data
     * @return mixed|void
     */
    public function Show($data) {
		if($view = $this->Format($data)) {
			header('Content-Type: application/json');
			echo $view;
		}
	}
}

class CNXmlView implements INXApiFormat {

	public $Root;
	public $NodeName;

    /**
     * CNXmlView constructor.
     * @param string $root
     * @param string $nodeName
     */
    public function __construct($root = 'Data', $nodeName = 'Item') {
		if($root) $this->root = $root;
		if($nodeName) $this->nodeName = $nodeName;
	}

    /**
     * @param $data
     * @return bool|mixed
     */
    public function Format($data) {
		try {
			 
			if (!$data) return false;
			elseif(!is_array($data)) throw new \Exception('Data not array');
			else {
				$xml_data = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><'.$this->Root.'></'.$this->Root.'>');
				$this->NxXmlEncode($data, $xml_data);
				return $xml_data->asXML();
			} 	
		}

		catch (\Exception $e) {
	 		var_dump($e->getMessage());
	    }	
	}

    /**
     * @param $data
     * @return mixed|void
     */
    public function Show($data) {
		if($view = $this->Format($data)) {
			header("Content-Type: text/xml");
			echo $view;
		}
	}

    /**
     * @param $data
     * @param $xml_data
     * @param bool $nodename
     */
    public function NxXmlEncode ($data, &$xml_data, $nodename = false ) {
	    foreach( $data as $key => $value ) {
	        if( is_numeric($key) ){
	        	if($nodename)  $key = $nodename;
	            else $key = $this->NodeName; 
	        }
	        if( is_array($value) ) {
	            $subnode = $xml_data->addChild($key);
	            if($key == 'warehouses') $nodaname = 'item';
	            else $nodaname = false;
	            $this->NxXmlEncode($value, $subnode,  $nodaname);
	        } else {
	            $xml_data->addChild("$key",htmlspecialchars("$value"));
	        }
	     }
	}	
}

class CNXCsvView implements INXApiFormat {

	public $Root;
	public $NodeName;

    /**
     * CNXCsvView constructor.
     * @param string $root
     * @param string $nodeName
     */
    public function __construct($root = 'Data', $nodeName = 'Item') {
		if($root) $this->root = $root;
		if($nodeName) $this->nodeName = $nodeName;
	}

    /**
     * @param $data
     * @return array|bool|mixed|string
     */
    public function Format($data) {
		try {
			 
			if (!$data) return false;
			elseif(!is_array($data)) throw new \Exception('Data not array');
			else {
				foreach ($data[0] as $key => $value) {
					$text[] = $key;
				}

				$text = implode('; ', $text).PHP_EOL;

				foreach ($data as $row) {
					
					$item = array();

					foreach ($row as $value) {
						$item[] = $value;
					}
					
					$text .= implode('; ', $item).PHP_EOL;
				}
				
				return $text;
			} 	
		}

		catch (\Exception $e) {
	 		var_dump($e->getMessage());
	    }	
	}

    /**
     * @param $data
     * @return mixed|void
     */
    public function Show($data) {
		if($view = $this->Format($data)) {
			header('Cache-Control: must-revalidate');
			header('Pragma: must-revalidate');
			header('Content-type: application/vnd.ms-excel');
			header('Content-disposition: attachment; filename=order.csv');
			echo $view;
		}
	}
}