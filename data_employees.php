<?php
require_once 'db.php';

$pdo = getDB();

$start = $_GET['start'] ?? 0;
$length = $_GET['length'] ?? 10;
$draw = $_GET['draw'] ?? 1;
$searchValue = $_GET['search']['value'] ?? '';
$deptFilter = $_GET['dept_filter'] ?? '';
$orderColumnIndex = $_GET['order'][0]['column'] ?? 0;
$orderDir = $_GET['order'][0]['dir'] ?? 'asc';

$columns = [
    0 => 'emp_no',
    1 => 'first_name',
    2 => 'gender',
    3 => 'birth_date',
    4 => 'hire_date',
    5 => 'dept_name',
    6 => 'title',
    7 => 'salary'
];

$orderBy = $columns[$orderColumnIndex] ?? 'emp_no';

$sqlBase = " FROM v_lista_empleados ";
$whereClauses = [];
$params = [];

if (!empty($searchValue)) {
    $whereClauses[] = "(first_name LIKE :search OR last_name LIKE :search OR title LIKE :search)";
    $params[':search'] = "%$searchValue%";
}

if (!empty($deptFilter)) {
    $whereClauses[] = "dept_name = :dept";
    $params[':dept'] = $deptFilter;
}

$whereSql = "";
if (count($whereClauses) > 0) {
    $whereSql = " WHERE " . implode(" AND ", $whereClauses);
}

$stmtTotal = $pdo->query("SELECT COUNT(*) FROM v_lista_empleados");
$recordsTotal = $stmtTotal->fetchColumn();

$sqlCountFiltered = "SELECT COUNT(*) " . $sqlBase . $whereSql;
$stmtFiltered = $pdo->prepare($sqlCountFiltered);
$stmtFiltered->execute($params);
$recordsFiltered = $stmtFiltered->fetchColumn();

$sqlData = "SELECT * " . $sqlBase . $whereSql . " ORDER BY $orderBy $orderDir LIMIT $start, $length";
$stmtData = $pdo->prepare($sqlData);
foreach ($params as $key => $val) {
    $stmtData->bindValue($key, $val);
}
$stmtData->execute();
$data = $stmtData->fetchAll();

$dataFormatted = [];
foreach ($data as $row) {
    $dataFormatted[] = [
        $row['emp_no'],
        $row['first_name'] . ' ' . $row['last_name'],
        $row['gender'],
        $row['birth_date'],
        $row['hire_date'],
        $row['dept_name'],
        $row['title'],
        '$' . number_format($row['salary'], 2)
    ];
}

header('Content-Type: application/json');
echo json_encode([
    "draw" => intval($draw),
    "recordsTotal" => intval($recordsTotal),
    "recordsFiltered" => intval($recordsFiltered),
    "data" => $dataFormatted
]);