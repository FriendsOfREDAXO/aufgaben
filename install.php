<?php

if (!$this->hasConfig()) {
  $this->setConfig('ansicht', 'beide');
  $this->setConfig('mails', []);
  $this->setConfig('time', 5);
  $this->setConfig('send-to-all', '0');
  $this->setConfig('betreff', "");
  $this->setConfig('absender', "");
}

$sql = rex_sql::factory();
$sql->setQuery('
  CREATE TABLE IF NOT EXISTS `rex_aufgaben` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `title` varchar(255) DEFAULT NULL,
    `description` longtext DEFAULT NULL,
    `category` int(10) DEFAULT NULL,
    `responsible` int(10) DEFAULT NULL,
    `prio` int(10) DEFAULT NULL,
    `status` int(10) DEFAULT NULL,
    `createdate` DATETIME DEFAULT NULL,
    `updatedate` DATETIME DEFAULT NULL,
    `createuser` varchar(255) DEFAULT NULL,
    `updateuser` varchar(255) DEFAULT NULL,
    `observer` varchar(255) DEFAULT NULL,
    `finaldate` varchar(255) DEFAULT NULL,
    `versendet` varchar(255) DEFAULT "2",
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
');


$sql = rex_sql::factory();
$sql->setQuery('CREATE TABLE IF NOT EXISTS `rex_aufgaben_categories` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `category` varchar(255) DEFAULT NULL,
    `color` varchar(255) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
');

$sql = rex_sql::factory();
$sql->setQuery('CREATE TABLE IF NOT EXISTS `rex_aufgaben_status` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `status` varchar(255) DEFAULT NULL,
    `icon` varchar(255)  DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
');


$sql = rex_sql::factory();
$sql->setQuery('CREATE TABLE IF NOT EXISTS `rex_aufgaben_filter` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `user` varchar(255) DEFAULT NULL,
    `category` varchar(255)  DEFAULT NULL,
    `responsible` varchar(255)  DEFAULT NULL,
    `prio` varchar(255)  DEFAULT NULL,
    `status` varchar(255) DEFAULT NULL,
    `done` varchar(255) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
');

$sql = rex_sql::factory();
$sql->setQuery('CREATE TABLE IF NOT EXISTS `rex_aufgaben_user_settings` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `user` int(10) DEFAULT NULL,
    `counter` int(10)  DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
');

// Sollte noch übersetzt werden -> wie?
$sql = rex_sql::factory();
$sql->setQuery("REPLACE INTO `rex_aufgaben_status` VALUES
    (1,'Offen','fa-folder-open-o'),
    (2,'Wird bearbeitet','fa-gears'),
    (3,'Frage','fa-question'),
    (4,'Warten auf etwas','fa-hourglass-start'),
    (5,'Auf später verschoben','fa-calendar'),
    (6,'Erledigt','fa-check');
");

$error = '';

if(!$error) {
  $this->setConfig('install', true);
}

