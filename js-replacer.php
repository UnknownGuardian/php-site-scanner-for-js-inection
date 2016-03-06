<html><head><title>Find String</title>

<style>
body {
font-family: 'Courier New';
line-height: 1em;
}

p {
    display: block;
    -webkit-margin-before: 0px;
    -webkit-margin-after: 0px;
    -webkit-margin-start: 0px;
    -webkit-margin-end: 0px;
}
div.block {
display:block;
}

div.pre-split{
width:90%;

margin-left:10%;
}

pre {
display:inline-block;
border:1px solid #bbb;
overflow:scroll;

}
pre.half {
width:45%;
margin:0px;
padding:0px;
margin-left:5px;
}





</style>


</head><body>
<?php

//Credit based on: https://aw-snap.info/articles/base64-decode.php
// Most hosting services will have a time limit on how long a php script can run, typically 30 seconds.
// On large sites with a lot of files this script may not be able to find and check all files within the time limit.
// If you get a time out error you can try over riding the default time limits by removing the // in the front of these two lines.

// ini_set('max_execution_time', '0');
// ini_set('set_time_limit', '0');


find_files('../');


$count_of_infected_files = 0;

function find_files($seed)
{
    if (!is_dir($seed))
        return false;
    $files = array();
    $dirs  = array(
        $seed
    );
    while (NULL !== ($dir = array_pop($dirs))) {
        if ($dh = opendir($dir)) {
            while (false !== ($file = readdir($dh))) {
                if ($file == '.' || $file == '..')
                    continue;
                $path = $dir . '/' . $file;
                if (is_dir($path)) {
                    $dirs[] = $path;
                }
                
                //RECUSION COUNT TO PREVENT STRESS, LARGE PAGE SIZE. 
                if($count_of_infected_files > 20000) { //start with 100 to test simple recusion without stressing page load. Then bump it up to a larger number.
                     echo("<p>We exited early with ".$count_of_infected_files."</p>");
                     exit;
                }
                // the line below tells the script to only check the content of files with a .php extension.
                // the if{} statement says if you "match" php[\d]? at the end of the file name then check the contents
                // of the file. The [\d]? part means also match if there is a digit \d such as .php4 in the file extension
                
                // else { if(preg_match('/\/*\.php[\d]?$/i', $path)) { check_files($path); }}
                
                // 07/26/2011 Based on some recent Pharma hacks I have changed the default to check php, js and txt files
                else {
                    if (preg_match('/^.*\.(php[\d]?|js|txt)$/i', $path)) {
                    $count_of_infected_files++;
                        check_files($path);
                    }
                }
                
                // if you would like to check other (all) file types you can comment out/un-comment and or modify
                // the following lines as needed. You can only have one of the else{} statements un-commented.
                // The first example contains a lengthy OR (the | means OR) statement, the part inside the (),
                // (php[\d]?|htm|html|shtml|js|asp|aspx) You can add/remove filetypes by modifying this part
                // (php[\d]?|htm|html|shtml) will only check .php, .htm, .html, .shtml files.
                
                // else { if(preg_match('/^.*\.(php[\d]?|htm|html|shtml|js|asp|aspx)$/i', $path)) { check_files($path); }}
                // In the next else{} statement there is no if{}, no checking of the file extension every file will be checked
                // else { check_files($path); } // will check all file types for the code
            }
            closedir($dh);
        }
    }
    echo("<p>We finished with ".$count_of_infected_files."</p>");
}
function check_files($this_file)
{
    // the variable $str_to_find is an array that contains the strings to search for inside the single quotes.
    // if you want to search for other strings replace base64_decode with the string you want to search for.
    
    //$str_to_find[] = 'base64_decode';
   // $str_to_find[] = 'edoced_46esab'; // base64_decode reversed
    $str_to_find[] = '*/;(funct'.'ion(){var ';
    //$str_to_find[] = '$_REQUEST[';
   // echo("<div class='file'><strong>Checking File: $this_file</strong>");
    if (!($content = file_get_contents($this_file))) {
        echo ("<p>Could not check $this_file You should check the contents manually!</p>\n");
    } else {
       /* preg_match("/\/\*\w{32}\*\/;\(function\(\)\{var .*\/\*\w{32}\*\//", $content , $arr);
        if(count($arr) > 0) {
               echo ("<p>   -> contains injected code</p>");
        }*/
        while (list(, $value) = each($str_to_find)) {
            if (stripos($content, $value) !== false) {
                    echo("<div class='file'><strong>Checking File: $this_file</strong>"); 
                    echo ("<p>   -> contains $value</p>");
                    
                    
                    //LEFT SIDE
                    echo ("<div class='pre-split'><pre class='half'>");
                    $arr = array();
                    echo htmlspecialchars($content);
                    echo ("</pre>");
                    
                    
                    //RIGHT SIDE DRY RUN
                    echo ("<pre class='half'>");
                    $normal_other_content = preg_replace("/\/\*\w{32}\*\/;\(function\(\)\{var .*\/\*\w{32}\*\//", " ", $content);
                    $other_content = htmlspecialchars($normal_other_content);
                    echo $other_content;
                    echo ("</pre></div>");
                    echo ("</div>");
                    
                    
                    
                    //ACTUAL REPLACING
                    if(strlen($other_content) < 10) {  //after replacing, is the file super small?
                    
                        //Some files were completely overwritten. Customize by just logging these to the screen, or just replacing them with a console.log for your site
                        $other_content = $other_content."console.log('Requires rebuild from source');";
                        file_put_contents($this_file, $other_content);
                    }
                    else { //normal file, just strip bad javascript.
                        file_put_contents($this_file, $normal_other_content);
                    }
                    
                    
                    
                    
                    
            }
            else{
                
           }
        }
    }
    echo("</div>\n");
    unset($content);
}
?>
</body></html>
