-- Listado de empleados
CREATE OR REPLACE VIEW v_lista_empleados AS
SELECT
    e.emp_no,
    e.first_name,
    e.last_name,
    e.birth_date,
    e.gender,
    e.hire_date,
    d.dept_no,
    d.dept_name,
    t.title,
    s.salary
FROM employees e
JOIN dept_emp de ON e.emp_no = de.emp_no
JOIN departments d ON de.dept_no = d.dept_no
JOIN titles t ON e.emp_no = t.emp_no
JOIN salaries s ON e.emp_no = s.emp_no
WHERE de.to_date = '9999-01-01'
AND t.to_date = '9999-01-01'
AND s.to_date = '9999-01-01';

-- Manager actual de cada departamento
CREATE OR REPLACE VIEW v_managers AS
SELECT
    d.dept_name,
    CONCAT(e.first_name, ' ', e.last_name) AS nombre_manager,
    dm.from_date AS fecha_inicio
FROM dept_manager dm
JOIN employees e ON dm.emp_no = e.emp_no
JOIN departments d ON dm.dept_no = d.dept_no
WHERE dm.to_date = '9999-01-01';

-- Empleado mejor pagado por departamento
CREATE OR REPLACE VIEW v_mejor_pagado AS
SELECT
    d.dept_name,
    e.emp_no,
    CONCAT(e.first_name, ' ', e.last_name) AS nombre_completo,
    s.salary
FROM dept_emp de
JOIN employees e ON de.emp_no = e.emp_no
JOIN salaries s ON de.emp_no = s.emp_no
JOIN departments d ON de.dept_no = d.dept_no
WHERE de.to_date = '9999-01-01'
  AND s.to_date = '9999-01-01'
  AND (de.dept_no, s.salary) IN (
      SELECT de2.dept_no, MAX(s2.salary)
      FROM dept_emp de2
      JOIN salaries s2 ON de2.emp_no = s2.emp_no
      WHERE de2.to_date = '9999-01-01' AND s2.to_date = '9999-01-01'
      GROUP BY de2.dept_no
  );

-- Contrataciones por año
CREATE OR REPLACE VIEW v_contrataciones AS
SELECT
    YEAR(hire_date) AS anio,
    COUNT(*) AS total_contratados
FROM employees
GROUP BY YEAR(hire_date)
ORDER BY anio DESC;

-- Estadísticas por Departamento
CREATE OR REPLACE VIEW v_stats_dept AS
SELECT
    d.dept_name,
    COUNT(de.emp_no) AS total_empleados,
    AVG(s.salary) AS salario_promedio,
    (MAX(s.salary) - MIN(s.salary)) AS brecha_salarial
FROM departments d
JOIN dept_emp de ON d.dept_no = de.dept_no
JOIN salaries s ON de.emp_no = s.emp_no
WHERE de.to_date = '9999-01-01'
AND s.to_date = '9999-01-01'
GROUP BY d.dept_no, d.dept_name;



-- Pastel Hombres vs Mujeres
CREATE OR REPLACE VIEW v_graf_genero AS
SELECT gender, COUNT(*) AS total
FROM employees
GROUP BY gender;

-- 10 Empleados mejor pagados
CREATE OR REPLACE VIEW v_graf_top_10 AS
SELECT CONCAT(e.first_name, ' ', e.last_name) AS nombre, s.salary
FROM employees e
JOIN salaries s ON e.emp_no = s.emp_no
WHERE s.to_date = '9999-01-01'
ORDER BY s.salary DESC
LIMIT 10;

SELECT COUNT(*) FROM employees;

SELECT COUNT(*) FROM salaries;