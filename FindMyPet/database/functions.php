<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/pdo.php';
require_once __DIR__ . '/../includes/notificationHelper.php';
date_default_timezone_set('Asia/Yangon');

function redirect($url)
{
    header('Location: ' . $url);
    exit();
}

function setMessage($message, $type = 'info')
{
    $_SESSION['form_message'] = $message;
    $_SESSION['form_message_type'] = $type;
}

function alertMessage()
{
    if (isset($_SESSION['status'])) {
        echo '<div class="alert">
        <h4>' . $_SESSION['status'] . '</h4>
        </div>';
        unset($_SESSION['status']);
    }
}

function hasError($field, $errors)
{
    return isset($errors[$field]);
}
function getError($field, $errors)
{
    return $errors[$field] ?? '';
}


function formatDateRange($from, $to)
{
    if (empty($from) && empty($to)) {
        return 'All Time';
    }

    $fromFormatted = !empty($from) ? date('M d, Y', strtotime($from)) : 'Start';
    $toFormatted = !empty($to) ? date('M d, Y', strtotime($to)) : 'Present';
    return $fromFormatted . ' - ' . $toFormatted;
}

function checkParamId($paramType)
{
    if (isset($_GET[$paramType])) {
        if ($_GET[$paramType] != null) {
            return $_GET[$paramType];
        } else {
            return 'No id found.';
        }
    } else {
        return 'No id given.';
    }
}

function getAll(PDO $pdo, $table)
{
    $sql = "SELECT * FROM $table";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function getById(PDO $pdo, $table, $id)
{
    $sql = "SELECT * FROM $table WHERE id = :id LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $response = [
            'status' => 200,
            'data' => $result
        ];
        return $response;
    } else {
        $response = [
            'status' => 404,
            'message' => 'No Data Record'
        ];
        return $response;
    }
}

function deleteQuery(PDO $pdo, $table, $id)
{
    $sql = "DELETE FROM $table WHERE id = :id LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute(['id' => $id]);
    return $result;
}

function buildTabUrl($filterValue)
{
    $params = $_GET;
    $params['filter'] = $filterValue;
    $params[$filterValue . '_page'] = 1;
    return '?' . http_build_query($params);
}

function getUsers(PDO $pdo)
{
    $sql = "SELECT u.*, t.township_name, c.city_name, c.id AS city_id
            FROM users u
            LEFT JOIN township t ON u.township_id = t.id
            LEFT JOIN city c ON t.city_id = c.id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function getUserById(PDO $pdo, $id)
{
    $sql = "SELECT u.*, t.township_name, c.city_name, c.id AS city_id
            FROM users u
            LEFT JOIN township t ON u.township_id = t.id
            LEFT JOIN city c ON t.city_id = c.id
            WHERE u.id = :id LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $response = [
            'status' => 200,
            'data' => $result
        ];
        return $response;
    } else {
        $response = [
            'status' => 404,
            'message' => 'No Data Record'
        ];
        return $response;
    }
}

function getUserByStatus(PDO $pdo, $status)
{
    $sql = "SELECT u.*, t.township_name, c.city_name, c.id AS city_id
            FROM users u
            LEFT JOIN township t ON u.township_id = t.id
            LEFT JOIN city c ON t.city_id = c.id
            WHERE u.status = :status";;
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['status' => $status]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function getBlogById($pdo, $blogId)
{
    $sql = "SELECT b.*, COALESCE(u.full_name, 'Unknown') as author_name 
            FROM blog b 
            LEFT JOIN users u ON b.user_id = u.id 
            WHERE b.id = :id
            LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $blogId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $response = [
            'status' => 200,
            'data' => $result
        ];
        return $response;
    } else {
        $response = [
            'status' => 404,
            'message' => 'No Data Record'
        ];
        return $response;
    }
}

function getReportByStatus(PDO $pdo, $status)
{
    $sql = "SELECT * FROM content_report WHERE status = :status";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['status' => $status]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function getCities(PDO $pdo)
{
    $sql = "SELECT * FROM city ORDER BY city_name ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function getCityById(PDO $pdo, $id)
{
    $sql = "SELECT * FROM city WHERE id = :id LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $response = [
            'status' => 200,
            'data' => $result
        ];
        return $response;
    } else {
        $response = [
            'status' => 404,
            'message' => 'No Data Record'
        ];
        return $response;
    }
}

function getAllTownships(PDO $pdo)
{
    $sql = "SELECT t.*, c.city_name FROM township t JOIN city c ON t.city_id = c.id 
            ORDER BY c.city_name ASC, t.township_name ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function getTownshipById(PDO $pdo, $id)
{
    $sql = "SELECT t.*, c.city_name 
            FROM township t 
            INNER JOIN city c ON t.city_id = c.id 
            WHERE t.id = :id 
            LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        return ['status' => 200, 'data' => $result];
    } else {
        return ['status' => 404, 'message' => 'No Record Found.'];
    }
}

function getTownshipsByCity(PDO $pdo, $id)
{
    $sql = "SELECT * FROM township WHERE city_id = :city_id ORDER BY township_name ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['city_id' => $id]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function getPetTypes(PDO $pdo)
{
    $sql = "SELECT * FROM pet_type ORDER BY type_name ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function getBreeds(PDO $pdo)
{
    $sql = "SELECT b.*, t.type_name FROM breed b JOIN pet_type t ON b.pet_type_id = t.id 
            ORDER BY t.type_name ASC, b.breed_name ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function getBreedById(PDO $pdo, $id)
{
    $sql = "SELECT b.*, t.type_name 
            FROM breed b
            INNER JOIN pet_type t ON b.pet_type_id = t.id 
            WHERE b.id = :id 
            LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        return ['status' => 200, 'data' => $result];
    } else {
        return ['status' => 404, 'message' => 'No Record Found.'];
    }
}

function getBreedsByType(PDO $pdo, $id)
{
    $sql = "SELECT * FROM breed WHERE pet_type_id = :id ORDER BY breed_name ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function getPosts(PDO $pdo)
{
    $sql = "SELECT 
            p.*,
            pet.*,
            b.breed_name,
            pt.type_name AS pet_type_name,
            t.township_name,
            c.city_name
            FROM post p
            INNER JOIN pet pet ON p.pet_id = pet.id
            INNER JOIN breed b ON pet.breed_id = b.id
            INNER JOIN pet_type pt ON b.pet_type_id = pt.id
            INNER JOIN township t ON p.township_id = t.id
            LEFT JOIN city c ON t.city_id = c.id
            WHERE p.status = 'Active'
            ORDER BY p.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function getPostById(PDO $pdo, $id)
{

    $sql = "SELECT 
            p.id,
            p.user_id,
            p.pet_id,
            p.township_id,
            p.post_type,
            p.description,
            p.last_seen_date,
            p.last_seen_time,
            p.last_seen_location,
            p.contact_phone,
            p.contact_email,
            p.status,
            p.created_at AS post_created_at,
            u.full_name,
            pet.*,
            b.breed_name,
            b.pet_type_id, 
            pt.type_name AS pet_type_name,
            t.township_name,
            t.city_id, 
            c.city_name
            FROM post p
            INNER JOIN users u ON p.user_id = u.id
            INNER JOIN pet pet ON p.pet_id = pet.id
            INNER JOIN breed b ON pet.breed_id = b.id
            INNER JOIN pet_type pt ON b.pet_type_id = pt.id
            INNER JOIN township t ON p.township_id = t.id
            LEFT JOIN city c ON t.city_id = c.id
            WHERE p.id = :id 
            LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        return ['status' => 200, 'data' => $result];
    } else {
        return ['status' => 404, 'message' => 'No Record Found.'];
    }
}

function getPostsByStatus(PDO $pdo, $status)
{
    $sql = "SELECT * FROM post WHERE status = :status";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['status' => $status]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function getCommentsByPost(PDO $pdo, $id)
{
    $sql = "SELECT c.*, u.full_name
            FROM comment c
            INNER JOIN users u ON c.user_id = u.id
            WHERE c.post_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function getPostReports(PDO $pdo)
{
    $sql = "SELECT r.*, u.full_name,  
            FROM content_report r
            INNER JOIN users u ON r.user_id = u.id
            INNER JOIN post p ON r.post_id = p.id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function getCommentReports(PDO $pdo)
{
    $sql = "SELECT r.*, u.full_name, c.comment_text 
            FROM content_report r
            INNER JOIN users u ON r.user_id = u.id
            INNER JOIN comment c ON r.comment_id = c.id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function getReportById(PDO $pdo, $id)
{
    $sql = "SELECT r.*, u.full_name, c.comment_text
            FROM content_report r
            INNER JOIN users u ON r.user_id = u.id
            INNER JOIN post p ON r.post_id = p.id
            INNER JOIN comment c ON r.comment_id = c.id
            WHERE r.id = id
            LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        return ['status' => 200, 'data' => $result];
    } else {
        return ['status' => 404, 'message' => 'Invalid Report.'];
    }
}

// Display User Pagination
function renderUserPagination($currentPage, $totalPages, $totalRecords, $perPage, $queryParams = null)
{
    if ($totalPages <= 1) {
        return;
    }

    $baseParams = $queryParams ?? $_GET;

    echo '<div class="pagination-wrapper">';

    // Pagination Info
    echo '<div class="pagination-info">';
    echo 'Showing <span class="font-medium">' . ((($currentPage - 1) * $perPage) + 1) . '</span>';
    echo ' to <span class="font-medium">' . min($currentPage * $perPage, $totalRecords) . '</span>';
    echo ' of <span class="font-medium">' . $totalRecords . '</span> posts';
    echo '</div>';

    echo '<div class="pagination-controls">';

    $buildUrl = function (int $page) use ($baseParams): string {
        $params = array_merge($baseParams, ['page' => $page]);
        return '?' . htmlspecialchars(http_build_query($params), ENT_QUOTES, 'UTF-8');
    };

    // Previous Button
    if ($currentPage > 1) {
        echo '<a href="' . $buildUrl($currentPage - 1) . '" class="pagination-btn"><i class="ri-arrow-left-s-line"></i> Prev</a>';
    } else {
        echo '<span class="pagination-btn disabled"><i class="ri-arrow-left-s-line"></i> Prev</span>';
    }

    // Calculate Visible Page Number Range
    $window = 2;
    $startPage = max(1, $currentPage - $window);
    $endPage = min($totalPages, $currentPage + $window);

    // First Page And Leading Ellipsis
    if ($startPage > 1) {
        echo '<a href="' . $buildUrl(1) . '" class="pagination-btn">1</a>';
        if ($startPage > 2) {
            echo '<span class="pagination-ellipsis">...</span>';
        }
    }

    // Page Numbers Buttons
    for ($i = $startPage; $i <= $endPage; $i++) {
        $activeClass = ($i == $currentPage) ? 'active' : '';
        echo '<a href="' . $buildUrl($i) . '" class="pagination-btn ' . $activeClass . '">' . $i . '</a>';
    }

    // Trailing Ellipsis And Last Page
    if ($endPage < $totalPages) {
        if ($endPage < $totalPages - 1) {
            echo '<span class="pagination-ellipsis">...</span>';
        }
        echo '<a href="' . $buildUrl($totalPages) . '" class="pagination-btn">' . $totalPages . '</a>';
    }

    // Next Button
    if ($currentPage < $totalPages) {
        echo '<a href="' . $buildUrl($currentPage + 1) . '" class="pagination-btn">Next <i class="ri-arrow-right-s-line"></i></a>';
    } else {
        echo '<span class="pagination-btn disabled">Next <i class="ri-arrow-right-s-line"></i></span>';
    }

    echo '</div>';
    echo '</div>';
}

// Display Admin Pagination
function adminPagination($currentPage, $totalPages, $totalRecords, $perPage)
{
    if ($totalPages <= 1) {
        return;
    }

    // Pagination Container
    echo '<div class="admin-pagination">';
    echo '<div class="admin-pagination-info">';

    // Current Record Range And Total Records
    echo 'Showing <span>' . ((($currentPage - 1) * $perPage) + 1) . '</span> to <span>' . min($currentPage * $perPage, $totalRecords) . '</span> of <span>' . $totalRecords . '</span> entries';
    echo '</div>';
    echo '<div class="admin-pagination-controls">';

    // Previous Button
    if ($currentPage > 1) {
        $prevParams = $_GET;
        $prevParams['page'] = $currentPage - 1;
        echo '<a href="?' . htmlspecialchars(http_build_query($prevParams), ENT_QUOTES, 'UTF-8') . '" class="admin-pagination-btn">';
        echo '<i class="ri-arrow-left-s-line"></i> Prev</a>';
    } else {
        echo '<span class="admin-pagination-btn disabled"><i class="ri-arrow-left-s-line"></i> Prev</span>';
    }

    // Calculate Visible Page Number Range
    $window = 2;
    $startPage = max(1, $currentPage - $window);
    $endPage = min($totalPages, $currentPage + $window);

    // First Page And Leading Ellipsis
    if ($startPage > 1) {
        $p1 = $_GET;
        $p1['page'] = 1;
        echo '<a href="?' . htmlspecialchars(http_build_query($p1), ENT_QUOTES, 'UTF-8') . '" class="admin-pagination-btn">1</a>';
        if ($startPage > 2) {
            echo '<span class="pagination-ellipsis">...</span>';
        }
    }

    // Page Numbers Buttons
    for ($i = $startPage; $i <= $endPage; $i++) {
        $p = $_GET;
        $p['page'] = $i;
        $activeClass = ($i == $currentPage) ? 'active' : '';
        echo '<a href="?' . htmlspecialchars(http_build_query($p), ENT_QUOTES, 'UTF-8') . '" class="admin-pagination-btn ' . $activeClass . '">' . $i . '</a>';
    }

    // Trailing Ellipsis And Last Page
    if ($endPage < $totalPages) {
        if ($endPage < $totalPages - 1) {
            echo '<span class="pagination-ellipsis">...</span>';
        }
        $pN = $_GET;
        $pN['page'] = $totalPages;
        echo '<a href="?' . htmlspecialchars(http_build_query($pN), ENT_QUOTES, 'UTF-8') . '" class="admin-pagination-btn">' . $totalPages . '</a>';
    }

    // Next Button
    if ($currentPage < $totalPages) {
        $nextParams = $_GET;
        $nextParams['page'] = $currentPage + 1;
        echo '<a href="?' . htmlspecialchars(http_build_query($nextParams), ENT_QUOTES, 'UTF-8') . '" class="admin-pagination-btn">';
        echo 'Next <i class="ri-arrow-right-s-line"></i></a>';
    } else {
        echo '<span class="admin-pagination-btn disabled">Next <i class="ri-arrow-right-s-line"></i></span>';
    }
    echo '</div>';
    echo '</div>';
}

function getDateCondition($from, $to, $tableAlias = '')
{
    $condition = "";
    $column = $tableAlias ? "$tableAlias.created_at" : "created_at";

    if ($from && $to) {
        $condition = " AND DATE($column) BETWEEN '" . addslashes($from) . "' AND '" . addslashes($to) . "' ";
    } elseif ($from) {
        $condition = " AND DATE($column) >= '" . addslashes($from) . "' ";
    } elseif ($to) {
        $condition = " AND DATE($column) <= '" . addslashes($to) . "' ";
    }

    return $condition;
}

function getPeriodConfig($period, $tableAlias = 'p')
{
    $configs = [
        'weekly' => [
            'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            'key_map' => [2 => 0, 3 => 1, 4 => 2, 5 => 3, 6 => 4, 7 => 5, 1 => 6],
            'sql_key' => "DAYOFWEEK({$tableAlias}.created_at)",
            'sql_where' => "YEARWEEK({$tableAlias}.created_at, 1) = YEARWEEK(CURDATE(), 1)"
        ],
        'monthly' => [
            'labels' => ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5'],
            'key_map' => [1 => 0, 2 => 1, 3 => 2, 4 => 3, 5 => 4],
            'sql_key' => "FLOOR((DAYOFMONTH({$tableAlias}.created_at) - 1) / 7) + 1",
            'sql_where' => "YEAR({$tableAlias}.created_at) = YEAR(CURDATE()) AND MONTH({$tableAlias}.created_at) = MONTH(CURDATE())"
        ],
        'yearly' => [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'key_map' => [1 => 0, 2 => 1, 3 => 2, 4 => 3, 5 => 4, 6 => 5, 7 => 6, 8 => 7, 9 => 8, 10 => 9, 11 => 10, 12 => 11],
            'sql_key' => "MONTH({$tableAlias}.created_at)",
            'sql_where' => "YEAR({$tableAlias}.created_at) = YEAR(CURDATE())"
        ]
    ];

    return $configs[$period] ?? $configs['monthly'];
}

function getPostsTimeSeriesData($pdo, $period, $dateCondition)
{
    $config = getPeriodConfig($period, 'p');

    $sql = "
        SELECT 
            {$config['sql_key']} as time_key,
            SUM(CASE WHEN p.post_type = 'lost' AND p.status != 'Reunited' THEN 1 ELSE 0 END) as lost_count,
            SUM(CASE WHEN p.post_type = 'found' AND p.status != 'Reunited' THEN 1 ELSE 0 END) as found_count,
            SUM(CASE WHEN p.status = 'Reunited' THEN 1 ELSE 0 END) as reunited_count
        FROM post p
        WHERE {$config['sql_where']} $dateCondition
        GROUP BY time_key
    ";

    $raw = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    $labels = $config['labels'];
    $lost = array_fill(0, count($labels), 0);
    $found = array_fill(0, count($labels), 0);
    $reunited = array_fill(0, count($labels), 0);

    foreach ($raw as $row) {
        $key = (int)$row['time_key'];
        if (isset($config['key_map'][$key])) {
            $index = $config['key_map'][$key];
            $lost[$index] = (int)$row['lost_count'];
            $found[$index] = (int)$row['found_count'];
            $reunited[$index] = (int)$row['reunited_count'];
        }
    }

    return [
        'labels' => $labels,
        'lost' => $lost,
        'found' => $found,
        'reunited' => $reunited
    ];
}

function getUsersTimeSeriesData($pdo, $period, $dateCondition)
{
    $config = getPeriodConfig($period, 'u');

    $sql = "
        SELECT 
            {$config['sql_key']} as time_key,
            COUNT(*) as user_count
        FROM users u
        WHERE {$config['sql_where']} $dateCondition
        GROUP BY time_key
    ";

    $raw = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    $labels = $config['labels'];
    $counts = array_fill(0, count($labels), 0);

    foreach ($raw as $row) {
        $key = (int)$row['time_key'];
        if (isset($config['key_map'][$key])) {
            $index = $config['key_map'][$key];
            $counts[$index] = (int)$row['user_count'];
        }
    }

    return [
        'labels' => $labels,
        'counts' => $counts
    ];
}

function getDashboardStatistics($pdo, $dateFrom, $dateTo)
{
    $conditionU = getDateCondition($dateFrom, $dateTo, 'u');
    $conditionP = getDateCondition($dateFrom, $dateTo, 'p');
    $conditionR = getDateCondition($dateFrom, $dateTo, 'r');
    $conditionM = getDateCondition($dateFrom, $dateTo, '');

    $totalUsers = $pdo->query("SELECT COUNT(*) FROM users u WHERE 1=1 $conditionU")->fetchColumn();
    $activeUsers = $pdo->query("SELECT COUNT(*) FROM users u WHERE u.status = 'Active' $conditionU")->fetchColumn();

    if ($dateFrom || $dateTo) {
        $newUsersPeriod = $pdo->query("SELECT COUNT(*) FROM users u WHERE 1=1 $conditionU")->fetchColumn();
    } else {
        $newUsersPeriod = $pdo->query("SELECT COUNT(*) FROM users WHERE created_at >= DATE_FORMAT(CURDATE(), '%Y-%m-01')")->fetchColumn();
    }

    $totalPosts = $pdo->query("SELECT COUNT(*) FROM post p WHERE 1=1 $conditionP")->fetchColumn();
    $reunitedPosts = $pdo->query("SELECT COUNT(*) FROM post p WHERE p.status = 'Reunited' $conditionP")->fetchColumn();
    $successRate = $totalPosts > 0 ? round(($reunitedPosts / $totalPosts) * 100, 1) : 0;

    $pendingReports = $pdo->query("SELECT COUNT(*) FROM content_report r WHERE r.status = 'Pending' $conditionR")->fetchColumn();

    $matchStats = $pdo->query("
        SELECT COUNT(*) as suggested, SUM(CASE WHEN status = 'Accepted' THEN 1 ELSE 0 END) as accepted
        FROM post_match WHERE 1=1 $conditionM
    ")->fetch(PDO::FETCH_ASSOC) ?: ['suggested' => 0, 'accepted' => 0];

    $matchSuggested = $matchStats['suggested'] ?? 0;
    $matchAccepted = $matchStats['accepted'] ?? 0;
    $matchAccuracy = $matchSuggested > 0 ? round(($matchAccepted / $matchSuggested) * 100, 1) : 0;

    return [
        'total_users' => $totalUsers,
        'active_users' => $activeUsers,
        'new_users_period' => $newUsersPeriod,
        'total_posts' => $totalPosts,
        'success_rate' => $successRate,
        'pending_reports' => $pendingReports,
        'match_accuracy' => $matchAccuracy,
        'has_date_filter' => ($dateFrom || $dateTo)
    ];
}

function getPostsDistributionData($pdo, $dateFrom, $dateTo)
{
    $conditionP = getDateCondition($dateFrom, $dateTo, 'p');
    $conditionU = getDateCondition($dateFrom, $dateTo, 'u');

    $postsByType = $pdo->query("SELECT post_type, COUNT(*) AS count FROM post p WHERE 1=1 $conditionP GROUP BY post_type")->fetchAll(PDO::FETCH_KEY_PAIR);
    $userStatusDist = $pdo->query("SELECT status, COUNT(*) as count FROM users u WHERE 1=1 $conditionU GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR);

    // Changed to Top 5 Cities
    $postsByCity = $pdo->query("
        SELECT c.city_name, COUNT(p.id) as count 
        FROM city c 
        LEFT JOIN township t ON c.id = t.city_id
        LEFT JOIN post p ON t.id = p.township_id 
        WHERE 1=1 $conditionP
        GROUP BY c.id ORDER BY count DESC LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);

    $topTownships = $pdo->query("
        SELECT t.township_name, COUNT(p.id) as count 
        FROM township t 
        LEFT JOIN post p ON t.id = p.township_id 
        WHERE 1=1 $conditionP
        GROUP BY t.id ORDER BY count DESC LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);

    $postsByPetType = $pdo->query("
        SELECT pt.type_name, COUNT(p.id) as count 
        FROM pet_type pt 
        LEFT JOIN breed b ON pt.id = b.pet_type_id 
        LEFT JOIN pet pe ON b.id = pe.breed_id 
        LEFT JOIN post p ON pe.id = p.pet_id 
        WHERE 1=1 $conditionP
        GROUP BY pt.id ORDER BY count DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

    return [
        'posts_by_type' => $postsByType,
        'user_status_dist' => $userStatusDist,
        'posts_by_city' => $postsByCity,
        'top_townships' => $topTownships,
        'posts_by_pet_type' => $postsByPetType
    ];
}

function getEngagementAndReportsData($pdo, $dateFrom, $dateTo)
{
    $conditionP = getDateCondition($dateFrom, $dateTo, 'p');
    $conditionR = getDateCondition($dateFrom, $dateTo, 'r');

    $engagedPosts = $pdo->query("
        SELECT p.id, pt.name AS pet_name, p.post_type, COUNT(c.id) AS comment_count
        FROM post p 
        LEFT JOIN pet pt ON p.pet_id = pt.id 
        LEFT JOIN comment c ON p.id = c.post_id 
        WHERE 1=1 $conditionP
        GROUP BY p.id ORDER BY comment_count DESC LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);

    $reportCategories = $pdo->query("SELECT reason, COUNT(*) AS count FROM content_report r WHERE 1=1 $conditionR GROUP BY reason ORDER BY count DESC")->fetchAll(PDO::FETCH_ASSOC);
    $reportStatus = $pdo->query("SELECT status, COUNT(*) as count FROM content_report r WHERE 1=1 $conditionR GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR);

    return [
        'engaged_posts' => $engagedPosts,
        'report_categories' => $reportCategories,
        'report_status' => $reportStatus
    ];
}

// User Filters Conditions
function buildUserFilters($filters)
{
    // Initialize Filter Conditions
    $whereClauses = [];
    $params = [];
    // Filter By User Status
    $status = strtolower($filters['filter'] ?? 'all');

    if ($status === 'active') {
        $whereClauses[] = "u.status = 'Active'";
    } elseif ($status === 'suspended') {
        $whereClauses[] = "u.status = 'Suspended'";
    } elseif ($status === 'banned') {
        $whereClauses[] = "u.status = 'Banned'";
    }
    // Filter by Search Keyword
    if (!empty($filters['search_direct'])) {
        $search = trim($filters['search_direct']);
        $whereClauses[] = "(u.full_name LIKE :search OR u.email LIKE :search)";
        $params['search'] = "%{$search}%";
    }
    // Filter by City
    if (!empty($filters['city_id'])) {
        $whereClauses[] = "t.city_id = :city_id";
        $params['city_id'] = $filters['city_id'];
    }
    // Filter by Township
    if (!empty($filters['township_id'])) {
        $whereClauses[] = "u.township_id = :township_id";
        $params['township_id'] = $filters['township_id'];
    }
    // Filter by Registration Date
    if (!empty($filters['date_from'])) {
        $whereClauses[] = "DATE(u.created_at) >= :date_from";
        $params['date_from'] = $filters['date_from'];
    }
    if (!empty($filters['date_to'])) {
        $whereClauses[] = "DATE(u.created_at) <= :date_to";
        $params['date_to'] = $filters['date_to'];
    }

    return [
        'where' => empty($whereClauses)
            ? ''
            : 'WHERE ' . implode(' AND ', $whereClauses),
        'params' => $params
    ];
}

// Retrieve Total Number of Filtered User
function getFilteredUsersCount(PDO $pdo, $filters)
{
    $filterData = buildUserFilters($filters);
    $sql = "SELECT COUNT(*) FROM users u 
            LEFT JOIN township t ON u.township_id = t.id 
            LEFT JOIN city c ON t.city_id = c.id 
            {$filterData['where']}";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($filterData['params']);
    return $stmt->fetchColumn();
}

// Retrieve Filtered Users
function getFilteredUsers(PDO $pdo, $filters, $limit = null, $offset = null)
{
    $filterData = buildUserFilters($filters);
    $sql = "SELECT u.*, t.township_name, c.city_name FROM users u 
            LEFT JOIN township t ON u.township_id = t.id 
            LEFT JOIN city c ON t.city_id = c.id 
            {$filterData['where']}
            ORDER BY u.created_at DESC";
    // Apply Pagination
    if ($limit !== null && $offset !== null) {
        $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($filterData['params']);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get CSS Badge Class for User Status
function getUserStatusBadgeClass($status)
{
    switch (strtolower($status)) {
        case 'active':
            return 'badge-active';
        case 'suspended':
            return 'badge-suspended';
        case 'banned':
            return 'badge-banned';
        default:
            return 'badge-pending';
    }
}

// Post Filters Conditions
function buildPostFilters($filters)
{
    // Initialize Filter Conditions
    $whereClauses = [];
    $params = [];
    // Filter By Post Status
    $status = $filters['filter'] ?? 'all';
    if ($status === 'active') {
        $whereClauses[] = "p.status = 'Active'";
    } elseif ($status === 'reunited') {
        $whereClauses[] = "p.status = 'Reunited'";
    } else {
        $whereClauses[] = "p.status IN ('Active', 'Reunited')";
    }
    // Filter by Search Keyword
    if (!empty($filters['search_direct'])) {
        $search = trim($filters['search_direct']);
        $whereClauses[] = "(p.description LIKE :search OR pet.name LIKE :search OR u.full_name LIKE :search OR p.contact_phone LIKE :search OR p.contact_email LIKE :search)";
        $params['search'] = "%$search%";
    }
    // Filter by Post Type
    if (!empty($filters['post_type']) && $filters['post_type'] !== 'all') {
        $whereClauses[] = "p.post_type = :post_type";
        $params['post_type'] = ucfirst(strtolower($filters['post_type']));
    }
    // Filter by Pet Type
    if (!empty($filters['pet_type_id']) && $filters['pet_type_id'] !== 'all') {
        $whereClauses[] = "pt.id = :pet_type_id";
        $params['pet_type_id'] = $filters['pet_type_id'];
    }
    // Filter by City
    if (!empty($filters['city_id'])) {
        $whereClauses[] = "t.city_id = :city_id";
        $params['city_id'] = $filters['city_id'];
    }
    // Filter by Township
    if (!empty($filters['township_id'])) {
        $whereClauses[] = "p.township_id = :township_id";
        $params['township_id'] = $filters['township_id'];
    }
    // Filter by Date Range
    if (!empty($filters['date_from'])) {
        $whereClauses[] = "DATE(p.created_at) >= :date_from";
        $params['date_from'] = $filters['date_from'];
    }
    if (!empty($filters['date_to'])) {
        $whereClauses[] = "DATE(p.created_at) <= :date_to";
        $params['date_to'] = $filters['date_to'];
    }

    return [
        'where' => empty($whereClauses)
            ? ''
            : 'WHERE ' . implode(' AND ', $whereClauses),
        'params' => $params
    ];
}

// Retrieve Total Number of Filtered Post
function getFilteredAdminPostsCount(PDO $pdo, $filters)
{
    $filterData = buildPostFilters($filters);
    $sql = "SELECT COUNT(*) 
            FROM post p
            INNER JOIN users u ON p.user_id = u.id
            INNER JOIN pet ON p.pet_id = pet.id
            INNER JOIN breed b ON pet.breed_id = b.id
            INNER JOIN pet_type pt ON b.pet_type_id = pt.id
            INNER JOIN township t ON p.township_id = t.id
            INNER JOIN city c ON t.city_id = c.id
            {$filterData['where']}";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($filterData['params']);
    return $stmt->fetchColumn();
}

// Retrieve Filtered Posts
function getFilteredAdminPosts(PDO $pdo, $filters, $limit = null, $offset = null)
{
    $filterData = buildPostFilters($filters);
    $sql = "SELECT 
                p.id, p.post_type, p.last_seen_date, p.status, p.created_at, p.contact_phone, p.contact_email,
                pet.name AS pet_name, pet.image AS pet_image,
                b.breed_name, 
                pt.type_name AS pet_type_name,
                u.full_name AS owner_name,
                t.township_name, 
                c.city_name
            FROM post p
            INNER JOIN users u ON p.user_id = u.id
            INNER JOIN pet ON p.pet_id = pet.id
            INNER JOIN breed b ON pet.breed_id = b.id
            INNER JOIN pet_type pt ON b.pet_type_id = pt.id
            INNER JOIN township t ON p.township_id = t.id
            INNER JOIN city c ON t.city_id = c.id
           {$filterData['where']}
            ORDER BY p.created_at DESC";

    if ($limit !== null && $offset !== null) {
        $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($filterData['params']);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get CSS Badge Class for Post Type
function getPostTypeBadgeClass($postType)
{
    switch (strtolower($postType)) {
        case 'lost':
            return 'badge-lost';
        case 'found':
            return 'badge-found';
        default:
            return 'badge-pending';
    }
}

// Get CSS Badge Class for Post Status
function getPostStatusBadgeClass($status)
{
    switch (strtolower($status)) {
        case 'active':
            return 'badge-active';
        case 'reunited':
            return 'badge-reunited';
        default:
            return 'badge-pending';
    }
}

function getReportStatusCounts(PDO $pdo)
{
    return [
        'all'       => $pdo->query("SELECT COUNT(*) FROM content_report")->fetchColumn(),
        'Pending'   => $pdo->query("SELECT COUNT(*) FROM content_report WHERE status = 'Pending'")->fetchColumn(),
        'Resolved'  => $pdo->query("SELECT COUNT(*) FROM content_report WHERE status = 'Resolved'")->fetchColumn(),
        'Dismissed' => $pdo->query("SELECT COUNT(*) FROM content_report WHERE status = 'Dismissed'")->fetchColumn()
    ];
}

// Content Report Filters Conditions
function buildReportFilters($filters)
{
    // Initialize Filter Conditions and Parameters
    $whereClauses = [];
    $params = [];
    // Filter By Status
    if ($filters['filter'] !== 'all') {
        $whereClauses[] = "cr.status = :status";
        $params['status'] = $filters['filter'];
    }
    // Filter By Search Keyword
    if (!empty($filters['search'])) {
        $whereClauses[] = "(u.full_name LIKE :search OR cr.reason LIKE :search OR cr.details LIKE :search)";
        $params['search'] = "%{$filters['search']}%";
    }
    // Filter By Target Type
    if ($filters['target'] === 'post') {
        $whereClauses[] = "cr.post_id IS NOT NULL";
    } elseif ($filters['target'] === 'comment') {
        $whereClauses[] = "cr.comment_id IS NOT NULL";
    }
    // Filter By Date Range
    if (!empty($filters['date_from'])) {
        $whereClauses[] = "DATE(cr.created_at) >= :date_from";
        $params['date_from'] = $filters['date_from'];
    }
    if (!empty($filters['date_to'])) {
        $whereClauses[] = "DATE(cr.created_at) <= :date_to";
        $params['date_to'] = $filters['date_to'];
    }

    return [
        'where' => empty($whereClauses)
            ? ''
            : 'WHERE ' . implode(' AND ', $whereClauses),
        'params' => $params
    ];
}

// Retrieve Total Number of Filtered Report
function getFilteredReportsCount(PDO $pdo, $filters)
{
    $filterData = buildReportFilters($filters);
    $sql = "SELECT COUNT(*) 
            FROM content_report cr 
            LEFT JOIN users u ON cr.reporter_id = u.id 
            LEFT JOIN comment c ON cr.comment_id = c.id 
            {$filterData['where']}";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($filterData['params']);
    return $stmt->fetchColumn();
}

// Retrieve Filtered Report
function getFilteredReports(PDO $pdo, $filters, $limit = 10, $offset = 0)
{
    $filterData = buildReportFilters($filters);
    $sql = "SELECT cr.*, 
                   u.full_name AS reporter_name, 
                   p.post_type AS reported_post_type, 
                   c.comment_text AS reported_comment_text,
                   c.post_id AS comment_post_id,
                   COALESCE(p.user_id, c.user_id) AS owner_id
            FROM content_report cr
            LEFT JOIN users u ON cr.reporter_id = u.id
            LEFT JOIN post p ON cr.post_id = p.id
            LEFT JOIN comment c ON cr.comment_id = c.id
            {$filterData['where']}
            ORDER BY cr.created_at DESC";
    if ($limit !== null && $offset !== null) {
        $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($filterData['params']);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get CSS Badge Class for Report Status
function getReportStatusBadgeClass($status)
{
    switch (strtolower($status)) {
        case 'pending':
            return 'badge-pending';
        case 'resolved':
            return 'badge-resolved';
        case 'dismissed':
            return 'badge-dismissed';
        default:
            return 'badge-pending';
    }
}

// Build Match Filter Conditions
function buildMatchFilters($filters)
{
    // Initialize Filter Conditions and Parameters
    $whereClauses = [];
    $params = [];
    // Filter by Match Status
    if (!empty($filters['match_status']) && $filters['match_status'] !== 'all') {
        $whereClauses[] = "pm.status = :match_status";
        $params['match_status'] = ucfirst(strtolower($filters['match_status']));
    }

    // Filter by Search Keyword
    if (!empty($filters['search_direct'])) {
        $whereClauses[] = "(
            pt1.name LIKE :search OR
            pt2.name LIKE :search OR
            u1.full_name LIKE :search OR
            u2.full_name LIKE :search OR
            CAST(pm.post_id AS CHAR) LIKE :search OR
            CAST(pm.matched_post_id AS CHAR) LIKE :search
        )";
        $params['search'] = "%" . trim($filters['search_direct']) . "%";
    }
    // Filter by Post Type
    if (!empty($filters['post_type']) && $filters['post_type'] !== 'all') {
        $whereClauses[] = "(p1.post_type = :post_type OR p2.post_type = :post_type2)";
        $params['post_type'] = ucfirst(strtolower($filters['post_type']));
        $params['post_type2'] = ucfirst(strtolower($filters['post_type']));
    }
    // Filter by Pet Type
    if (!empty($filters['pet_type_id']) && $filters['pet_type_id'] !== 'all') {
        $whereClauses[] = "(pty1.id = :pet_type_id OR pty2.id = :pet_type_id2)";
        $params['pet_type_id'] = (int)$filters['pet_type_id'];
        $params['pet_type_id2'] = (int)$filters['pet_type_id'];
    }
    // Filter by City
    if (!empty($filters['city_id'])) {
        $whereClauses[] = "(c1.id = :city_id OR c2.id = :city_id2)";
        $params['city_id'] = (int)$filters['city_id'];
        $params['city_id2'] = (int)$filters['city_id'];
    }
    // Filter by Township
    if (!empty($filters['township_id'])) {
        $whereClauses[] = "(t1.id = :township_id OR t2.id = :township_id2)";
        $params['township_id'] = (int)$filters['township_id'];
        $params['township_id2'] = (int)$filters['township_id'];
    }
    // Filter by Date Range
    if (!empty($filters['date_from'])) {
        $whereClauses[] = "(p1.created_at >= :date_from OR p2.created_at >= :date_from2)";
        $params['date_from'] = $filters['date_from'] . ' 00:00:00';
        $params['date_from2'] = $filters['date_from'] . ' 00:00:00';
    }
    if (!empty($filters['date_to'])) {
        $whereClauses[] = "(p1.created_at <= :date_to OR p2.created_at <= :date_to2)";
        $params['date_to'] = $filters['date_to'] . ' 23:59:59';
        $params['date_to2'] = $filters['date_to'] . ' 23:59:59';
    }

    return [
        'where' => empty($whereClauses)
            ? ''
            : 'WHERE ' . implode(' AND ', $whereClauses),
        'params' => $params
    ];
}

// Retrieve Total Number of Filtered Matches
function getFilteredMatchCounts($pdo, $filters)
{
    $counts = ['all' => 0, 'pending' => 0, 'accepted' => 0, 'rejected' => 0];
    $baseFilters = $filters;
    unset($baseFilters['match_status']);
    // Build Match Filter Conditions
    $filterData = buildMatchFilters($baseFilters);

    $sql = "SELECT pm.status, COUNT(*) as cnt 
            FROM post_match pm
            JOIN post p1 ON pm.post_id = p1.id 
            JOIN users u1 ON p1.user_id = u1.id 
            JOIN pet pt1 ON p1.pet_id = pt1.id 
            JOIN breed b1 ON pt1.breed_id = b1.id 
            JOIN pet_type pty1 ON b1.pet_type_id = pty1.id 
            JOIN township t1 ON p1.township_id = t1.id 
            JOIN city c1 ON t1.city_id = c1.id
            JOIN post p2 ON pm.matched_post_id = p2.id 
            JOIN users u2 ON p2.user_id = u2.id 
            JOIN pet pt2 ON p2.pet_id = pt2.id 
            JOIN breed b2 ON pt2.breed_id = b2.id 
            JOIN pet_type pty2 ON b2.pet_type_id = pty2.id 
            JOIN township t2 ON p2.township_id = t2.id 
            JOIN city c2 ON t2.city_id = c2.id
            {$filterData['where']} GROUP BY pm.status";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($filterData['params']);
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $statusKey = strtolower($row['status']);

        if (isset($counts[$statusKey])) {
            $counts[$statusKey] = (int)$row['cnt'];
        }
        $counts['all'] += (int)$row['cnt'];
    }

    return $counts;
}

// Retrieve Filtered Matches
function getFilteredMatches($pdo, $filters, $limit, $offset)
{
    $filterData = buildMatchFilters($filters);

    $sql = "SELECT 
                pm.id AS match_id, pm.post_id, pm.matched_post_id, pm.match_score, pm.status AS match_status,
                p1.post_type AS orig_post_type, p1.created_at AS orig_post_date, pt1.name AS orig_pet_name, pty1.type_name AS orig_pet_type, u1.full_name AS orig_owner, c1.city_name AS orig_city, t1.township_name AS orig_township, pt1.image AS orig_image,
                p2.post_type AS match_post_type, p2.created_at AS match_post_date, pt2.name AS match_pet_name, pty2.type_name AS match_pet_type, u2.full_name AS match_owner, c2.city_name AS match_city, t2.township_name AS match_township, pt2.image AS match_image
            FROM post_match pm
            JOIN post p1 ON pm.post_id = p1.id 
            JOIN pet pt1 ON p1.pet_id = pt1.id 
            JOIN breed b1 ON pt1.breed_id = b1.id 
            JOIN pet_type pty1 ON b1.pet_type_id = pty1.id 
            JOIN users u1 ON p1.user_id = u1.id 
            JOIN township t1 ON p1.township_id = t1.id 
            JOIN city c1 ON t1.city_id = c1.id
            JOIN post p2 ON pm.matched_post_id = p2.id 
            JOIN pet pt2 ON p2.pet_id = pt2.id 
            JOIN breed b2 ON pt2.breed_id = b2.id 
            JOIN pet_type pty2 ON b2.pet_type_id = pty2.id 
            JOIN users u2 ON p2.user_id = u2.id 
            JOIN township t2 ON p2.township_id = t2.id 
            JOIN city c2 ON t2.city_id = c2.id
            {$filterData['where']}
            ORDER BY pm.id DESC";
    if ($limit !== null && $offset !== null) {
        $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($filterData['params']);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get CSS Badge Class for Match Status
function getMatchStatusBadgeClass($status)
{
    switch (strtolower($status)) {
        case 'pending':
            return 'badge-pending';
        case 'accepted':
            return 'badge-accepted';
        case 'rejected':
            return 'badge-rejected';
        default:
            return 'badge-pending';
    }
}

// Comment Filters Conditions
function buildCommentFilters($search)
{
    // Initialize Filter Conditions and Parameters
    $whereClauses = [];
    $params = [];
    // Filter By Search Keyword
    if (!empty($search)) {
        $whereClauses[] = "c.comment_text LIKE :search OR u.full_name LIKE :search";
        $params['search'] = "%$search%";
    }
    return [
        'where' => empty($whereClauses)
            ? ''
            : 'WHERE ' . implode(' AND ', $whereClauses),
        'params' => $params
    ];
}

// Retrieve Total Number of Filtered Comment
function getFilteredCommentsCount(PDO $pdo, $search = '')
{
    $filterData = buildCommentFilters($search);
    $sql = "SELECT COUNT(*) FROM comment c JOIN users u ON c.user_id = u.id {$filterData['where']}";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($filterData['params']);
    return $stmt->fetchColumn();
}

// Retrieve Filtered Comment
function getFilteredComments(PDO $pdo, $search = '', $limit = 10, $offset = 0)
{
    $filterData = buildCommentFilters($search);
    $sql = "SELECT c.*, 
                   u.full_name AS commenter_name, 
                   po.full_name AS post_owner_name, 
                   p.post_type
            FROM comment c
            JOIN users u ON c.user_id = u.id
            JOIN post p ON c.post_id = p.id
            JOIN users po ON p.user_id = po.id
            {$filterData['where']}
            ORDER BY c.created_at DESC";
    if ($limit !== null && $offset !== null) {
        $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($filterData['params']);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Contact Message Filters Conditions
function buildContactMessageFilters($search)
{
    // Initialize Filter Conditions and Parameters
    $whereClauses = [];
    $params = [];
    // Filter by Search Keyword
    if (!empty($search)) {
        $whereClauses[] = "full_name LIKE :search OR email LIKE :search OR subject LIKE :search OR message LIKE :search";
        $params['search'] = "%$search%";
    }
    return [
        'where' => empty($whereClauses)
            ? ''
            : 'WHERE ' . implode(' AND ', $whereClauses),
        'params' => $params
    ];
}

// Retrieve Total Number of Filtered Contact Message
function getContactMessagesCount(PDO $pdo, $search = '')
{
    $filterData = buildContactMessageFilters($search);
    $sql = "SELECT COUNT(*) FROM contact_message {$filterData['where']}";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($filterData['params']);
    return $stmt->fetchColumn();
}

// Retrieve Filtered Contact Message
function getFilteredContactMessages(PDO $pdo, $search = '', $limit = 10, $offset = 0)
{
    $filterData = buildContactMessageFilters($search);
    $sql = "SELECT * FROM contact_message {$filterData['where']} ORDER BY created_at DESC";
    if ($limit !== null && $offset !== null) {
        $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($filterData['params']);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function getUserTypeBadgeClass($isRegistered)
{
    return $isRegistered ? 'badge-registered' : 'badge-guest';
}

// Announcement Filters Conditions
function buildAnnouncementFilters($filters)
{
    // Initialize Filter Conditions and Parameters
    $whereClauses = [];
    $params = [];

    // Retrieve Announcement Notifications Only
    $whereClauses[] = "type = 'announcement'";

    // Filter By Search Keyword
    if (!empty($filters['search'])) {
        $whereClauses[] = "message LIKE :search";
        $params['search'] = "%" . trim($filters['search']) . "%";
    }
    // Filter by Date Range
    if (!empty($filters['date_from'])) {
        $whereClauses[] = "DATE(created_at) >= :date_from";
        $params['date_from'] = $filters['date_from'];
    }

    if (!empty($filters['date_to'])) {
        $whereClauses[] = "DATE(created_at) <= :date_to";
        $params['date_to'] = $filters['date_to'];
    }

    return [
        'where' => empty($whereClauses)
            ? ''
            : 'WHERE ' . implode(' AND ', $whereClauses),
        'params' => $params
    ];
}

// Retrieve Total Number of Filtered Announcements
function getFilteredAnnouncementsCount($pdo, $filters)
{
    $filterData = buildAnnouncementFilters($filters);
    $sql = "SELECT COUNT(DISTINCT message) FROM notification {$filterData['where']}";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($filterData['params']);
    return $stmt->fetchColumn();
}

// Retrieve Filtered Announcements
function getFilteredAnnouncements($pdo, $filters, $limit = 10, $offset = 0)
{
    $filterData = buildAnnouncementFilters($filters);
    $sql = "SELECT MIN(id) AS id, message, MAX(created_at) AS created_at 
            FROM notification 
            {$filterData['where']} 
            GROUP BY message 
            ORDER BY created_at DESC";
    if ($limit !== null && $offset !== null) {
        $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($filterData['params']);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Blog Filters Conditions
function buildBlogFilters($filters)
{
    // Initialize Filter Conditions and Parameters
    $whereClauses = [];
    $params = [];
    // Filter By Search Keyword
    if (!empty($filters['search_direct'])) {
        $whereClauses[] = "(b.title LIKE :search OR u.full_name LIKE :search)";
        $params['search'] = "%" . trim($filters['search_direct']) . "%";
    }
    // Filter by Category
    if (!empty($filters['category']) && $filters['category'] !== 'all') {
        $whereClauses[] = "b.category = :category";
        $params['category'] = $filters['category'];
    }
    // Filter by Date Range
    if (!empty($filters['date_from'])) {
        $whereClauses[] = "DATE(b.created_at) >= :date_from";
        $params['date_from'] = $filters['date_from'];
    }
    if (!empty($filters['date_to'])) {
        $whereClauses[] = "DATE(b.created_at) <= :date_to";
        $params['date_to'] = $filters['date_to'];
    }
    return [
        'where' => empty($whereClauses)
            ? ''
            : 'WHERE ' . implode(' AND ', $whereClauses),
        'params' => $params
    ];
}

// Retrieve Total Number of Filtered Blogs
function getFilteredBlogsCount(PDO $pdo, $filters)
{
    $filterData = buildBlogFilters($filters);
    $sql = "SELECT COUNT(*)
            FROM blog b
            LEFT JOIN users u ON b.user_id = u.id
            {$filterData['where']}";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($filterData['params']);
    return $stmt->fetchColumn();
}

// Retrieve Filtered Blogs
function getFilteredBlogs(PDO $pdo, $filters, $limit = null, $offset = null)
{
    $filterData = buildBlogFilters($filters);
    $sql = "SELECT b.id, b.title, b.content, b.image, b.category, b.created_at, u.full_name AS author_name
            FROM blog b
            LEFT JOIN users u ON b.user_id = u.id
            {$filterData['where']}
            ORDER BY b.created_at DESC";
    if ($limit !== null && $offset !== null) {
        $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($filterData['params']);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCategoryBadgeClass($category)
{
    switch (strtolower($category)) {
        case 'emergency':
            return 'badge-emergency';
        case 'health':
            return 'badge-health';
        case 'prevention':
            return 'badge-prevention';
        case 'community':
        default:
            return 'badge-community';
    }
}

// Pet Type Filters Conditions
function buildPetTypeFilters($search)
{
    // Initialize Filter Conditions and Parameters
    $whereClauses = [];
    $params = [];
    // Filter by Search Keyword
    if (!empty($search)) {
        $whereClauses[] = "type_name LIKE :search";
        $params['search'] = "%{$search}%";
    }
    return [
        'where' => empty($whereClauses)
            ? ''
            : 'WHERE ' . implode(' AND ', $whereClauses),
        'params' => $params
    ];
}

// Retrieve Total Number of Filtered Pet Type
function getPetTypesCount(PDO $pdo, $search = '')
{
    $filterData = buildPetTypeFilters($search);
    $sql = "SELECT COUNT(*) FROM pet_type {$filterData['where']}";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($filterData['params']);
    return $stmt->fetchColumn();
}

// Retrieve Filtered Pet Type
function getFilteredPetTypes(PDO $pdo, $search = '', $limit = 10, $offset = 0)
{
    $filterData = buildPetTypeFilters($search);
    $sql = "SELECT * FROM pet_type {$filterData['where']} ORDER BY type_name ASC";
    if ($limit !== null && $offset !== null) {
        $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($filterData['params']);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Breed Filters Conditions
function buildBreedFilters($search, $filter)
{
    // Initialize Filter Conditions and Parameters
    $whereClauses = [];
    $params = [];
    // Filter by Search Keyword
    if (!empty($search)) {
        $whereClauses[] = "b.breed_name LIKE :search";
        $params['search'] = "%$search%";
    }
    // Filter by Pet Type
    if ($filter !== 'all') {
        $whereClauses[] = "b.pet_type_id = :filter_type";
        $params['filter_type'] = $filter;
    }
    return [
        'where' => empty($whereClauses)
            ? ''
            : 'WHERE ' . implode(' AND ', $whereClauses),
        'params' => $params
    ];
}

// Retrieve Total Number of Filtered Breed
function getFilteredBreedsCount(PDO $pdo, $search = '', $filter = 'all')
{
    $filterData = buildBreedFilters($search, $filter);
    $sql = "SELECT COUNT(*) FROM breed b {$filterData['where']}";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($filterData['params']);
    return $stmt->fetchColumn();
}

// Retrieve Filtered Breed
function getFilteredBreeds(PDO $pdo, $search = '', $filter = 'all', $limit = 10, $offset = 0)
{
    $filterData = buildBreedFilters($search, $filter);
    $sql = "SELECT b.*, pt.type_name 
            FROM breed b 
            JOIN pet_type pt ON b.pet_type_id = pt.id 
            {$filterData['where']}
            ORDER BY pt.type_name ASC, b.breed_name ASC";
    if ($limit !== null && $offset !== null) {
        $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($filterData['params']);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// City Filters Conditions
function buildCityFilters($search)
{
    // Initialize Filter Conditions and Parameters
    $whereClauses = [];
    $params = [];
    // Filter by Search Keyword
    if (!empty($search)) {
        $whereClauses[] = "city_name LIKE :search";
        $params['search'] = "%{$search}%";
    }
    return [
        'where' => empty($whereClauses)
            ? ''
            : 'WHERE ' . implode(' AND ', $whereClauses),
        'params' => $params
    ];
}

// Retrieve Total Number of Filtered City
function getCitiesCount(PDO $pdo, $search = '')
{
    $filterData = buildCityFilters($search);
    $sql = "SELECT COUNT(*) FROM city {$filterData['where']}";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($filterData['params']);
    return $stmt->fetchColumn();    
}

// Retrieve Filtered City
function getFilteredCities(PDO $pdo, $search = '', $limit = 10, $offset = 0)
{
    $filterData = buildCityFilters($search);
    $sql = "SELECT * FROM city  {$filterData['where']} ORDER BY city_name ASC";
    if ($limit !== null && $offset !== null) {
        $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($filterData['params']);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Township Filters Conditions
function buildTownshipFilters($search, $filter_city)
{
    // Initialize Filter Conditions and Parameters
    $whereClauses = [];
    $params = [];
    // Filter By Search Keyword
    if (!empty($search)) {
        $whereClauses[] = "t.township_name LIKE :search";
        $params['search'] = "%$search%";
    }
    // Filter by City
    if (!empty($filter_city)) {
        $whereClauses[] = "t.city_id = :city_id";
        $params['city_id'] = $filter_city;
    }
    return [
        'where' => empty($whereClauses)
            ? ''
            : 'WHERE ' . implode(' AND ', $whereClauses),
        'params' => $params
    ];
}

// Retrieve Total Number of Filtered Township
function getFilteredTownshipsCount(PDO $pdo, $search = '', $filter_city = '')
{
    $filterData = buildTownshipFilters($search, $filter_city);
    $sql = "SELECT COUNT(*) FROM township t {$filterData['where']}";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($filterData['params']);
    return $stmt->fetchColumn();
}

// Retrieve Filtered Township
function getFilteredTownships(PDO $pdo, $search = '', $filter_city = '', $limit = 10, $offset = 0)
{
    $filterData = buildTownshipFilters($search, $filter_city);
    $sql = "SELECT t.*, c.city_name 
            FROM township t 
            JOIN city c ON t.city_id = c.id 
            {$filterData['where']}
            ORDER BY c.city_name ASC, t.township_name ASC";
    if ($limit !== null && $offset !== null) {
        $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($filterData['params']);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Retrieve Filtered Posts
function getFilteredPosts($pdo, $filters)
{
    // Get filter values
    $post_type = $filters['post_type'] ?? 'all';
    $pet_type  = $filters['pet_type'] ?? 'all';
    $city      = $filters['city'] ?? 'all';
    $township  = $filters['township'] ?? 'all';
    $dateFrom = $filters['date_from'] ?? '';
    $dateTo   = $filters['date_to'] ?? '';
    $sort      = $filters['sort'] ?? 'newest';

    $status    = $filters['status'] ?? 'active';

    // Search parameters
    $search_query = trim($filters['q'] ?? '');
    $pet_name     = trim($filters['pet_name'] ?? '');
    $breed_id     = !empty($filters['breed']) ? (int)$filters['breed'] : 0;

    $sql = "
        SELECT 
            p.*,
            pt.name AS pet_name, pt.color, pt.image, 
            br.breed_name, br.id AS breed_id,
            pty.type_name AS pet_type_name, pty.id AS pet_type_id,
            c.city_name, c.id AS city_id,
            t.township_name, t.id AS township_id
        FROM post p
        JOIN pet pt ON p.pet_id = pt.id
        JOIN breed br ON pt.breed_id = br.id
        JOIN pet_type pty ON br.pet_type_id = pty.id
        JOIN township t ON p.township_id = t.id
        JOIN city c ON t.city_id = c.id
        WHERE 1 = 1
    ";

    $params = [];

    // Filter by post status
    if ($status === 'active') {
        $sql .= " AND p.status = 'Active'";
    } elseif ($status === 'reunited') {
        $sql .= " AND p.status = 'Reunited'";
    }

    // Filter by post type
    if ($post_type !== 'all') {
        $sql .= " AND LOWER(p.post_type) = :post_type";
        $params['post_type'] = strtolower($post_type);
    }

    // Filter by pet type
    if ($pet_type !== 'all') {
        $sql .= " AND pty.id = :pet_type";
        $params['pet_type'] = (int)$pet_type;
    }

    // Filter by city
    if ($city !== 'all') {
        $sql .= " AND c.id = :city";
        $params['city'] = (int)$city;
    }

    // Filter by township
    if ($township !== 'all') {
        $sql .= " AND t.id = :township";
        $params['township'] = (int)$township;
    }

    // Filter by date range
    if (!empty($dateFrom)) {
        $sql .= " AND p.last_seen_date >= :date_from";
        $params['date_from'] = $dateFrom;
    }
    if (!empty($dateTo)) {
        $sql .= " AND p.last_seen_date <= :date_to";
        $params['date_to'] = $dateTo;
    }

    // Search by pet name
    if (!empty($pet_name)) {
        $sql .= " AND LOWER(pt.name) LIKE :pet_name";
        $params['pet_name'] = '%' . strtolower($pet_name) . '%';
    }

    //Search by breed
    if ($breed_id > 0) {
        $sql .= " AND br.id = :breed_id";
        $params['breed_id'] = $breed_id;
    }


    if (!empty($search_query)) {
        $sql .= " AND (
            LOWER(p.post_type) LIKE :q1 OR
            LOWER(pt.name) LIKE :q2 OR
            LOWER(br.breed_name) LIKE :q3 OR 
            LOWER(pty.type_name) LIKE :q4 OR
            LOWER(t.township_name) LIKE :q5 OR
            LOWER(c.city_name) LIKE :q6
        )";
        $q = '%' . strtolower($search_query) . '%';
        $params['q1'] = $q;
        $params['q2'] = $q;
        $params['q3'] = $q;
        $params['q4'] = $q;
        $params['q5'] = $q;
        $params['q6'] = $q;
    }

    // Sort by created date
    if ($sort === 'oldest') {
        $sql .= " ORDER BY p.created_at ASC";
    } else {
        $sql .= " ORDER BY p.created_at DESC";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getFirstNWords($text, $wordCount = 20)
{
    if (empty($text)) return '';
    $words = explode(' ', trim($text));
    $selectedWords = array_slice($words, 0, $wordCount);
    $short = implode(' ', $selectedWords);
    return (count($words) > $wordCount) ? $short . '...' : $short;
}

// Process and resize a pet image for poster
function processPetImage($imagePath, $targetWidth = 400, $targetHeight = 400)
{
    if (!file_exists($imagePath)) return null;

    // Retrieve image information and validate the file
    $imageInfo = getimagesize($imagePath);
    if (!$imageInfo) return null;

    // Create an image resource based on the image MIME type
    $mimeType = $imageInfo['mime'];
    switch ($mimeType) {
        case 'image/jpeg':
            $source = imagecreatefromjpeg($imagePath);
            break;
        case 'image/png':
            $source = imagecreatefrompng($imagePath);
            break;
        case 'image/gif':
            $source = imagecreatefromgif($imagePath);
            break;
        default:
            return null;
    }

    // Get original image dimensions
    $sourceWidth = imagesx($source);
    $sourceHeight = imagesy($source);

    // Calculate crop for centered square image
    if ($sourceWidth > $sourceHeight) {
        $cropWidth = $sourceHeight;
        $cropHeight = $sourceHeight;
        $cropX = ($sourceWidth - $cropWidth) / 2;
        $cropY = 0;
    } else {
        $cropWidth = $sourceWidth;
        $cropHeight = $sourceWidth;
        $cropX = 0;
        $cropY = ($sourceHeight - $cropHeight) / 2;
    }

    // Create new canvas with target dimensions
    $resized = imagecreatetruecolor($targetWidth, $targetHeight);

    // Make background white
    $white = imagecolorallocate($resized, 255, 255, 255);
    imagefill($resized, 0, 0, $white);

    // Crop and resize the image
    imagecopyresampled($resized, $source, 0, 0, $cropX, $cropY, $targetWidth, $targetHeight, $cropWidth, $cropHeight);

    // Save the processed image as a temporary JPEG file
    $tempPath = sys_get_temp_dir() . '/poster_pet_' . uniqid() . '.jpg';
    imagejpeg($resized, $tempPath, 90);

    imagedestroy($source);
    imagedestroy($resized);

    return $tempPath;
}

// Generate the HTML template
function generatePosterHtml($post)
{
    $imageBase64 = '';

    // Process uploaded pet image and convert it to Base64
    if (!empty($post['image'])) {

        $fullImagePath = __DIR__ . '/../' . $post['image'];
        $tempImagePath = processPetImage($fullImagePath);

        if ($tempImagePath && file_exists($tempImagePath)) {
            $imageData = file_get_contents($tempImagePath);
            $imageBase64 = 'data:image/jpeg;base64,' . base64_encode($imageData);
            unlink($tempImagePath);
        }
    }

    // Set title and color based on post type
    $posterTitle = strtoupper($post['post_type']) . ' PET';
    $titleColor = (strtolower($post['post_type']) === 'lost') ? '#d32f2f' : '#388e3c';

    // Create HTML template
    $html = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='utf-8'>
        <style>
            body { font-family: 'Arial', sans-serif; margin: 0; padding: 20px; background-color: #ffffff; color: #333333; }
            .poster-container { width: 600px; margin: 0 auto; border: 6px solid {$titleColor}; padding: 25px; text-align: center; }
            .poster-title { font-size: 52px; font-weight: 900; color: {$titleColor}; margin: 0 0 15px 0; text-transform: uppercase; letter-spacing: 2px; }
            .pet-image { width: 400px; height: 400px; object-fit: cover; border: 3px solid #cccccc; margin: 15px auto; display: block; }
            .pet-details { text-align: left; margin: 25px 40px; font-size: 18px; line-height: 1.8; }
            .pet-details strong { color: #000000; display: inline-block; width: 160px; }
            .contact-info { margin-top: 30px; padding: 20px; background-color: #f5f5f5; border-radius: 8px; font-size: 20px; font-weight: bold; border-left: 5px solid {$titleColor}; }
            .footer { margin-top: 25px; font-size: 14px; color: #777777; border-top: 1px solid #eee; padding-top: 15px; }
        </style>
    </head>
    <body>
        <div class='poster-container'>
            <h1 class='poster-title'>{$posterTitle}</h1>
            " . (!empty($imageBase64) ? "<img src='{$imageBase64}' class='pet-image' alt='Pet Image'>" : "<div class='pet-image' style='display:flex;align-items:center;justify-content:center;background:#eeeeee;color:#888;'>No Image Available</div>") . "
            <div class='pet-details'>
                <p><strong>Pet Name:</strong> " . htmlspecialchars($post['name']) . "</p>
                <p><strong>Pet Type:</strong> " . htmlspecialchars($post['pet_type_name']) . "</p>
                <p><strong>Breed:</strong> " . htmlspecialchars($post['breed_name'] ?? 'Unknown') . "</p>
                <p><strong>Color:</strong> " . htmlspecialchars($post['color']) . "</p>
                <p><strong>Description:</strong> " . htmlspecialchars($post['description']) . "</p>
                <p><strong>City & Township:</strong> " . htmlspecialchars($post['city_name'] ?? '') . ", " . htmlspecialchars($post['township_name']) . "</p>
                <p><strong>Last Seen:</strong> " . htmlspecialchars($post['last_seen_location']) . "</p>
                <p><strong>Date:</strong> " . htmlspecialchars($post['last_seen_date']) . "</p>
                <p><strong>Contact Phone:</strong> " . htmlspecialchars($post['contact_phone']) . "</p>
                <p><strong>Contact Email:</strong> " . htmlspecialchars($post['contact_email']) . "</p>
            </div>
            <div class='footer'>
                <p>Generated by FindMyPet</p>
            </div>
        </div>
    </body>
    </html>
    ";

    return $html;
}

// Process New Active Post
function processActivePost($pdo, $newPostId, $data)
{
    // Retrieve Context for Notification and Match
    $context = getPostContext($pdo, $data);
    // Notify Users in Same Township
    notifyTownshipUsers($pdo, $newPostId, $data, $context);
    // Process Potential Matches
    processPostMatches($pdo, $newPostId, $data, $context);
}

// Retrieve Post Context for Notification & Match
function getPostContext($pdo, $data)
{
    $context = [];

    // Retrieve Pet Type
    $petTypeSql = "SELECT pet_type_id FROM breed WHERE id = :id";
    $petTypeStmt = $pdo->prepare($petTypeSql);
    $petTypeStmt->execute(['id' => $data['breed_id']]);
    $context['pet_type_id'] = $petTypeStmt->fetchColumn();

    // Retrieve City ID and Township Name
    $townshipSql = "SELECT city_id, township_name FROM township WHERE id = :id";
    $townshipStmt = $pdo->prepare($townshipSql);
    $townshipStmt->execute([
        'id' => $data['township_id']
    ]);

    $township = $townshipStmt->fetch(PDO::FETCH_ASSOC);

    $context['city_id'] = $township['city_id'];
    $context['township_name'] = $township['township_name'];

    // Retrieve City Name
    $citySql = "SELECT city_name FROM city WHERE id = :id";
    $cityStmt = $pdo->prepare($citySql);
    $cityStmt->execute(['id' => $context['city_id']]);
    $context['city_name'] = $cityStmt->fetchColumn();

    return $context;
}

// Retrieve User Notification Preferences
function getUserNotiPreferences(PDO $pdo, $userId)
{
    $sql = "SELECT email_notifications, match_notifications, comment_notifications, post_notifications 
            FROM users WHERE id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $userId]);
    $preferences = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$preferences) {
        return [
            'email_notifications' => 1,
            'match_notifications' => 1,
            'comment_notifications' => 1,
            'post_notifications' => 1
        ];
    }

    return [
        'email_notifications' => $preferences['email_notifications'],
        'match_notifications' => $preferences['match_notifications'],
        'comment_notifications' => $preferences['comment_notifications'],
        'post_notifications' => $preferences['post_notifications']
    ];
}

// Notify Users for New Active Post
function notifyTownshipUsers($pdo, $newPostId, $data, $context)
{
    $notiHelper = new NotificationHelper($pdo);
    $townshipId = (int)$data['township_id'];
    $userId = (int)$data['user_id'];
    $users = [];

    try {
        // Retrieve Users in Same Township
        $userSql = "SELECT id, email, email_notifications, post_notifications FROM users WHERE township_id = :township_id AND id != :user_id";
        $userStmt = $pdo->prepare($userSql);
        $userStmt->execute([
            'township_id' => $townshipId,
            'user_id' => $userId
        ]);
        $users = $userStmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Notify Users Error: " . $e->getMessage());
    }

    if (!empty($users)) {
        $postTypeLabel = $data['post_type'];
        $message = "A new {$postTypeLabel} pet post was created in your township, {$context['township_name']}.";
        $link = "post_details.php?id=" . $newPostId;

        $emailSubject = "New {$postTypeLabel} Pet Post in {$context['township_name']}";
        $emailBody = "
            <h2>Hello,</h2>
            <p>A new <strong>{$postTypeLabel}</strong> pet post was just created in your township, <strong>{$context['township_name']}</strong>.</p>
            <p>Click the link below to view the details and see if you can help:</p>
            <a href='http://localhost/cproj/user/{$link}'>View Post Details</a>
            <p>Thank you for helping reunite pets with their families!</p>
            <p>Best regards,<br>FindMyPet Team</p>
        ";

        // Send Notifications Based on User Preferences
        foreach ($users as $user) {
            $uid = (int)$user['id'];

            // Send In-App Notification
            if (!empty($user['post_notifications'])) {
                $notiHelper->sendToUser($uid, $newPostId, $message, 'new_post', $link);
            }

            // Send Email Notification
            if (!empty($user['email_notifications']) && !empty($user['email'])) {
                sendEmailNotification($user['email'], $emailSubject, $emailBody);
            }
        }
    }
}

// Calculate and Store Potential Matches
function processPostMatches($pdo, $newPostId, $data, $context)
{
    $notiHelper = new NotificationHelper($pdo);

    $userId = (int)$data['user_id'];
    $townshipId = (int)$data['township_id'];

    $newPostLink = "post_details.php?id=" . $newPostId;
    $matchesLink = "potential_matches.php?id=" . $newPostId;

    $postType = ucfirst(strtolower($data['post_type']));
    $postTypeLabel = $postType;
    $oppositeType = ($postType === 'Lost') ? 'Found' : 'Lost';
    $oppositeTypeLabel = $oppositeType;

    $matchSql = "
                SELECT p.id, p.user_id, p.last_seen_date, p.township_id, pt.gender, pt.color, b.pet_type_id, b.id AS breed_id
                FROM post p
                JOIN pet pt ON p.pet_id = pt.id
                JOIN breed b ON pt.breed_id = b.id
                JOIN township t ON p.township_id = t.id
                WHERE p.post_type = :post_type
                AND b.pet_type_id = :pet_type_id 
                AND p.user_id != :user_id
                AND p.status = 'Active'
                AND t.city_id = :city_id
                AND ABS(DATEDIFF(p.last_seen_date, :last_seen_date)) <= 30
            ";
    $matchStmt = $pdo->prepare($matchSql);
    $matchStmt->execute([
        'post_type' => $oppositeType,
        'pet_type_id' => $context['pet_type_id'],
        'user_id' => $userId,
        'city_id' => $context['city_id'],
        'last_seen_date' => $data['last_seen_date']
    ]);
    $matches = $matchStmt->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($matches)) {
        // Insert Match Record
        $insertMatchSql = "INSERT INTO post_match (post_id, matched_post_id, match_score) VALUES (:post_id, :matched_post_id, :score)";
        $insertMatchStmt = $pdo->prepare($insertMatchSql);

        $matchCount = 0;
        // Calculate Match Score
        foreach ($matches as $match) {
            $score = 0;

            if ((int)$match['breed_id'] === (int)$data['breed_id']) {
                $score += 30;
            }

            if (!empty($match['color']) && !empty($data['pet_color']) && strtolower(trim($match['color'])) === strtolower(trim($data['pet_color']))) {
                $score += 20;
            }

            if (!empty($match['gender']) && !empty($data['pet_gender']) && strtolower($match['gender']) === strtolower($data['pet_gender'])) {
                $score += 10;
            }

            if ((int)$match['township_id'] === $townshipId) {
                $score += 25;
            } else {
                $score += 15;
            }

            $daysDiff = abs(strtotime($match['last_seen_date']) - strtotime($data['last_seen_date'])) / 86400;
            if ($daysDiff <= 3) {
                $score += 15;
            } elseif ($daysDiff <= 7) {
                $score += 10;
            } elseif ($daysDiff <= 14) {
                $score += 5;
            } else {
                $score += 2;
            }

            $score = min($score, 100);

            // Store Match Record for Both Posts
            $insertMatchStmt->execute([
                'post_id' => $newPostId,
                'matched_post_id' => $match['id'],
                'score' => $score
            ]);
            $insertMatchStmt->execute([
                'post_id' => $match['id'],
                'matched_post_id' => $newPostId,
                'score' => $score
            ]);

            // Retrieve User Preference
            $matchPreferences = getUserNotiPreferences($pdo, (int)$match['user_id']);
            // Send Match Notification If Enabled
            if ($matchPreferences['match_notifications']) {
                $matchMessage = "A potential match (" . round($score) . "%) was found for your {$oppositeTypeLabel} pet!";
                $notiHelper->sendToUser((int)$match['user_id'], $newPostId, $matchMessage, 'match', $newPostLink);
            }
            $matchCount++;
        }

        if ($matchCount > 0) {
            // Retrieve Owner Preference
            $ownerPreferences = getUserNotiPreferences($pdo, $userId);
            // Send Match Notification If Enabled
            if ($ownerPreferences['match_notifications']) {
                $ownerMatchMessage = "We found {$matchCount} potential match(es) for your new {$postTypeLabel} pet.";
                $notiHelper->sendToUser($userId, $newPostId, $ownerMatchMessage, 'match', $matchesLink);
            }
        }
    }
}

// Send Email Notification for New Active Post
function sendEmailNotification($email, $subject, $message)
{
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->SMTPAuth = true;

        $mail->Host = 'smtp.gmail.com';
        $mail->Username   = '';
        $mail->Password   = '';

        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom("", "FindMyPet Support");
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;

        return $mail->send();
    } catch (Exception $e) {
        error_log("Email notification failed: {$mail->ErrorInfo}");
        return false;
    }
}

function markNotificationAsRead(PDO $pdo, $userId)
{
    $sql = "UPDATE notification SET is_read = 1 WHERE user_id = :user_id AND is_read = 0";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        'user_id' => $userId
    ]);
    return $result;
}

function getUnreadNotificationCount(PDO $pdo, $userId)
{
    $sql = "SELECT COUNT(*) FROM notification WHERE user_id = :user_id AND is_read = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $userId]);
    $result = $stmt->fetchColumn();
    return $result;
}

function getNotifications(PDO $pdo, $userId, $filter = 'all', $limit = 10, $offset = 0)
{
    $sql = "SELECT * FROM notification WHERE user_id = :user_id ORDER BY created_at DESC LIMIT :limit OFFSET :offset";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function getNotificationCount(PDO $pdo, $userId, $filter = 'all')
{
    $sql = "SELECT COUNT(*) FROM notification WHERE user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $userId]);
    return $stmt->fetchColumn();
}

function getPostMatches(PDO $pdo, $postId)
{
    $stmt = $pdo->prepare("
        SELECT 
            pm.matched_post_id,
            pm.match_score,
            pm.status,
            p.post_type,
            p.last_seen_date,
            pt.name AS pet_name,
            b.breed_name,
            t.township_name,
            c.city_name
        FROM post_match pm
        JOIN post p ON pm.matched_post_id = p.id
        JOIN pet pt ON p.pet_id = pt.id
        JOIN breed b ON pt.breed_id = b.id
        JOIN township t ON p.township_id = t.id
        JOIN city c ON t.city_id = c.id
        WHERE pm.post_id = :post_id AND pm.status = 'pending'
        ORDER BY pm.match_score DESC
    ");
    $stmt->execute(['post_id' => $postId]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function updateMatchStatus(PDO $pdo, $postId, $matchedPostId, $status = 'accepted')
{
    $sql = "UPDATE post_match SET status = :status WHERE post_id = :post_id AND matched_post_id = :matched_post_id";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        'status' => $status,
        'post_id' => $postId,
        'matched_post_id' => $matchedPostId
    ]);
    return $result;
}

// Retrieve Latest Active Posts
function getLatestPosts(PDO $pdo, $limit = 4)
{
    // Select Latest Active Posts With Pet and Location Information
    $sql = "SELECT 
                p.id, p.post_type, p.description, p.last_seen_date, p.created_at,
                pet.name AS pet_name, pet.image AS pet_image,
                b.breed_name,
                pt.type_name AS pet_type_name,
                t.township_name,
                c.city_name
            FROM post p
            INNER JOIN pet ON p.pet_id = pet.id
            INNER JOIN breed b ON pet.breed_id = b.id
            INNER JOIN pet_type pt ON b.pet_type_id = pt.id
            INNER JOIN township t ON p.township_id = t.id
            INNER JOIN city c ON t.city_id = c.id
            WHERE p.status = 'Active'
            ORDER BY p.created_at DESC
            LIMIT :limit";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Retrieve Latest Blog Articles
function getLatestBlogs(PDO $pdo, $limit = 3)
{
    $sql = "SELECT id, title, content, image, category, created_at 
            FROM blog 
            ORDER BY created_at DESC 
            LIMIT :limit";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Count User Posts By Status
function getUserPostsCountByStatus($pdo, $userId, $status)
{
    $sql = "SELECT COUNT(*) FROM post p 
            WHERE p.user_id = :user_id AND p.status = :status";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $userId, 'status' => $status]);
    return (int)$stmt->fetchColumn();
}

function getUserPostsByStatusPaginated($pdo, $userId, $status, $limit = 10, $offset = 0)
{
    $sql = "
        SELECT 
            p.id, p.post_type, p.status, p.created_at,
            pt.name, pt.image,
            pty.type_name AS pet_type_name,
            c.city_name,
            t.township_name
        FROM post p
        JOIN pet pt ON p.pet_id = pt.id
        JOIN breed b ON pt.breed_id = b.id
        JOIN pet_type pty ON b.pet_type_id = pty.id
        JOIN township t ON p.township_id = t.id
        JOIN city c ON t.city_id = c.id
        WHERE p.user_id = :user_id AND p.status = :status
        ORDER BY p.created_at DESC
        LIMIT :limit OFFSET :offset
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue('user_id', $userId, PDO::PARAM_INT);
    $stmt->bindValue('status', $status);
    $stmt->bindValue('limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue('offset', (int)$offset, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function buildPageUrl($page)
{
    $params = $_GET;
    $params['page'] = $page;
    return '?' . http_build_query($params);
}

function buildPostPageUrl($page, $currentTab)
{
    $params = $_GET;
    $params[$currentTab . '_page'] = $page;
    return '?' . http_build_query($params);
}

function getPotentialMatchesCount($pdo, $postId, $filterStatus = 'all')
{
    $sql = "SELECT COUNT(*) FROM post_match pm WHERE pm.post_id = :post_id";
    $params = ['post_id' => $postId];

    if ($filterStatus !== 'all') {
        $sql .= " AND pm.status = :filter_status";
        $params['filter_status'] = ucfirst(strtolower($filterStatus));
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return (int)$stmt->fetchColumn();
}

function getPotentialMatchesPaginated($pdo, $postId, $filterStatus = 'all', $limit = 9, $offset = 0)
{
    $sql = "
        SELECT 
            pm.id AS match_id,
            pm.match_score,
            pm.status AS match_status,
            p.id AS post_id, 
            p.post_type, 
            p.last_seen_date,
            p.last_seen_location, 
            p.created_at,
            p.contact_phone, 
            p.contact_email,
            pt.name AS pet_name, 
            pt.color, 
            pt.age, 
            pt.gender, 
            pt.image,
            br.breed_name, 
            pty.type_name,
            c.city_name, 
            t.township_name
        FROM post_match pm
        JOIN post p ON pm.matched_post_id = p.id
        JOIN pet pt ON p.pet_id = pt.id
        JOIN breed br ON pt.breed_id = br.id
        JOIN pet_type pty ON br.pet_type_id = pty.id
        JOIN township t ON p.township_id = t.id
        JOIN city c ON t.city_id = c.id
        WHERE pm.post_id = :current_post_id
    ";

    $params = ['current_post_id' => $postId];

    if ($filterStatus !== 'all') {
        $sql .= " AND pm.status = :filter_status";
        $params['filter_status'] = ucfirst(strtolower($filterStatus));
    }

    $sql .= " ORDER BY pm.match_score DESC, p.created_at DESC
              LIMIT :limit OFFSET :offset";

    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue('limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue('offset', (int)$offset, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getMatchStatusCounts($pdo, $postId)
{
    $sql = "
        SELECT 
            pm.status,
            COUNT(*) as count
        FROM post_match pm
        WHERE pm.post_id = :current_post_id
        GROUP BY pm.status
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['current_post_id' => $postId]);
    $counts = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    return [
        'pending'   => $counts['Pending'] ?? 0,
        'accepted'  => $counts['Accepted'] ?? 0,
        'rejected'  => $counts['Rejected'] ?? 0,
        'total'     => array_sum($counts)
    ];
}

function buildMatchTabUrl($status, $postId)
{
    $params = $_GET;
    $params['status'] = $status;
    $params['id'] = $postId;
    unset($params['page']);
    return '?' . http_build_query($params);
}

function getMatchStatusBadge($status)
{
    $status = strtolower($status);
    switch ($status) {
        case 'pending':
            return 'bg-yellow-100 text-yellow-700 border-yellow-300';
        case 'accepted':
            return 'bg-green-100 text-green-700 border-green-300';
        case 'rejected':
            return 'bg-red-100 text-red-700 border-red-300';
        default:
            return 'bg-gray-100 text-gray-700 border-gray-300';
    }
}

function getMatchScoreColor($score)
{
    if ($score >= 80) return 'text-green-600';
    if ($score >= 60) return 'text-blue-600';
    if ($score >= 40) return 'text-yellow-600';
    return 'text-gray-600';
}

function getMatchScoreLabel($score)
{
    if ($score >= 80) return 'High Match';
    if ($score >= 60) return 'Good Match';
    if ($score >= 40) return 'Possible Match';
    return 'Low Match';
}

function verifyPostOwnership($pdo, $postId, $userId)
{
    $post = getPostById($pdo, $postId);
    if ($post['status'] != 200) {
        return ['authorized' => false, 'message' => 'Post not found.', 'data' => null];
    }
    if ($post['data']['user_id'] != $userId) {
        return ['authorized' => false, 'message' => 'You are not authorized to view matches for this post.', 'data' => null];
    }
    return ['authorized' => true, 'message' => '', 'data' => $post['data']];
}

function getBlogs($pdo, $category = 'all', $perPage = 9, $offset = 0)
{
    $whereClause = "";
    $params = [];

    if ($category !== 'all') {
        $whereClause = "WHERE b.category = :category";
        $params['category'] = $category;
    }

    $sql = "SELECT b.*, u.full_name as author_name 
            FROM blog b 
            JOIN users u ON b.user_id = u.id 
            $whereClause 
            ORDER BY b.created_at DESC 
            LIMIT :limit OFFSET :offset";

    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue('limit', (int)$perPage, PDO::PARAM_INT);
    $stmt->bindValue('offset', (int)$offset, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getBlogsCount($pdo, $category = 'all')
{
    $whereClause = "";
    $params = [];

    if ($category !== 'all') {
        $whereClause = "WHERE b.category = :category";
        $params['category'] = $category;
    }

    $sql = "SELECT COUNT(*) FROM blog b $whereClause";
    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();

    return (int)$stmt->fetchColumn();
}

function buildBlogPageUrl($page, $category)
{
    $url_params = ['page' => $page];
    if ($category !== 'all') {
        $url_params['category'] = $category;
    }
    return '?' . http_build_query($url_params);
}

function formatDate($dateString)
{
    if (!$dateString) return '';
    try {
        $date = new DateTime($dateString);
        return $date->format('M d, Y');
    } catch (Exception $e) {
        return '';
    }
}

function profileHasError($key)
{
    global $profileErrors;
    return isset($profileErrors[$key]);
}

function profileErrorMessage($key)
{
    global $profileErrors;
    return profileHasError($key) ? '<small class="error-text"><i class="ri-error-warning-line"></i> ' . htmlspecialchars($profileErrors[$key]) . '</small>' : '';
}

function old($key, $default = '')
{
    global $formData;
    return isset($formData[$key]) ? htmlspecialchars($formData[$key]) : $default;
}

function hasFormError($key)
{
    global $formErrors;
    return isset($formErrors[$key]);
}

function errorMessage($key)
{
    global $formErrors;
    return hasFormError($key) ? '<small class="error-text">' . htmlspecialchars($formErrors[$key]) . '</small>' : '';
}
