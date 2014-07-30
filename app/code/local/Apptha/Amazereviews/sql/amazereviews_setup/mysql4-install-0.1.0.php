<?php
/**
 * Apptha
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.apptha.com/LICENSE.txt
 *
 * ==============================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * ==============================================================
 * This package designed for Magento COMMUNITY edition
 * Apptha does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Apptha does not provide extension support in case of
 * incorrect edition usage.
 * ==============================================================
 *
 * @category    Apptha
 * @package     Apptha_Amazereviews
 * @version     0.2.2
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2014 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 **/

// CREATING NEW TABLES amazereviews & advancedratings
// ALTER EXISTING TABLE review BY ADDED TWO COLUMNS counter_yes and counter_no 

$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
DROP TABLE IF EXISTS {$this->getTable('amazereviews')};

   CREATE TABLE {$this->getTable('amazereviews')} (
  `abuse_id` int(11) NOT NULL AUTO_INCREMENT,
  `review` text CHARACTER SET utf8,
  `customer` varchar(150) CHARACTER SET utf8 DEFAULT NULL,
  `product` text CHARACTER SET utf8,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL,
  `delete` tinyint(4) NOT NULL,
  `email` varchar(100) CHARACTER SET utf8 NOT NULL,
  `comment` text CHARACTER SET utf8,
  `review_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`abuse_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=42 ;

DROP TABLE IF EXISTS {$this->getTable('reviewhelpful')};

 CREATE TABLE {$this->getTable('reviewhelpful')} (
  `accepted_id` int(11) NOT NULL AUTO_INCREMENT,
  `customerid` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `reviewid` int(11) DEFAULT NULL,
  `accepted_status` varchar(4) CHARACTER SET utf8 DEFAULT NULL,
  `voted_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`accepted_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;


ALTER TABLE  {$this->getTable('review')} ADD  `counter_yes` INT NOT NULL DEFAULT  '0' AFTER  `entity_pk_value` ,
ADD  `counter_no` INT NOT NULL DEFAULT  '0' AFTER  `counter_yes` , ADD  `abuse_counter` INT NOT NULL DEFAULT  '0' AFTER  `counter_no` ;



SQLTEXT;

$installer->run($sql);

$installer->endSetup();
	 