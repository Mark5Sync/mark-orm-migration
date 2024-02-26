CREATE TABLE `courses` (
	id int(11) NOT NULL PRIMARY KEY auto_increment ,
	title varchar(300) DEFAULT NULL   ,
	price int(11) DEFAULT NULL   ,
	sale int(11) DEFAULT NULL   ,
	start date DEFAULT NULL   ,
	saleEnd date DEFAULT NULL   ,
	link varchar(300) DEFAULT NULL   ,
	freeLessonName varchar(300) DEFAULT NULL   ,
	freeLessonLink varchar(300) DEFAULT NULL   
)
;
CREATE TABLE `promocodes` (
	curseId int(11) DEFAULT NULL   REFERENCES courses(id),
	promocode varchar(300) DEFAULT NULL   ,
	sale int(11) DEFAULT NULL   
)
;
