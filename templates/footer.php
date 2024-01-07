</div>

<!-- BEGIN Footer -->

<footer class="py-3 my-4">
    <ul class="nav justify-content-center border-bottom pb-3 mb-3">
			<? if ($oAuth->IsValidUser()) { ?>
				<li class="nav-item"><a href="/logout" class="nav-link px-2 text-body-secondary">Logout</a></li>
				<li class="nav-item"><a href="https://www.oneworld365.org" class="nav-link px-2 text-body-secondary">OneWorld365.org</a></li>
			<? } ?>
    </ul>
    <p class="text-center text-body-secondary"><?= $this->Get("DESCRIPTION"); ?></p>
    <br />
    <p class="text-center text-body-secondary"><?= $this->Get("COPYRIGHT"); ?></p>
</footer>

<!-- END Footer -->



</form>
</div>


<script src="/includes/js/underscore-min.js"></script>
<script src="/includes/js/backbone-min.js"></script>
<script src="/includes/js/app.js"></script>


<script src="./includes/js/umd/popper.min.js"></script>
<script src="./includes/js/bootstrap.min.js"></script>


</html>
