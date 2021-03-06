 <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="<?= BASEURL ?>/auth/logout">Logout</a>
                </div>
            </div>
        </div>
    </div>
</div>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="<?= BASEURL ?>/vendor/jquery/jquery.min.js"></script>
	<script src="<?= BASEURL ?>/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
	<script src="<?= BASEURL ?>/js/script.js" type="text/javascript" charset="utf-8" async defer></script>
</body>
</html>
