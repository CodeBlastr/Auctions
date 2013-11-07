<?php
// set up defaults
$highestBid = (isset($auction['AuctionBid'][0]['amount'])) ? '$'.ZuhaInflector::pricify($auction['AuctionBid'][0]['amount']) : 'no bids';
?>

<div class="row-fluid">
	
	<div class="span6">
		Current bid:
	</div>
	<div class="span6">
		<b><?php echo $highestBid ?></b>
	</td>
</div>
<div class="row-fluid">
	<div class="span6">
		Starting bid:
	</div>
	<div class="span6">
		<b><?php echo ZuhaInflector::pricify( $auction['Auction']['start'], array('currency' => 'USD')); ?></b>
	</td>
</div>
</div>
<div class="row-fluid">
	<div class="span6">
		Your bid:
	</div>
	<div class="span6">
		<?php echo $this->Form->create('AuctionBid', array('url' => array('plugin' => 'auctions', 'controller' => 'auction_bids', 'action' => 'add'))); ?>
		<?php echo $this->Form->hidden('AuctionBid.auction_id', array('value' => $auction['Auction']['id'])); ?>
		<?php echo $this->Form->input('AuctionBid.amount', array('label' => false, 'class' => 'required input-small', 'placeholder' => ZuhaInflector::pricify($auction['AuctionBid'][0]['amount'] + 1))); ?>
		<?php echo $this->Form->submit('Place bid', array('class' => 'btn-primary')); ?>
		<?php echo $this->Form->end(); ?>
	</div>
</div>

<hr />

<?php if (!empty($auction['Auction']['price'])) : ?>
	<div class="row-fluid">
		<div class="span6">
			<?php echo ZuhaInflector::pricify($auction['Auction']['price'], array('currency' => 'USD')); ?>
		</div>
		<div class="span6">
			<?php echo $this->Form->create('TransactionItem', array('url' => array('plugin' => 'transactions', 'controller' => 'transaction_items', 'action' => 'add'), 'class' => 'form-inline')); ?>
			<?php echo $this->Form->hidden('TransactionItem.name', array('value' => $auction['Auction']['name'])); ?>
			<?php echo $this->Form->hidden('TransactionItem.model', array('value' => 'Auction')); ?>
			<?php echo $this->Form->hidden('TransactionItem.foreign_key', array('value' => $auction['Auction']['id'])); ?>
			<?php echo $this->Form->hidden('TransactionItem.price', array('value' => $auction['Auction']['price'])); ?>
			<?php echo $this->Form->hidden('TransactionItem.quantity', array('value' => 1)); ?>
			<?php echo $this->Form->submit('Buy Now', array('class' => 'btn btn-primary btn-mini')); ?>
			<?php $this->Form->end(); ?>
		</div>
	</div>
<?php endif; ?>
