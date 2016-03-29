# Supervizor
Vizualizace výdajů státní správy

## Obsah repozitáře

**`//mysql` - soubory pro tvorbu databázové struktury**

`//www` - zdrojové kódy aplikace  
`//www/app` - jádro aplikace  
`//www/app/api`  
**`//www/app/import` - třída FakturyImport a [Mappingové](#mapping) soubory pro import**  
`//www/app/lib` - PHP knihovny  
`//www/app/tmp` - dočasné soubory  
**`//www/config` - statická nastavení aplikace**  
`//www/include` - šablony pro generování výstupu  
`//www/static` - statické veřejné soubory

## Nastavení

## Import
- Součátní aplikace je automatický mechanismus na import faktur z CSV souborů.
- Proces, jakým probíhá napojení jednotlivých polí je popsán v sekci [Mapping](#mapping)
- Systém volby cílového profilu je popsán v sekci [Profily](#profily)

### Profily
- Pro uložení více organizací a let jsou zvoleny identifikátory profil a dataset
- Tyto údaje se nastavují v souboru `//www/config/profiles.json` ve formátu JSON
- Soubor obsahuje JSON objekt kde jednotlivé názvy vlastností jsou identifikátory profilů a obsahem jsou objekty reprezentující profily
- Objekt profilu má následující vlastnosti:
  - `(string) title` - název datasetu k zobrazení
  - `(object) datasets` - objekt kde jednotlivé názvy vlastností jsou identifikátory datasetů (unikátrní v rámci profilu) a obsahemn jsou objekty reprezentující datasety
- Objekt datasetu má následující vlastnosti
  - `(string) title` - název datasetu k zobrazení
  - `(string) endpoint` - url adresa odkazující na metadata datové sady katalogu (použito pro [Mapping](#mapping))
  - `(string) database` - název databáze pro uložení dat
  - `(string) mapping` - identifikátor [Mappingu](#mapping)
  - `(string) source_name` - název datového zdroje k zobrazení
  - `(string) source_url` - url datového zdroje jako odkaz pro uživatele 

##Import

### Mapping
- Vytvořením Mappingu pro váš formát faktur definujete napojení polí faktury v CSV zdroji na pole v databázi Supervizoru
- Který Mapping se použije se nastaví v souboru [profiles.json](#profily)
- Mapping je PHP soubor ve složce `//www/app/import` s názvem `FakturyMapping_XXXX.php`, kde XXXX je identifikátor Mappingu
- Soubor obsahuje třídu `FakturyMapping_XXXX` (stejný identifikátor), která implementuje rozhraní `IFakturyMapping`
- Třída `FakturyMapping_XXXX`:
  - metoda `getTimestamp()` - vrátí datum a čas poslední aktualizace dat na datovém zdroji ve formátu UNIX time
  - metoda `setSource( (string) $source )` - nastaví zdroj dat; jako parametr dostane údaj endpoint z [objektu datasetu v profiles.json](#profily)
  - metoda `import( FakturyImport $fi )` - provede import zápis do databáze provádí pomocí metod insertRow() a insertRows() dodaného objektu třídy `FakturyImport`
- Třída `FakturyImport`:
  - metoda `insertRow( (array) $row )`
    - metoda kterou se vloží jedna faktura do databáze
    - parametrem je pole, kde klíče jsou názvy položek databáze
    - array( pole1 => hodnota1, pole2 => hodnota2, ...)   
  -  metoda `insertRows( (array) $rows )`
    - metoda kterou se vloží více faktur do databáze v rámci jedné žádosti
    - parametrem je pole polí, kde klíče jsou názvy položek databáze
    - array(0 => array( pole1 => hodnota1, pole2 => hodnota2, ...))    
