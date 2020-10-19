-- phpMyAdmin SQL Dump
-- version 4.4.14
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vytvořeno: Úte 22. bře 2016, 15:33
-- Verze serveru: 5.6.22
-- Verze PHP: 5.6.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáze: `vizualizace_faktury_mf_2016`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `dodavatel`
--

CREATE TABLE IF NOT EXISTS `dodavatel` (
  `id` varchar(10) COLLATE utf8_czech_ci NOT NULL COMMENT 'ID',
  `ico_st` varchar(8) COLLATE utf8_czech_ci NOT NULL COMMENT 'IČO',
  `nazev_st` varchar(255) COLLATE utf8_czech_ci NOT NULL COMMENT 'Název'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Dodavatelé';

-- --------------------------------------------------------

--
-- Struktura tabulky `etl`
--

CREATE TABLE IF NOT EXISTS `etl` (
  `id` int(11) NOT NULL COMMENT 'ID importu',
  `timestamp_dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Datum a čas',
  `success_in` tinyint(1) NOT NULL COMMENT 'Úspěch',
  `updated_in` tinyint(1) DEFAULT NULL COMMENT 'Aktualizováno',
  `forced_in` tinyint(1) DEFAULT NULL,
  `error_tx` text COLLATE utf8_czech_ci COMMENT 'Chyba',
  `endpoint_st` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'URL metadat',
  `resource_st` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'URL souboru',
  `imported_no` int(11) NOT NULL DEFAULT '0' COMMENT 'Importováno',
  `affected_no` int(11) NOT NULL DEFAULT '0' COMMENT 'Ovlivněno',
  `time_no` int(11) NOT NULL COMMENT 'Doba importu [s]',
  `size_no` int(11) DEFAULT NULL COMMENT 'Velikost',
  `last_modified_dt` timestamp NULL DEFAULT NULL COMMENT 'Poslední změna zdroje'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='ETL';

-- --------------------------------------------------------

--
-- Struktura tabulky `faktura`
--

CREATE TABLE IF NOT EXISTS `faktura` (
  `id` varchar(20) COLLATE utf8_czech_ci NOT NULL COMMENT 'UID Faktury',
  `dodavatel_id` varchar(10) COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'Dodavatel',
  `typ_dokladu_st` varchar(40) COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'Typ dokladu',
  `rozliseni_st` varchar(40) COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'Rozlišení',
  `evidence_dph_in` tinyint(1) DEFAULT NULL COMMENT 'Evidence DPH',
  `castka_am` decimal(14,2) DEFAULT NULL COMMENT 'Částka',
  `castka_bez_dph_am` decimal(14,2) DEFAULT NULL COMMENT 'Částka bez DPH',
  `castka_orig_am` decimal(14,2) DEFAULT NULL COMMENT 'Částka (originální)',
  `uhrazeno_am` decimal(14,2) DEFAULT NULL COMMENT 'Uhrazeno',
  `uhrazeno_orig_am` decimal(14,2) DEFAULT NULL COMMENT 'Uhrazeno (originální)',
  `mena_curr` varchar(4) COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'Měna',
  `vystaveno_dt` timestamp NULL DEFAULT NULL COMMENT 'Datum vystavení',
  `prijato_dt` timestamp NULL DEFAULT NULL COMMENT 'Datum přijetí',
  `splatnost_dt` timestamp NULL DEFAULT NULL COMMENT 'Datum splatnosti',
  `uhrazeno_dt` timestamp NULL DEFAULT NULL COMMENT 'Datum uhrazení',
  `ucel_tx` text COLLATE utf8_czech_ci COMMENT 'Účel'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Faktury';

-- --------------------------------------------------------

--
-- Struktura tabulky `faktura_polozka`
--

CREATE TABLE IF NOT EXISTS `faktura_polozka` (
  `faktura_id` varchar(20) COLLATE utf8_czech_ci NOT NULL COMMENT 'Faktura',
  `polozka_id` int(4) NOT NULL COMMENT 'Rozpočtová položka',
  `castka_am` decimal(14,2) DEFAULT NULL COMMENT 'Částka'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Vazba faktura - položka';

-- --------------------------------------------------------

--
-- Struktura tabulky `polozka`
--

CREATE TABLE IF NOT EXISTS `polozka` (
  `id` int(4) NOT NULL COMMENT 'ID',
  `nazev_st` varchar(255) COLLATE utf8_czech_ci NOT NULL COMMENT 'Název'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Rozpočtové položky';

-- --------------------------------------------------------

--
-- Struktura tabulky `raw_load`
--

CREATE TABLE IF NOT EXISTS `raw_load` (
  `uid` int(11) NOT NULL,
  `faktura_id` varchar(20) COLLATE utf8_czech_ci NOT NULL COMMENT 'UID Faktury',
  `dodavatel_id` varchar(10) COLLATE utf8_czech_ci DEFAULT NULL,
  `typ_dokladu_st` varchar(40) COLLATE utf8_czech_ci DEFAULT NULL,
  `rozliseni_st` varchar(40) COLLATE utf8_czech_ci DEFAULT NULL,
  `evidence_dph_in` tinyint(1) DEFAULT NULL,
  `castka_am` decimal(14,2) DEFAULT NULL,
  `castka_bez_dph_am` decimal(14,2) DEFAULT NULL,
  `castka_orig_am` decimal(14,2) DEFAULT NULL,
  `uhrazeno_am` decimal(14,2) DEFAULT NULL,
  `uhrazeno_orig_am` decimal(14,2) DEFAULT NULL,
  `mena_curr` varchar(4) COLLATE utf8_czech_ci DEFAULT NULL,
  `vystaveno_dt` timestamp NULL DEFAULT NULL,
  `prijato_dt` timestamp NULL DEFAULT NULL,
  `splatnost_dt` timestamp NULL DEFAULT NULL,
  `uhrazeno_dt` timestamp NULL DEFAULT NULL,
  `ucel_tx` text COLLATE utf8_czech_ci,
  `dodavatel_ico_st` varchar(8) COLLATE utf8_czech_ci DEFAULT NULL,
  `dodavatel_nazev_st` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `polozka_id` int(4) DEFAULT NULL,
  `polozka_castka_am` decimal(14,2) DEFAULT NULL,
  `polozka_nazev_st` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Importovací tabulka';

-- --------------------------------------------------------

--
-- Struktura tabulky `skupina`
--

CREATE TABLE IF NOT EXISTS `skupina` (
  `id` varchar(80) COLLATE utf8_czech_ci NOT NULL COMMENT 'ID',
  `nazev_st` varchar(255) COLLATE utf8_czech_ci NOT NULL COMMENT 'Název',
  `popis_tx` text COLLATE utf8_czech_ci NOT NULL COMMENT 'Popis',
  `x` int(11) NOT NULL COMMENT 'X souřadnice bubliny',
  `y` int(11) NOT NULL COMMENT 'Y souřadnice bubliny',
  `barva` varchar(40) COLLATE utf8_czech_ci NOT NULL COMMENT 'Barva bubliny'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Skupiny položek';

-- --------------------------------------------------------

--
-- Struktura tabulky `skupina_polozka`
--

CREATE TABLE IF NOT EXISTS `skupina_polozka` (
  `polozka_id` int(4) NOT NULL COMMENT 'Položka',
  `skupina_id` varchar(80) COLLATE utf8_czech_ci NOT NULL COMMENT 'Skupina'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Vazba skupina - položka';

-- --------------------------------------------------------

--
-- Struktura tabulky `stranka`
--

CREATE TABLE IF NOT EXISTS `stranka` (
  `id` varchar(80) COLLATE utf8_czech_ci NOT NULL COMMENT 'ID',
  `titulek` varchar(255) COLLATE utf8_czech_ci NOT NULL COMMENT 'Titulek',
  `obsah` text COLLATE utf8_czech_ci NOT NULL COMMENT 'Obsah (Texy!)',
  `posledni_zmena` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Datum poslední změny'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Stránky';

--
-- Klíče pro exportované tabulky
--

--
-- Klíče pro tabulku `dodavatel`
--
ALTER TABLE `dodavatel`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ico` (`ico_st`);

--
-- Klíče pro tabulku `etl`
--
ALTER TABLE `etl`
  ADD PRIMARY KEY (`id`);

--
-- Klíče pro tabulku `faktura`
--
ALTER TABLE `faktura`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dodavatel_id` (`dodavatel_id`);

--
-- Klíče pro tabulku `faktura_polozka`
--
ALTER TABLE `faktura_polozka`
  ADD PRIMARY KEY (`faktura_id`,`polozka_id`),
  ADD KEY `polozka_id` (`polozka_id`);

--
-- Klíče pro tabulku `polozka`
--
ALTER TABLE `polozka`
  ADD PRIMARY KEY (`id`);

--
-- Klíče pro tabulku `raw_load`
--
ALTER TABLE `raw_load`
  ADD PRIMARY KEY (`uid`);

--
-- Klíče pro tabulku `skupina`
--
ALTER TABLE `skupina`
  ADD PRIMARY KEY (`id`);

--
-- Klíče pro tabulku `skupina_polozka`
--
ALTER TABLE `skupina_polozka`
  ADD PRIMARY KEY (`polozka_id`,`skupina_id`),
  ADD KEY `skupina_id` (`skupina_id`);

--
-- Klíče pro tabulku `stranka`
--
ALTER TABLE `stranka`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `etl`
--
ALTER TABLE `etl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID importu';
--
-- AUTO_INCREMENT pro tabulku `raw_load`
--
ALTER TABLE `raw_load`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT;
--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `faktura`
--
ALTER TABLE `faktura`
  ADD CONSTRAINT `faktura_ibfk_1` FOREIGN KEY (`dodavatel_id`) REFERENCES `dodavatel` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Omezení pro tabulku `faktura_polozka`
--
ALTER TABLE `faktura_polozka`
  ADD CONSTRAINT `faktura_polozka_ibfk_2` FOREIGN KEY (`polozka_id`) REFERENCES `polozka` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `faktura_polozka_ibfk_3` FOREIGN KEY (`faktura_id`) REFERENCES `faktura` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Omezení pro tabulku `skupina_polozka`
--
ALTER TABLE `skupina_polozka`
  ADD CONSTRAINT `skupina_polozka_ibfk_1` FOREIGN KEY (`skupina_id`) REFERENCES `skupina` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
