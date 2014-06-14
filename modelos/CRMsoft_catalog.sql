

CREATE TABLE IF NOT EXISTS `CRMsoft_catalog` (
  `idsoft` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `idcategory` int(11) NOT NULL,
  PRIMARY KEY (`idsoft`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 ;

--
-- Volcado de datos para la tabla `CRMsoft_catalog`
--

INSERT INTO `CRMsoft_catalog` (`idsoft`, `nombre`, `descripcion`, `idcategory`) VALUES
(1, 'Emax', 'This is Sparta', 1),
(2, 'CRM', 'Software para control de Servicio aClientes', 3),
(3, 'Bugtracker', 'Software para control de Bugs Interno2', 1),
(4, 'Cvmax', 'nose para que fnciona', 1),
(5, 'Kraken', 'software para dispensario', 2),
(6, 'Pixis', 'Software para dispensario', 2);
