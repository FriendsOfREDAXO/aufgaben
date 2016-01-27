-- Aufgaben

CREATE TABLE IF NOT EXISTS `%TABLE_PREFIX%aufgaben_aufgaben` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `titel` varchar(255) DEFAULT NULL,
    `beschreibung` longtext DEFAULT NULL,
    `kategorie` int(10) DEFAULT NULL,
    `eigentuemer` int(10) DEFAULT NULL,
    `prio` int(10) DEFAULT NULL,
    `status` int(10) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- Kategorien

CREATE TABLE IF NOT EXISTS `%TABLE_PREFIX%aufgaben_kategorien` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `kategorie` varchar(255) DEFAULT NULL,
    `farbe` varchar(255) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- Status

CREATE TABLE IF NOT EXISTS `%TABLE_PREFIX%aufgaben_status` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `status` varchar(255) DEFAULT NULL,
    `icon` varchar(255)  DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `%TABLE_PREFIX%aufgaben_filter` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `user` int(10) DEFAULT NULL,
    `kategorie` int(10)  DEFAULT NULL,
    `eigentuemer` int(10)  DEFAULT NULL,
    `prio` int(10)  DEFAULT NULL,
    `status` int(10)  DEFAULT NULL,
    `erledigt` int(10)  DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- Status Inhalte

REPLACE INTO `%TABLE_PREFIX%aufgaben_status` VALUES
    (1,'Offen','fa-folder-open-o'),
    (2,'Wird bearbeitet','fa-gears'),
    (3,'Frage','fa-question'),
    (4,'Warten auf etwas','fa-hourglass-start'),
    (5,'Auf später verschoben','fa-calendar'),
    (6,'Erledigt','fa-check');

/*
-- Aufgaben

INSERT IGNORE `%TABLE_PREFIX%aufgaben_aufgaben` VALUES
    (1, 'Fav Icon erstellen', 'Wird immer benötigt',1,1,0,1),
    (2, 'Touch Icon erstellen', '',1,1,0,1),
    (3, 'Meta Infos erstellen', 'Sind Ortsbezogene meta Infos wichtig?',1,1,0,1),
    (4, 'Print.css entwickeln', 'Wird immer vergessen',1,1,0,1),
    (5, 'robots.txt prüfen', ':-)',7,1,0,1);

-- Kategorien

INSERT IGNORE `%TABLE_PREFIX%aufgaben_kategorien` VALUES
    (1,'Grundlagen','#9EAEC2'),
    (2,'Backend','#588D76'),
    (3,'Design','#8D588A'),
    (4,'Funktion','#9EAEC2'),
    (5,'Fehler','#72A3A7'),
    (6,'Wunsch','#FFD83D'),
    (7,'SEO','#437047');
*/