<div class="auction view media row" id="<?php echo __('auction%s', $auction['Auction']['id']); ?>" itemscope itemtype="http://schema.org/Auction">
	<?php if($iAmWinning) : ?>
	<div class="alert alert-success">Congratulations! You Won! <?php echo $this->Html->link('View Your Auctions', array('plugin' => 'users', 'controller' => 'users', 'action' => 'my')); ?></div>
	<?php endif; ?>
	<div class="alert alert-danger">Auction Expired - <?php echo $this->Html->link('View Current Auctions', array('action' => 'index')); ?></div>
	
	<h2 class="media-heading" itemprop="name"><?php echo $auction['Auction']['name']; ?></h2>
	<div class="item auction gallery pull-left media-object">
        <?php echo $this->Media->display($auction['Media'][0], array('width' => 181, 'height' => 121, 'alt' => $auction['Auction']['name'])); ?>
    </div>

    <div class="item auction description span5 col-md-5 pull-left media-body">
        <div class="itemSummary auctionSummary">
            <span itemprop="description"><?php echo $auction['Auction']['summary']; ?></span>
        </div>
        <?php echo $auction['Auction']['description']; ?>
    </div>
</div>

<?php
// set contextual search options
$this->set('forms_search', array(
    'url' => '/auctions/auctions/index/', 
	'inputs' => array(
		array(
			'name' => 'contains:name', 
			'options' => array(
				'label' => '', 
				'placeholder' => 'Auctions Search',
				'value' => !empty($this->request->params['named']['contains']) ? substr($this->request->params['named']['contains'], strpos($this->request->params['named']['contains'], ':') + 1) : null,
				)
			),
		)
	));
// set the contextual menu items
$this->set('context_menu', array('menus' => array(
    array(
		'heading' => 'Auction',
		'items' => array(
			$this->Html->link(__('Dashboard'), array('admin' => true, 'controller' => 'products', 'action' => 'dashboard')),
			$this->Html->link(__d('products', 'List'), array('action' => 'index')),
			$this->Html->link(__d('products', 'Edit'), array('action' => 'edit', $auction['Auction']['id'])),
			$this->Html->link(__d('products', 'Delete'), array('action' => 'delete', $auction['Auction']['id']), null, __('Are you sure you want to delete %s?', $auction['Auction']['name'])),
			),
		),
	)));
