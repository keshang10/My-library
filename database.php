<?php
// Integration checks against the configured database. All test writes are rolled back.
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
define('LIBRARY_ENTRY', true);
$website=$argv[1]??dirname(__DIR__,2).'/public_html/library-v2';
require $website.'/includes/db.php';
require $website.'/includes/security.php';
function expect(bool $ok,string $label): void { if (!$ok) throw new RuntimeException('FAIL: '.$label); echo "PASS: $label\n"; }
try {
    $pdo=database(); $pdo->begin_transaction(); $store=new LibraryStore($pdo);
    $unique='QA-'.bin2hex(random_bytes(8));
    $record=['title'=>$unique.' <script>alert(1)</script>','author'=>'QA Author','genre'=>'Science Fiction','publication_year'=>'2023','isbn'=>'','copies'=>'2'];
    $store->save($record,null); $id=(int)$pdo->insert_id;
    expect($id>0,'Create record');
    $read=$store->book($id); expect($read!==null && $read['title']===$record['title'],'Read record including literal XSS text');
    $rows=$store->search(filters(['title'=>$unique,'author'=>'QA Author','genre'=>'Science Fiction','year'=>'2023']));
    expect(count($rows)===1 && (int)$rows[0]['id']===$id,'Simultaneous title, author, genre and year filters');
    expect(count($store->search(filters(['title'=>$unique,'year'=>'2022'])))===0,'Combined filters exclude nonmatching year');
    expect(count($store->search(filters(['title'=>"$unique' OR 1=1 -- "])))===0,'SQL injection string treated as literal');
    expect(count($store->search(filters(['title'=>$unique.'%'])))===0,'LIKE wildcard treated as literal');
    $record['copies']='5'; $store->save($record,$id); expect((int)$store->book($id)['copies']===5,'Update record');
    $store->delete($id); expect($store->book($id)===null,'Delete record');
    $hash=password_hash('QA-only-'.$unique,PASSWORD_DEFAULT); $store->createUser($unique,$hash);
    $user=$store->user($unique); expect($user!==null && $user['password_hash']===$hash && password_verify('QA-only-'.$unique,$user['password_hash']),'Stored password hash verifies');
    $pdo->rollback(); expect($store->user($unique)===null,'Test user rolled back');
    echo "Database integration checks complete. Test writes rolled back.\n";
} catch(Throwable $e) {
    if (isset($pdo)) $pdo->rollback();
    fwrite(STDERR,"Database test unavailable or failed: ".$e->getMessage()."\n"); exit(1);
}
