
CREATE TABLE IF NOT EXISTS `CRMsoft_server` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `custid` int(11) NOT NULL,
  `idsoft` int(11) NOT NULL,
  `version` varchar(50) NOT NULL,
  `estatus` enum('activo','inactivo') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;


