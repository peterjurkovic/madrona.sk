<?php 

/**
*	The Shopping cart clas
*	Depands : session_start();
*
*	@version 1
*/



class Cart {
	
	private $totalPrice	= 0;
	private $totalQuantity	= 0;
	
	/**
	* Add product to the shopping cart 
	*
	* @param DPH
	* @throws InvalidArgumentException if dph is not a number, or if is dhp smaller than 1
	*/
	
	public function __construct(){
		if(!isset($_SESSION["cart"])){
				$_SESSION["cart"] = array();
		}
	}
	
	
	/**
	* Add product to the shopping cart 
	*
	* @param Product ID
	* @param Product quantity
	* @throws InvalidArgumentException if params are invalid.
	*/
	
	public function addProduct( $pID , $vID = 0, $quantity = 1 ){
		
		if(
			($quantity < 0 || $pID < 0)
		){
			throw new InvalidArgumentException("Tovar sa nepodarilo vložiť do košíka.");
		}
		
		
		if(array_key_exists($pID."-".$vID, $_SESSION["cart"]) ){
			$d = explode("-", $_SESSION["cart"][$pID."-".$vID]);
			$_SESSION["cart"][$pID."-".$vID] = ($d[0] + $quantity)."-".$d[1];
		}else{
			$_SESSION["cart"] = array_merge($_SESSION["cart"] , array ($pID."-".$vID =>  $quantity."-".$this->getPrice($pID, $vID) ));
		}
	}
	
	
	/**
	* Remove product from the shopping cart 
	*
	* @param Product ID
	* @return true if was product succesfuly remove, false otherwise
	*/
	
	public function removeProduct( $pID ){
		if(isset($_SESSION["cart"][$pID])){
			unset($_SESSION["cart"][$pID]);
			$this->calculatePrice();
			return true;
		}
		return false;
	}
	
	
	/**
	* Remove all products from the shopping cart 
	*/
	
	public function removeAllProduct( ){
		$_SESSION["cart"] = array();
	}
	
	
	
	
	/**
	* Select price
	*
	* If is set varian and isnt zero, return vID price. Otherwise return product price.
	* If is product price in status ID 3 (Akcia) or ID 4 (Vypredaj) return sale price, otherwise normal price.
	*	
	* @param Product ID
	* @return Price of product
	*/
	private function getPrice($pID, $vID){
		if($vID == 0 || $vID < 50){
			return $this->getProductPrice($pID);
		}
		
		$vIDPrice = $this->getVariantPrice($pID, $vID);
		
		if($vIDPrice == 0){
			return $this->getProductPrice($pID);
		}
		
		return $vIDPrice;
	}
	
	
	/**
	* Select price of given vID, 
	*
	* @param Product ID
	* @param vID ID
	* @return Price of vID
	* @throws InvalidArgumentException if not found
	*/
	private function getVariantPrice($pID, $vID){
		global $conn;
		$data = $conn->select("SELECT `price` FROM `shop_variant` WHERE `id_shop_variant`=? AND `active`=1 LIMIT 1", array( $vID ));
		
		if(!$data){
			throw new InvalidArgumentException("Tovar so zovlenou variantou nebol nájdený.");
		}
		
		return $data[0]['price'];
	}
	
	
	/**
	* Select price of product by ID. If product status is set "akcia" or "vypreda" nad price sale is not zero, return price in sale, 
	* otherwise return nromal price of product.
	*
	* @param Product ID
	* @return Price of product (Sale or normal)
	* @throws InvalidArgumentException if not found
	*/
	private function getProductPrice($pID){
		global $conn;
		$data = $conn->select("SELECT `price`, `price_sale`, `id_shop_product_status` FROM `shop_product` WHERE `id_shop_product`=? AND `active`=1 LIMIT 1", array( $pID ));
		
		
		if(!$data){
			throw new InvalidArgumentException("Tovar nebolo mžné vložiť do košíka, pretože neexistuje.");
		}
		
		if($data[0]['id_shop_product_status'] == 2 || $data[0]['id_shop_product_status'] == 3 && $_GET['price_sale'] != 0){
			return $data[0]['price_sale'];
		}else{
			return $data[0]['price'];
		}
	}
	
	/**
	* Calculeate sum of products
	*/
	public function calculate(){

		if(!is_array($_SESSION["cart"]) || count($_SESSION["cart"]) == 0 ) {
			return;
		}
		foreach ($_SESSION["cart"] as $item){
			$d = explode("-", $item);
			$this->totalPrice += $d[0] * $d[1];
			$this->totalQuantity += $d[0];
		}
	}
	
	/**
	* Update quantity of product
	*/
	public function updateQuantity($id, $q){
		
		if(!is_numeric($q) || !array_key_exists($id, $_SESSION["cart"]) || $q <= 0 ){
			throw new InvalidArgumentException("Vyskytla sa chyba, položku sa nepodarilo aktualizovať.");
		}

		$d = explode("-", $_SESSION["cart"][$id]);
		$_SESSION["cart"][$id] = $q."-".$d[1];
	}
	
	/**
	* Update quantity of product
	*/
	public function deleteItem($id){
		
		if(!array_key_exists($id, $_SESSION["cart"])){
			throw new InvalidArgumentException("Vyskytla sa chyba, položku sa nepodarilo odstrániť.");
		}
		
		unset($_SESSION["cart"][$id]);
	}
	
	/**
	* @return full price of products
	*/
	public function getTotalPrice(){
		return number_format($this->totalPrice, 2);
	}
	
	
	/**
	* Calculate products count, added in shopping cart
	*
	* @return quantity of product in shopping cart 
	*/
	public function getTotalQuantity(){
		return $this->totalQuantity;
	}

}

?>