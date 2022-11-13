

CREATE TABLE `Settings` (
  `indx` smallint(6) NOT NULL AUTO_INCREMENT,
  `userid` smallint(6) DEFAULT NULL,
  `menu` varchar(2048) DEFAULT NULL,
  `active` tinyint(2) DEFAULT NULL,
  PRIMARY KEY (`indx`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


INSERT INTO Settings VALUES
('1','1','My Doctors|Prescriptions|Emergency Contacts|Pres Services','1');




CREATE TABLE `UserPages` (
  `indx` smallint(6) NOT NULL AUTO_INCREMENT,
  `userid` smallint(4) NOT NULL,
  `menu_item` tinyint(1) DEFAULT NULL,
  `headers` varchar(2048) DEFAULT NULL,
  `box1` varchar(2048) DEFAULT NULL,
  `box2` varchar(2048) DEFAULT NULL,
  `box3` varchar(2048) DEFAULT NULL,
  `box4` varchar(2048) DEFAULT NULL,
  `row_length` smallint(4) DEFAULT NULL,
  PRIMARY KEY (`indx`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


INSERT INTO UserPages VALUES
('1','1','1','Doctor|Phone|Specialty|Location','Dr. Mir|Dr. Rasmussen|Dr Ajoy|Dr. Duraj G Reddy','phone||505|505-225-2500','Gastroenterology|General Surgery|General Practice|Dermatology','Rust|Rust|High Resort|610 Broadway Blvd NE','936'),
('2','1','2',NULL,'Box2|ClydeCBeatty2|Frinker2|Styde2','phone|toisdf|sytde no|framisad','nothing|pallistat|palsdftritz|hilitz','|tombo|togh|','518'),
('3','1','4','Presbyterian Service|Phone No.|Location|Not specified','COVID Test Site','(505)','High Resort','','446');




CREATE TABLE `Users` (
  `userid` smallint(6) NOT NULL AUTO_INCREMENT,
  `username` varchar(40) NOT NULL,
  `passwd` varchar(255) DEFAULT NULL,
  `passwd_expire` date DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `cookies` varchar(20) DEFAULT NULL,
  `questions` varchar(60) DEFAULT NULL,
  `an1` varchar(4096) DEFAULT NULL,
  `an2` varchar(4096) DEFAULT NULL,
  `an3` varchar(4096) DEFAULT NULL,
  PRIMARY KEY (`userid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


INSERT INTO Users VALUES
('1','krcowles','$2y$10$wl3X4c7Qdt0AetNeaYptAutJX/aF2eNQwBtzbO90utmFwwYIXs92i','2023-10-18','Cowles','Ken','krcowles29@gmail.com','accept','3,5,6','4d8ace8203bafbcb95ea8ef263fe06e636a3860f3d7ee78d3f29e4a2fcf32d7a6257b31738046da44586ba5b48559832c79b28a79b7e861db3762bb66ef113b9f4f40ab7c76a3144f6cfca4730123d719cef766806cb8b09afc61660f285dedccc2bef447bbedbb3524d5a54bb796227f4592c8308ed165a605821e821a7ecdc','48626f79eb1ec37cdb54587c725a39d200d453a6715a056f6aa50f2ae601262db823beacae2320080a267ac3fc4a53f5c0296b59c7f2be87b281cb415a63ffc1b35edb796279ae599a6cfc89aea32381d757388058b6e655da134b8162455c8a1b1a8e97689f6df04ee4a0888ede87a3d804650f7fc54679eb3c940c7433cc12','16c066f1b90c6403a9bc3720a9c4403af28322c5933a2f02f4d78f6afae5d3d2fac17dee3babbc3634e22f601c7657671d46b7b4d9e05af7b8b4a8058f2b848f28bc49261d671d42597404ad7dfa8f5d7ec61f463193ac147a4322b86130aa8c2dda2c8549e4b50e114353879af58aae3066409849456acb6fe9ec49a69d9d5e');


