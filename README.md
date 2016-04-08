# Supervizor Nette implementace
Supervizor je aplikace na vizualizaci výdajů státní správy, kerou vytvořili pracovníci Ministerstva financí České republiky. Uvítáme další použití i úpravy/opravy zdrojového kódu, proto ho dáváme k dispozici pod licencí GNU GPL v3


![Header](http://temp.smallhill.cz/supervizor-heading-github.png)

## Requirements
  * PHP 5.6>
  * MySQL / MariaDB / PostgreSQL
  * Nginx / Apache
  * Linux

## Install
  1. `composer install`
  2. `bower install`
  3. create `/app/config/config.local.neon` with own parameters, from `/app/config/config.local.neon.example` template
  4. run `php www/index.php orm:s:c` for create default database schema
  5. run `php www/index.php orm:default-data:load` for load default data. WARNING: this command DROP ALL DATA IN DATABASE!
  