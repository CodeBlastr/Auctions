<?php if (!empty($auction['Auction']['price'])) : ?>
	<h3>Reverse Auction</h3>
	<p>This auction goes down in price by <?php echo ZuhaInflector::pricify($auction['Auction']['increment'], array('currency' => 'USD')); ?> every <?php echo $auction['Auction']['interval']; ?> seconds, until someone purchases it, or it hits the minimum price of <?php echo ZuhaInflector::pricify($auction['Auction']['floor'], array('currency' => 'USD')); ?>.</p>
	<hr />
	<div class="row">
		<div class="span6">
			<?php echo $auction['Auction']['_displayPrice']; ?>
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