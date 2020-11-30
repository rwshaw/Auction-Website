
-- RUN THIS USER CREATION IF IT'S THE FIRST TIME YOU ARE RUNNING THE CODE, OTHERWISE LEAVE COMMENTED OUT.
/*
 CREATE USER 'website'@'localhost' IDENTIFIED BY '3ZqpGsAsmC6U2opZ';
GRANT INSERT, SELECT, UPDATE, DELETE ON *.* TO 'website'@'localhost'; 
*/

-- CHECK DATABASE HAS BEEN CREATED, IF NOT CREATE
CREATE DATABASE IF NOT EXISTS auctionsite;

USE auctionsite;

-- DROP ANY TABLES THAT CURRENTLY EXIST TO REPOPULATE.
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS auction_listing;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS bids;
DROP TABLE IF EXISTS watchlist; 
DROP TABLE IF EXISTS watch_notifications; 
DROP VIEW IF EXISTS v_auction_info;

-- TABLE NAMES ARE LOWER CASE PER MYSQL CONVENTIONS WITH SPACE REPLACED WITH _

-- CREATE TABLES
CREATE TABLE users (
    userID INT NOT NULL AUTO_INCREMENT, 
    fName VARCHAR(50) NOT NULL,
    lName VARCHAR(50) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    addressLine1 VARCHAR(255) NOT NULL,
    addressLine2 VARCHAR(255),
    city VARCHAR(50) NOT NULL,
    postcode CHAR(8) NOT NULL,
    buyer BOOLEAN DEFAULT TRUE,
    seller BOOLEAN DEFAULT FALSE,
    PRIMARY KEY (userID),
    UNIQUE (email)
);


CREATE TABLE auction_listing (
    listingID INT NOT NULL AUTO_INCREMENT,
    sellerUserID INT NOT NULL,
    itemName VARCHAR(255) NOT NULL,
    itemDescription TEXT,
    itemImage MEDIUMBLOB, -- IMAGES AS EXTRA OPTIONAL FEATURE, SHOULD BE IMAGEURL TO DISPLAY, RATHER THAN STORING WHOLE IMAGE FILE IN DB FOR NOW. 
    startPrice DECIMAL(8,2) NOT NULL DEFAULT 0, -- CHOICE OF DECIMAL, 8 DIGIT PRECISION WITH 2 SCALE, TO RELIABLY STORE MONETRAY VALUE.
    reservePrice DECIMAL(8,2) NOT NULL DEFAULT 0,
    startTime TIMESTAMP NOT NULL DEFAULT NOW(),
    endTime DATETIME NOT NULL,
    categoryID INT NOT NULL,
    resultsEmailed BOOLEAN NOT NULL DEFAULT FALSE,
    PRIMARY KEY (listingID),
    FOREIGN KEY (sellerUserID) REFERENCES users(userID) ON UPDATE CASCADE ON DELETE NO ACTION,
    INDEX (endTime,categoryID)
);

CREATE TABLE categories (
    categoryID INT NOT NULL AUTO_INCREMENT,
    deptName VARCHAR(50) NOT NULL,
    subCategoryName VARCHAR(50) NOT NULL,
    PRIMARY KEY (categoryID),
    UNIQUE (subCategoryName)
);

CREATE TABLE bids (
    bidID INT NOT NULL AUTO_INCREMENT,
    userID INT NOT NULL,
    listingID INT NOT NULL,
    bidPrice DECIMAL(8,2) NOT NULL CHECK (bidPrice > 0),    -- ADDING CONSTRAINT
    bidTimestamp TIMESTAMP NOT NULL DEFAULT NOW(),
    PRIMARY KEY (bidID),
    FOREIGN KEY (userID) REFERENCES users(userID) ON UPDATE CASCADE ON DELETE NO ACTION,
    FOREIGN KEY (listingID) REFERENCES auction_listing(listingID) ON UPDATE CASCADE ON DELETE NO ACTION
);


CREATE TABLE watchlist (
    watchID INT NOT NULL AUTO_INCREMENT,
    listingID INT NOT NULL,
    userID INT NOT NULL,
    isWatching BOOLEAN NOT NULL,
    PRIMARY KEY (watchID),
    FOREIGN KEY (userID) REFERENCES users(userID) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (listingID) REFERENCES auction_listing(listingID) ON UPDATE CASCADE ON DELETE CASCADE,
    INDEX (listingID), -- NEED TO SEARCH FOR THOSE WATCHING QUICKLY BY LISTING, TO NOTIFY BASED ON LISTING CHANGE.
    INDEX (userID)  -- NEED TO FIND A USER'S WATCHLIST QUICKLY TO DISPLAY TO THEM THEIR NOTIFICATIONS.
);

CREATE TABLE watch_notifications (
    notificationID INT NOT NULL AUTO_INCREMENT,
    watchID INT NOT NULL,
    bidID INT NOT NULL,
    message TEXT,
    emailSent BOOLEAN NOT NULL DEFAULT FALSE,
    PRIMARY KEY (notificationID),
    FOREIGN KEY (watchID) REFERENCES watchlist(watchID) ON UPDATE CASCADE ON DELETE NO ACTION,
    FOREIGN KEY (bidID) REFERENCES bids(bidID) ON UPDATE CASCADE ON DELETE CASCADE,
    UNIQUE(watchID,bidID) -- ENSURES WE CANNOT GIVE CUSTOMER MORE THAN ONE NOTIFICATION FOR EACH BID UPDATE.
);

ALTER TABLE auction_listing
ADD FOREIGN KEY (categoryID) REFERENCES categories(categoryID) ON UPDATE CASCADE ON DELETE CASCADE;

/* Create view for standarised access to key auction information that is needed frequently  and gathered from multiple tables+ simpler auction queries accross the site. */
CREATE VIEW v_auction_info AS
SELECT a.listingID
        , ItemName
        , ItemDescription
        , itemImage
        , a.sellerUserID
        , ifnull(max(bidPrice),startPrice) as currentPrice -- if no bids, show starting price as current price.
        , reservePrice
        , count(bidID) as num_bids
        , endTime
        , endTime < now() as auction_ended
        , c.deptName
        , c.subCategoryName 
from auction_listing a 
left join bids b 
on a.listingID = b.listingID 
left join categories c 
on a.categoryID = c.categoryID 
group by a.listingID, a.ItemName, a.ItemDescription, a.endTime, c.deptName, c.subCategoryName;

-- SPECIFYING CATEGORIES

-- insert dummy users 
INSERT INTO auctionsite.users (fName,lName,email,password, addressLine1,addressLine2,city,postcode,buyer,seller) 
VALUES 
('Tom', 'Cruise', 'tom.cruise@ourauctionsite.com','$2y$10$bfVC2y.ae4xJvvUZS6kk9uxxFHAsc0GNqPdzFfDQ9SfMpd9VDnDLm','15 Honey Drive',null,'London','NW15JBE',TRUE, TRUE),
('John', 'Doe', 'john.doe@ourauctionsite.com', '2y$10$bfVC2y.ae4xJvvUZS6kk9uxxFHAsc0GNqPdzFfDQ9SfMpd9VDnDLm', '8 Street', 'John''s Avenue', 'London', 'WH547PE', TRUE, TRUE),
('Bob', 'Smith', 'bob.smith@ourauctionsite.com', '$2y$10$bfVC2y.ae4xJvvUZS6kk9uxxFHAsc0GNqPdzFfDQ9SfMpd9VDnDLm', '6 Street', 'Bob''s Avenue', 'London', 'GS392JF', TRUE, TRUE), 
('Kato','Harris','ut.lacus@consectetuer.org','$2y$10$PWephQPcIstR/g6fOU0wWeU.OxwofZg/F9sLy3MfuP7lP7E2sl82y','Ap #890-2577 In St.',null,'Lewiston','75782',TRUE,TRUE),
("Ronan","Camacho","dolor.dapibus@disparturient.org","$2y$10$PWephQPcIstR/g6fOU0wWeU.OxwofZg/F9sLy3MfuP7lP7E2sl82y","6584 Mi Ave",null,"Saarlouis","54182",TRUE,TRUE),
("Nolan","Orr","arcu@nullaanteiaculis.net","$2y$10$PWephQPcIstR/g6fOU0wWeU.OxwofZg/F9sLy3MfuP7lP7E2sl82y","643 Fusce Road",null,"Fort Laird","14155",TRUE,TRUE),
("Naida","Rosales","ipsum.leo.elementum@ac.org","$2y$10$PWephQPcIstR/g6fOU0wWeU.OxwofZg/F9sLy3MfuP7lP7E2sl82y","P.O. Box 944, 3171 Ultricies St.",null,"Piagge","9144",TRUE,TRUE),
("Brooke","Sherman","blandit@ullamcorper.org","$2y$10$PWephQPcIstR/g6fOU0wWeU.OxwofZg/F9sLy3MfuP7lP7E2sl82y","2219 Proin St.",null,"Waalwijk","27174",TRUE,TRUE),
("Ahmed","Lester","interdum.ligula@Loremipsumdolor.co.uk","$2y$10$PWephQPcIstR/g6fOU0wWeU.OxwofZg/F9sLy3MfuP7lP7E2sl82y","675-6064 Pellentesque Av.",null,"Sasaram","Z1725",TRUE,TRUE),
("Taylor","Cruz","id.enim@eu.ca","$2y$10$PWephQPcIstR/g6fOU0wWeU.OxwofZg/F9sLy3MfuP7lP7E2sl82y","P.O. Box 596, 9194 Non Street",null,"Loppem","45714",TRUE,TRUE),
("Rylee","Mccullough","tristique.neque.venenatis@Etiamlaoreetlibero.co.uk","$2y$10$PWephQPcIstR/g6fOU0wWeU.OxwofZg/F9sLy3MfuP7lP7E2sl82y","Ap #152-4993 Egestas, Av.",null,"Helena","95399",TRUE,TRUE),
("Aubrey","Scott","massa.Mauris@diamProindolor.edu","$2y$10$PWephQPcIstR/g6fOU0wWeU.OxwofZg/F9sLy3MfuP7lP7E2sl82y","252-2559 Nunc Rd.",null,"Tiverton","85508",TRUE,TRUE),
("Quin","Howell","ac.mattis.semper@congueturpisIn.org","$2y$10$PWephQPcIstR/g6fOU0wWeU.OxwofZg/F9sLy3MfuP7lP7E2sl82y","594-8509 Erat Avenue",null,"Lens-Saint-Servais","713779",TRUE,TRUE),
("Amal","Castaneda","tempor@fringillaeuismodenim.org","$2y$10$PWephQPcIstR/g6fOU0wWeU.OxwofZg/F9sLy3MfuP7lP7E2sl82y","2812 Consectetuer Rd.",null,"Sint-Denijs","65671",TRUE,TRUE),
("Laith","Mcguire","quam.Curabitur@Nulla.edu","$2y$10$PWephQPcIstR/g6fOU0wWeU.OxwofZg/F9sLy3MfuP7lP7E2sl82y","104-6808 Posuere Ave",null,"Detroit","425305",TRUE,TRUE),
("Quentin","Porter","In@odiotristiquepharetra.org","$2y$10$PWephQPcIstR/g6fOU0wWeU.OxwofZg/F9sLy3MfuP7lP7E2sl82y","P.O. Box 340, 660 Feugiat Av.",null,"Tanjung Pinang","9571",TRUE,TRUE),
("Lamar","Dillard","in@aliquet.com","$2y$10$PWephQPcIstR/g6fOU0wWeU.OxwofZg/F9sLy3MfuP7lP7E2sl82y","Ap #605-1332 Ullamcorper St.",null,"Chesapeake","Z8923",TRUE,TRUE),
("Denise","Mcknight","Praesent.luctus.Curabitur@elit.com","$2y$10$PWephQPcIstR/g6fOU0wWeU.OxwofZg/F9sLy3MfuP7lP7E2sl82y","257-7179 Ligula. Road",null,"Gadag Betigeri","323486",TRUE,TRUE),
("Jamal","Reeves","eros.Nam.consequat@rutrum.org","$2y$10$PWephQPcIstR/g6fOU0wWeU.OxwofZg/F9sLy3MfuP7lP7E2sl82y","Ap #360-303 Sed Rd.",null,"Mitú","Z7123",TRUE,TRUE),
("Steel","Barnett","euismod@sitametante.ca","$2y$10$PWephQPcIstR/g6fOU0wWeU.OxwofZg/F9sLy3MfuP7lP7E2sl82y","Ap #658-6784 In Road",null,"King's Lynn","16768",TRUE,TRUE);

INSERT INTO auctionsite.categories (deptName, subCategoryName)
VALUES
('Books', 'Fiction'),
('Books','Nonfiction'),
('Electronics', 'TVs'),
('Electronics', 'Computers'),
('Electronics', 'Mobile Phones'),
('Electronics', 'Video Games'),
('Sport & Leisure', 'Cycling'),
('Sport & Leisure','Golf'),
('Sport & Leisure','Tennis'),
('Sport & Leisure','Art'),
('Electronics', 'Cameras'),
('Electronics', 'Audio'),
('Health', 'Gym'),
('Health', 'Female Beauty'),
('Health', 'Supplements')
;

-- insert dummy listings

INSERT INTO auctionsite.auction_listing (sellerUserID, itemName,itemDescription ,startPrice , reservePrice,
    startTime , endTime, categoryID)
SELECT userID
, 'Lenovo Laptop', 'This is my lenovo laptop I got back in 2018. I love it but now I have just bought a mac so do not need it anymore.'
, 105.99, 150, now(),date_add(now(), interval 7 day),4
FROM users where email = 'tom.cruise@ourauctionsite.com'
;

INSERT INTO auctionsite.auction_listing (sellerUserID, itemName,itemDescription , reservePrice,
    startTime , endTime, categoryID)
SELECT userID
, 'Assassins Creed Valhalla PS5', 'Great Game. Already played it so selling it.'
, 25.49, now(),date_add(now(), interval 10 day),6
FROM users where email = 'tom.cruise@ourauctionsite.com'
;

INSERT INTO auctionsite.auction_listing (sellerUserID, itemName,itemDescription , startPrice, reservePrice,
    startTime , endTime, categoryID)
SELECT userID
, 'Samsung 55 inch TV', 'Samsung TV, amazing condiiton. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur sagittis pretium velit egestas venenatis. 
Donec vel lacinia sem, non tristique neque. Suspendisse pellentesque tempus sodales. Vestibulum rhoncus maximus diam. Aenean ut nisi non felis tristique egestas a eget diam. 
Integer suscipit ac ex vel semper. Donec efficitur blandit elit, id scelerisque quam suscipit eu. Donec sagittis tempor erat, nec posuere nisi efficitur ac. Nulla ac justo fermentum, consequat ipsum ac, condimentum lectus. 
Maecenas ac congue arcu, eget consectetur augue. Phasellus eget tortor risus. Pellentesque feugiat libero accumsan, rhoncus magna eget, commodo ligula. Nunc sed suscipit ex. Nulla finibus interdum lectus, sit amet gravida orci dictum eget.
Sed sed velit sollicitudin, dapibus nibh sit amet, vulputate neque. Morbi in luctus metus. Nullam nec lacus pellentesque, auctor lorem ut, egestas nisi. Nullam iaculis nibh molestie sapien facilisis congue. 
Etiam id felis non erat ultricies ornare vitae nec sapien. Morbi dapibus sollicitudin diam et efficitur. Etiam porttitor risus at mauris gravida mollis. Vestibulum in nulla nec mauris sollicitudin malesuada eleifend nec odio. 
Sed quam ipsum, faucibus id laoreet eget, vehicula sit amet diam. Phasellus ac massa ut mi aliquet fermentum vitae sed velit.'
,210, 375, now(),date_add(now(), interval 8 day),3
FROM users where email = 'john.doe@ourauctionsite.com'
;

INSERT INTO auctionsite.auction_listing (sellerUserID, itemName,itemDescription , startPrice, reservePrice,
    startTime , endTime, categoryID)
SELECT userID
, 'PS5', 'Birthday gift, brand new, still in original box with 2 controllers.'
, 400,450, now(),date_add(now(), interval 5 day),6
FROM users where email = 'ut.lacus@consectetuer.org'
;

INSERT INTO auctionsite.auction_listing (sellerUserID, itemName,itemDescription , startPrice,
    startTime , endTime, categoryID)
SELECT userID
, 'Becoming: Now a Major Netflix Documentary', "Michelle Robinson Obama served as First Lady of the United States from 2009 to 2017. 
A graduate of Princeton University and Harvard Law School, Mrs. Obama started her career as an attorney at the Chicago law firm Sidley & Austin, where she met her future husband, Barack Obama. 
She later worked in the Chicago mayor's office, at the University of Chicago, and at the University of Chicago Medical Center. Mrs. Obama also founded the Chicago chapter of Public Allies, an organization that prepares young people for careers in public service. 
The Obamas currently live in Washington, DC, and have two daughters, Malia and Sasha."
, 15, now(),date_add(now(), interval 10 day),2
FROM users where email = 'dolor.dapibus@disparturient.org'
;

INSERT INTO auctionsite.auction_listing (sellerUserID, itemName,itemDescription , startPrice,
    startTime , endTime, categoryID)
SELECT userID
, 'Barack Obama: A Promised Land', 'Barack Obama was elected President of the United States on November 4, 2008. He is the author of the New York Times bestsellers Dreams from My Father and The Audacity of Hope: Thoughts on Reclaiming the American Dream.'
, 15, now(),date_add(now(), interval 5 day),2
FROM users where email = 'ut.lacus@consectetuer.org'
;

INSERT INTO auctionsite.auction_listing (sellerUserID, itemName,itemDescription , startPrice,
    startTime , endTime, categoryID)
SELECT userID
, 'Mountain bike', 'Good condition, used for a few years, comes with lock and hand pump.'
, 60, now(),date_add(now(), interval 5 day),7
FROM users where email = 'arcu@nullaanteiaculis.net'
;

INSERT INTO auctionsite.auction_listing (sellerUserID, itemName,itemDescription , startPrice, reservePrice,
    startTime , endTime, categoryID)
SELECT userID
, 'tennis racket', 'Few strings loose but served me well. Hope it does the same for the next person'
, 15,30, now(),date_add(now(), interval 5 day),9
FROM users where email = 'id.enim@eu.ca'
;

INSERT INTO auctionsite.auction_listing (sellerUserID, itemName,itemDescription , startPrice, reservePrice,
    startTime , endTime, categoryID)
SELECT userID
, 'Canon PowerShot G7 X Mark III', 'Great in day and night'
, 400,600, now(),date_add(now(), interval 5 day),11
FROM users where email = 'massa.Mauris@diamProindolor.edu'
;

INSERT INTO auctionsite.auction_listing (sellerUserID, itemName,itemDescription , startPrice,
    startTime , endTime, categoryID)
SELECT userID
, 'The Graveyard Book', 'Great book.'
, 10, now(),date_add(now(), interval 5 day),1
FROM users where email = 'tempor@fringillaeuismodenim.org'
;

INSERT INTO auctionsite.auction_listing (sellerUserID, itemName,itemDescription , startPrice,
    startTime , endTime, categoryID)
SELECT userID
, 'Golf club set with bag', 'Everything you need to get out on a course: a bag, a driver, a 3-wood, a 5-hybrid, 6- to 9-irons, a pitching wedge, and a mallet putter.'
, 10, now(),date_add(now(), interval 5 day),8
FROM users where email = 'quam.Curabitur@Nulla.edu'
;

INSERT INTO auctionsite.auction_listing (sellerUserID, itemName,itemDescription , startPrice, reservePrice,
    startTime , endTime, categoryID)
SELECT userID
, '42 Inch TV', 'Wi-Fi enabled so quick access to Netflix and Amazon Prime video.',
400.00, 600.00, now(),date_add(now(), interval 5 day),3
FROM users where email = 'eros.Nam.consequat@rutrum.org'
;

INSERT INTO auctionsite.auction_listing (sellerUserID, itemName,itemDescription , startPrice,
    startTime , endTime, categoryID)
SELECT userID
, 'Spa kit', 'Great for staying at home during a lockdown!',
30, now(),date_add(now(), interval 5 day),14
FROM users where email = 'Praesent.luctus.Curabitur@elit.com'
;

INSERT INTO auctionsite.auction_listing (sellerUserID, itemName,itemDescription , startPrice,
    startTime , endTime, categoryID)
SELECT userID
, 'Vitamin D tablets', 'Apparently effective against the Rona',
10, now(),date_add(now(), interval 5 day),15
FROM users where email = 'in@aliquet.com'
;

INSERT INTO auctionsite.auction_listing (sellerUserID, itemName,itemDescription , startPrice,
    startTime , endTime, categoryID)
SELECT userID
, 'Foam roller', 'Great for stretching',
15.99, now(),date_add(now(), interval 5 day),13
FROM users where email = 'euismod@sitametante.ca'
;

INSERT INTO auctionsite.auction_listing (sellerUserID, itemName,itemDescription , startPrice, reservePrice, 
    startTime , endTime, categoryID)
SELECT userID
, 'Apple iPhone 12', 'Good phone, switching to a Samsung.',
500, 900, now(),date_add(now(), interval 5 day),5
FROM users where email = 'In@odiotristiquepharetra.org'
;

INSERT INTO auctionsite.auction_listing (sellerUserID, itemName,itemDescription , startPrice, reservePrice, 
    startTime , endTime, categoryID)
SELECT userID
, 'Sculpture', 'Original rock sculpture by Mahmoud Mokhtar.',
500, 900, now(),date_add(now(), interval 5 day),10
FROM users where email = 'In@odiotristiquepharetra.org'
;

INSERT INTO auctionsite.auction_listing (sellerUserID, itemName,itemDescription , startPrice, reservePrice, 
    startTime , endTime, categoryID)
SELECT userID
, 'Pollock painting', 'Original',
1, 10000, now(),date_add(now(), interval 5 day),10
FROM users where email = 'In@odiotristiquepharetra.org'
;

-- Dummy bids

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 2, listingID, 110
from auction_listing where listingID=1;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 2, listingID, 150.33
from auction_listing where listingID=1;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 2, listingID, 234.58
from auction_listing where listingID=1;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 3, listingID, 29.58
from auction_listing where listingID=2;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 1, listingID, 400
from auction_listing where listingID=3;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 1, listingID, 33
from auction_listing where listingID=2;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 1, listingID, 250
from auction_listing where listingID=1;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 4, listingID, 10
from auction_listing where listingID=5;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 4, listingID, 9
from auction_listing where listingID=6;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 4, listingID, 5
from auction_listing where listingID=14;

-- Techy

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 5, listingID, 510
from auction_listing where listingID=16;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 5, listingID, 410
from auction_listing where listingID=12;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 5, listingID, 250
from auction_listing where listingID=3;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 5, listingID, 410
from auction_listing where listingID=9;

-- Sporty

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 6, listingID, 160
from auction_listing where listingID=1;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 6, listingID, 20
from auction_listing where listingID=8;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 6, listingID, 61
from auction_listing where listingID=7;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 6, listingID, 12
from auction_listing where listingID=11;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 6, listingID, 15
from auction_listing where listingID=15;

-- comfort

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 7, listingID, 10
from auction_listing where listingID=14;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 7, listingID, 16
from auction_listing where listingID=15;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 7, listingID, 31
from auction_listing where listingID=13;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 7, listingID, 12
from auction_listing where listingID=5;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 7, listingID, 550
from auction_listing where listingID=17;

-- gamer

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 8, listingID, 35
from auction_listing where listingID=2;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 8, listingID, 350
from auction_listing where listingID=3;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 8, listingID, 425
from auction_listing where listingID=4;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 8, listingID, 420
from auction_listing where listingID=12;

-- art and crafts

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 9, listingID, 11
from auction_listing where listingID=14;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 9, listingID, 560
from auction_listing where listingID=17;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 9, listingID, 3000
from auction_listing where listingID=18;

-- newest tech 

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 10, listingID, 950
from auction_listing where listingID=16;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 10, listingID, 410
from auction_listing where listingID=9;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 10, listingID, 450
from auction_listing where listingID=4;

-- reader

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 11, listingID, 16
from auction_listing where listingID=5;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 11, listingID, 16
from auction_listing where listingID=6;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 11, listingID, 10
from auction_listing where listingID=10;

-- sporty

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 12, listingID, 15
from auction_listing where listingID=11;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 12, listingID, 17
from auction_listing where listingID=8;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 12, listingID, 61
from auction_listing where listingID=7;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 12, listingID, 17
from auction_listing where listingID=15;

-- reader

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 13, listingID, 15
from auction_listing where listingID=5;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 13, listingID, 18
from auction_listing where listingID=6;

-- arty

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 14, listingID, 560
from auction_listing where listingID=17;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 14, listingID, 4000
from auction_listing where listingID=18;

-- comfort

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 15, listingID, 11
from auction_listing where listingID=14;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 15, listingID, 15.99
from auction_listing where listingID=15;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 15, listingID, 35
from auction_listing where listingID=13;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 15, listingID, 600
from auction_listing where listingID=17;

-- tech 

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 16, listingID, 410
from auction_listing where listingID=12;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 16, listingID, 260
from auction_listing where listingID=3;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 16, listingID, 500
from auction_listing where listingID=9;

-- random

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 17, listingID, 450
from auction_listing where listingID=4;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 17, listingID, 35
from auction_listing where listingID=8;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 17, listingID, 1000
from auction_listing where listingID=16;

-- random

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 18, listingID, 11
from auction_listing where listingID=14;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 18, listingID, 65
from auction_listing where listingID=7;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 18, listingID, 12
from auction_listing where listingID=10;

-- random

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 19, listingID, 20
from auction_listing where listingID=5;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 19, listingID, 40
from auction_listing where listingID=13;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 19, listingID, 100
from auction_listing where listingID=18;

-- random

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 20, listingID, 1200
from auction_listing where listingID=16;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 20, listingID, 500
from auction_listing where listingID=9;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 20, listingID, 30
from auction_listing where listingID=2;


-- 20 users 





