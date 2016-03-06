# php-site-scanner-for-js-inection
A PHP site scanner for identifying and repairing an attack where malicious javascript was added to existing javascript files.

## A robust scanner for a specific javascript injection

The javascript appended to existing javascript files was obfuscated. It looked something like this:


```
/*25a520a617147585c97ef01f04c898e3*/;(function(){var azesryri="";var nhhdsnkf="76e6f2c63297b69662
86397b7661220423206670441465[... redacted...]65306f73697466736f653e3c2f6469763e223b6f6356d56e4262
6f4792e6707056e64368696c4287232647293b7d7";for (var tndandrr=0;tndandrr<nhhdsnkf.length;tndandrr+=2) {azesryri=azesryri+parseInt(nhhdsnkf.substring(tndandrr,tndandrr+2),16)+",";}azesryri=azesryri.subst
ring(0,azesryri.length-1);eval(eval('String.fromCharCode('+azesryri+')'));})();/*25a520a617147585c97ef01f04c898e3*/
```

To identify if the malicious javascript is similar, look for this template:

```
32 character hexadecimal string surrounded by block style comments
;(function(){var {some variable}="";
var {another variable}= {payload of large hexadecimal string}
loop to decrypt
eval(eval('String.fromCharCode('+ {some variable}  +')'))
32 character hexadecimal string surrounded by block style comments
```

The intent is to allow for code to be executed based on paramaters from a POST variable.

## The Scan

Sections that are important to modify

```Line 82: if($count_of_infected_files > 20000)``` Edit this line to allow for larger recursion. Exits early, so as to not generate a super large page (for dry-run purposes). 2000 would likely be a full server. Limit to 100 or so for testing.

```Line 160:file_put_contents($this_file, $other_content);``` Do you really want to replace? Comment out until you are ready! This first one is for javascript files that appear to be completely replaced. Since the scanner removes this, the finished file is really small. It will replace the file with a console.log so you can remember to rebuild that individual file from the original source if necessary.
```Line 162: file_put_contents($this_file, $normal_other_content);``` Do you really want to replace? Comment out until you are ready! This is the master replace. Works on all larger files.

## Test!

The script is written to launch, not to test. Please do a dry run by adjusting the variables and code that are important, as seen in the section above.
