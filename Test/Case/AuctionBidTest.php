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
		'plugin.Auctions.AuctionBid'
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
 * testSave method
 *
 * @return void
 */
	public function testSaveUnderBid() {
		$data['AuctionBid'] = array(
			'user_id' => 7,
			'auction_id' => 15,
			'amount' => '5.00'
			);
		$this->AuctionBid->create();
		if ($this->AuctionBid->save($data)) {
			$data['AuctionBid'] = array(
				'user_id' => 7,
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
