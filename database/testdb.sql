

CREATE TABLE `Budgets` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user` varchar(40) NOT NULL,
  `budname` varchar(60) NOT NULL,
  `budpos` smallint(6) NOT NULL,
  `status` varchar(1) NOT NULL,
  `budamt` smallint(6) NOT NULL,
  `prev0` decimal(8,2) DEFAULT NULL,
  `prev1` decimal(8,2) DEFAULT NULL,
  `current` decimal(8,2) NOT NULL,
  `autopay` varchar(30) DEFAULT NULL,
  `moday` tinyint(4) DEFAULT NULL,
  `autopd` varchar(10) DEFAULT NULL,
  `funded` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=63 DEFAULT CHARSET=latin1;


INSERT INTO Budgets VALUES
('1','krc','Tithe','1','A','180','0.00','0.00','0.00','','0','','180'),
('2','krc','Mortgage','2','A','1655','0.00','0.00','3.96','','0','','1655'),
('3','krc','Electric','4','A','70','0.00','0.00','84.11','','0','','70'),
('4','krc','Natural Gas','5','A','50','0.00','0.00','147.22','','0','','50'),
('5','krc','Auto Gas','6','A','125','0.00','0.00','239.42','','0','','125'),
('6','krc','Pres HMO','7','A','38','0.00','0.00','38.00','Visa','25','','38'),
('7','krc','Chiro','8','A','151','0.00','0.00','9.78','Visa','20','12','151'),
('8','krc','Medical/Dental','9','A','150','0.00','0.00','5.61','','0','','150'),
('9','krc','Cash','10','A','240','0.00','0.00','0.00','','0','','240'),
('10','krc','TV/Internet','11','A','160','0.00','0.00','24.54','Wells Fargo','16','12','160'),
('11','krc','Auto Insurance','12','A','130','0.00','0.00','795.30','','0','','130'),
('12','krc','Auto Maintenance','13','A','150','0.00','0.00','-482.76','','0','','150'),
('13','krc','Appliances/Repair','14','A','120','0.00','0.00','1080.32','','0','','120'),
('14','krc','Home Misc','15','A','350','0.00','0.00','27.30','','0','','350'),
('15','krc','Verizon','16','A','70','0.00','0.00','17.21','Visa','7','12','70'),
('16','krc','Combined','17','A','165','0.00','0.00','326.53','','0','','165'),
('17','krc','Clothing','18','A','30','0.00','0.00','87.54','','0','','30'),
('18','krc','Health Items','19','A','50','0.00','0.00','25.71','','0','','50'),
('19','krc','Vision','20','A','50','0.00','0.00','689.22','','0','','20'),
('20','krc','Travel','22','A','250','0.00','0.00','588.71','','0','','250'),
('21','krc','Charities','23','A','30','0.00','0.00','80.00','','0','','30'),
('22','krc','Philately','25','A','20','0.00','0.00','251.51','','0','','20'),
('23','krc','Wine','26','A','25','0.00','0.00','-21.24','','0','','25'),
('24','krc','Entertainment','27','A','250','0.00','0.00','464.92','','0','','250'),
('25','krc','3C Annual Fee','28','A','44','0.00','0.00','500.00','','0','','44'),
('26','krc','Groceries','3','A','1000','0.00','0.00','428.60','','0','','558'),
('27','krc','Gifts','21','A','40','0.00','0.00','82.06','','0','','0'),
('28','krc','Christmas','24','A','100','0.00','0.00','268.72','','0','','0'),
('29','krc','Undistributed Funds','30000','T','0','0.00','0.00','4.93','','0','','0'),
('30','krc','Computer','30001','T','0','0.00','0.00','-2246.73','','0','','0'),
('31','krc','Escrow Account','30002','T','0','0.00','0.00','-32.69','','0','','0'),
('32','krc','Crowns','30003','T','0','0.00','0.00','0.00','','0','','0'),
('33','krc','Tmp4','30004','T','0','0.00','0.00','0.00','','0','','0'),
('34','krc','Tmp5','30005','T','0','0.00','0.00','0.00','','0','','0'),
('41','Albuquerque Gal','Undistributed Funds','30000','T','0','0.00','0.00','0.00','','0','','0'),
('42','Albuquerque Gal','Medicare','30001','T','0','0.00','0.00','4666.14','','0','','0'),
('43','Albuquerque Gal','Tmp2','30002','T','40','0.00','0.00','800.00','','0','','0'),
('44','Albuquerque Gal','Tmp3','30003','T','150','0.00','0.00','70.00','','0','','0'),
('45','Albuquerque Gal','Tmp4','30004','T','0','0.00','0.00','0.00','','0','','0'),
('46','Albuquerque Gal','Tmp5','30005','T','0','0.00','0.00','0.00','','0','','0'),
('47','Albuquerque Gal','cash','1','A','240','0.00','0.00','0.00','','0','','0'),
('48','Albuquerque Gal','vet/cat','2','A','123','0.00','0.00','725.00','','0','','0'),
('49','Albuquerque Gal','hair','3','A','11','0.00','0.00','0.00','','0','','0'),
('50','Albuquerque Gal','Gas','4','A','60','0.00','0.00','60.00','','0','','0'),
('51','Albuquerque Gal','Qi Gong','5','A','80','0.00','0.00','80.00','','0','','0'),
('52','Albuquerque Gal','Car maintenance ','6','A','70','0.00','0.00','800.00','','0','','0'),
('53','Albuquerque Gal','Dentist ','7','A','20','0.00','0.00','620.00','','0','','0'),
('54','Albuquerque Gal','Birthday','8','A','20','0.00','0.00','340.00','','0','','0'),
('55','Albuquerque Gal','Christmas','9','A','20','0.00','0.00','400.00','','0','','0'),
('56','Albuquerque Gal','MD/Glasses','10','A','20','0.00','0.00','180.00','','0','','0'),
('57','Albuquerque Gal','Bird','11','A','20','0.00','0.00','25.00','','0','','0'),
('58','Albuquerque Gal','Phone','12','A','67','0.00','0.00','67.00','','0','','0'),
('59','Albuquerque Gal','Vitamins','13','A','20','0.00','0.00','0.00','','0','','0'),
('60','Albuquerque Gal','Travel','14','A','20','0.00','0.00','300.00','','0','','0'),
('61','Albuquerque Gal','Misc','15','A','49','0.00','0.00','0.00','','0','','0'),
('62','Albuquerque Gal','Massage','16','A','20','0.00','0.00','200.00','','0','','0');




CREATE TABLE `Cards` (
  `cdindx` smallint(6) NOT NULL AUTO_INCREMENT,
  `user` varchar(40) NOT NULL,
  `cdname` varchar(30) NOT NULL,
  `type` varchar(6) NOT NULL,
  PRIMARY KEY (`cdindx`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;


INSERT INTO Cards VALUES
('1','krc','Visa','Credit'),
('2','krc','Citi','Credit'),
('3','krc','Wells Fargo','Debit'),
('9','Albuquerque Gal','MC','Credit'),
('10','Albuquerque Gal','Kohls','Credit'),
('11','Albuquerque Gal','Pennys','Credit');




CREATE TABLE `Charges` (
  `expid` mediumint(9) NOT NULL AUTO_INCREMENT,
  `user` varchar(40) NOT NULL,
  `method` varchar(30) NOT NULL,
  `cdname` varchar(30) NOT NULL,
  `expdate` date DEFAULT NULL,
  `expamt` decimal(8,2) NOT NULL,
  `payee` varchar(60) DEFAULT NULL,
  `acctchgd` varchar(60) NOT NULL,
  `paid` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`expid`)
) ENGINE=MyISAM AUTO_INCREMENT=70 DEFAULT CHARSET=latin1;


INSERT INTO Charges VALUES
('1','krc','Credit','Visa','2019-11-15','29.13','Moon\'s','Groceries','Y'),
('2','krc','Credit','Visa','2019-11-15','4.99','FileZilla','Combined','Y'),
('3','krc','Credit','Visa','2019-11-17','75.53','Amazon: Glyco','Health Items','Y'),
('4','krc','Credit','Visa','2019-11-20','150.23','HealthQuest','Chiro','Y'),
('5','krc','Credit','Visa','2019-11-21','28.83','Tiwa Gas','Auto Gas','Y'),
('6','krc','Credit','Visa','2019-11-21','449.34','Garcia Subaru','Auto Maintenance','Y'),
('7','krc','Credit','Visa','2019-11-25','38.00','Presbyterian','Pres HMO','Y'),
('8','krc','Credit','Visa','2019-11-30','54.16','Amazon - Lara Bars','Groceries','Y'),
('9','krc','Credit','Visa','2019-11-30','61.73','NuViews / Karen XMas','Christmas','Y'),
('11','krc','Debit','Wells Fargo','2019-12-04','120.00','Cash','Cash','N'),
('12','krc','Credit','Citi','2019-12-05','59.58','TSC','Groceries','N'),
('13','krc','Credit','Citi','2019-12-05','48.25','Albertsons','Groceries','N'),
('14','krc','Credit','Citi','2019-12-05','61.86','Albertsons','Groceries','N'),
('15','krc','Credit','Citi','2019-12-05','45.97','Sprouts','Groceries','N'),
('16','krc','Credit','Visa','2019-12-05','50.21','CVS','Health Items','Y'),
('17','krc','Credit','Visa','2019-12-05','44.29','Barnes & Noble','Christmas','Y'),
('18','krc','Credit','Visa','2019-12-05','33.30','Lowe\'s','Home Misc','Y'),
('19','krc','Credit','Citi','2019-12-05','37.43','Albertsons','Groceries','N'),
('20','krc','Credit','Citi','2019-12-05','7.77','Albertsons','Groceries','N'),
('21','krc','Credit','Citi','2019-12-05','28.57','Sprouts','Groceries','N'),
('22','krc','Credit','Visa','2019-12-05','46.17','Whatever Works','Christmas','Y'),
('23','krc','Credit','Visa','2019-12-05','69.37','Amazon','Christmas','Y'),
('24','krc','Check','check','2019-12-06','57.62','PNM','Electric','N'),
('25','krc','Check','check','2019-12-06','70.40','NM Gas Co','Natural Gas','N'),
('26','krc','Credit','Visa','2019-12-06','172.59','Best Buy','Christmas','Y'),
('27','krc','Credit','Visa','2019-12-07','69.51','Verizon','Verizon','Y'),
('28','krc','Credit','Citi','2019-12-07','49.41','Albertsons','Groceries','N'),
('29','krc','Credit','Citi','2019-12-07','41.44','Albertsons','Groceries','N'),
('30','Albuquerque Gal','Credit','MC','0000-00-00','11.00','Amazon Burts','','N'),
('31','Albuquerque Gal','Credit','MC','0000-00-00','30.00','Jacq Lawson','','N'),
('32','Albuquerque Gal','Credit','MC','0000-00-00','59.00','PayPal','','N'),
('33','Albuquerque Gal','Credit','MC','0000-00-00','81.00','Duluth','','N'),
('34','Albuquerque Gal','Credit','MC','0000-00-00','64.00','gas','','N'),
('35','Albuquerque Gal','Credit','MC','0000-00-00','145.00','amazon laser, sscat','','N'),
('36','Albuquerque Gal','Credit','MC','0000-00-00','109.00','Marys','','N'),
('37','Albuquerque Gal','Credit','MC','0000-00-00','31.00','Marshalls shoes','','N'),
('38','Albuquerque Gal','Credit','MC','0000-00-00','29.00','Coldwater','','N'),
('39','Albuquerque Gal','Credit','Kohls','0000-00-00','0.00','','','N'),
('40','Albuquerque Gal','Credit','Kohls','0000-00-00','0.00','','','N'),
('41','Albuquerque Gal','Credit','Kohls','0000-00-00','0.00','','','N'),
('42','Albuquerque Gal','Credit','Pennys','0000-00-00','149.00','','','N'),
('43','Albuquerque Gal','Credit','MC','0000-00-00','45.00','amazon chai','','N'),
('44','Albuquerque Gal','Credit','MC','0000-00-00','21.00','amazon gold lights','','N'),
('45','Albuquerque Gal','Credit','MC','0000-00-00','44.00','Bed Bath','','N'),
('46','Albuquerque Gal','Credit','MC','0000-00-00','130.00','earrings','cash','N'),
('47','krc','Credit','Citi','2019-12-09','18.99','Sprouts','Groceries','N'),
('48','krc','Credit','Visa','2019-12-11','147.80','Cottonwood Smiles','Medical/Dental','Y'),
('49','krc','Credit','Visa','2019-12-13','399.55','Harry & David','Christmas','Y'),
('50','krc','Credit','Visa','2019-12-13','73.48','Wolfermans','Christmas','Y'),
('51','krc','Credit','Visa','2019-12-13','42.04','Amazon - Pickleball','Christmas','N'),
('52','krc','Credit','Citi','2019-12-13','25.18','Petco','Groceries','N'),
('53','krc','Credit','Citi','2019-12-13','38.73','Albertsons','Groceries','N'),
('54','krc','Credit','Citi','2019-12-13','33.91','Albertsons','Groceries','N'),
('55','krc','Credit','Citi','2019-12-13','15.96','Sprouts','Groceries','N'),
('56','krc','Credit','Citi','2019-12-15','54.69','Albertsons','Groceries','N'),
('57','krc','Debit','Wells Fargo','2019-12-16','158.17','Century Link','TV/Internet','N'),
('58','krc','Debit','Wells Fargo','2019-12-17','120.00','ATM','Cash','N'),
('59','krc','Credit','Visa','2019-12-17','22.06','Hallmark','Christmas','N'),
('60','krc','Credit','Visa','2019-12-17','109.85','Sprouts','Groceries','N'),
('61','krc','Credit','Visa','2019-12-17','32.64','Murphys','Auto Gas','N'),
('62','krc','Credit','Citi','2019-12-17','88.69','Albertsons','Groceries','N'),
('63','krc','Credit','Citi','2019-12-17','110.42','CostCo','Groceries','N'),
('64','krc','Credit','Visa','2019-12-19','458.44','Safelite Glass','Auto Maintenance','Y'),
('65','krc','Credit','Visa','2019-12-19','49.48','Harry &David - Ed B-day','Gifts','Y'),
('66','krc','Credit','Visa','2019-12-19','29.13','Moon\'s Coffee','Groceries','Y'),
('67','krc','Credit','Visa','2019-12-19','25.00','B & N Dues','Combined','Y'),
('68','krc','Credit','Visa','2019-12-20','150.23','HealthQuest','Chiro','N'),
('69','krc','Check','check','2019-12-20','180.00','Adidam Midwest','Tithe','N');




CREATE TABLE `Users` (
  `uid` int(10) NOT NULL AUTO_INCREMENT,
  `email` varchar(60) NOT NULL,
  `username` varchar(40) NOT NULL,
  `LCM` varchar(12) DEFAULT NULL,
  `password` varchar(100) NOT NULL,
  `passwd_expire` date DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;


INSERT INTO Users VALUES
('4','krcowles29@gmail.com','krc','December','$2y$10$.pTJ6Rtv1HSqfcQZsAJS6u6.KKcSFgQfTQ8hp13E4lXjBg.rbJN1O','2020-12-16'),
('11','tonks130@gmail.com','Albuquerque Gal','December','$2y$10$qBtJpqDdBAOHCwxmlv5UDumsIHd6qyoPKAMZ72apX62fJAnBl2X/W','2020-12-08');


