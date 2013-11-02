<?php
App::uses('AuctionsAppModel', 'Auctions.Model');

class Auction extends AuctionsAppModel {

	public $name = 'Auction';
    
    public $filterPrice = true;

	public $validate = array(
		'name' => array('notempty'),
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
		$this->expire(); // expires ended auctions, but we probably will want to have this in a different spot (eg. cron) on a higher traffic site
		return parent::afterFind($results, $primary = false);
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
	public function expire(array $data = array(), array $options = array()){
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
					unset($data[$i]);
				}
				if(!empty($data[$i][$this->alias]['ended']) && strtotime($data[$i][$this->alias]['ended']) < time()) {
					$this->id = $data[$i][$this->alias]['id'];
					if ($this->saveField('is_expired', 1, false)) {
						$data[$i][$this->alias]['type'] == 'auction' ? $this->AuctionBid->finishAuction($data[$i], $options) : null;
					} else {
						throw new Exception(__('Error expiring auctions, please alert an administrator.'));	
					}
					unset($data[$i]);
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
 * origin_afterFind callback
 * 
 * A callback from related plugins which are only related by the abstract model/foreign_key in the db
 * 
 * @param array $results
 */
    public function origin_afterFind(Model $Model, $results = array(), $primary = false) {
    	if ($Model->name == 'TransactionItem') {
	        $ids = Set::extract('/TransactionItem/foreign_key', $results);
	        $auctions = $this->_concatName($this->find('all', array('conditions' => array($this->alias . '.id' => $ids), 'contain' => array('Option'))));
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