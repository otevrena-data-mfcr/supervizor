# Supervizor Nette implementace
Supervizor je aplikace na vizualizaci výdajů, kerou vytvořili pracovníci Ministerstva financí České republiky. Samotné ministerstvo zveřejňuje své výdaje na [http://data.mfcr.cz/supervizor/](http://data.mfcr.cz/supervizor/) 

Uvítáme další použití i úpravy/opravy zdrojového kódu, proto ho dáváme k dispozici pod licencí GNU GPL v3


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
  4. run `chmod -R a+rw temp log www/webtemp`
  5. run `php www/index.php orm:s:c` to create default database schema
  6. run `php www/index.php orm:default-data:load` to load default data. WARNING: this command DROP ALL DATA IN DATABASE!
  7. run `php www/index.php importer:import:all` to import mf2016 data configured in `app/config/importer.neon` (you can configure your imports there)


## Your own data source

For you own data source you will need two things:

   1. Create your own data parser in `extensions/Importer/parsers/`, you can use `Mfcr.php` as example
   2. Configure your data sources in `config/importer.neon` :

   ```neon
   importer:
     target: App\Model\ImportTarget
     imports:
       # here you can configure your custom imports
       mf: #Import group key
           title: "Ministerstvo financí" #Import group name
           default: true # Is default import group ?
           datasets: # List of datasets
               mf2016: # Dataset key
                   title: "Rok 2016" #Data set title
                   description: "Přehled faktur Ministerstva financí" #Data set description
                   source: "http://data.mfcr.cz/cs/api/3/action/resource_show?id=aec18a6a-0d8f-49a4-a8e7-ae0fbd32125f" #Data set source, it can be file:// http:// ftp://
                   homepage: "http://data.mfcr.cz/cs/dataset/prehled-faktur-ministerstva-financi-cr" # Homepage of source (if any, used only for info)
                   parser: Extensions\Importer\Parsers\Mfcr #Parser used to parse this dataset
                   default: true # Is default ? (data from this dataset will be shown as default configuration when landing on homepate)
   ```


## Devel
   * run `php www/index.php orm:validate-schema` to validate doctrine schema
