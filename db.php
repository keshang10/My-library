<?php
declare(strict_types=1);
if (!defined('LIBRARY_ENTRY')) { http_response_code(404); exit; }

// Reuse the university's existing mysqli connector in public_html/db.php.
// No credentials are duplicated or stored in this project.
function database(): mysqli {
    static $connection = null;
    if ($connection instanceof mysqli) return $connection;
    if (!extension_loaded('mysqli')) throw new RuntimeException('mysqli extension unavailable.');
    $file = dirname(__DIR__, 2) . '/db.php';
    if (!is_readable($file)) throw new RuntimeException('Existing university database connector is not readable.');
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $mysqli = null;
    ob_start();
    try { require $file; } finally { ob_end_clean(); }
    if (!$mysqli instanceof mysqli) throw new RuntimeException('Expected $mysqli from university connector.');
    $mysqli->set_charset('utf8mb4');
    return $connection = $mysqli;
}

final class LibraryStore {
    public function __construct(private mysqli $db) {}
    private function query(string $sql, array $args = []): mysqli_stmt {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($args);
        return $stmt;
    }
    private function rows(string $sql, array $args = []): array {
        $stmt = $this->query($sql, $args);
        try { return $stmt->get_result()->fetch_all(MYSQLI_ASSOC); }
        finally { $stmt->close(); }
    }
    public function search(array $filters): array {
        $where = []; $args = [];
        foreach (['title', 'author'] as $key) {
            if ($filters[$key] !== '') {
                $where[] = "$key LIKE ? ESCAPE '!'";
                $args[] = '%' . strtr($filters[$key], ['!' => '!!', '%' => '!%', '_' => '!_']) . '%';
            }
        }
        if ($filters['genre'] !== '') { $where[] = 'genre = ?'; $args[] = $filters['genre']; }
        if ($filters['year'] !== '') { $where[] = 'publication_year = ?'; $args[] = (int)$filters['year']; }
        $sql = 'SELECT id,title,author,genre,publication_year,isbn,copies FROM library_books';
        if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
        return $this->rows($sql . ' ORDER BY title,id', $args);
    }
    public function stats(): array {
        return $this->rows('SELECT COUNT(*) AS titles, COUNT(DISTINCT genre) AS genres, COALESCE(SUM(copies),0) AS copies FROM library_books')[0];
    }
    public function book(int $id): ?array { return $this->rows('SELECT * FROM library_books WHERE id=?', [$id])[0] ?? null; }
    public function save(array $b, ?int $id): void {
        $args = [$b['title'],$b['author'],$b['genre'],(int)$b['publication_year'],$b['isbn'],(int)$b['copies']];
        if ($id === null) $sql = 'INSERT INTO library_books (title,author,genre,publication_year,isbn,copies) VALUES (?,?,?,?,?,?)';
        else { $sql = 'UPDATE library_books SET title=?,author=?,genre=?,publication_year=?,isbn=?,copies=? WHERE id=?'; $args[] = $id; }
        $this->query($sql,$args)->close();
    }
    public function delete(int $id): void { $this->query('DELETE FROM library_books WHERE id=?',[$id])->close(); }
    public function user(string $name): ?array { return $this->rows('SELECT id,username,password_hash FROM library_users WHERE username=?',[$name])[0] ?? null; }
    public function createUser(string $name, string $hash): void { $this->query('INSERT INTO library_users (username,password_hash) VALUES (?,?)',[$name,$hash])->close(); }
}
