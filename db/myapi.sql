/*Table structure for table `api_objects` */

DROP TABLE IF EXISTS `api_objects`;

CREATE TABLE `api_objects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ckey` varchar(100) DEFAULT NULL,
  `cvalue` blob,
  `deleted` tinyint(1) DEFAULT '0',
  `date_created` datetime DEFAULT NULL,
  `date_modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `IDX_Key` (`ckey`),
  KEY `IDX_Date` (`date_modified`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `api_objects` */

/*Table structure for table `api_objects_audit_log` */

DROP TABLE IF EXISTS `api_objects_audit_log`;

CREATE TABLE `api_objects_audit_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ckey` varchar(100) DEFAULT NULL,
  `old_value` blob,
  `new_value` blob,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `IDX_Key` (`ckey`),
  KEY `IDX_Date` (`date_created`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `api_objects_audit_log` */