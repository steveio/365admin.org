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
    <p class="text-center text-body-primary"><?= $this->Get("COPYRIGHT"); ?></p>
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
