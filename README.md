# Supervizor
Vizualizace výdajů státní správy

## Obsah repozitáře

**//mysql** - soubory pro tvorbu databázové struktury

**//www - zdrojové kódy aplikace určené ke spuštění bez instalace (nikoliv bez nastavení základních konfiguračních údajů

## Nastavení

## Import
- Součátní aplikace je automatický mechanismus na import faktur z CSV souborů.
- Soubory musí být v kódování UTF-8 (lze přepsat v [Mappingu](#mapping))
- Proces, jakým probíhá napojení jednotlivých polí je popsán v sekci [Mapping](#mapping)
- Systém volby cílového profilu je popsán v sekci Profily

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
  - 
### Mapping
Napojení CSV
