/* Update to 1.1.0 */

ALTER TABLE `PREFIX_genzo_category_lang`
    DROP COLUMN id_genzo_category,
    ADD PRIMARY KEY (`id_category`,`id_lang`,`id_shop`);

CREATE TABLE IF NOT EXISTS `PREFIX_genzo_category` (
    `id_category` INT(12) NOT NULL,
    PRIMARY KEY ( `id_category` )
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=CHARSET_TYPE;