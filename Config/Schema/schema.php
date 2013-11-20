<?php 
class AuctionsSchema extends CakeSchema {

	public $renames = array();

	public function __construct($options = array()) {
		parent::__construct();
	}
	
	public function before($event = array()) {
		App::uses('UpdateSchema', 'Model'); 
		$this->UpdateSchema = new UpdateSchema;
		$before = $this->UpdateSchema->before($event);
		return $before;
	}

	public function after($event = array()) {
		$this->UpdateSchema->rename($event, $this->renames);
		$this->UpdateSchema->after($event);
	}

	public $auction_bids = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'bidder_id' => array('type' => 'string', 'null' => false, 'length' => 36, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'auction_id' => array('type' => 'string', 'null' => false, 'length' => 36, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'amount' => array('type' => 'float', 'null' => false,),
		'created' => array('type' => 'datetime', 'null' => false,),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $auctions = array( 
		'id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'summary' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'price' => array('type' => 'float', 'null' => true, 'default' => null, 'comment' => 'Buy it now price'),
		'start' => array('type' => 'float', 'null' => true, 'default' => null, 'comment' => 'Starting bid'),
		'reserve' => array('type' => 'float', 'null' => true, 'default' => null, 'comment' => 'Reserve price'),
		'increment' => array('type' => 'float', 'null' => true, 'default' => null, 'comment' => 'Minimum amount you can bid above current high bid.'),
		'interval' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'comment' => 'The time - in seconds - between reverse auction bid lowering'),
		'floor' => array('type' => 'float', 'null' => true, 'default' => null, 'comment' => 'Lowest price a reverse auction can hit.'),
		'extension' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'comment' => 'The time - in seconds - to add each time a bid is placed'),
		'weight' => array('type' => 'float', 'null' => true, 'default' => null),
		'height' => array('type' => 'float', 'null' => true, 'default' => null),
		'width' => array('type' => 'float', 'null' => true, 'default' => null),
		'length' => array('type' => 'float', 'null' => true, 'default' => null),
		'shipping_type' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'shipping_charge' => array('type' => 'float', 'null' => true, 'default' => null),
		'payment_type' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'type' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'is_public' => array('type' => 'boolean', 'null' => false, 'default' => 1),
		'is_virtual' => array('type' => 'boolean', 'null' => false, 'default' => 0),
		'is_buyable' => array('type' => 'boolean', 'null' => false, 'default' => 1),
		'is_expired' => array('type' => 'boolean', 'null' => false, 'default' => 0),
		'seller_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'creator_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'modifier_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'started' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'ended' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'search_tags' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM') 
	);
}
