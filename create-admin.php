<?php
declare(strict_types=1);
if (PHP_SAPI!=='cli') { http_response_code(404); exit; }
define('LIBRARY_ENTRY', true);
require dirname(__DIR__).'/public_html/library-v2/includes/db.php';
if ($argc!==2 || !preg_match('/^[A-Za-z0-9_.-]{3,40}$/D',$argv[1])) { fwrite(STDERR,"Usage: php ~/library-v2-support/create-admin.php YOUR_ADMIN_USERNAME\n"); exit(1); }
if (!function_exists('shell_exec') || !function_exists('system')) { fwrite(STDERR,"CLI terminal required.\n"); exit(1); }
echo 'Choose a password (12–72 bytes): '; system('stty -echo');
try { $password=rtrim((string)fgets(STDIN),"\r\n"); } finally { system('stty echo'); echo PHP_EOL; }
if (strlen($password)<12 || strlen($password)>72) { fwrite(STDERR,"Invalid password length.\n"); exit(1); }
try { (new LibraryStore(database()))->createUser($argv[1],password_hash($password,PASSWORD_DEFAULT)); echo "Administrator created.\n"; }
catch(Throwable $ex) { fwrite(STDERR,"Could not create administrator: check database setup and username uniqueness.\n"); exit(1); }
