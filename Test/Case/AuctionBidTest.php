<?php
App::uses('AuctionBid', 'Auctions.Model');

/**
 * Auction Test Case
 *
 */
class AuctionTestCase extends CakeTestCase {
/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.Auctions.AuctionBid',
		'plugin.Auctions.Auction',
		
		'plugin.Media.Media',
		'plugin.Media.MediaAttachment',
		'plugin.Users.User'
        );
/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->AuctionBid = ClassRegistry::init('Auctions.AuctionBid');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->AuctionBid);
		parent::tearDown();
	}
/**
 * testNotifyOutbid method
 *
 * @return void
 */
	// public function testNotifyOutbid() {
		// $data['AuctionBid'] = array(
			// 'auction_id' => 15,
			// 'bidder_id' => 100,
			// 'amount' => '5.00'
			// );
		// $this->AuctionBid->create();
		// $this->AuctionBid->save($data); // we wish we could test that no email was sent here, 
		// // but we're not returning values that would let us know from the save function,
		// // once we change to a queue method on __sendMail we could check the db to see if a notification
		// // was saved.
// 		
		// $data['AuctionBid'] = array(
			// 'bidder_id' => 101,
			// 'auction_id' => 15,
			// 'amount' => '6.00'  // this is lower than the current high bid of 5.00
			// );
		// $this->AuctionBid->create();
		// $this->AuctionBid->save($data); // we wish we could test that no email was sent here, 
		// // but we're not returning values that would let us know from the save function,
		// // once we change to a queue method on __sendMail we could check the db to see if a notification
		// // was saved.
	// }

/**
 * testSave method
 *
 * @return void
 */
	public function testSaveUnderBid() {
		$data['AuctionBid'] = array(
			'bidder_id' => 7,
			'auction_id' => 15,
			'amount' => '5.00'
			);
		$this->AuctionBid->create();
		if ($this->AuctionBid->save($data)) {
			$data['AuctionBid'] = array(
				'bidder_id' => 7,
				'auction_id' => 15,
				'amount' => '4.00'  // this is lower than the current high bid of 5.00
				);
			$this->AuctionBid->create();
			$this->AuctionBid->save($data);
		}
		$invalidFields = $this->AuctionBid->invalidFields();
		$this->assertTrue(!empty($invalidFields['amount']));
	}

/**
 * testSave method
 *
 * @return void
 */
	public function testSave() {
		$data['AuctionBid'] = array(
			'user_id' => 7,
			'auction_id' => 15,
			'amount' => '5.00'
			);
		$this->AuctionBid->save($data);
		$this->assertTrue(!empty($this->AuctionBid->id));
	}
}
