
CREATE TABLE IF NOT EXISTS `CRMalerts` (
  `idalert` int(11) NOT NULL AUTO_INCREMENT COMMENT 'identificador de alerta',
  `eid` int(11) NOT NULL COMMENT 'identificador de identidad',
  `iduser` int(11) NOT NULL COMMENT 'usuario creador',
  `severidad` int(11) NOT NULL COMMENT 'indicador de severidad',
  `createdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` int(11) NOT NULL COMMENT 'indicador de ststus',
  PRIMARY KEY (`idalert`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

