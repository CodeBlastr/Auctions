<?php
App::uses('AuctionsAppController', 'Auctions.Controller');
class AuctionBidsController extends AuctionsAppController {

	public $name = 'AuctionBids';
	public $uses = 'Auctions.AuctionBid';

	public function add() { //added $auctions, $options = array() as params
		if ($this->request->is('post')) {
			$this->request->data('AuctionBid.bidder_id', $this->userId);
			if ($this->AuctionBid->save($this->request->data)) {
				$this->Session->setFlash('Bid received');
				$this->redirect($this->referer());
			} else {
				$this->Session->setFlash('Unable to process bid. Please try again. ' . ZuhaInflector::flatten($this->AuctionBid->invalidFields()));
				$this->redirect($this->referer());
			}
		}
	}

}
