
CREATE TABLE IF NOT EXISTS `CRMsoft_modules` (
  `idmodule` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `idsoft` int(11) NOT NULL,
  PRIMARY KEY (`idmodule`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `CRMsoft_modules`
--

INSERT INTO `CRMsoft_modules` (`idmodule`, `nombre`, `idsoft`) VALUES
(1, 'Estable', 1),
(2, 'Credito y Prepago', 3),
(3, 'Caja Chica', 2),
(4, 'Facturacion', 4),
(5, 'Pendientes Administrativos', 1),
(6, 'Recordatorios', 2),
(7, 'Base', 3);
