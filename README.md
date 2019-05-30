Named-args
==========
[![Build Status](https://travis-ci.org/manuwhat/named-args.svg?branch=master)](https://travis-ci.org/manuwhat/named-args)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/manuwhat/named-args/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/manuwhat/named-args/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/manuwhat/named-args/badges/build.png?b=master)](https://scrutinizer-ci.com/g/manuwhat/named-args/build-status/master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/manuwhat/named-args/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)


Allows to (define functions that can be called/call existing functions)  using named Arguments


**Requires**: PHP 5.3+


### Named Arguments?
This is a well known concept in other programming languages.PHP is  planning to support it natively too.
PHP functions can have as many parameters as needed and the order in which they are defined  really matters.Some may be 
mandatory and some optional.It is also important to specify mandatory parameters before any optional parameters.Sometimes 
There are so many arguments that one could forget the exact order to  specify them when trying to use the function.Sometimes 
we are embarrassed while defining the function by the optimal order to choose in order to make easy the future usage of the 
function and specially when it comes to optional arguments order.Sometimes ,when calling a function we need some optional 
arguments but not all and their predefined order doesn't  facilitate at all things. The present package contains a generic 
class which allows to define  named parameters functions.It allows to define function in a way which allows to call both 
optional and mandatory parameters in any order we want. Of course there are already some packages which tried to solve the same problem.However this package use different approach which actually allows to handle 
any type of parameters and not only those which can be specified as string,and it also handle references.It also allows to specify 
parameters without naming them just as a simple native function 


### How to use it

Require the library by issuing this command:

```bash
composer require manuwhat/named-args
```

Add `require 'vendor/autoload.php';` to the top of your script.



```php
require 'Args.php';//require helpers file
How  to use :
let's say that you want to define a function  with parameters $first ,$second,$third,$fourth,$fifth
where $first and $fourth are required and the other optional:

Natively you should do something like

function test($first,$fourth ,$second=null,$third=null,$fifth=null){

	return $first+$second+$third+$fourth+$fifth;
}

with this package you must proceed like this:

function test(NamedArgs $mandatory){
	$required=['first','fourth'];//specify required parameters here
    $default=['first'=>null,'second'=>null,'third'=>null,'fourth'=>null,'fifth'=>null];//define all parameters required and optional with their default values here
	extract($mandatory->getParams($required,$default));
	unset($mandatory);//gain space 
	
	
	
	//then you can use your parameters as you did before in your functions
	return $first+$second+$third+$fourth+$fifth;
}


and for the call you can use:

test(Args(['fourth'=>9,'first'=>3,'third'=>79]));

or either 

test(Args([1,2,3])); //just as native function but you will then need to respect predefined order as done natively

You can also turn normal functions to named args functions this way:
on my PHP version when i do :

echo  new reflectionFunction('preg_match');

i obtain:

Function [  function preg_match ] {

  - Parameters [5] {
    Parameter #0 [  $pattern ]
    Parameter #1 [  $subject ]
    Parameter #2 [  &$subpatterns ]
    Parameter #3 [  $flags ]
    Parameter #4 [  $offset ]
  }
}

so i can use the following code to specify my arguments anywhere in the order i want:



NamedArgs::preg_match_all(args(['subpatterns'=>&$matches,'pattern'=>'#a|o|i|u|e#','subject'=>'gris|gros|gras|grue'])); 

or  even more simply by removing the NamedArgs object step:



NamedArgs::preg_match_all(['subpatterns'=>&$match,'pattern'=>'#a|o|i|u|e#','subject'=>'gris|gros|gras|grue']);


 
Note that i have just called statically preg_match and pass as the only one parameter a NamedArgs object or an array.

which print out as you should see when you run var_dump($matches):

array(1) {
  [0]=>
  array(5) {
    [0]=>
    string(1) "i"
    [1]=>
    string(1) "o"
    [2]=>
    string(1) "a"
    [3]=>
    string(1) "u"
    [4]=>
    string(1) "e"
  }
}

 
As you can see, few lines of code transform any of your functions to something which will boost your productivity because 
you won't be forced to search for the exact order in which parameters are defined before call your function and this can be really helpful on big project and you can just switch between named args calling and native calling easily.

See the TestNAmedArgs.php file to see a complete how to define functions with references and without references and how to call them ....

Keep in mind that although the parameters are required you can specify them in any order for the call...


To run unit tests 
```bash
phpunit  ./tests
```
