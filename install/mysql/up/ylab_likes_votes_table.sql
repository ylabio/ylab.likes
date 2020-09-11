CREATE TABLE `ylab_likes_votes` (
  `ID`           INT        NOT NULL AUTO_INCREMENT,
  `CONTENT_ID`   INT(11)    NOT NULL
  COMMENT 'ID Контента',
  `CONTENT_TYPE` SMALLINT NOT NULL
  COMMENT 'Тип контента (см. Константы орм класса)',
  `USER_ID`      INT(11)    NOT NULL
  COMMENT 'ID пользователя',
  `VOTE`         TINYINT(4) NOT NULL
  COMMENT '0-Дизлайк 1-Лайк',
  INDEX `LIKES_INDEX` (`CONTENT_ID`, `CONTENT_TYPE`, `USER_ID`, `VOTE`),
  PRIMARY KEY (`ID`)
)
  COLLATE = 'utf8_unicode_ci'
  ENGINE = InnoDB;