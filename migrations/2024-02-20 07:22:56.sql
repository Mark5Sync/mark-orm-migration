INSERT INTO courses (id, title, price, sale, start, saleEnd, link, freeLessonName, freeLessonLink) VALUES 
 ('1', 'Проектирование в КОМПАС-3D: для начинающих', '4900', '20', NULL, '2024-01-31', 'https://fluidcourse.ru/compas1', NULL, NULL), 
('2', 'Excel для начинающих', '8900', '50', NULL, '2024-01-31', 'https://fluidcourse.ru/excel-beginners', 'Формат данных. Типы данных в ячейках', 'https://youtu.be/t3CPfxhh37o'), 
('3', 'NanoCAD с нуля. Основы проектирования в 2D', '10800', '50', NULL, '2024-01-31', 'https://fluidcourse.ru/nanocad1', 'Работа с блоками', 'https://youtu.be/QCv4E5vteTo'), 
('4', 'Google Docs + MS Word', '6900', '50', '2023-12-04', '2024-01-31', 'https://fluidcourse.ru/google-docs', 'Работа с объектами в Google Презентациях', 'https://youtu.be/j-HKXFXlBXQ'), 
('5', 'Современная промышленная пневмоавтоматика', '14900', '30', '2024-02-05', '2024-01-31', 'https://fluidcourse.ru/industrial-automation', NULL, NULL), 
('6', 'Основы работы на станках ЧПУ', '13900', '30', '2024-02-12', '2024-01-31', 'https://fluidcourse.ru/cnc-machine', NULL, NULL), 
('7', 'Фитинги в промышленности', '4900', '30', NULL, '2024-01-31', 'https://fluidcourse.ru/fiting', NULL, NULL), 
('8', 'Excel: продвинутый уровень', '10800', '30', NULL, '2024-01-31', 'https://fluidcourse.ru/excel-pro', NULL, NULL), 
('9', 'КОМПАС-График. Подготовка к экзамену M2D', '14900', '30', NULL, '2024-01-31', 'https://fluidcourse.ru/compas-m2d', NULL, NULL), 
('10', 'SOLIDWORKS для начинающих', '17900', '30', NULL, '2024-01-31', 'https://fluidcourse.ru/solidworks-beginners', NULL, NULL), 
('11', 'AutoCAD для начинающих', '2900', '50', NULL, '2024-01-31', 'https://fluidcourse.ru/autocad-dlya-nachinayushhix', NULL, NULL), 
('12', 'КОМПАС-3D. Подготовка к экзамену M3D', '17900', '30', NULL, '2024-01-31', 'https://fluidcourse.ru/compas-m3', NULL, NULL), 
('13', 'Печать на 3D принтерах для начинающих', '15600', '0', NULL, NULL, 'https://fluidcourse.ru/pechat-na-3d-printerax-dlya-nachinayushhix', NULL, NULL) 

 ON DUPLICATE KEY UPDATE title=VALUES(title), 
price=VALUES(price), 
sale=VALUES(sale), 
start=VALUES(start), 
saleEnd=VALUES(saleEnd), 
link=VALUES(link), 
freeLessonName=VALUES(freeLessonName), 
freeLessonLink=VALUES(freeLessonLink)


;
INSERT INTO promocodes (curseId, promocode, sale) VALUES 
 ('6', 'CNC2024', '90'), 
('6', 'чпу-10', '10'), 
('1', 'FL2024', '90'), 
('2', 'FL2025', '90'), 
('3', 'FL2026', '90'), 
('4', 'FL2027', '90'), 
('5', 'FL2028', '90'), 
('6', 'FL2029', '90'), 
('7', 'FL2030', '90'), 
('8', 'FL2031', '90'), 
('9', 'FL2032', '90'), 
('10', 'FL2033', '90'), 
('11', 'FL2033', '90'), 
('12', 'FL2033', '90') 

 ON DUPLICATE KEY UPDATE curseId=VALUES(curseId), 
promocode=VALUES(promocode), 
sale=VALUES(sale)


;
