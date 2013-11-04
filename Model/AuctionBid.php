<?php
App::uses('AuctionsAppModel', 'Auctions.Model');
class AuctionBid extends AuctionsAppModel {

	public $name = 'AuctionBid';

	public $validate = array(
		'amount' => array(
			'notempty' => array(
				'rule' => 'notEmpty',
				'allowEmpty' => false, 
				'message' => 'Please enter bid value',
				'required' => 'create'
				),
			'checkHighBid' => array(
				'rule' => array('_checkHighestBid'),
				'allowEmpty' => false, 
				'message' => 'Your bid should be higher than the current highest bid.',
				),
			)
		);

	public $belongsTo = array(
		'Auction' => array(
			'className' => 'Auctions.Auction',
			'foreignKey' => 'auction_id',
			'conditions' => '',
			'fields' => ''
		),
		'Bidder' => array(
			'className' => 'Users.User',
			'foreignKey' => 'bidder_id',
			'conditions' => '',
			'fields' => '',
			'order' => array('amount' => 'DESC'),
			'limit' => 1
		)
	);
	
	public function _checkHighestBid() {
		if (!empty($this->data['AuctionBid']['auction_id'])) {
			$highestBid = $this->field('amount', array('AuctionBid.auction_id' => $this->data['AuctionBid']['auction_id']), 'AuctionBid.amount DESC');
			if ($highestBid > $this->data['AuctionBid']['amount']) {
				return false;
			}
		}
		return true;
	}

/**
 * Finish Auction method
 * 
 */
	public function finishAuction($auction, $options = array()) {
		if ($options['email'] !== false) {
			/// then email the winner
			$this->notifySeller($auction, $options); 
			$this->notifyWinner($auction, $options);
		}
		// do any other auction wrap up stuff here, just don't know what that might be right now...
	}
	
	
/**
 * Notify Seller (Site Admin/Owner) that Auction Auction has Expired.
 * @param array $results
 * @return array
 * 
 */
	public function notifySeller($auction, $options = array()){
		// note we need to add a field to the auction model called sellerid
		$this->__sendMail($auction['Seller']['email'],'Webpages.Auctioneer Expired Auction', $auction);
	}
	
/**
 * Notify Auction Bidder that auction has expired
 * @param array $results
 * @return array
 * 
 */	
	public function notifyWinner($auction, $options = array()){
		$winner = $this->getWinner($auction[$this->alias]['id'], $options);
		if (!empty($winner)) { // there may not have been a winner
			$emailarr = $auction + $winner;
			$this->__sendMail($winner['Bidder']['email'],'Webpages.Auction Winner Notification', $emailarr);	
		}
	}

/**
 * Get Winner method
 * Find the highest bid and return the bid and the user. 
 */
	public function getWinner($auctionId, $options = array()) {
		return $this->find('first', array('conditions' => array('AuctionBid.auction_id' => $auctionId), 'contain' => array('Bidder')));
	}
	
}
