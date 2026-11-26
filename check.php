<?php
declare(strict_types=1);
define('LIBRARY_ENTRY', true);
$website=$argv[1]??dirname(__DIR__,2).'/public_html/library-v2';
require $website.'/includes/security.php'; require $website.'/includes/template.php';
$n=0; function check(bool $condition,string $message): void { global $n; if (!$condition) { fwrite(STDERR,"FAIL: $message\n"); exit(1); } ++$n; echo "PASS: $message\n"; }
$valid=['title'=>'Dune','author'=>'Frank Herbert','genre'=>'Science Fiction','publication_year'=>'1965','isbn'=>'','copies'=>'2'];
[$b,$errors]=book_input($valid); check(!$errors && $b===$valid,'Valid book accepted');
foreach(['title'=>'','author'=>str_repeat('a',121),'genre'=>'invalid','publication_year'=>'9999','isbn'=>'xyz','copies'=>'-1'] as $key=>$value) { $bad=$valid; $bad[$key]=$value; check(count(book_input($bad)[1])>0,"Reject invalid $key"); }
check(filters(['title'=>' Dune ','author'=>['bad'],'genre'=>'Science Fiction','year'=>'2023'])===['title'=>'Dune','author'=>'','genre'=>'Science Fiction','year'=>'2023'],'Combined filters and array input');
$_SESSION=[]; $token=csrf_token(); check(csrf_valid(['csrf'=>$token])&&!csrf_valid(['csrf'=>'wrong']),'CSRF token accepted and invalid rejected');
$challenge=captcha_new(); $answer=$_SESSION['captcha']['answer']; check(captcha_valid(['captcha'=>$answer])&&!captcha_valid(['captcha'=>$answer]),'CAPTCHA correct answer and one-time consumption');
captcha_new(); $_SESSION['captcha']['expires']=time()-1; check(!captcha_valid(['captcha'=>$_SESSION['captcha']['answer']]),'Expired CAPTCHA rejected');
$hash=password_hash('test-password-123',PASSWORD_DEFAULT); check($hash!=='test-password-123'&&password_verify('test-password-123',$hash)&&!password_verify('wrong',$hash),'Password hash and verification');
check(text_value(['x'=>"\xff"],'x')==='' && preg_match('//u',utf8_limit(str_repeat('é',100),149))===1,'Reject malformed UTF-8 and truncate without splitting characters');
$template=new LocalTemplate(); check($template->render('{% if active %}{% for x in items %}{{ x.name }}{% endfor %}{% else %}none{% endif %}',['active'=>true,'items'=>[['name'=>'<script>'],['name'=>'&']]])==='&lt;script&gt;&amp;','Template loop, branch and escaping');
$base=['catalog'=>false,'login'=>false,'form'=>false,'edit'=>false,'delete'=>false,'contact'=>false,'error_page'=>false,'auth'=>true,'username'=>'<test>','csrf'=>$token,'errors'=>[],'flash'=>'','book'=>$valid,'books'=>[],'has_books'=>false,'count'=>0,'stats'=>['titles'=>1,'genres'=>1,'copies'=>2],'filters'=>filters([]),'genres'=>[['name'=>'Science Fiction','selected'=>true]],'id'=>1,'captcha'=>'2 + 3','max_year'=>2027];
foreach(['catalog'=>'Book catalogue','login'=>'Librarian sign in','form'=>'Add a book','delete'=>'Delete this book?','contact'=>'Library help','error_page'=>'Request unavailable'] as $flag=>$expected) { $d=$base;$d[$flag]=true;$html=render_page($d);check(str_contains($html,$expected)&&!str_contains($html,'{%')&&!str_contains($html,'<test>'),"Render $flag page"); }
$d=$base;$d['catalog']=true;$d['has_books']=true;$d['books']=[array_merge($valid,['id'=>1,'available'=>true,'title'=>'<script>alert(1)</script>'])];$html=render_page($d);check(str_contains($html,'&lt;script&gt;alert(1)&lt;/script&gt;')&&!str_contains($html,'<script>alert(1)</script>'),'Stored XSS payload rendered as text');
$d=$base;$d['form']=true;$d['edit']=true;check(str_contains(render_page($d),'Edit book'),'Edit form rendered');
echo "$n checks passed. No MySQL server or browser security scanner used.\n";
