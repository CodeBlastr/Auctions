<div class="auction view media row-fluid" id="<?php echo __('auction%s', $auction['Auction']['id']); ?>" itemscope itemtype="http://schema.org/Auction">
	<h2 class="media-heading" itemprop="name"><?php echo $auction['Auction']['name']; ?></h2>
	<div class="item auction gallery pull-left media-object">
        <?php echo $this->Media->display($auction['Media'][0], array('width' => 181, 'height' => 121, 'alt' => $auction['Auction']['name'])); ?>
    </div>

    <div class="item auction description span5 pull-left media-body">
        <div class="itemSummary auctionSummary">
            <span itemprop="description"><?php echo $auction['Auction']['summary']; ?></span>
        </div>
        <?php echo $auction['Auction']['description']; ?>
    </div>

    <div class="last span4">
    	<div>
    		<table>
    			<tr><td>Time left:</td><td><b><time datetime="<?php echo date('c', strtotime($auction['Auction']['ended']))?>"><?php echo $this->Time->timeAgoInWords($auction['Auction']['ended'])?></time></b><br />(<?php echo $this->Time->nice($auction['Auction']['ended']) ?>)</td></tr>
    			<!--tr><td>Bid history:</td><td><?php echo count($auction['AuctionBid']) ?> bids</td></tr-->
    		</table>
    	</div>
    	<div class="well">
        	<?php echo $this->Element('Auctions.reverse', array('auction' => $auction)); ?>      
        </div>
    </div>
</div>

<?php
// set contextual search options
$this->set('forms_search', array(
    'url' => '/products/products/index/', 
	'inputs' => array(
		array(
			'name' => 'contains:name', 
			'options' => array(
				'label' => '', 
				'placeholder' => 'Product Search',
				'value' => !empty($this->request->params['named']['contains']) ? substr($this->request->params['named']['contains'], strpos($this->request->params['named']['contains'], ':') + 1) : null,
				)
			),
		)
	));
// set the contextual menu items
$this->set('context_menu', array('menus' => array(
    array(
		'heading' => 'Products',
		'items' => array(
			$this->Html->link(__('Dashboard'), array('admin' => true, 'controller' => 'products', 'action' => 'dashboard')),
			)
		),
	array(
		'heading' => 'Product',
		'items' => array(
			$this->Html->link(__d('products', 'List'), array('action' => 'index')),
			$this->Html->link(__d('products', 'Edit'), array('action' => 'edit', $auction['Auction']['id'])),
			$this->Html->link(__d('products', 'Delete'), array('action' => 'delete', $auction['Auction']['id']), null, __('Are you sure you want to delete %s?', $auction['Auction']['name'])),
			),
		),
	)));
