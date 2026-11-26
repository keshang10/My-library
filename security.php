<?php
declare(strict_types=1);
if (!defined('LIBRARY_ENTRY')) { http_response_code(404); exit; }
function text_value(array $source,string $key): string { $v=$source[$key]??''; return is_string($v) && preg_match('//u',$v)===1 ? trim($v) : ''; }
function e(string $v): string { return htmlspecialchars($v,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8'); }
function genres(): array { return ['Fiction','Non-fiction','Science Fiction','Fantasy','Mystery','Romance','History','Biography','Technology','Other']; }
function valid_year(string $v): bool { return preg_match('/^[0-9]{1,4}$/D',$v)===1 && (int)$v>=1 && (int)$v<=(int)date('Y')+1; }
function utf8_limit(string $v,int $max): string { $v=substr($v,0,$max); while (preg_match('//u',$v)!==1) $v=substr($v,0,-1); return $v; }
function filters(array $s): array {
    $title=text_value($s,'title'); $author=text_value($s,'author'); $genre=text_value($s,'genre'); $year=text_value($s,'year');
    return ['title'=>utf8_limit($title,150),'author'=>utf8_limit($author,120),'genre'=>in_array($genre,genres(),true)?$genre:'','year'=>valid_year($year)?$year:''];
}
function book_input(array $s): array {
    $b=[]; $errors=[];
    foreach (['title'=>150,'author'=>120] as $key=>$max) { $b[$key]=text_value($s,$key); if ($b[$key]===''||strlen($b[$key])>$max||preg_match('/[\x00-\x1f\x7f]/',$b[$key])) $errors[]=ucfirst($key).' is required and must be at most '.$max.' bytes.'; }
    $b['genre']=text_value($s,'genre'); if (!in_array($b['genre'],genres(),true)) $errors[]='Choose a valid genre.';
    $b['publication_year']=text_value($s,'publication_year'); if (!valid_year($b['publication_year'])) $errors[]='Enter a valid publication year.';
    $b['isbn']=text_value($s,'isbn'); if ($b['isbn']!=='' && !preg_match('/^[0-9Xx -]{10,20}$/D',$b['isbn'])) $errors[]='ISBN must contain 10–20 digits, spaces, hyphens or X (or be blank).';
    $b['copies']=text_value($s,'copies'); if (!preg_match('/^[0-9]{1,5}$/D',$b['copies']) || (int)$b['copies']>10000) $errors[]='Copies must be between 0 and 10,000.';
    return [$b,$errors];
}
function start_secure_session(): void {
    ini_set('session.use_strict_mode','1'); ini_set('session.use_only_cookies','1');
    $secure=(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off') || (($_SERVER['SERVER_PORT']??'')==='443');
    session_name('LEAFLEDGERSESSID'); $path=rtrim(str_replace('\\','/',dirname($_SERVER['SCRIPT_NAME']??'/index.php')),'/').'/'; session_set_cookie_params(['lifetime'=>0,'path'=>$path,'secure'=>$secure,'httponly'=>true,'samesite'=>'Lax']); session_start();
    if (isset($_SESSION['last_seen']) && time()-$_SESSION['last_seen']>1800) { $_SESSION=[]; session_regenerate_id(true); }
    $_SESSION['last_seen']=time();
}
function csrf_token(): string { if (!isset($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(32)); return $_SESSION['csrf']; }
function csrf_valid(array $s): bool { return isset($_SESSION['csrf']) && is_string($s['csrf']??null) && hash_equals($_SESSION['csrf'],$s['csrf']); }
function captcha_new(): string { $a=random_int(2,12); $b=random_int(2,12); $_SESSION['captcha']=['answer'=>(string)($a+$b),'expires'=>time()+300]; return "$a + $b"; }
function captcha_valid(array $s): bool { $c=$_SESSION['captcha']??null; unset($_SESSION['captcha']); return is_array($c) && $c['expires']>=time() && hash_equals($c['answer'],text_value($s,'captcha')); }
function security_headers(): void {
    header('Content-Type: text/html; charset=UTF-8'); header('X-Content-Type-Options: nosniff'); header('X-Frame-Options: DENY'); header('Referrer-Policy: no-referrer'); header('Cache-Control: no-store');
    header("Content-Security-Policy: default-src 'none'; script-src 'self'; style-src 'self'; img-src 'self'; connect-src 'self'; font-src 'self'; form-action 'self'; frame-ancestors 'none'; base-uri 'none'");
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
}
function redirect_to(string $page='catalog'): void { header('Location: index.php?page='.$page,true,303); exit; }
