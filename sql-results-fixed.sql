-- Calculator Results - 2 GOOD, 2 BAD, 1 VERY BAD for each user
-- Each result is DIFFERENT and spread over past 2.5 months

INSERT INTO calculator_results (USER_ID, TOTAL_CO2, GRADE, PERIOD, CREATED_AT) VALUES
-- memberrhys (ID 2)
(2, 42.5, 'A', 'weekly', '2026-02-15'),
(2, 55.0, 'B', 'weekly', '2026-03-01'),
(2, 95.0, 'C', 'weekly', '2026-02-28'),
(2, 125.0, 'D', 'weekly', '2026-03-10'),
(2, 198.0, 'F', 'weekly', '2026-02-20'),

-- member rhys 2 (ID 3)
(3, 38.0, 'A', 'weekly', '2026-02-18'),
(3, 48.5, 'A', 'weekly', '2026-03-05'),
(3, 88.0, 'C', 'weekly', '2026-02-25'),
(3, 115.0, 'D', 'weekly', '2026-03-12'),
(3, 205.0, 'F', 'weekly', '2026-02-22'),

-- member rhys 3 (ID 4)
(4, 52.0, 'B', 'weekly', '2026-02-14'),
(4, 65.0, 'B', 'weekly', '2026-03-08'),
(4, 105.0, 'D', 'weekly', '2026-02-27'),
(4, 138.0, 'F', 'weekly', '2026-03-15'),
(4, 188.0, 'F', 'weekly', '2026-02-19'),

-- viewer rhys (ID 5)
(5, 35.0, 'A', 'weekly', '2026-02-16'),
(5, 45.0, 'A', 'weekly', '2026-03-03'),
(5, 92.0, 'C', 'weekly', '2026-02-24'),
(5, 142.0, 'F', 'weekly', '2026-03-11'),
(5, 215.0, 'F', 'weekly', '2026-02-21'),

-- viewer rhys 2 (ID 6)
(6, 40.0, 'A', 'weekly', '2026-02-17'),
(6, 58.0, 'B', 'weekly', '2026-03-06'),
(6, 98.0, 'C', 'weekly', '2026-02-26'),
(6, 128.0, 'F', 'weekly', '2026-03-14'),
(6, 195.0, 'F', 'weekly', '2026-02-23'),

-- viewer rhys 3 (ID 7)
(7, 48.0, 'B', 'weekly', '2026-02-13'),
(7, 62.0, 'B', 'weekly', '2026-03-02'),
(7, 108.0, 'D', 'weekly', '2026-02-29'),
(7, 145.0, 'F', 'weekly', '2026-03-13'),
(7, 225.0, 'F', 'weekly', '2026-02-18');

SELECT cr.*, u.USERNAME 
FROM calculator_results cr 
JOIN users u ON cr.USER_ID = u.USER_ID 
ORDER BY u.USERNAME, cr.CREATED_AT DESC;