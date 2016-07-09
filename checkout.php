<?php

// Turn on error display
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include required classes
require_once('classes/Inventory.php');
require_once('classes/Cart.php');
require_once('classes/Product.php');

// Make inventory
$inventory = new Inventory();
$cart = new Cart($inventory, 'cart');
$wishlist = new Cart ($inventory, 'wishlist');

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
					<div id="cart_preview">
			
						<?php 
						
							if (array_sum($cart->items_in_cart) == 1) {						
								echo '<p><span class="preview_total">'.array_sum($cart->items_in_cart).'</span> item in cart | <span class="preview_total">$'.round($cart->getSubTotal(),2).'</span></p>';
							} else {
								echo '<p>'.array_sum($cart->items_in_cart).' items in cart | <span class="preview_total">$'.round($cart->getSubTotal(),2).'</span></p>';
							}
							
							echo '<p><a class="shop_check" href="index.php">Continue Shopping &#62;</a></p>';
												
						?>
						
					</div>
			</div>
		</div>
		
		<div class="container">
			<div class="width">
				<div class="main_list cart">
					<h2>Your Cart</h2>
					
					<form action="checkout.php" method="POST">
						<input type="hidden" name="target" value="cart" />
						<ul class="cart_items">
				 
							<?php 
			
							if ($cart->items_in_cart) {
								$errors = $cart->errors;
								foreach ($cart->items_in_cart as $key => $value) {
									$product = $inventory->getItem($key);
									echo '<li>
										  <div class="item">
										    <table>
										      <tr>
										        <td class="product_name">'.$product->name.'</td>
										        <td><a href="checkout.php?action=remove&target=cart&id='.$product->id.'" title="Remove">Remove</a> <span class="divider">|</span> <a href="checkout.php?action=move&source=cart&target=wishlist&id='.$product->id.'" title="Move to Wish List">Move to Wish List</a>
										        <td style="text-align:center;"><input name="item'.$key.'" type="text" value="'.$value.'" maxlength="2" size="3" /> @ $'.round($product->finalPrice(), 2).' each</td>
										        <td style="text-align:center;">$'.round(($value*$product->finalPrice()),2).'</td>
										      </tr>
										      <tr>
										        <td colspan="2"></td>
										        <td style="text-align:center;"><button type="submit">Update</button></td>
										        <td></td>
										      </tr>
										    </table>
										   </div>      
										   </li>';
									if (isset($errors['item'.$key])) {
									echo '<p>'.$errors['item'.$key].'</p>';
									}
								}
							} else {
								echo '<div class="empty_cart">
										<p>Your cart is empty. Go shopping!</p>
									  </div>';
							}
			
							?>
						</ul>
					</form>
					
					<?php 
						
						if ($cart->items_in_cart) {
							
							echo '<div class="cart_totals">
									
									<p>*Please verify the contents of your cart before placing order.*</p>
									
									<table>
										<tr>
											<th>Subtotal</th>
											<td>$'.number_format(round($cart->getSubTotal(),2),2).'</td>
										</tr>
										<tr>
											<th>Tax (@ 8%)</th>
											<td>$'.number_format(round($cart->getSalesTax(),2),2).'</td>
										</tr>
										<tr>
											<th>Total</th>
											<td>$'.number_format(round($cart->getTotal(),2),2).'</td>
										</tr>
										<tr>
											<td class="confirm_order" colspan="2"><a href="#">Place Order</a></td>
										</tr>
									</table>
								  </div>';
							
						}
						
					?>
					
				</div>	
				
				<div class="main_list wishlist">
					
					<h2>Wish List</h2>
					<p class="wish_intro">Something you want but not ready to buy? Not in stock? Add it here!</p>
					<ul id="wishlist_items">
						
						<?php 
						
							if ($wishlist->items_in_cart) {
								$errors = $wishlist->errors;
								foreach ($wishlist->items_in_cart as $key => $value) {
									$product = $inventory->getItem($key);
									echo '<li>
											  <div class="item">
											    <table>
											      <tr>
												  	<td class="product_name">'.$product->name.'</td>
												  	<td><a href="checkout.php?action=remove&target=wishlist&id='.$product->id.'" title="Remove">Remove</a> <span class="divider">|</span> <a href="checkout.php?action=move&source=wishlist&target=cart&id='.$product->id.'" title="Move to Cart">Move to Cart</a></td>
												  	<td style="text-align:right;">$'.round(($value*$product->finalPrice()),2).'</td>
												  </tr>
												</table>
											  </div>
										  </li>'; 
									if (isset($errors['item'.$key])) {
										echo '<p>'.$errors['item'.$key].'</p>';
									}
								}
							} else {
								echo '<div class="empty_cart">
										<p>Your wishlist is empty.</p>
									  </div>';
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
		</footer?
	</body>
</html>

