<?php
declare(strict_types=1);
if (!defined('LIBRARY_ENTRY')) { http_response_code(404); exit; }
// A dependency-free engine for the subset of Twig used by this project.
// Supports escaped variables, dot paths, conditionals and iteration; no eval or raw output.
final class LocalTemplate {
    private array $tokens=[]; private int $pos=0;
    public function render(string $source,array $context): string {
        $this->tokens=preg_split('/(\{\{.*?\}\}|\{%.*?%\})/s',$source,-1,PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
        $this->pos=0; [$nodes,$end]=$this->parse([]); if ($end!=='') throw new RuntimeException('Unexpected end tag');
        return $this->output($nodes,$context);
    }
    private function parse(array $stops): array {
        $nodes=[];
        while ($this->pos<count($this->tokens)) {
            $t=$this->tokens[$this->pos++];
            if (str_starts_with($t,'{{')) { $nodes[]=['value',trim(substr($t,2,-2))]; continue; }
            if (!str_starts_with($t,'{%')) { $nodes[]=['text',$t]; continue; }
            $tag=trim(substr($t,2,-2)); if (in_array($tag,$stops,true)) return [$nodes,$tag];
            if (preg_match('/^if (not )?([\w.]+)$/D',$tag,$m)) {
                [$yes,$end]=$this->parse(['else','endif']); $no=[];
                if ($end==='else') [$no,$end]=$this->parse(['endif']);
                if ($end!=='endif') throw new RuntimeException('Missing endif');
                $nodes[]=['if',$m[2],$m[1]!=='',$yes,$no];
            } elseif (preg_match('/^for (\w+) in ([\w.]+)$/D',$tag,$m)) {
                [$body,$end]=$this->parse(['endfor']); if ($end!=='endfor') throw new RuntimeException('Missing endfor');
                $nodes[]=['for',$m[1],$m[2],$body];
            } else throw new RuntimeException('Unsupported template tag: '.$tag);
        }
        return [$nodes,''];
    }
    private function value(array $c,string $path) { $v=$c; foreach(explode('.',$path) as $part) { $v=is_array($v)?($v[$part]??null):null; } return $v; }
    private function output(array $nodes,array $c): string {
        $s=''; foreach($nodes as $n) {
            if ($n[0]==='text') $s.=$n[1];
            elseif ($n[0]==='value') $s.=htmlspecialchars((string)$this->value($c,$n[1]),ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');
            elseif ($n[0]==='if') $s.=$this->output(((bool)$this->value($c,$n[1])!==$n[2])?$n[3]:$n[4],$c);
            elseif ($n[0]==='for') { foreach((array)$this->value($c,$n[2]) as $v) { $child=$c; $child[$n[1]]=$v; $s.=$this->output($n[3],$child); } }
        } return $s;
    }
}
function render_page(array $data): string {
    $source = require __DIR__ . '/view.php';
    // Composer lives outside public_html; all application files stay in the website folder.
    $autoload = dirname(__DIR__, 3) . '/library-v2-support/vendor/autoload.php';
    if (is_readable($autoload)) require_once $autoload;
    if (class_exists('Twig\\Environment')) {
        $twig = new Twig\Environment(new Twig\Loader\ArrayLoader(['site.twig'=>$source]), ['autoescape'=>'html','cache'=>false]);
        return $twig->render('site.twig',$data);
    }
    return (new LocalTemplate())->render($source,$data);
}
