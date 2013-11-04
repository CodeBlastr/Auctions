<?php
App::uses('AuctionsAppModel', 'Auctions.Model');

class Auction extends AuctionsAppModel {

	public $name = 'Auction';
    
    public $filterPrice = true;

	public $validate = array(
		'name' => array('notempty'),
		'started' => array('notempty'),
        );

	public $order = '';
	
	public $types = array(
		'standard' => 'Standard',
		'reverse' => 'Reverse'
		);

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	public $hasMany = array(
		'AuctionBid' => array(
			'className' => 'Auctions.AuctionBid',
			'foreignKey' => 'auction_id',
			'dependent' => true,
			'order' => array('amount' => 'DESC'),
			'limit' => 1
			)
        );
        
    public $hasAndBelongsToMany = array(
        'Winner' => array(
			'className' => 'Users.User',
			'joinTable' => 'auction_bids',
			'foreignKey' => 'auction_id',
			'associationForeignKey' => 'user_id',
			'order' => array('amount' => 'DESC'),
			'limit' => 1
			)
		);

	//auctions association.
	public $belongsTo = array(
		'Seller' => array(
			'className' => 'Users.User',
			'foreignKey' => 'seller_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
            )
        );
    
	public function __construct($id = null, $table = null, $ds = null) {
		if (CakePlugin::loaded('Media')) {
			$this->actsAs[] = 'Media.MediaAttachable';
		}
		if (CakePlugin::loaded('Categories')) {
			$this->hasAndBelongsToMany['Category'] = array(
	            'className' => 'Categories.Category',
	       		'joinTable' => 'categorized',
	            'foreignKey' => 'foreign_key',
	            'associationForeignKey' => 'category_id',
	    		'conditions' => array('Categorized.model' => 'Auction'),
	    		// 'unique' => true,
	            );
			$this->actsAs['Categories.Categorizable'] = array('modelAlias' => 'Auction');
		}
		if(CakePlugin::loaded('Transactions')) {
			$this->actsAs[] = 'Transactions.Buyable';
		}
		parent::__construct($id, $table, $ds); // this order is imortant
		
		$this->categorizedParams = array('conditions' => array($this->alias.'.parent_id' => null));
		$this->order = array($this->alias . '.' . 'price');
	}
    
/**
 * Before save callback
 * 
 * @param type $options
 * @return boolean
 */
    public function beforeSave($options = array()) {
    	$this->data = $this->cleanData($this->data);
        $this->Behaviors->attach('Media.MediaAttachable');
        return parent::beforeSave($options);
    }

/**
 * After find callback
 * 
 * @param array $results
 * @param int $primary
 * @return array
 */
	public function afterFind($results, $primary = false) {
		!empty($results) ? $results = $this->expire($results) : null; // expires ended auctions, but we probably will want to have this in a different spot (eg. cron) on a higher traffic site
		$results = $this->reverseOutput($results);
		return parent::afterFind($results, $primary = false);
	}

/**
 * Reverse Output method
 * 
 * @param array $data
 */
	public function reverseOutput($data = array()) {
		for($i = 0; $i < count($data); ++$i) {
			if($data[$i][$this->alias]['type'] == 'reverse') {
				$time = time() - strtotime($data[$i][$this->alias]['started']);
				if ($time > 0) { // else the auction hasn't started yet
				 	// number of intervals that have passed
					$intervals = floor($time / $data[$i][$this->alias]['interval']);
					$data[$i][$this->alias]['_intervals'] = $intervals;
					// the price minus the intervals multiplied times the increment value
					$data[$i][$this->alias]['price'] = $data[$i][$this->alias]['price'] - ($intervals * $data[$i][$this->alias]['increment']);
					// the price if the floor has been reached
					$data[$i][$this->alias]['price'] = $data[$i][$this->alias]['price'] > $data[$i][$this->alias]['floor'] ? $data[$i][$this->alias]['price'] : $data[$i][$this->alias]['floor'];
				}
			}
		}
		return $data;
	}

/**
 * Check auction expiration 
 * 
 * Expires items in the data array.  If the array is empty it will 
 * expire all ended auctions found in the db.
 * 
 * @param array $data
 * @param array $options
 * @return array
 */
	public function expire($data = array(), $options = array()){		
		if(!empty($data[$this->alias])){ //handles single auctions
			$data[0] = $data;
			$single = true;
		}
		if (empty($data[0])) {
			// if $data is empty then we will expire all ended auctions
			$data = $this->find('all', array('conditions' => array($this->alias . '.is_expired' => false, $this->alias . '.ended' < date('Y-m-d h:i:s')), 'callbacks' => false));
		}
		if(isset($data[0][$this->alias])) { //this handles many Auctions
			$count = count($data); // order is important because we are using unset() in the loop
			for ($i = 0; $i < $count; ++$i) {
				if(!empty($data[$i][$this->alias]['is_expired'])) {
					// unset($data[$i]); // these unsets are causing problems with pagination, and page data (we just set is_expired to 1 below instead, handle it in the controller or view)
				}
				if(!empty($data[$i][$this->alias]['ended']) && strtotime($data[$i][$this->alias]['ended']) < time()) {
					$this->id = $data[$i][$this->alias]['id'];
					if ($this->saveField('is_expired', 1, false)) {
						$data[$i][$this->alias]['is_expired'] = 1;
						$this->AuctionBid->finishAuction($data[$i], $options);
					} else {
						throw new Exception(__('Error expiring auctions, please alert an administrator.'));	
					}
					// unset($data[$i]); // these unsets are causing problems with pagination, and page data (we just set is_expired to 1 above instead, handle it in the controller or view)
				}
			}
		}
		if(!empty($single) && !empty($data[0][$this->alias])){
			$data = $data[0];
			unset($data[0]);
		}
		return $data;
	}

/**
 * Clean data method
 * 
 */
 	public function cleanData($data) {
 		if (isset($data[$this->alias]['is_expired']) && empty($data[$this->alias]['is_expired'])) {
 			$data[$this->alias]['is_expired'] = 0;
 		}
		return $data;
 	}

/**
 * origin_afterFind callback
 * 
 * A callback from related plugins which are only related by the abstract model/foreign_key in the db
 * 
 * @param array $results
 */
    public function origin_afterFind(Model $Model, $results = array(), $primary = false) {
    	if ($Model->name == 'TransactionItem') {
	        $ids = Set::extract('/TransactionItem/foreign_key', $results);
	        $auctions = $this->_concatName($this->find('all', array('conditions' => array($this->alias . '.id' => $ids), 'callbacks' => false)));
			exit;
	        $names = Set::combine($auctions, '{n}.Auction.id', '{n}.Auction.name');
	        $i = 0;
	        foreach ($results as $result) {
	            if ($names[$result['TransactionItem']['foreign_key']]) {
	                $results[$i]['TransactionItem']['name'] = $names[$result['TransactionItem']['foreign_key']];
	                $results[$i]['TransactionItem']['_associated']['name'] = $names[$result['TransactionItem']['foreign_key']];
	                $results[$i]['TransactionItem']['_associated']['viewLink'] = __('/auctions/auctions/view/%s', $result['TransactionItem']['foreign_key']);
	            }
				$i++;
	        }
	        return $results;
    	}
    }
	
/**
 * Types method
 * 
 * @return array
 */
 	public function types() {
 		return $this->types;
 	}
}