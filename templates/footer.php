</div>

<!-- BEGIN Footer -->

<footer class="py-3 my-4">


    <ul class="nav justify-content-center border-bottom pb-3 mb-3">
			<? if ($oAuth->IsValidUser()) { ?>
				<li class="nav-item"><a href="/logout" class="nav-link px-2">Logout</a></li>
				<li class="nav-item"><a href="/contact" class="nav-link px-2 ">Contact Us</a></li>
				<li class="nav-item"><a href="https://www.oneworld365.org" class="nav-link px-2">Visit oneworld365.org</a></li>
			<? } ?>
    </ul>
    <p class="text-center text-body-secondary"><?= $this->Get("DESCRIPTION"); ?></p>

    <div class="row">
        <div class="nav justify-content-center">
            <div class="col-8">
                <h4>Follow Our Social Media Pages</h4>
                <p><a href="http://www.facebook.com/oneworld365" target="_blank"><img alt="One World 365 Facebook" src="http://www.oneworld365.org/img/101/facebook_84.png" /></a>&nbsp;&nbsp;<a href="http://www.instagram.com/oneworld365" target="_blank"><img alt="One World 365 Instagram" src="http://www.oneworld365.org/img/101/instagram_84.png" /></a>&nbsp;&nbsp;<a href="http://www.twitter.com/oneworld365" target="_blank"><img alt="One World 365 Twitter" src="http://www.oneworld365.org/img/101/twitter_84.jpg" /></a>&nbsp;&nbsp;<a href="http://www.pinterest.com/oneworld365" target="_blank"><img alt="One World 365 Pinterest" src="http://www.oneworld365.org/img/101/pinterest_84.png" /></a></p>
            
                <h4>Partners &amp; Featured In</h4>
            	<img alt="FCO" src="http://www.oneworld365.org/img/101/fco_84.png" />&nbsp;&nbsp;<img alt="BBC Worldwide" src="http://www.oneworld365.org/img/101/bbc_worldwide_84.png" />&nbsp; <img alt="USA Today" src="http://www.oneworld365.org/img/101/usa_today_84.png" />&nbsp;&nbsp;<img alt="Rough Guides" src="http://www.oneworld365.org/img/101/rough_guides_84.jpg" />&nbsp;&nbsp;<img alt="Lonely Planet" src="http://www.oneworld365.org/img/101/lonely_planet_84.png" />&nbsp;&nbsp;<img alt="National Geographic" src="http://www.oneworld365.org/img/101/Natgeo_84.png" />
            </div>
        </div>
    </div>

    <div class="row my-3">
    <p class="small text-center text-body-secondary"><?= $this->Get("COPYRIGHT"); ?></p>
    </div>
    
</footer>

<!-- END Footer -->



</form>
</div>


<script src="/includes/js/underscore-min.js"></script>
<script src="/includes/js/backbone-min.js"></script>
<script src="/includes/js/app.js"></script>


<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
 

</html>
