/* Install */
CREATE TABLE IF NOT EXISTS `PREFIX_genzo_category` (
  `id_category_helper` INT(12) NOT NULL AUTO_INCREMENT,
  `id_category` INT(12) NOT NULL,
  PRIMARY KEY (`id_category_helper`),
  INDEX id_category (`id_category`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=CHARSET_TYPE;

CREATE TABLE IF NOT EXISTS `PREFIX_genzo_category_lang` (
  `id_category_helper` INT(12) NOT NULL,
  `id_shop` INT(12) NOT NULL,
  `id_lang` INT(12) NOT NULL,
  `footer_description` VARCHAR(100000) NOT NULL,
  PRIMARY KEY (`id_category_helper`,`id_shop`,`id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=CHARSET_TYPE;