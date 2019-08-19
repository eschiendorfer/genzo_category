/* Install */

CREATE TABLE IF NOT EXISTS `PREFIX_genzo_category` (
  `id_category` INT(12) NOT NULL,
  PRIMARY KEY ( `id_category` )
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=CHARSET_TYPE;

CREATE TABLE IF NOT EXISTS `PREFIX_genzo_category_lang` (
  `id_category` INT(12) NOT NULL,
  `id_shop` INT(12) NOT NULL,
  `id_lang` INT(12) NOT NULL,
  `footer_description` VARCHAR(100000) NOT NULL,
  PRIMARY KEY ( `id_category` )
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=CHARSET_TYPE;