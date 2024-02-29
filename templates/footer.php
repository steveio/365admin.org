
</div>
<!-- BEGIN Footer -->

<footer class="py-3 my-4">

<div class="container">
<div class="align-items-center justify-content-center">


<? if (!$oAuth->IsValidUser()) { ?>
    <div class="row">
            <div class="col-12">
            <script type="text/javascript"><!--
            google_ad_client = "ca-pub-9874604497476880";
            /* Gap Year 365 */
            google_ad_slot = "2273353061";
            google_ad_width = 728;
            google_ad_height = 90;
            //-->
            </script>
            <script type="text/javascript"
            src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
            </script>
            </div>

            <div class="span8 adbanner_mob">
            <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <!-- One World365 Mobile Banner Header -->
            <ins class="adsbygoogle"
                 style="display:inline-block;width:320px;height:100px"
                 data-ad-client="ca-pub-9874604497476880"
                 data-ad-slot="1198653468"></ins>
            <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
            </div>
    </div>
<? } ?>

    <ul class="nav justify-content-center border-bottom pb-3 mb-3">
	<? if ($oAuth->IsValidUser()) { ?>
		<li class="nav-item"><a class="nav-link px-2 text-muted" href="/logout">Logout</a></li>
	<? } ?>
        <li class="nav-item"><a class="nav-link px-2 text-muted" href="/about-us" title="About">About</a></li>
        <li class="nav-item"><a class="nav-link px-2 text-muted" href="/disclaimer" title="Disclaimer">Disclaimer</a></li>
        <li class="nav-item"><a class="nav-link px-2 text-muted" href="/privacy" title="Privacy">Privacy Policy</a></li>
        <li class="nav-item"><a class="nav-link px-2 text-muted" href="/company/a-z" title="Company A-Z">Company A-Z</a></li>
        <li class="nav-item"><a class="nav-link px-2 text-muted" href="/advertising" title="Advertise">Advertise</a></li>
        <li class="nav-item"><a class="nav-link px-2 text-muted" href="/press" title="Press">Press</a></li>
        <li class="nav-item"><a class="nav-link px-2 text-muted" href="/contact" title="Contact">Contact</a></li>
    </ul>
    <p class="text-center text-body-secondary"><?= $this->Get("DESCRIPTION"); ?></p>

    <div class="row">
        <div class="">
            <div class="footer-icon">
                <h4>Follow Our Social Media Pages</h4>
        		<div class="col-12 my-3">
                        <a href="http://www.facebook.com/oneworld365" target="_blank"><img alt="One World 365 Facebook" src="http://www.oneworld365.org/img/101/facebook_84_50.png" /></a>
                        <a href="http://www.instagram.com/oneworld365" target="_blank"><img alt="One World 365 Instagram" src="http://www.oneworld365.org/img/101/instagram_84_50.png" /></a>
                        <a href="http://www.twitter.com/oneworld365" target="_blank"><img alt="One World 365 Twitter" src="http://www.oneworld365.org/img/101/twitter_84_50.jpg" /></a>
                        <a href="http://www.pinterest.com/oneworld365" target="_blank"><img alt="One World 365 Pinterest" src="http://www.oneworld365.org/img/101/pinterest_84_50.png" /></a>
        		</div>
            
                <h4>Partners &amp; Featured In</h4>
        		<div class="col-12 my-3">
                    	<img alt="FCO" src="http://www.oneworld365.org/img/101/fco_84_50.png" />
                    	<img alt="BBC Worldwide" src="http://www.oneworld365.org/img/101/bbc_worldwide_84_50.png" /> 
                    	<img alt="USA Today" src="http://www.oneworld365.org/img/101/usa_today_84_50.png" />
                    	<img alt="Rough Guides" src="http://www.oneworld365.org/img/101/rough_guides_84_50.jpg" />
                    	<img alt="Lonely Planet" src="http://www.oneworld365.org/img/101/lonely_planet_84_50.png" />
                    	<img alt="National Geographic" src="http://www.oneworld365.org/img/101/Natgeo_84_50.png" />
            	</div>
            </div>
        </div>
    </div>

    <div class="row my-3">
    <p class="small text-center text-body-secondary"><?= $this->Get("COPYRIGHT"); ?></p>
    </div>
    
</div>
</div>

</footer>

<!-- END Footer -->



</form>
</div>


<script src="/includes/js/app.js"></script>
<script src="/includes/js/popper.min.js" integrity="" crossorigin="anonymous"></script>
<script src="/includes/js/bootstrap.min.js" integrity="" crossorigin="anonymous"></script>
 

</html>
