<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0" />
	<title>New Password</title>
	<link rel="icon" type="image/png" href="<?=base_url();?>public/img/favicon.ico"/>
    <link href="<?=base_url();?>public/css/main.css" rel="stylesheet">
    <link href="<?=base_url();?>public/css/style.css" rel="stylesheet">
</head>
<body>
    <?php
    include APP_DIR.'views/templates/nav_auth.php';
    ?>
    <main class="py-4">
        <div class="container">
            <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Login</div>
                    <div class="card-body">
                        <span class="valid-feedback" role="alert">
                            <strong>Note: Password must be at least 8 characters and contains one of this special characters (!@£$%^&*-_+=?), number, uppercase and lowercase letters.</strong>
                        </span>                        
                        <?php flash_alert() ;?>
                        <form id="myForm" action="<?=site_url('auth/set-new-password');?>" method="post">
                            <?php csrf_field(); ?>
                            <input type="hidden" name="token" value="<?php !empty($_GET['token']) && print $_GET['token'];?>"> 
                            <div class="row mb-3">
                                <label for="password" class="col-md-4 col-form-label text-md-end">New Password</label>
                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control " name="password" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="password" class="col-md-4 col-form-label text-md-end">Confirm New Password</label>
                                <div class="col-md-6">
                                    <input id="re_password" type="password" class="form-control " name="re_password" required>
                                </div>
                            </div>
                            <div class="row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary">Proceed</button><a class="btn btn-link" href="<?=site_url();?>"> Back to Home</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js" integrity="sha512-aVKKRRi/Q/YV+4mjoKBsE4x3H+BkegoM/em46NNlCqNTmUYADjBbeNefNxYV7giUp0VxICtqdrbqU7iVaeZNXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.js"></script>
    <script>
        $(function() {
            var myForm = $("#myForm")
                if(myForm.length) {
                    myForm.validate({
                        rules: {
                            password: {
                                required: true,
                                minlength: 8
                            },
                            re_password: {
                                required: true,
                                minlength: 8
                            }
                        },
                        messages: {
                            password: {
                                required: "Please input your password",
                                minlength: jQuery.validator.format("Password must be atleast {0} characters.")
                            },
                            re_password: {
                                required: "Please input your password",
                                minlength: jQuery.validator.format("Password must be atleast {0} characters.")
                            }
                        },
                    })
                }
        })
    </script>
</body>
</html>