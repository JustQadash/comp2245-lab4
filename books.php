
<?php
header('Content-Type: application/json; charset=UTF-8');

require __DIR__ . '/db.php';

// Get the search term
$q = $_GET['q'] ?? '';

if (!is_string($q)) {
    $q = '';
}

$q = trim($q);

$pattern = '%' . $q . '%';

// Get the subject filter
$subject = filter_input(INPUT_GET, 'subject', FILTER_VALIDATE_INT);

if ($subject === false || $subject < 1) {
    $subject = null;
}

// Get the availability filter
$availableOnly = ($_GET['available'] ?? '') === '1';

// Starting SQL query
$sql = '
    SELECT
        books.title,
        books.author,
        books.year,
        subjects.name AS subject,
        books.copies,
        books.copies - books.on_loan AS available
    FROM books
    JOIN subjects ON books.subject_id = subjects.id
    WHERE (
        books.title LIKE :title
        OR books.author LIKE :author
    )
';

// Prepare the search parameters
$params = [
    ':title' => $pattern,
    ':author' => $pattern
];

// Add subject filter if selected
if (is_int($subject)) {
    $sql .= ' AND books.subject_id = :subject';
    $params[':subject'] = $subject;
}

// Add availability filter if selected
if ($availableOnly) {
    $sql .= ' AND books.copies > books.on_loan';
}

// Sort results by title
$sql .= ' ORDER BY books.title';

// Execute the query
$stmt = $pdo->prepare($sql);
$stmt->execute($params);

// Return results as JSON
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($books);
