/* Install */

CREATE TABLE IF NOT EXISTS `PREFIX_genzo_category_lang` (
  `id_genzo_category` INT(12) AUTO_INCREMENT,
  `id_category` INT(12) NOT NULL,
  `id_shop` INT(12) NOT NULL,
  `id_lang` INT(12) NOT NULL,
  `footer_description` VARCHAR(100000) NOT NULL,
  PRIMARY KEY ( `id_genzo_category` )
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=CHARSET_TYPE;