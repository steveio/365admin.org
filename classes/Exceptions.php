<?php 

/* errors that result in a redirect to a 404 page */
class NotFoundException extends Exception{};
/* no session found or session expired */
class InvalidSessionException extends Exception{};


?>
