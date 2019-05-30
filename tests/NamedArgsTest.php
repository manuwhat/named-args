<?php
use PHPUnit\Framework\TestCase;
require ($dir = dirname(__DIR__)).DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'namedArgsHelper.php';
require ($dir = dirname(__DIR__)).DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'namedArgs.php';
require $dir.DIRECTORY_SEPARATOR.'Args.php';
use EZAMA\NamedArgs;
class NamedArgsTest extends TestCase
{
    public function testStaticCall()
    {
        $this->assertEquals(
            NamedArgs::preg_match_all(
                args(
                    [
                        'subpatterns'=>&$matches0,
                        'pattern'=>'#a|o|i|u|e#',
                        'subject'=>'gris|gros|gras|grue'
                    ]
                )
            ),
            preg_match_all('#a|o|i|u|e#', 'gris|gros|gras|grue', $matches1)
        );
        
        $this->assertEquals($matches0, $matches1);
        
        $this->assertTrue(
            (bool) NamedArgs::preg_match(
                [
                            'subpatterns'=>&$match,
                            'pattern'=>'#a|o|i|u|e#',
                            'subject'=>'gris|gros|gras|grue'
                ]
            )
        );
    }
}