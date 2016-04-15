<?php

rex_sql_table::get("rex_aufgaben_aufgaben")
->ensureColumn(new rex_sql_column('titel', 'varchar(255)'))
->ensureColumn(new rex_sql_column('beschreibung', 'longtext'))
->ensureColumn(new rex_sql_column('kategorie', 'int(10)'))
->ensureColumn(new rex_sql_column('eigentuemer', 'int(10)'))
->ensureColumn(new rex_sql_column('prio', 'int(10)'))
->ensureColumn(new rex_sql_column('status', 'int(10)'))
->ensureColumn(new rex_sql_column('createdate', 'DATETIME'))
->ensureColumn(new rex_sql_column('updatedate', 'DATETIME'))
->ensureColumn(new rex_sql_column('createuser', 'varchar(255)'))
->ensureColumn(new rex_sql_column('updateuser', 'varchar(255)'))
->alter();


rex_sql_table::get("rex_aufgaben_kategorien")
->ensureColumn(new rex_sql_column('kategorie', 'varchar(255)'))
->ensureColumn(new rex_sql_column('farbe', 'varchar(255)'))
->alter();

rex_sql_table::get("rex_aufgaben_status")
->ensureColumn(new rex_sql_column('status', 'varchar(255)'))
->ensureColumn(new rex_sql_column('icon', 'varchar(255)'))
->alter();

rex_sql_table::get("rex_aufgaben_filter")
->ensureColumn(new rex_sql_column('user', 'int(10)'))
->ensureColumn(new rex_sql_column('kategorie', 'int(10)'))
->ensureColumn(new rex_sql_column('eigentuemer', 'int(10)'))
->ensureColumn(new rex_sql_column('prio', 'int(10)'))
->ensureColumn(new rex_sql_column('status', 'int(10)'))
->ensureColumn(new rex_sql_column('erledigt', 'int(10)'))
->alter();
