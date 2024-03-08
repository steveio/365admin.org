<?php 
function isValidPassword($password) {
    //if (!preg_match_all('$\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])(?=\S*[\W])\S*$', $password))
    //    return FALSE;


    if (preg_match("/[a-zA-Z0-9\W]/",$password))
    	return TRUE;
}

/*
    Regular Expression: $\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])(?=\S*[\W])\S*$
    $ = beginning of string
    \S* = any set of characters
    (?=\S{8,}) = of at least length 8
    (?=\S*[a-z]) = containing at least one lowercase letter
    (?=\S*[A-Z]) = and at least one uppercase letter
    (?=\S*[\d]) = and at least one number
    (?=\S*[\W]) = and at least a special character (non-word characters)
    $ = end of the string
 */
 
$password = 'example101'; //invalid password
if(isValidPassword($password)) { 
    echo "$password is a valid password<br />";
} else {
  echo "$password is not a valid password<br />";
}  

$password = '6myP@ssword01'; //valid password
if(isValidPassword($password)) {
  echo "$password is a valid password<br />";
} else {
  echo "$password is not a valid password<br />";
}  

?>
