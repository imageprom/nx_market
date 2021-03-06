CREATE TABLE IF NOT EXISTS `nx_update_status` (
  `ID` int(20) NOT NULL AUTO_INCREMENT,
  `NAME` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `STATUS` int(1) NOT NULL DEFAULT '0',
  `MODIFIED` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


INSERT INTO `nx_update_status` (`ID`, `NAME`, `STATUS`, `MODIFIED`) VALUES
(36, 'Rozn', 0, '2014-03-28 12:55:27'),
(50, 'Test', 0, '2014-03-28 12:56:24'); 

CREATE TABLE IF NOT EXISTS `nx_ostatki_test` (
  `ID` int(20) NOT NULL AUTO_INCREMENT,
  `XML_ID` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `ost_52` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `nx_ostatki_rozn` (
  `ID` int(20) NOT NULL AUTO_INCREMENT,
  `XML_ID` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `ost_52` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
