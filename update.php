<?php

rex_sql_table::get("rex_aufgaben")
->ensureColumn(new rex_sql_column('title', 'varchar(255)'))
->ensureColumn(new rex_sql_column('description', 'longtext'))
->ensureColumn(new rex_sql_column('category', 'int(10)'))
->ensureColumn(new rex_sql_column('responsible', 'int(10)'))
->ensureColumn(new rex_sql_column('prio', 'int(10)'))
->ensureColumn(new rex_sql_column('status', 'int(10)'))
->ensureColumn(new rex_sql_column('createdate', 'DATETIME'))
->ensureColumn(new rex_sql_column('updatedate', 'DATETIME'))
->ensureColumn(new rex_sql_column('createuser', 'varchar(255)'))
->ensureColumn(new rex_sql_column('updateuser', 'varchar(255)'))
->ensureColumn(new rex_sql_column('observer', 'varchar(255)'))
->ensureColumn(new rex_sql_column('finaldate', 'varchar(255)'))
->alter();


rex_sql_table::get("rex_aufgaben_categories")
->ensureColumn(new rex_sql_column('category', 'varchar(255)'))
->ensureColumn(new rex_sql_column('color', 'varchar(255)'))
->alter();


rex_sql_table::get("rex_aufgaben_status")
->ensureColumn(new rex_sql_column('status', 'varchar(255)'))
->ensureColumn(new rex_sql_column('icon', 'varchar(255)'))
->alter();

rex_sql_table::get("rex_aufgaben_filter")
->ensureColumn(new rex_sql_column('user', 'varchar(255)'))
->ensureColumn(new rex_sql_column('category', 'varchar(255)'))
->ensureColumn(new rex_sql_column('responsible', 'varchar(255)'))
->ensureColumn(new rex_sql_column('prio', 'varchar(255)'))
->ensureColumn(new rex_sql_column('status', 'varchar(255)'))
->ensureColumn(new rex_sql_column('done', 'varchar(255)'))
->alter();

rex_sql_table::get("rex_aufgaben_user_settings")
->ensureColumn(new rex_sql_column('user', 'int(10)'))
->ensureColumn(new rex_sql_column('counter', 'int(10)'))
->alter();

rex_sql_table::get("rex_aufgaben_status")
->ensureColumn(new rex_sql_column('user', 'int(10)'))
->ensureColumn(new rex_sql_column('counter', 'int(10)'))
->alter();
