<?php 

/**
* 
*/
class decrease_stock {

	protected $qty;
	public $date;

	function __construct($qty)
	{
		$this->date = date("Y-m-d",mktime(0,0,0,date("m"),date("d"),date("Y")));
		$this->qty = $qty;
	}
}
/**
* 
*/
/**
* 
*/
class decrease_one extends decrease_stock
{

	public function decrease()
	{

		$qty = $this->qty - 1;
		return $qty;

	}
}

 ?>