<?php
/**
 * Imageprom
 * @package    nx
 * @subpackage nx_market
 * @copyright  2014 Imageprom
 */

namespace NXMarket;

class COrderElement {
	private $id = 0;
	private $price = 0;
	public  $count = 0;
	public  $name = 'untitled';
	public  $note = false;

    /**
     * COrderElement constructor.
     * @param $ID
     * @param int $Price
     * @param int $Count
     * @param string $Name
     * @param string $Note
     */

    function __construct ($ID, $Price = 0, $Count = 0, $Name = 'untitled', $Note = '') {
		if ($ID) {
			$this->id = $ID;
			if ($Price > 0) $this->price = $Price; else $this->price = 0;	
			if ($Count > 0) $this->count = $Count; else $this->count = 0;
			if (strlen($Name)>0) $this->name = $Name; else $this->name= 'untitled';
			if ($Note) $this->note = $Note; else $this->note = '';
		}
	}

    /**
     * @return int
     */

    function GetPrice() {return $this->price;}

    /**
     * @return float|int
     */

    function GetSum()   {return ($this->price*$this->count);}

    /**
     * @return int
     */

    function GetId()    {return $this->id;}

    /**
     * @return array
     */

    function GetArray() {
		return array(
			'ID' => $this->GetId(), 
			'PRICE'=> $this->GetPrice(), 
			'COUNT'=> $this->count, 
			'NAME'=> $this->name, 
			'NOTE'=> $this->note
		);
	}
}

interface IOrder {
    /**
     * @param $id
     * @param $count
     * @param $price
     * @param $note
     * @param $name
     * @return mixed
     */
    public function Add($id, $count, $price, $note, $name);

    /**
     * @param $id
     * @param $count
     * @param $price
     * @param $note
     * @param $name
     * @return mixed
     */
    public function Replace($id, $count, $price, $note, $name);

    /**
     * @param $id
     * @param $count
     * @param $price
     * @param $note
     * @param $name
     * @return mixed
     */
    public function Change($id, $count, $price, $note, $name);

    /**
     * @param $id
     * @return mixed
     */
    public function Delete($id);

    /**
     * @param $id
     * @return mixed
     */
    public function GetById($id);

    /**
     * @param $id
     * @return mixed
     */
    public function InBasket($id);

    /**
     * @param $ids
     * @return mixed
     */
    public function SomeInBasket($ids);

    /**
     * @return mixed
     */
    public function GetSum();

    /**
     * @return mixed
     */
    public function GetCount();

    /**
     * @return mixed
     */
    public function GetListing();

    /**
     * @return mixed
     */
    public function Clear();

    /**
     * @return mixed
     */
    public function IsEmpty();
}
 
class COrder implements IOrder {
	
	private $OrderItems = array();


    /**
     * COrder constructor.
     * @param bool|array $Order
     */

    function __construct ($Order = false) {
		if(is_array($Order)) {
			$this->OrderItems=array();
			foreach ($Order as $Item) {
				if ($Item['ID']=intval($Item['ID'])) {
					$this->OrderItems[$Item['ID']] = new COrderElement($Item['ID'], $Item['PRICE'], $Item['COUNT'], $Item['NAME'], $Item['NOTE']);
				}
			}
			return true;
		}
		if(!$Order) return true;
		return false;  
	}

    /**
     * @param bool $id
     * @param int $count
     * @param int $price
     * @param bool $note
     * @param bool $name
     * @return bool|mixed
     */

    public function Add($id = false, $count=1, $price = 0, $note = false, $name = false) {
		if(is_int($id) && $count) { 
			if (array_key_exists($id, $this->OrderItems)){
				$this->OrderItems[$id]->count += intval($count);
				if($this->OrderItems[$id]->note != $note && $note) $this->OrderItems[$id]->note = $note;
				if($this->OrderItems[$id]->name != $note && $name) $this->OrderItems[$id]->name = $name;
			}
			else {
				$this->OrderItems[$id] = new COrderElement($id, floatval($price), floatval($count), $name, $note);
			}
			return true;
		}
		else return false;
	}

    /**
     * @param $id
     * @return bool|mixed
     */

    public function Delete($id) {
		unset ($this->OrderItems[intval($id)]);
		return true;
	}

    /**
     * @param bool $id
     * @param int $count
     * @param int $price
     * @param bool $note
     * @param bool $name
     * @return bool|mixed
     */

    public function Replace($id = false, $count = 1, $price = 0, $note = false, $name = false) {
		if($id=intval($id)) { 
			$this->Delete($id);
			return $this->Add($id, $count, $price, $note, $name);
		}  
		else return false;
	}

    /**
     * @param bool $id
     * @param bool $count
     * @param bool $price
     * @param bool $note
     * @param bool $name
     * @return bool|mixed
     */

    public function Change($id = false, $count = false, $price = false, $note = false, $name = false) {
		$id = intval($id);
		if($this->InBasket($id)) { 
		    if($count = intval($count))   $this->OrderItems[$id]->count = $count;
			if($price = floatval($count)) $this->OrderItems[$id]->count = $price;
			if($note) $this->OrderItems[$id]->$note = $note;
			if($name) $this->OrderItems[$id]->$name = $name;	
			return true;
		}  
		else return false;
	}

    /**
     * @param $id
     * @return bool|mixed
     */

    public function GetById($id) {
		if($res = $this->OrderItems[intval($id)])
			 return $res->GetArray();
		else return false;
	}

    /**
     * @param $id
     * @return bool|mixed
     */

    public function InBasket($id){
		if($res = $this->OrderItems[intval($id)])
			 return true;
		else return false;
	}

    /**
     * @param $ids
     * @return array|bool|mixed
     */

    public function SomeInBasket($ids){
		if(is_array($ids)){
			$result = array();
			foreach($ids as $id) {
				if($res = $this->OrderItems[intval($id)]) $result[] = $id;
			}
			if(count($result) > 0) return $result;
            else return false;			
		}
		else return false; 
	}

    /**
     * @return int|mixed
     */

    public function GetSum() {
		$result = 0; 
		foreach ($this->OrderItems as $Item) { 
			$result += $Item->GetSum();
		} 
		return $result;
	}

    /**
     * @return int|mixed
     */

    public function GetCount(){
		return count($this->OrderItems);
	}

    /**
     * @return array|mixed
     */

    public function GetListing(){
		$result = array();
		foreach ($this->OrderItems as $Item) {	
			$result[$Item->GetId()] = $Item->GetArray();}
		return $result;
	}

    /**
     * @return mixed|void
     */

    public function Clear(){
		$this->OrderItems = array();
	}


    /**
     * @return bool
     */

    public function IsEmpty(){
		if (count($this->OrderItems) > 0) return false; 
		return true;
	}
}