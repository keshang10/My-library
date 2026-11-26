<?php
declare(strict_types=1);
if (!defined('LIBRARY_ENTRY')) { http_response_code(404); exit; }
require_once __DIR__.'/db.php'; require_once __DIR__.'/security.php'; require_once __DIR__.'/template.php';
ini_set('display_errors','0');
security_headers(); start_secure_session();
$page=text_value($_GET,'page') ?: 'catalog'; $method=$_SERVER['REQUEST_METHOD']??'GET';
$auth=isset($_SESSION['user_id']); $error=[]; $book=['title'=>'','author'=>'','genre'=>'','publication_year'=>'','isbn'=>'','copies'=>'1'];
$filters=filters($_GET); $rows=[]; $stats=['titles'=>0,'genres'=>0,'copies'=>0]; $captcha=''; $flash=$_SESSION['flash']??''; unset($_SESSION['flash']);
$id=filter_var(text_value($_GET,'id'),FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]) ?: null;
$allowed=['catalog','login','logout','add','edit','delete','contact','ajax'];
if (!in_array($page,$allowed,true)) { http_response_code(404); $page='error'; $error[]='Page not found.'; }
try {
    if ($method!=='GET' && $method!=='POST') { http_response_code(405); header('Allow: GET, POST'); $page='error'; $error[]='Method not allowed.'; }
    elseif ($method==='POST' && !csrf_valid($_POST)) { http_response_code(403); $page='error'; $error[]='Invalid security token. Reload the page and try again.'; }
    elseif (in_array($page,['add','edit','delete','logout'],true) && !$auth) { $_SESSION['flash']='Sign in to manage books.'; redirect_to('login'); }
    elseif ($page==='logout') {
        if ($method!=='POST') { http_response_code(405); header('Allow: POST'); $page='error'; $error[]='Logout requires POST.'; }
        else { $_SESSION=[]; session_regenerate_id(true); $_SESSION['flash']='You have signed out.'; redirect_to(); }
    } elseif ($page==='login') {
        if ($auth) redirect_to();
        if ($method==='POST') {
            $name=text_value($_POST,'username'); $pw=is_string($_POST['password']??null)?$_POST['password']:'';
            $human=captcha_valid($_POST); $tries=$_SESSION['login_tries']??0;
            if ($tries>=5 && time()-($_SESSION['login_last']??0)<60) { $error[]='Too many attempts. Wait one minute.'; }
            elseif (!$human) { $error[]='Incorrect or expired CAPTCHA. Try again.'; }
            elseif (!preg_match('/^[A-Za-z0-9_.-]{3,40}$/D',$name) || strlen($pw)<1 || strlen($pw)>72) { $error[]='Invalid username or password.'; }
            else {
                if (time()-($_SESSION['login_last']??0)>=60) $tries=0;
                $user=(new LibraryStore(database()))->user($name);
                // Verify a dummy hash for unknown users too, reducing username timing differences.
                $hash=$user['password_hash']??'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.';
                $valid=password_verify($pw,$hash);
                if ($user && $valid) {
                    session_regenerate_id(true); $_SESSION=['user_id'=>$user['id'],'username'=>$user['username'],'last_seen'=>time(),'flash'=>'Welcome back.']; redirect_to();
                }
                $_SESSION['login_tries']=$tries+1; $_SESSION['login_last']=time(); $error[]='Invalid username or password.';
            }
        }
        $captcha=captcha_new();
    } elseif (in_array($page,['catalog','ajax','add','edit','delete'],true)) {
        $store=new LibraryStore(database());
        if ($page==='catalog' || $page==='ajax') {
            if ($method!=='GET') { http_response_code(405); header('Allow: GET'); $page='error'; $error[]='Method not allowed.'; }
            else { $rows=$store->search($filters); if ($page==='ajax') { header('Content-Type: application/json; charset=UTF-8'); echo json_encode(['books'=>$rows],JSON_INVALID_UTF8_SUBSTITUTE|JSON_THROW_ON_ERROR); exit; } $stats=$store->stats(); }
        } else {
            if ($page!=='add') { $found=$id?$store->book($id):null; if (!$found) { http_response_code(404); $page='error'; $error[]='Book not found.'; } else $book=$found; }
            if ($method==='POST' && $page==='delete') { $store->delete($id); $_SESSION['flash']='Book deleted.'; redirect_to(); }
            if ($method==='POST' && in_array($page,['add','edit'],true)) {
                [$book,$error]=book_input($_POST);
                if (!$error) { $store->save($book,$page==='add'?null:$id); $_SESSION['flash']=$page==='add'?'Book added.':'Book updated.'; redirect_to(); }
                http_response_code(422);
            }
        }
    }
} catch(Throwable $ex) {
    error_log('Library application: '.$ex->getMessage()); http_response_code(500);
    if ($page==='ajax') { header('Content-Type: application/json; charset=UTF-8'); echo '{"error":"Unable to load books."}'; exit; }
    $page='error'; $error=['Unable to complete the request. Check database setup or contact the administrator.'];
}
$options=[]; foreach(genres() as $g) $options[]=['name'=>$g,'selected'=>$g===($page==='add'||$page==='edit'?$book['genre']:$filters['genre'])];
foreach($rows as &$r) { $r['available']=(int)$r['copies']>0; } unset($r);
$data=['catalog'=>$page==='catalog','login'=>$page==='login','form'=>$page==='add'||$page==='edit','edit'=>$page==='edit','delete'=>$page==='delete','contact'=>$page==='contact','error_page'=>$page==='error','auth'=>$auth,'username'=>$_SESSION['username']??'', 'csrf'=>csrf_token(),'errors'=>$error,'flash'=>$flash,'book'=>$book,'books'=>$rows,'has_books'=>count($rows)>0,'count'=>count($rows),'stats'=>$stats,'filters'=>$filters,'genres'=>$options,'id'=>$id,'captcha'=>$captcha,'max_year'=>(int)date('Y')+1];
echo render_page($data);
