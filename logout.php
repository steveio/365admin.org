<?

setcookie("oneworld365", "", time()-3600,"","oneworld365.org");
setcookie("PHPSESSID", "", time()-3600,"","admin.oneworld365.org");

session_unset();
session_destroy();

unset($_COOKIE);
unset($_SESSION);

Http::Redirect("/");


?>
