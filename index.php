<?php

// Turn on error display
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include required classes
require_once('classes/Inventory.php');
require_once('classes/Cart.php');
require_once('classes/Product.php');

// Load json inventory from server
$inventory = new Inventory();
$cart = new Cart($inventory, 'cart');
$wishlist = new Cart ($inventory, 'wishlist');
$product = new Product ($inventory, 'product');

?>

<html>
	<head>
		<title>::GrabStore::</title>
		
		<!-- font-family: 'Lobster', cursive; -->
		<link href='https://fonts.googleapis.com/css?family=Lobster' rel='stylesheet' type='text/css'>
		<!-- font-family: 'Source Sans Pro', sans-serif; -->
	    <link href='https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,700,600,300' rel='stylesheet' type='text/css'>
		
		<link rel='stylesheet' href='css/cart_styles.css' type="text/css" />
		
	</head>
	<body>
		
		<div id="topBar">
			<div class="width">
				<h1><a href="index.php">GrabStore</a></h1>
				
				<div class="sale_orb">
					<div class="sale_container">
						<p>We're having a<br /><span class="big_ad">SALE</span><br />Shop Now!</p>
					</div>
				</div>
				
				<div id="cart_preview">
		
					<?php 
											
					if (array_sum($cart->items_in_cart) == 1) {						
						echo '<p><span class="preview_total">'.array_sum($cart->items_in_cart).'</span> Item in Cart | <span class="preview_total">$'.round($cart->getSubTotal(),2).'</span></p>';
					} else {
						echo '<p>'.array_sum($cart->items_in_cart).' Items in Cart | <span class="preview_total">$'.round($cart->getSubTotal(),2).'</span></p>';
					}
					
					echo '<p><a class="shop_check" href="checkout.php">Go to Checkout &#62;</a></p>';
											
					?>
					
				</div>
			</div>
		</div>
		
		
		<div class="container">
			<div class="width">
				<div class="main_list">
					<ul id="inventory_list">
			
						<?php 
			
							// Display inventory items
							$items = $inventory->getItems();
							foreach ($items as $key => $value) {
								
								$quant_in_cart = $cart->itemInCart($value->id);
								$is_in_wishlist = $wishlist->itemInCart($value->id) ? true : false;
								
								echo '<li>
										<div class="item">
											<div class="item_header">
												<h3>'.$value->name.'</h3>';
												if ($value->is_on_sale) {
													echo '<p><span class="strike">$'.number_format(round($value->price, 2),2).'</span>&nbsp; &nbsp; &nbsp; &nbsp;<span class="sale_price">On sale! $'.number_format(round($value->finalPrice(), 2),2).'</span></p>';
												} else {
													echo '<p class="price">$'.number_format(round($value->finalPrice(), 2),2).'</p>';
												}
											echo '</div>
											<div class="item_body">
												<p>'.$value->description.'</p>';
												
												if(!$value->quantity_in_stock) {
													echo '<p class="no_stock">Out of stock</p>';
												} else {
													echo '<p>'.$value->quantity_in_stock.' available in stock</p>';
												}
											echo '</div>
											
											<div class="item_footer">	
												
												<p><a class="addButton"'.($quant_in_cart ? 'href="checkout.php" title="Edit Cart">'.$quant_in_cart.' in Cart</a>' : 'href="checkout.php?action=add&id='.$value->id.'&target=cart" title="Add to Cart">Add to Cart</a>').'</p>
												
												<p><a class="wishButton"'.($is_in_wishlist ? 'href="checkout.php?action=remove&id='.$value->id.'&target=wishlist" title="Remove from Wish List">Remove from WishList</a>' : 'href="checkout.php?action=add&id='.$value->id.'&target=wishlist" title="Add to Wish List">Add to Wish List</a>').'</p>
											</div>
										</div>	
									</li>';
										
							}
									
						?>
					</ul>
				</div>
			</div>
		</div>
		
		
		<footer>
			
			<div class="footer_container">
				
				<h1><a href="index.php">GrabStore</a></h1>
				
				<p class="copyright">Copyright &copy;2016 GrabStore</p>
				
			</div>
			
		</footer>
		
	</body>
</html>
