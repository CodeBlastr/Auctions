<?php
App::uses('Auction', 'Auctions.Model');

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
        'plugin.Categories.Category',
        'plugin.Categories.Categorized',
        'plugin.Auctions.Auction',
        'plugin.Auctions.AuctionBid',
        'plugin.Users.User'
        );
/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Auction = ClassRegistry::init('Auctions.Auction');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Auction);
		parent::tearDown();
	}
	
/**
 * Test add
 */
 	public function testSaveAll() {
 		$data = array(
			'Auction' => array(
				'id' => '5249c848-f100-4cc4-a0b0-04df0ad25527',
				'name' => 'Brown Cow',
				'description' => '<p>How now, brown cow?</p>',
				'price' => '45',
				'is_expired' => 0,
				'started' => '2013-09-30 11:50:00',
				'ended' => '2013-10-20 11:50:00',
				'type' => 'reverse'
			)
		);
		$result = $this->Auction->saveAll($data);
		$this->assertTrue($result);
 	}

/**
 * Test expire
 * 
 */
 	public function testExpire() {
 		$this->Auction->notifications = false; // supress notifications so that we don't need a fixture for them
 		$data = array(
			'Auction' => array(
				'id' => '5249c848-f100-4cc4-a0b0-04df0ad25527',
				'name' => 'Brown Cow',
				'description' => '<p>How now, brown cow?</p>',
				'price' => '45',
				'is_expired' => 0,
				'started' => '2013-09-30 11:50:00',
				'ended' => '2013-10-20 11:50:00',
				'type' => 'auction'
			)
		);
		$this->Auction->create();
		$this->Auction->save($data);
		$results = $this->Auction->expire($data, array('email' => false));
		$results = $this->Auction->find('first', array('conditions' => array('Auction.id' => $data['Auction']['id'])));
		$this->assertTrue($results['Auction']['is_expired']); ///this should be expired
		
		$results = array(
			(int) 0 => array(
				'Auction' => array(
					'id' => '4249c848-f100-4cc4-a0b0-04df0ad25528',
					'name' => 'Brown Cow',
					'description' => '<p>How now, brown cow?</p>',
					'price' => '16',
					'is_expired' => 0,
					'started' => '2013-09-30 11:50:00',
					'ended' => date('Y-m-d h:i:s', strtotime('+3 days')),
					'type' => 'auction'
				),
			)
		);
		$results = $this->Auction->expire($results, array('email' => false));
		$this->assertTrue($results[0]['Auction']['is_expired'] === 0); //this should not expire
 	}

/**
 * testExpireEmpty method
 */
 	public function testExpireEmpty() {
 		$this->Auction->notifications = false; // supress notifications so that we don't need a fixture for them
 		// if the first parameter in expire is empty then 
 		$data[] = array(
			'Auction' => array(
				'name' => 'BI-UNIQUE-37377 13912 318238',
				'stock' => null,
				'price' => '45.00',
				'is_expired' => 0,
				'started' => '2013-09-30 11:50:00',
				'ended' => '2013-10-20 11:50:00'
			)
		);
		$data[] = array(
			'Auction' => array(
				'name' => 'BI-UNIQUE-37377 13912 318238',
				'stock' => null,
				'price' => '18.00',
				'is_expired' => 0,
				'started' => '2013-09-30 11:50:00',
				'ended' => '2013-10-24 11:50:00'
			)
		);
		$this->Auction->create();
		$this->Auction->saveAll($data);
 		$this->Auction->expire(array(), array('email' => false));
		$results = Set::extract('/Auction/is_expired', $this->Auction->find('all', array('conditions' => array('Auction.name' => $data[0]['Auction']['name']))));
		$this->assertTrue(array(true, true) == $results);  // both products should have been expired
 	}
}