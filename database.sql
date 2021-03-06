DROP TABLE IF EXISTS `ProductsInCarts`;
DROP TABLE IF EXISTS `Purchases`;
DROP TABLE IF EXISTS `Carts`;
DROP TABLE IF EXISTS `Tokens`;
DROP TABLE IF EXISTS `Users`;
DROP TABLE IF EXISTS `Roles`;
DROP TABLE IF EXISTS `Stock`;
DROP TABLE IF EXISTS `Products`;


/* ROLES */ 

CREATE TABLE `Roles`(
  `Id` INT NOT NULL AUTO_INCREMENT,
  `Name` TEXT NOT NULL,
  PRIMARY KEY(`Id`)
) engine = innoDB;

/* USERS */ 

CREATE TABLE `Users`(
  `Id` INT NOT NULL AUTO_INCREMENT,
  `Username` VARCHAR(
    /* SET character limit here */
    20
  ) NOT NULL,
  `Password` VARCHAR(
    /* MD5 hash */
    32
  ) NOT NULL,
  `Email` TEXT NOT NULL,
  `Date_Created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Roles_Id` INT NOT NULL DEFAULT 2,
  PRIMARY KEY(`Id`),
  FOREIGN KEY(`Roles_Id`) REFERENCES Roles(`Id`)
) engine = innoDB;

/* PRODUCTS */ 

CREATE TABLE `Products`(
    `Id` INT NOT NULL AUTO_INCREMENT,
    `Name` VARCHAR(100) NOT NULL,
    `Date_Created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `Last_Updated` DATETIME,
    `Price` INT NOT NULL,
    `Brand` TEXT NOT NULL,
    `Color` TEXT NOT NULL,
    PRIMARY KEY(`Id`)
) engine = innoDB;

/* CART */ 

CREATE TABLE `Carts`(
    `Id` INT NOT NULL AUTO_INCREMENT,
    `User_Id` INT NOT NULL,
    `Checkout_Done` BOOLEAN NOT NULL DEFAULT FALSE,
    `Date_Created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `Date_Updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(`Id`),
    FOREIGN KEY(`User_Id`) REFERENCES Users(`Id`)

) engine = innoDB;

/* ProductsInCarts */

CREATE TABLE `ProductsInCarts`(
    `Id` INT NOT NULL AUTO_INCREMENT,
    `Carts_Id` INT NOT NULL,
    `Products_Id` INT NOT NULL,
    `Date_Added` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(`Id`),
    FOREIGN KEY(`Products_Id`) REFERENCES Products(`Id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
    FOREIGN KEY(`Carts_Id`) REFERENCES Carts(`Id`)
    ON DELETE CASCADE
) engine = innoDB;

/* PURCHASES */ 

CREATE TABLE `Purchases`(
  `Id` INT NOT NULL AUTO_INCREMENT,
  `Carts_Id` INT NOT NULL,
  `Date_Checkout` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Total` INT NOT NULL,
  PRIMARY KEY(`Id`),
  FOREIGN KEY(`Carts_Id`) REFERENCES Carts(`Id`)
) engine = innoDB;

/* STOCK */ 

CREATE TABLE `Stock`(
    `Id` INT NOT NULL AUTO_INCREMENT,
    `Products_Id` INT NOT NULL,
    PRIMARY KEY(`Id`),
    FOREIGN KEY(`Products_Id`) REFERENCES Products(`Id`)
) engine = innoDB;

/* TOKENS */ 

CREATE TABLE `Tokens`(
    `Id` INT NOT NULL AUTO_INCREMENT,
    `Users_Id` INT NOT NULL,
    `Date_Created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `Date_Updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `Token` TEXT,
    PRIMARY KEY(`Id`),
    FOREIGN KEY(`Users_Id`) REFERENCES Users(`Id`)
) engine = innoDB;


/* TEST DATA */ 

INSERT INTO Roles(Name) VALUES ("Admin");
INSERT INTO Roles(Name) VALUES ("User");
INSERT INTO Users(Username, Password, Email, Roles_Id) VALUE ("Admin", "5f4dcc3b5aa765d61d8327deb882cf99", "Admin@myCompany.com", 1);
INSERT INTO Users(Username, Password, Email, Roles_Id) VALUE ("User", "5f4dcc3b5aa765d61d8327deb882cf99", "User@myCompany.com", 2);

INSERT INTO Products(Name, Price, Brand, Color) VALUES ("Aero Pro Drive", 2400, "Babolat", "Yellow");
INSERT INTO Products(Name, Price, Brand, Color) VALUES ("Pure Drive Team", 2100, "Babolat", "Blue");
INSERT INTO Products(Name, Price, Brand, Color) VALUES ("Pure Control", 2700, "Babolat", "Red");
INSERT INTO Products(Name, Price, Brand, Color) VALUES ("Pure Storm", 2500, "Babolat", "Orange");
INSERT INTO Products(Name, Price, Brand, Color) VALUES ("Pro Staff", 3100, "Wilson", "Orange");
INSERT INTO Products(Name, Price, Brand, Color) VALUES ("Pro Team", 2600, "Wilson", "Yellow");
INSERT INTO Products(Name, Price, Brand, Color) VALUES ("Ncode N4", 2900, "Wilson", "Red");
INSERT INTO Products(Name, Price, Brand, Color) VALUES ("Ncode N3", 2600, "Wilson", "Black");
INSERT INTO Products(Name, Price, Brand, Color) VALUES ("Pro Tour", 2800, "Prince", "Black");
INSERT INTO Products(Name, Price, Brand, Color) VALUES ("Rebel 20", 2800, "Prince", "Purple");
INSERT INTO Products(Name, Price, Brand, Color) VALUES ("O3 Silver", 2700, "Prince", "Silver");
INSERT INTO Products(Name, Price, Brand, Color) VALUES ("O3 Red", 2900, "Prince", "Red");
INSERT INTO Products(Name, Price, Brand, Color) VALUES ("MicroGEL Extreme Pro", 2600, "Head", "Black");
INSERT INTO Products(Name, Price, Brand, Color) VALUES ("Crossbow 10", 2900, "Head", "White");
INSERT INTO Products(Name, Price, Brand, Color) VALUES ("Flexpoint 10", 2700, "Head", "Gray");
INSERT INTO Products(Name, Price, Brand, Color) VALUES ("Protector", 2200, "Head", "Yellow");