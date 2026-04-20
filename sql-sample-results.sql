-- Sample Calculator Results for all users
-- 2 good, 2 bad, 1 very bad per user, spread over 2.5 months

-- First, check if calculator_results table exists, create if not
CREATE TABLE IF NOT EXISTS calculator_results (
    RESULT_ID INT AUTO_INCREMENT PRIMARY KEY,
    USER_ID INT NOT NULL,
    TOTAL_CO2 DECIMAL(10,2) NOT NULL,
    GRADE VARCHAR(1),
    PERIOD VARCHAR(20),
    CREATED_AT DATE NOT NULL,
    FOREIGN KEY (USER_ID) REFERENCES USERS(USER_ID)
);

-- Get user IDs
SET @u1 = (SELECT USER_ID FROM USERS WHERE USERNAME = 'memberrhys');
SET @u2 = (SELECT USER_ID FROM USERS WHERE USERNAME = 'memberrhys2');
SET @u3 = (SELECT USER_ID FROM USERS WHERE USERNAME = 'memberrhys3');
SET @u4 = (SELECT USER_ID FROM USERS WHERE USERNAME = 'viewerrhys');
SET @u5 = (SELECT USER_ID FROM USERS WHERE USERNAME = 'viewerrhys2');
SET @u6 = (SELECT USER_ID FROM USERS WHERE USERNAME = 'viewerrhys3');

-- memberrhys - 2 good, 2 bad, 1 very bad
INSERT INTO calculator_results (USER_ID, TOTAL_CO2, GRADE, PERIOD, CREATED_AT) VALUES
(@u1, 45.5, 'A', 'weekly', DATE_SUB(CURDATE(), INTERVAL 5 DAY)),
(@u1, 52.0, 'A', 'weekly', DATE_SUB(CURDATE(), INTERVAL 19 DAY)),
(@u1, 95.0, 'C', 'weekly', DATE_SUB(CURDATE(), INTERVAL 12 DAY)),
(@u1, 110.5, 'D', 'weekly', DATE_SUB(CURDATE(), INTERVAL 26 DAY)),
(@u1, 185.0, 'F', 'weekly', DATE_SUB(CURDATE(), INTERVAL 40 DAY));

-- memberrhys2
INSERT INTO calculator_results (USER_ID, TOTAL_CO2, GRADE, PERIOD, CREATED_AT) VALUES
(@u2, 38.0, 'A', 'weekly', DATE_SUB(CURDATE(), INTERVAL 3 DAY)),
(@u2, 48.5, 'A', 'weekly', DATE_SUB(CURDATE(), INTERVAL 17 DAY)),
(@u2, 88.0, 'C', 'weekly', DATE_SUB(CURDATE(), INTERVAL 10 DAY)),
(@u2, 125.0, 'D', 'weekly', DATE_SUB(CURDATE(), INTERVAL 30 DAY)),
(@u2, 210.0, 'F', 'weekly', DATE_SUB(CURDATE(), INTERVAL 45 DAY));

-- memberrhys3
INSERT INTO calculator_results (USER_ID, TOTAL_CO2, GRADE, PERIOD, CREATED_AT) VALUES
(@u3, 55.0, 'B', 'weekly', DATE_SUB(CURDATE(), INTERVAL 7 DAY)),
(@u3, 62.0, 'B', 'weekly', DATE_SUB(CURDATE(), INTERVAL 21 DAY)),
(@u3, 102.0, 'D', 'weekly', DATE_SUB(CURDATE(), INTERVAL 14 DAY)),
(@u3, 118.0, 'D', 'weekly', DATE_SUB(CURDATE(), INTERVAL 28 DAY)),
(@u3, 195.0, 'F', 'weekly', DATE_SUB(CURDATE(), INTERVAL 50 DAY));

-- viewerrhys
INSERT INTO calculator_results (USER_ID, TOTAL_CO2, GRADE, PERIOD, CREATED_AT) VALUES
(@u4, 42.0, 'A', 'weekly', DATE_SUB(CURDATE(), INTERVAL 2 DAY)),
(@u4, 58.0, 'B', 'weekly', DATE_SUB(CURDATE(), INTERVAL 16 DAY)),
(@u4, 92.0, 'C', 'weekly', DATE_SUB(CURDATE(), INTERVAL 9 DAY)),
(@u4, 135.0, 'F', 'weekly', DATE_SUB(CURDATE(), INTERVAL 23 DAY)),
(@u4, 220.0, 'F', 'weekly', DATE_SUB(CURDATE(), INTERVAL 55 DAY));

-- viewerrhys2
INSERT INTO calculator_results (USER_ID, TOTAL_CO2, GRADE, PERIOD, CREATED_AT) VALUES
(@u5, 35.0, 'A', 'weekly', DATE_SUB(CURDATE(), INTERVAL 4 DAY)),
(@u5, 44.0, 'A',  'weekly', DATE_SUB(CURDATE(), INTERVAL 18 DAY)),
(@u5, 98.0, 'C',  'weekly', DATE_SUB(CURDATE(), INTERVAL 11 DAY)),
(@u5, 140.0, 'F', 'weekly', DATE_SUB(CURDATE(), INTERVAL 25 DAY)),
(@u5, 230.0, 'F', 'weekly', DATE_SUB(CURDATE(), INTERVAL 60 DAY));

-- viewerrhys3
INSERT INTO calculator_results (USER_ID, TOTAL_CO2, GRADE, PERIOD, CREATED_AT) VALUES
(@u6, 50.0, 'B', 'weekly', DATE_SUB(CURDATE(), INTERVAL 6 DAY)),
(@u6, 68.0, 'B', 'weekly', DATE_SUB(CURDATE(), INTERVAL 20 DAY)),
(@u6, 105.0, 'D', 'weekly', DATE_SUB(CURDATE(), INTERVAL 13 DAY)),
(@u6, 128.0, 'F', 'weekly', DATE_SUB(CURDATE(), INTERVAL 27 DAY)),
(@u6, 205.0, 'F', 'weekly', DATE_SUB(CURDATE(), INTERVAL 65 DAY));

SELECT 'Added ' AS status, COUNT(*) AS records FROM calculator_results;