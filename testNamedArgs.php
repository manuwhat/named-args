<?php
require_once('./src/namedArgs.php');
require_once('./src/namedArgsHelper.php');


function codeWithoutRef(NamedArgs $mandatory)
{
    $required=['first','fourth'];//specify required parameters here
    $default=['first'=>0,'second'=>1,'third'=>2,'fourth'=>9,'fifth'=>7];//define all parameters required and optional with their default values here
    extract($mandatory->getParams($required, $default));
    unset($mandatory);
    return $first+$second+$third+$fourth+$fifth;
}


function codeWithRef(NamedArgs $mandatory)
{
    $required=['first'];
    $default=['first'=>0,'second'=>1,'third'=>2];
    extract($mandatory->getParams($required, $default), EXTR_OVERWRITE|EXTR_REFS);
    unset($mandatory);
    $first=$first+$second+$third;
}


function test(&$tada, &$tada2, &$test=6)
{
    $tada=1;
    $tada2=2;
    return  $tada+$tada2+$test;
}


echo "<pre>";
var_dump(codeWithoutRef(Args(['fourth'=>9,'first'=>3,'third'=>79])));
var_dump(codeWithoutRef(Args([1,2,3,0])));


$first=3;
codeWithRef(Args(['third'=>79,'first'=>&$first]));
var_dump($first);
$first2=3;
codeWithRef(Args([&$first2,79]));
var_dump($first2);

$tada=$tada2=null;
var_dump(NamedArgs::test(args(['tada'=>&$tada,'tada2'=>&$tada2])), $tada, $tada2);

$tada=$tada2=null;
var_dump(NamedArgs::test(args([&$tada,&$tada2])), $tada, $tada2);


NamedArgs::preg_match_all(args(['subpatterns'=>&$matches,'pattern'=>'#a|o|i|u|e#','subject'=>'gris|gros|gras|grue']));

var_dump($matches);
var_dump($x=NamedArgs::strtoupper(args(['str'=>'gris|gros|gras|grue'])), NamedArgs::strtolower(args(['str'=>$x])));//just for  funny example.
NamedArgs::preg_match(['subpatterns'=>&$match,'pattern'=>'#a|o|i|u|e#','subject'=>'gris|gros|gras|grue']);
var_dump($match);

highlight_string('
<?php 
var_dump(codeWithoutRef(Args([\'third\'=>79])));//generate error here for example
?>
');
var_dump(codeWithoutRef(Args(['third'=>79])));

echo "</pre>";
