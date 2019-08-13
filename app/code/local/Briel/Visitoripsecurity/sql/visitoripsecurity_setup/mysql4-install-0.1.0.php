<?php

$installer = $this;

$installer->startSetup();

$installer->run("SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `{$this->getTable('log_remoteaddr_notes')}`;
CREATE TABLE IF NOT EXISTS `{$this->getTable('log_remoteaddr_notes')}` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `remote_addr` bigint(16) NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `first_email_sent_at` bigint(16) NOT NULL DEFAULT '0',
  `second_email_sent_at` bigint(16) NOT NULL DEFAULT '0',
  `blocked` tinyint(1) NOT NULL DEFAULT '0',
  `white` tinyint(1) NOT NULL DEFAULT '0',
  `watch` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `remote_addr` (`remote_addr`),
  KEY `note` (`note`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;");

$installer->endSetup();
