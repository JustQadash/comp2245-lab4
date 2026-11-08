<?php
header('Content-Type: application/json; charset=UTF-8');

require __DIR__ . '/db.php';

$q = $_GET['q'] ?? '';

if (!is_string($q)) {
    $q = '';
}

$q = trim($q);

$pattern = '%' . $q . '%';

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
    WHERE books.title LIKE :title
       OR books.author LIKE :author
    ORDER BY books.title
';

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ':title' => $pattern,
    ':author' => $pattern
]);

$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($books);