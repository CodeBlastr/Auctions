<div class="auction add form clearfix">
	<?php echo $this->Form->create('Auction', array('type' => 'file')); ?>
	<?php echo $this->Form->hidden('Auction.seller_id', array('value' => $this->Session->read('Auth.User.id'))); ?>
	
	<div class="span4 col-md-4"> 
		<fieldset>
			<?php echo $this->Form->input('Auction.name', array('label' => 'Auction Name')); ?>
			<?php echo $this->Form->input('Auction.type', array('label' => 'Auction Type', 'type' => 'select')); ?>
			<?php echo $this->Form->input('Auction.price', array('label' => 'Buy it Now Price', 'type' => 'number', 'step' => '0.01', 'min' => '0', 'max' => '99999999999')); ?>
			<?php echo $this->Form->input('Auction.start', array('label' => 'Starting bid', 'default' => 0, 'type' => 'number', 'step' => '0.01', 'min' => '0', 'max' => '99999999999')); ?>
			<?php echo $this->Form->input('Auction.interval', array('label' => 'Reverse Auction Time Incremet (secs)', 'type' => 'number', 'step' => '1', 'min' => '0', 'max' => '99999999999')); ?>
			<?php echo $this->Form->input('Auction.increment', array('label' => 'Reverse Auction Price Increment (0.00)', 'type' => 'number', 'step' => '1', 'min' => '0', 'max' => '99999999999')); ?>
			<?php echo $this->Form->input('Auction.floor', array('label' => 'Lowest Price for a Reverse Auction (0.00)', 'type' => 'number', 'step' => '1', 'min' => '0', 'max' => '99999999999')); ?>
			<?php echo $this->Form->input('Auction.started', array('type' => 'datetimepicker', 'label' => 'Date & Time to Start Auction')); ?>
			<?php echo $this->Form->input('Auction.ended', array('type' => 'datetimepicker', 'label' => 'Date & Time to End Auction')); ?>
			</fieldset>
		<fieldset>
			<legend class="toggleClick"><?php echo __('Optional Details'); ?></legend>
			<?php echo $this->Form->input('Auction.summary', array('type' => 'text', 'label' => 'Promo Text <br /><small><em>Used to entice people to view more about this item.</em></small>')); ?>
			<?php // echo $this->Form->input('Auction.auction_brand_id', array('empty' => '-- Select --', 'label' => 'What is the brand name for this auction? (' . $this->Html->link('add', array('controller' => 'auction_brands', 'action' => 'add')) . ' / ' . $this->Html->link('edit', array('controller' => 'auction_brands', 'action' => 'index')) . ' brands)')); ?>
			<?php echo $this->Form->input('Auction.is_public', array('default' => 1, 'label' => 'Published')); ?>
			<?php echo $this->Form->input('Auction.is_buyable', array('default' => 1,'label' => 'Buyable (uncheck to disable buy it now)')); ?>
		</fieldset>
	</div>
	
	<div class="span8 col-md-8"> 
		<fieldset>
			<legend><?php echo __('Auction details'); ?></legend>
			<?php echo CakePlugin::loaded('Media') ? $this->Element('Media.selector', array('media' => $this->request->data['Media'], 'multiple' => true)) : null; ?>
			<?php echo $this->Form->input('Auction.description', array('type' => 'richtext', 'label' => 'What is the sales copy for this item?')); ?>
			<?php echo $this->Form->submit('Save', array('type' => 'submit')); ?>
		</fieldset>
	</div>

	<!--fieldset>
		<legend class="toggleClick">
		<?php echo __('Does this auction belong to a category?'); ?></legend>
		<?php echo $this->Form->input('Category', array('multiple' => 'checkbox', 'label' => __('Which categories? (%s)', $this->Html->link('edit categories', array('admin' => 1, 'plugin' => 'auctions', 'controller' => 'auctions', 'action' => 'categories'))))); ?>
	</fieldset-->
		
	<?php echo $this->Form->end(); ?>
</div>

<?php
// set the contextual menu items
$this->set('context_menu', array(
	'menus' => array(
		array(
			'heading' => 'Auctions',
			'items' => array(
				$this->Html->link(__('Dashboard'), array('admin' => true, 'controller' => 'auctions', 'action' => 'dashboard')),
				$this->Html->link(__('List'), array('controller' => 'auctions', 'action' => 'index'))
			)
		)
	)
));
