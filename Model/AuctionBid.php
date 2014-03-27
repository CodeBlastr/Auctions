<?php
App::uses('AuctionsAppModel', 'Auctions.Model');
class AuctionBid extends AuctionsAppModel {

/**
 *
 * @var string
 */
	public $name = 'AuctionBid';

/**
 *
 * @var array
 */
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
			'checkBidIncrement' => array(
				'rule' => array('_checkBidIncrement'),
				'allowEmpty' => false,
				'message' => 'Your bid should be at least $1.00 higher than the current bid.',
				),
			'checkStartBid' => array(
				'rule' => array('_checkStartBid'),
				'allowEmpty' => false,
				'message' => 'Your bid is lower than the lowest allowed starting bid.',
				),
			'checkExpired' => array(
				'rule' => array('_checkExpired'),
				'allowEmpty' => false,
				'message' => 'Sorry, your bid was a little too late.',
				),
			)
		);

/**
 *
 * @var array
 */
	public $belongsTo = array(
		'Auction' => array(
			'className' => 'Auctions.Auction',
			'foreignKey' => 'auction_id',
			'conditions' => '',
			'fields' => ''
		),
		'HighBidder' => array(
			'className' => 'Users.User',
			'foreignKey' => 'bidder_id',
			'conditions' => '',
			'fields' => '',
			'order' => array('amount' => 'DESC'),
			'limit' => 1
		)
	);

/**
 * @param boolean $created
 * @param array $options
 */
 	public function afterSave($created, $options = array()){
 		$this->notifyOutbid($this->data, $options);
	 	}

/**
 * Check Auction.is_expired
 * @return boolean
 */
	public function _checkExpired() {
		if (!empty($this->data['AuctionBid']['auction_id'])) {
			$expired = $this->Auction->field('is_expired', array('Auction.id' => $this->data['AuctionBid']['auction_id']));
			if ($expired) {
				return false;
			}
		}
		return true;
	}

/**
 * Check highest bid validation
 * @return boolean
 */
	public function _checkHighestBid() {
		if (!empty($this->data['AuctionBid']['auction_id'])) {
			$highestBid = $this->field('amount', array('AuctionBid.auction_id' => $this->data['AuctionBid']['auction_id']), 'AuctionBid.amount DESC');
			if ($highestBid >= $this->data['AuctionBid']['amount']) {
				return false;
			}
		}
		return true;
	}


/**
 * Notify Bidder who has just been outbid.
 * @param array $bid
 * @param array $options
 */
	public function notifyOutbid($bid, $options = array()){
		if($outbid = $this->getOutbid($bid[$this->alias]['auction_id'], $options)){
			App::uses('User', 'Users.Model');
			$User = new User;
			if($email = $User->field('email', array('User.id' => $outbid['AuctionBid']['bidder_id']))){
				$this->__sendMail($email,'Webpages.Auction Outbid Notification', $bid);
			}
		}
	}


/**
 * Get Bidder who has just been outbid.
 * @param string $auctionId
 * @param array $options
 * @return array|boolean
 */
	public function getOutbid($auctionId, $options = array()){
		$outbidUser = $this->find('all', array('limit' => 2, 'conditions' => array('AuctionBid.auction_id' => $auctionId), 'order' => array('amount' => 'DESC')));
		return isset($outbidUser[1]) ? $outbidUser[1] : false;
	}


/**
 * Check Bid Increment
 * @return boolean
 */
	public function _checkBidIncrement() {
		if (!empty($this->data['AuctionBid']['auction_id'])) {
			$highestBid = $this->field('amount', array('AuctionBid.auction_id' => $this->data['AuctionBid']['auction_id']), 'AuctionBid.amount DESC');
			if ($highestBid + 0.99 >= $this->data['AuctionBid']['amount']) {
				return false;
			}
		}
		return true;
	}

/**
 * Check starting bid validation
 * @return boolean
 */
	public function _checkStartBid() {
		if (!empty($this->data['AuctionBid']['auction_id'])) {
			$minimumBid = $this->Auction->field('start', array('Auction.id' => $this->data['AuctionBid']['auction_id']));
			if ($this->data['AuctionBid']['amount'] < $minimumBid) {
				return false;
			}
		}
		return true;
	}

/**
 * do any other auction wrap up stuff here
 * @param array $auction
 * @param array $options
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
 * @param array $auction
 * @param array $options
 */
	public function notifySeller($auction, $options = array()){
		$seller = $this->Auction->Seller->find('first', array('conditions' => array('Seller.id' => $auction['Auction']['seller_id'])));
		$this->__sendMail($seller['Seller']['email'], 'Webpages.Auctioneer Expired Auction', $auction);
	}

/**
 * Notify Auction Bidder that auction has expired
 * @param array $auction
 * @param array $options
 */
	public function notifyWinner($auction, $options = array()){
		$winner = $this->getWinner($auction['Auction']['id'], $options);
		if (!empty($winner)) { // there may not have been a winner
			$emailarr = $auction + $winner;
			$this->__sendMail($winner['HighBidder']['email'],'Webpages.Auction Winner Notification', $emailarr);
		}
	}

/**
 * Get Winner method
 * Find the highest bid and return the bid and the user.
 * @param string $auctionId
 * @param array $options
 * @return type
 */
	public function getWinner($auctionId, $options = array()) {
		return $this->find('first', array('conditions' => array('AuctionBid.auction_id' => $auctionId), 'contain' => array('HighBidder')));
	}

}
