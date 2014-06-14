

CREATE TABLE IF NOT EXISTS `CRMsoft_changelog` (
  `idlog` int(11) NOT NULL AUTO_INCREMENT,
  `custid` int(11) NOT NULL,
  `descripcion` text NOT NULL,
  PRIMARY KEY (`idlog`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
