<?php
App::uses('AuctionsAppController', 'Auctions.Controller');
/**
 * Auctions Controller
 *
 * Handles the logic for auctions.
 *
 * PHP versions 5
 *
 * Zuha(tm) : Business Management Applications (http://zuha.com)
 * Copyright 2009-2012, Zuha Foundation Inc. (http://zuha.org)
 *
 * Licensed under GPL v3 License
 * Must retain the above copyright notice and release modifications publicly.
 *
 * @copyright     Copyright 2009-2012, Zuha Foundation Inc. (http://zuha.com)
 * @link          http://zuha.com Zuha� Project
 * @package       zuha
 * @subpackage    zuha.app.plugins.auctions
 * @since         Zuha(tm) v 0.0.1
 * @license       GPL v3 License (http://www.gnu.org/licenses/gpl.html) and
 * Future Versions
 */
class AuctionsController extends AuctionsAppController {

/**
 * Name
 *
 * @var string
 */
	public $name = 'Auctions';

/**
 * Uses
 *
 * @var string
 */
	public $uses = 'Auctions.Auction';

/**
 * Index method
 *
 * @param void
 * @return void
 */
	public function index() {
		$this->paginate['conditions']['Auction.is_expired'] = 0;
		$this->paginate['conditions']['Auction.started >'] = date('Y-m-d h:i:s');
		$this->paginate['contain'][] = 'Seller';
		$this->set('title_for_layout', __('Auctions') . ' | ' . __SYSTEM_SITE_NAME);
		$this->set('page_title_for_layout', __('Auctions') . ' | ' . __SYSTEM_SITE_NAME);
		$this->set('displayName', 'name');
		$this->set('displayDescription', 'summary');
		$this->set('showGallery', true);
		$this->set('galleryForeignKey', 'id');
		//$this->paginate['conditions'][] = array('Auction.is_expired' => 0);
		$this->paginate['contain']['AuctionBid'] = array(
			'limit' => 1,
			'order' => array('AuctionBid.amount' => 'DESC')
		);
		
		$this->set('title_for_layout', __('Auctions') . ' | ' . __SYSTEM_SITE_NAME);
		$this->set('page_title_for_layout', __('Auctions') . ' | ' . __SYSTEM_SITE_NAME);
		$this->set('auctions', $auctions = $this->paginate());
		return $auctions;
	}

/**
 * Add method
 *
 * @param return void
 */
	public function add() {
		$this->redirect('admin');
		// if (CakePlugin::loaded('Categories')) {
		// $this->set('categories',
		// $this->Auction->Category->generateTreeList(array('Category.model' =>
		// 'Auction')));
		// }
		if ($this->request->is('post')) {
			$this->Auction->create();
			if ($this->Auction->saveAll($this->request->data)) {
				$this->Session->setFlash(__('Auction saved.'));
				$this->redirect(array(
					'admin' => false,
					'action' => 'view',
					$this->Auction->id
				));
			}
		}
		$this->set('types', $this->Auction->types());
		$this->set('page_title_for_layout', __('Create an Auction'));
		$this->set('title_for_layout', __('Create an Auction'));
	}

/**
 * Edit method
 *
 * @access public
 * @param string
 * @param type $id
 * @throws NotFoundException
 */
	public function edit($id = null) {
		$this->redirect('admin');
		// check to see if we have a auction before even worrying about anything else
		$this->Auction->id = $id;
		if (!$this->Auction->exists()) {
			throw new NotFoundException(__('Invalid auction'));
		}
		if (!empty($this->request->data)) {
			$this->Auction->create();
			if ($this->Auction->saveAll($this->request->data)) {
				$this->Session->setFlash(__('Auction saved.'));
				if (isset($this->request->data['SaveAndContinue'])) {
					$this->redirect(array(
						'action' => 'edit',
						$this->Auction->id
					));
				} else {
					$this->redirect(array(
						'admin' => false,
						'action' => 'view',
						$this->Auction->id
					));
				}
			}
		}
		// order is important (categories for all auctions)
		if (CakePlugin::loaded('Categories')) {
			$this->set('categories', $this->Auction->Category->generateTreeList());
			$selectedCategories = $this->Auction->Category->Categorized->find('all', array(
				'conditions' => array(
					'Categorized.model' => $this->Auction->alias,
					'Categorized.foreign_key' => $this->Auction->id
				),
				'contain' => array('Category')
			));
			$this->set('selectedCategories', Set::extract($selectedCategories, '/Category/id'));
		}
		$this->request->data = $this->Auction->find('first', array(
			'conditions' => array('Auction.id' => $id),
			'contain' => array(),
		));
		$this->set('page_title_for_layout', __('Edit %s ', $this->request->data['Auction']['name']));
		$this->set('title_for_layout', __('Edit %s ', $this->request->data['Auction']['name']));
	}

/**
 * View method
 *
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->Auction->id = $id;
		if (!$this->Auction->exists()) {
			throw new NotFoundException(__('Invalid auction'));
		}
		$auction = $this->Auction->find('first', array(
			'conditions' => array('Auction.id' => $id),
			'contain' => array('AuctionBid' => array(
					'limit' => 1,
					'order' => array('AuctionBid.amount' => 'DESC')
				), )
		));
		$this->set('title_for_layout', $auction['Auction']['name'] . ' < Auctions | ' . __SYSTEM_SITE_NAME);
		$this->set(compact('auction'));
		$auction['Auction']['type'] == 'reverse' ? $this->view = 'view_reverse' : null;
		$auction['Auction']['is_expired'] == 1 ? $this->view = 'view_expired' : null;
	}

/**
 * Delete method
 *
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Auction->id = $id;
		if (!$this->Auction->exists()) {
			throw new NotFoundException(__('Invalid auction'));
		}
		if ($this->Auction->delete($id)) {
			$this->Session->setFlash(__('Auction deleted'));
		}
		$this->redirect(array('action' => 'index'));
	}

/**
 * Categories method
 * A page for editing auction categories.
 */
	public function categories($parentId = null) {
		$this->redirect('admin');
		if (!empty($this->request->data['Category'])) {
			if ($this->Auction->Category->save($this->request->data)) {
				$this->Session->setFlash(__('Category saved'));
			}
		}
		$conditions = !empty($parentId) ? array(
			'Category.parent_id' => $parentId,
			'Category.model' => 'Auction'
		) : array('Category.model' => 'Auction');
		$categories = $this->Auction->Category->find('threaded', array('conditions' => $conditions));
		$options = $this->Auction->Option->find('threaded');
		$this->set(compact('categories', 'options'));
		$this->set('page_title_for_layout', __('Auction Categories & Options'));
		return $categories;
		// used in element Categories/categories
	}

/**
 * Auction dashboard.
 *
 */
	public function dashboard() {
		$this->redirect('admin');
		$Transaction = ClassRegistry::init('Transactions.Transaction');
		$TransactionItem = ClassRegistry::init('Transactions.TransactionItem');
		$this->set('counts', $counts = array_count_values(array_filter(Set::extract('/Transaction/status', $Transaction->find('all')))));
		$this->set('statsSalesToday', $Transaction->salesStats('today'));
		$this->set('statsSalesThisWeek', $Transaction->salesStats('thisWeek'));
		$this->set('statsSalesThisMonth', $Transaction->salesStats('thisMonth'));
		$this->set('statsSalesThisYear', $Transaction->salesStats('thisYear'));
		$this->set('statsSalesAllTime', $Transaction->salesStats('allTime'));
		$this->set('transactionStatuses', $Transaction->statuses());
		$this->set('itemStatuses', $TransactionItem->statuses());
		$this->set('title_for_layout', __('Ecommerce Dashboard'));
		$this->set('page_title_for_layout', __('Ecommerce Dashboard'));
	}

/**
 * Category method.
 *
 * @param void
 * @return void
 */
	public function category($categoryId = null) {
		if (!empty($categoryId)) {
			$this->paginate['joins'] = array( array(
					'table' => 'categorized',
					'alias' => 'Categorized',
					'type' => 'INNER',
					'conditions' => array(
						"Categorized.foreign_key = Auction.id",
						"Categorized.model = 'Auction'",
						"Categorized.category_id = '{$categoryId}'",
					),
				));
			$this->paginate['contain'][] = 'Category';
		}
		$this->view = 'index';
		return $this->index();
	}

}
