<?php

class Cart {

	private $errors = array();
	private $cartname;
	private $valid_actions = array('add', 'remove', 'move');
	private $inventory;
	private $items_in_cart = array();
	const TAX_RATE = .0635;

	public function __construct($inventory, $cartname = 'cart') {
		$this->cartname = $cartname;
		$this->inventory = $inventory;
		$this->getCurrentCart();
		$this->checkChanges();
		$this->saveCart();
	}

	public function __get($prop) {
		if (property_exists(__CLASS__, $prop))
			return $this->$prop;
		else
			return null;
	}

	// Add items to our local cart
	public function addToCart($id, $quantity) {
		$this->items_in_cart[$id] = $quantity;
	}

	// Remove items from our local cart
	public function removeFromCart($id) {
		unset($this->items_in_cart[$id]);
	}

	// Save our current local cart items to the cookie
	private function saveCart() {
		setCookie($this->cartname, json_encode($this->items_in_cart), $_SERVER['REQUEST_TIME']+2592000, "/", "sub-terrain.com");
	}

	// Get our current cart items from the cookie and save locally
	private function getCurrentCart() {
		if (isset($_COOKIE[$this->cartname])) {
			$cart = $_COOKIE[$this->cartname];
			$decoded = json_decode($cart);
			if ($decoded) {
				foreach ($decoded as $key => $value) {
					$this->items_in_cart[$key] = $value;
				}
			}
		}
	}

	// Print out our entire cart
	public function __toString() {
		$str = '';
		if ($this->items_in_cart) {
			foreach ($this->items_in_cart as $key => $value) {
				$product = $this->inventory->getItem($key);
				$str .= '<li>'.$product->name.'</li>';
			}
		}
		return $str;
	}

	// Return an item from the cart
	public function itemInCart($id) {
		if (isset($this->items_in_cart[$id]))
			return $this->items_in_cart[$id];
		else
			return null;
	}

	public function getSubTotal() {
		$subtotal = 0;
		foreach ($this->items_in_cart as $key => $value) {
			$product = $this->inventory->getItem($key);
			$subtotal += $value*$product->finalPrice();
		}
		return $subtotal;
	}

	public function getSalesTax() {
		return $this->getSubTotal()*self::TAX_RATE;
	}

	public function getTotal() {
		return $this->getSubTotal()*(1+self::TAX_RATE);
	}

	// Check for users actions sent through GET
	private function checkChanges() {

		// Process possible quantity changes
		if (isset($_POST) && $_POST) { // post is set and has items

			// Check if this post is for this instance of Cart
			if (isset($_POST['target']) && $_POST['target'] == $this->cartname) {

				// Loop through all products POSTed
				$errors = array();
				foreach ($_POST as $key => $value) {

					// Use substr to remove 'item' text from product id
					$id = substr($key, 4);

					// Check if product id exists
					$product = $this->inventory->getItem($id);
					if ($product) {

						// Check if quantity passed is an integer
						if (!preg_match('/^[0-9]+$/', $value)) {
							
							// Set error message
							$errors[$key] = 'Please enter an integer';
						
						// Check if the quantity is 0
						} elseif (!$value) {

							// Remove product from items_in_cart
							$this->removeFromCart($id);

						// Check if quantity exceeds number in stock
						} elseif ($value > $product->quantity_in_stock) {

							// Update quant to max in quantity and return message
							$this->addToCart($id, $product->quantity_in_stock);
							$errors[$key] = 'We\'ve updated the quantity to the maximum we have in stock';

						// Update items_in_cart with new quant
						} else {

							// Update quant to max in quantity
							$this->addToCart($id, $value);
						
						}
						
					} else {

						// Remove bad item from cart
						$this->removeFromCart($id);

					}

				}

				// Store possible errors
				$this->errors = $errors;
		
			}

		// Process possible add/remove action
		} elseif (isset($_GET['action']) && in_array($_GET['action'], $this->valid_actions) && isset($_GET['id'])) {

			// Store some vars
			$source = isset($_GET['source']) ? $_GET['source'] : null;
			$target = isset($_GET['target']) ? $_GET['target'] : null;

			// Validate vars
			if ($target && ($source || $_GET['action'] != 'move') && $source != $target) { // We always have to have a target, we need a source if this is a move, and the source and target should never be the same

				// Check if this action is for this instance of Cart
				if ($source == $this->cartname || $target == $this->cartname) { // This action is for this cart

					// Get the product that was sent
					$product = $this->inventory->getItem($_GET['id']);
					
					// Check if the id of this product exists
					if ($product != null) { // Product exists

						if ($_GET['action'] == 'add') { // This is an add

							// Check if item is in stock
							if ($product->quantity_in_stock > 0) {

								// Check if the item is already in the cart
								if (!$this->itemInCart($_GET['id'])) { // Not in cart
								
									// Perform cart action
									$this->addToCart($product->id, 1);
							
								}

							}

						} elseif ($_GET['action'] == 'remove') { // This is a remove

							// Perform cart action
							$this->removeFromCart($_GET['id']);

						} elseif ($source == $this->cartname) { // This is a move and the source is this cart

							// Remove product from items_in_cart
							$this->removeFromCart($_GET['id']);

						} else { // This is a move and the target is this cart

							// Check if item is in stock
							if ($product->quantity_in_stock > 0) {

								// Check if the item is already in the cart
								if (!$this->itemInCart($_GET['id'])) { // Not in cart

									// Add product to items_in_cart
									$this->addToCart($_GET['id'], 1);

								}

							}

						}

					}

				}
			
			}	
	
		}

	}



}

?>