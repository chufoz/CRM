

CREATE TABLE IF NOT EXISTS `CRMsoft_categories` (
  `idcategory` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(100) NOT NULL,
  PRIMARY KEY (`idcategory`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Volcado de datos para la tabla `CRMsoft_categories`
--

INSERT INTO `CRMsoft_categories` (`idcategory`, `descripcion`) VALUES
(1, 'Web'),
(2, 'Dispensarios'),
(3, 'Servicio'),
(4, 'Base de Datos'),
(5, 'Escritorio'),
(6, 'Embebido'),
(7, 'Otro');
