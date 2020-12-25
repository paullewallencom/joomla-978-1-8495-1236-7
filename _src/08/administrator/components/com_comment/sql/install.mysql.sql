CREATE TABLE IF NOT EXISTS `#__comment` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `contentid` int(10) NOT NULL DEFAULT '0',
  `component` varchar(50) NOT NULL DEFAULT '',
  `ip` varchar(15) NOT NULL DEFAULT '',
  `userid` int(11) DEFAULT NULL,
  `usertype` varchar(25) NOT NULL DEFAULT 'Unregistered',
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `name` varchar(30) NOT NULL DEFAULT '',
  `email` varchar(50) NOT NULL DEFAULT '',
  `website` varchar(100) NOT NULL DEFAULT '',
  `notify` tinyint(1) NOT NULL DEFAULT '0',
  `title` varchar(50) NOT NULL DEFAULT '',
  `comment` text NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `voting_yes` int(10) NOT NULL DEFAULT '0',
  `voting_no` int(10) NOT NULL DEFAULT '0',
  `parentid` int(10) NOT NULL DEFAULT '-1',
  `importtable` varchar(30) NOT NULL DEFAULT '',
  `importid` int(10) NOT NULL DEFAULT '0',
  `importparentid` int(10) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`id`),
  KEY `com_contentid` (`component`,`contentid`)
) CHARACTER SET `utf8` COLLATE `utf8_general_ci`;


CREATE TABLE IF NOT EXISTS `#__comment_captcha` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `insertdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `referenceid` varchar(100) NOT NULL DEFAULT '',
  `hiddentext` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`)
) CHARACTER SET `utf8` COLLATE `utf8_general_ci`;

CREATE TABLE IF NOT EXISTS `#__comment_joomvertising` (
    `id` int(11) unsigned NOT NULL auto_increment,
    `type` varchar(255) NOT NULL,
    `code` TEXT NOT NULL ,
    PRIMARY KEY ( `id` )
) CHARACTER SET `utf8` COLLATE `utf8_general_ci`;

CREATE TABLE IF NOT EXISTS `#__comment_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `set_name` varchar(50) NOT NULL DEFAULT '',
  `set_component` varchar(50) NOT NULL DEFAULT '',
  `set_sectionid` int(11) NOT NULL DEFAULT '0',
  `params` text NOT NULL,
  PRIMARY KEY (`id`)
) CHARACTER SET `utf8` COLLATE `utf8_general_ci`;

CREATE TABLE IF NOT EXISTS `#__comment_voting` (
  `id` int(10) NOT NULL DEFAULT '0',
  `ip` varchar(15) NOT NULL DEFAULT '',
  `time` int(11) NOT NULL DEFAULT '0'
) CHARACTER SET `utf8` COLLATE `utf8_general_ci`;