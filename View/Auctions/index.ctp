<?php // used on beefjockey don't edit unless you copy it to that site ?>
<div class="auctions">
	<h1>Auctions</h1>
	<?php if (!empty($auctions)) : ?>
	    <?php foreach ($auctions as $auction) : ?>
	    	<?php $highestBid = (isset($auction['AuctionBid'][0]['amount'])) ? '$'.ZuhaInflector::pricify($auction['AuctionBid'][0]['amount']) : 'no bids'; ?>
			<div class="ad-row media row-fluid">
				<div class="span2 col-md-2">
					<?php echo $this->Html->link($this->Media->display($auction['Media'][0], array('width' => 181, 'height' => 121, 'alt' => $auction['Auction']['name'])), array('plugin' => 'auctions', 'controller' => 'auctions', 'action' => 'view', $auction['Auction']['id']), array('class' => 'pull-left', 'escape' => false)); ?>
				</div>
				<div class="span7 col-md-7">
					<div class="media-body">
						<h4 class="media-heading"><?php echo $this->Html->link($auction['Auction']['name'], array('plugin' => 'auctions', 'controller' => 'auctions', 'action' => 'view', $auction['Auction']['id'])); ?></h4>
						<div class="metas">
							from <?php echo $this->Html->link($auction['Seller']['full_name'], array('plugin'=>'users', 'controller'=>'users', 'action'=>'view', $auction['Seller']['id'])) ?>
							<?php if ($auction['Auction']['is_expired'] == true) : ?>
								<label class="alert alert-danger">Expired</label>
							<?php else : ?>
								<i class="icon-time"></i><b><time datetime="<?php echo date('c', strtotime($auction['Auction']['ended']))?>"><?php echo $this->Time->timeAgoInWords($auction['Auction']['ended']); ?></time> left</b>
							<?php endif; ?>
						</div>
						<?php echo $this->Text->truncate($auction['Auction']['description']) ?>
					</div>
				</div>
				<div class="span3 col-md-3">
					<div class="price-tag">
						<?php echo $auction['Auction']['is_expired'] == true ? null : $this->Html->link($highestBid, array('action' => 'view', $auction['Auction']['id'])); ?>
					</div>
				</div>	
			</div>
		<?php endforeach; ?>
	<?php else : ?>
		<p>No auctions found.</p>
	<?php endif; ?>
	<?php echo $this->Element('paging');?>
</div>

<?php
// set the contextual sorting items
$this->set('forms_sort', array(
    'type' => 'select',
    'sorter' => array(array(
        'heading' => '',
        'items' => array(
            $this->Paginator->sort('price'),
            $this->Paginator->sort('name'),
        )
    ))
));
// set contextual search options
$this->set('forms_search', array(
    'url' => '/products/products/index/auction/', 
	'inputs' => array(
		array(
			'name' => 'contains:name', 
			'options' => array(
				'label' => '', 
				'placeholder' => 'Search Auctions',
				'value' => !empty($this->request->params['named']['contains']) ? substr($this->request->params['named']['contains'], strpos($this->request->params['named']['contains'], ':') + 1) : null,
			)
		)
	)
));
// set the contextual menu items
$this->set('context_menu', array('menus' => array(
    array(
        'heading' => 'Products',
        'items' => array(
            $this->Html->link(__('List'), array('controller' => 'products', 'action' => 'index')),
            $this->Html->link(__('Add'), array('controller' => 'products', 'action' => 'add')),
        )
    )
)));

