<table class="table table-hover">
	<tr>
		<td>
			<?php
			echo $this->Form->input("TransactionItem.{$i}.quantity", array(
				    'label' => false,
					'class' => 'TransactionItemCartQty span5',
				    'div' => false,
				    'value' => $transactionItem['quantity'],
				    'min' => $minQty, 'max' => $maxQty,
				    'size' => 1,
				    'type' => 'hidden',
				    'after' => __(' %s')
				));
			echo __('<p>%s %s</p>', 
					$this->Html->link($transactionItem['name'], '/auctions/auctions/view/'.$transactionItem['foreign_key'], null, __('Are you sure you want to leave this page?')), 
					$this->Html->link('<i class="icon-trash"></i>', array('plugin' => 'transactions', 'controller' => 'transaction_items', 'action' => 'delete', $transactionItem['id']), array('title' => 'Remove from cart', 'escape' => false))
					);
			
			$transactionItemCartPrice = $transactionItem['price'] * $transactionItem['quantity'];
			?>
		</td>
		<td>
    		<div class="TransactionItemCartPrice">
		        $<span class="floatPrice"><?php echo number_format($transactionItemCartPrice, 2); ?></span>
		    	<span class="priceOfOne"><?php echo number_format($transactionItem['price'], 2) ?></span>
		    </div>
		</td>
	</tr>
</table>