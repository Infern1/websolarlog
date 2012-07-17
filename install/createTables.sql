CREATE TABLE IF NOT EXISTS `production1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datetime` timestamp NULL DEFAULT NULL,
  `kwh` float(5,3) DEFAULT NULL,
  `insertDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);


CREATE TABLE IF NOT EXISTS `live` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datetime` datetime NOT NULL,
  `mpp1Voltage` varchar(15) NOT NULL,
  `mpp1Current` varchar(15) NOT NULL,
  `mpp1Power` varchar(15) NOT NULL,
  `mpp2Voltage` varchar(15) NOT NULL,
  `mpp2Current` varchar(15) NOT NULL,
  `mpp2Power` varchar(15) NOT NULL,
  `gridVoltage` varchar(15) NOT NULL,
  `gridCurrent` varchar(15) NOT NULL,
  `gridPower` varchar(15) NOT NULL,
  `gridFrequency` varchar(15) NOT NULL,
  `invEfficiency` varchar(15) NOT NULL,
  `invTemp` varchar(15) NOT NULL,
  `boosTemp` varchar(15) NOT NULL,
  `kwht` varchar(15) NOT NULL,
  `azimuth` varchar(20) DEFAULT NULL,
  `altitude` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
);