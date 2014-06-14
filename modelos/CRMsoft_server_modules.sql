

CREATE TABLE IF NOT EXISTS `CRMsoft_server_modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idinstall` int(11) NOT NULL,
  `idmodule` int(11) NOT NULL,
  `estatus` enum('activo','inactivo') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

