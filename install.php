<?php

$sql = rex_sql::factory();
$sql->setQuery('
  CREATE TABLE IF NOT EXISTS `rex_aufgaben_aufgaben` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `titel` varchar(255) DEFAULT NULL,
    `beschreibung` longtext DEFAULT NULL,
    `kategorie` int(10) DEFAULT NULL,
    `eigentuemer` int(10) DEFAULT NULL,
    `prio` int(10) DEFAULT NULL,
    `status` int(10) DEFAULT NULL,
    `createdate` DATETIME DEFAULT NULL,
    `updatedate` DATETIME DEFAULT NULL,
    `createuser` varchar(255) DEFAULT NULL,
    `updateuser` varchar(255) DEFAULT NULL,
    `observer` varchar(255) DEFAULT NULL,
    `finaldate` varchar(255) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
');

$sql = rex_sql::factory();
$sql->setQuery('CREATE TABLE IF NOT EXISTS `rex_aufgaben_kategorien` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `kategorie` varchar(255) DEFAULT NULL,
    `farbe` varchar(255) DEFAULT NULL,
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
    `kategorie` varchar(255)  DEFAULT NULL,
    `eigentuemer` varchar(255)  DEFAULT NULL,
    `prio` varchar(255)  DEFAULT NULL,
    `status` varchar(255) DEFAULT NULL,
    `erledigt` varchar(255) DEFAULT NULL,
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

$sql = rex_sql::factory();
$sql->setQuery("REPLACE INTO `rex_aufgaben_status` VALUES
    (1,'Offen','fa-folder-open-o'),
    (2,'Wird bearbeitet','fa-gears'),
    (3,'Frage','fa-question'),
    (4,'Warten auf etwas','fa-hourglass-start'),
    (5,'Auf später verschoben','fa-calendar'),
    (6,'Erledigt','fa-check');
");

rex_sql_table::get("rex_aufgaben_aufgaben")
->ensureColumn(new rex_sql_column('titel', 'varchar(255)'))
->ensureColumn(new rex_sql_column('beschreibung', 'longtext'))
->ensureColumn(new rex_sql_column('kategorie', 'int(10)'))
->ensureColumn(new rex_sql_column('eigentuemer', 'int(10)'))
->ensureColumn(new rex_sql_column('prio', 'int(10)'))
->ensureColumn(new rex_sql_column('status', 'int(10)'))
->alter();

rex_sql_table::get("rex_aufgaben_kategorien")
->ensureColumn(new rex_sql_column('kategorie', 'varchar(255)'))
->ensureColumn(new rex_sql_column('farbe', 'varchar(255)'))
->alter();

rex_sql_table::get("rex_aufgaben_user_settings")
->ensureColumn(new rex_sql_column('user', 'int(10)'))
->ensureColumn(new rex_sql_column('counter', 'int(10)'))
->ensureColumn(new rex_sql_column('filter', 'int(20)'))
->alter();

rex_sql_table::get("rex_aufgaben_status")
->ensureColumn(new rex_sql_column('status', 'varchar(255)'))
->ensureColumn(new rex_sql_column('icon', 'varchar(255)'))
->alter();

rex_sql_table::get("rex_aufgaben_filter")
->ensureColumn(new rex_sql_column('user', 'varchar(255)'))
->ensureColumn(new rex_sql_column('kategorie', 'varchar(255)'))
->ensureColumn(new rex_sql_column('eigentuemer', 'varchar(255)'))
->ensureColumn(new rex_sql_column('prio', 'varchar(255)'))
->ensureColumn(new rex_sql_column('status', 'varchar(255)'))
->ensureColumn(new rex_sql_column('erledigt', 'varchar(255)'))
->alter();



$error = '';
// Überprüfungen

if(!$error AND !$this->hasConfig()) {
  $this->setConfig('install', true);
  $this->setConfig('kanban',  'aktiviert');
}

