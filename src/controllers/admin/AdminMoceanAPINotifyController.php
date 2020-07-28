<?php
/**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

class AdminMoceanAPINotifyController extends ModuleAdminController
{
	public function __construct() {
        // With all this info set, it's about time to call the parent constructor
        parent::__construct();
	}
	
	public function initContent() {
		parent::initContent();
		$template_file = _PS_MODULE_DIR_.'moceanapinotify/views/templates/log/table.tpl';
		$log_history =  \Db::getInstance()->executeS("SELECT * FROM "._DB_PREFIX_."moceansms_notify_log");
		
		//Paginator
		
		$history_raw = [];
		foreach($log_history as $val) {
			$history_raw[] = $val;
		}
		$page = ! empty( $_GET['page'] ) ? (int) $_GET['page'] : 1;
		$total = count( $history_raw ); //total items in array    
		$limit = 10; //per page    
		$totalPages = ceil( $total/ $limit ); //calculate total pages
		$page = max($page, 1); //get 1 page when $_GET['page'] <= 0
		$page = min($page, $totalPages); //get last page when $_GET['page'] > $totalPages
		$offset = ($page - 1) * $limit;
		if( $offset < 0 ) $offset = 0;
		
		//Link Paginator
		$token = Tools::getAdminTokenLite('AdminMoceanAPINotify');
		$link = '?controller=AdminMoceanAPINotify&page=%d&token='.$token;
		$pagerContainer = '<div style="width: 300px;">';   
		if( $totalPages != 0 ) 
		{
		  if( $page == 1 ) 
		  { 
			$pagerContainer .= ''; 
		  } 
		  else 
		  { 
			$pagerContainer .= sprintf( '<a href="' . $link . '" style="color: #c00"> &#171; prev page</a>', $page - 1 ); 
		  }
		  $pagerContainer .= ' <span> page <strong>' . $page . '</strong> from ' . $totalPages . '</span>'; 
		  if( $page == $totalPages ) 
		  { 
			$pagerContainer .= ''; 
		  }
		  else 
		  { 
			$pagerContainer .= sprintf( '<a href="' . $link . '" style="color: #c00"> next page &#187; </a>', $page + 1 ); 
		  }           
		}                   
		$pagerContainer .= '</div>';


		$log_history = array_slice( $history_raw, $offset, $limit );
		$token = Tools::getAdminTokenLite('AdminModules');
		$this->context->smarty->assign('log_history', $log_history);
		$this->context->smarty->assign('token', $token);
		$this->context->smarty->assign('pagerContainer', $pagerContainer);
		$content = $this->context->smarty->fetch($template_file); 
		$this->context->smarty->assign([
			'content' => $content,
		]);
	}
	
}
