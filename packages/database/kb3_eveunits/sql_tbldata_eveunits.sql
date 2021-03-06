TRUNCATE `kb3_eveunits`;
INSERT IGNORE INTO `kb3_eveunits` VALUES
('1', 'Length', 'm', 'Meter'),
('2', 'Mass', 'kg', 'Kilogram'),
('3', 'Time', 'sec', 'Second'),
('4', 'Electric Current', 'A', 'Ampere'),
('5', 'Temperature', 'K', 'Kelvin'),
('6', 'Amount Of Substance', 'mol', 'Mole'),
('7', 'Luminous Intensity', 'cd', 'Candela'),
('8', 'Area', 'm2', 'Square meter'),
('9', 'Volume', 'm3', 'Cubic meter'),
('10', 'Speed', 'm/sec', 'Meter per second'),
('11', 'Acceleration', 'm/sec', 'Meter per second squared'),
('12', 'Wave Number', 'm-1', 'Reciprocal meter'),
('13', 'Mass Density', 'kg/m3', 'Kilogram per cubic meter'),
('14', 'Specific Volume', 'm3/kg', 'Cubic meter per kilogram'),
('15', 'Current Density', 'A/m2', 'Ampere per square meter'),
('16', 'Magnetic Field Strength', 'A/m', 'Ampere per meter'),
('17', 'Amount-Of-Substance Concentration', 'mol/m3', 'Mole per cubic meter'),
('18', 'Luminance', 'cd/m2', 'Candela per square meter'),
('19', 'Mass Fraction', 'kg/kg = 1', 'Kilogram per kilogram, which may be represented by the number 1'),
('101', 'Milliseconds', 's', ''),
('102', 'Millimeters', 'mm', ''),
('103', 'MegaPascals', '', ''),
('104', 'Multiplier', 'x', 'Indicates that the unit is a multiplier.'),
('105', 'Percentage', '%', ''),
('106', 'Teraflops', 'tf', ''),
('107', 'MegaWatts', 'MW', ''),
('108', 'Inverse Absolute Percent', '%', 'Used for resistance.\r\n0.0 = 100% 1.0 = 0%\r\n'),
('109', 'Modifier Percent', '%', 'Used for multipliers displayed as %\r\n1.1 = +10%\r\n0.9 = -10%'),
('111', 'Inversed Modifier Percent', '%', 'Used to modify damage resistance. Damage resistance bonus.\r\n0.1 = 90%\r\n0.9 = 10%'),
('112', 'Radians/Second', 'rad/sec', 'Rotation speed.'),
('113', 'Hitpoints', 'HP', ''),
('114', 'capacitor units', 'GJ', 'Giga Joule'),
('115', 'groupID', 'groupID', ''),
('116', 'typeID', 'typeID', ''),
('117', 'Sizeclass', '1=small 2=medium 3=l', ''),
('118', 'Ore units', 'Ore units', ''),
('119', 'attributeID', 'attributeID', ''),
('120', 'attributePoints', 'points', ''),
('121', 'realPercent', '%', 'Used for real percentages, i.e. the number 5 is 5%'),
('122', 'Fitting slots', '', ''),
('123', 'trueTime', 'sec', 'Shows seconds directly'),
('124', 'Modifier Relative Percent', '%', 'Used for relative percentages displayed as %'),
('125', 'Newton', 'N', ''),
('126', 'Light Year', 'ly', ''),
('127', 'Absolute Percent', '%', '0.0 = 0% 1.0 = 100%'),
('128', 'Drone bandwidth', 'Mbit/sec', 'Mega bits per second'),
('129', 'Hours', '', 'Hours'),
('133', 'Money', 'ISK', 'ISK'),
('134', 'Logistical Capacity', 'm3/hour', 'Bandwidth for PI'),
('135', 'Astronomical Unit', 'AU', 'Used to denote distance, 1AU = The distance from the Earth to the Sun.'),
('136', 'Slot', 'Slot', 'Slot number prefix for various purposes'),
('137', 'Boolean', '1=True 0=False', 'For displaying boolean flags'),
('138', 'Units', 'units', 'Units of something, for example fuel'),
('139', 'Bonus', '+', 'Forces a plus sign for positive values'),
('140', 'Level', 'Level', 'For anything which is divided by levels'),
('141', 'Hardpoints', 'hardpoints', 'For various counts to do with turret, launcher and rig hardpoints'),
('142', 'Sex', '1=Male 2=Unisex 3=Female', '');
