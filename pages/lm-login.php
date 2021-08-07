
<body class="bg-dark">


    <div class="sufee-login d-flex align-content-center flex-wrap">
        <div class="container">
            <div class="login-content">
                <div class="login-logo">
                    <a href="index.php">
                      <?php echo "Libremaint"; ?>
                    </a>
                </div>
                <div class="login-form">
                    <form method="post">
                        <div class="form-group">
                           <?php echo "<label>".(gettext("Username"))."</label>";
                           echo "<input type=\"text\" name=\"username\" class=\"form-control\" placeholder=\"".gettext("username")."\">";putenv("LC_ALL=hu_HU");?>
                        </div>
                            <div class="form-group">
                                <?php 
                                
                                echo "<label>".gettext('Password')."</label>";?>
                                <input type="password" name="password" class="form-control" placeholder="Password">
                        </div>
                               <button type="submit" class="btn btn-success btn-flat m-b-30 m-t-30">
		<?php echo gettext('Sign in');?>
				</button>
                               
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo VENDORS_LOC;?>jquery/dist/jquery.min.js"></script>
    <script src="<?php echo VENDORS_LOC;?>popper.js/dist/umd/popper.min.js"></script>
    <script src="<?php echo VENDORS_LOC;?>bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="<?php echo CSS_LOC;?>js/main.js"></script>


</body>

</html>

