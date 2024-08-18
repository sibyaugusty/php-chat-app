<?php require_once '../includes/header.php'; ?>
<section class="forgot-password-form-container">
    <form class="forgot-password-form" method="post">
        <div id="error-message" class="error-message"></div>
        <label for="Title" class="title">Forgot Password</label>
        <div class="row">
            <div class="input-group">
                <input type="email" name="email" placeholder=" " id="email" class="input" autocomplete="off">
                <label for="email" class="placeholder">Email</label>
            </div>
        </div>
        <button type="submit" class="submit-button" id="sendForgotPasswordMail">Send Mail</button>

        <div class="form-footer">
            <!-- <p class="is-a-member">Please verify from your shared mail id <a href="">Log In</a></p> -->
        </div>
    </form>

</section>
<?php require_once '../includes/footer.php' ?>
<script>
    $(document).ready(function() {

        $('#sendForgotPasswordMail').click(function(e) {
            e.preventDefault();

        });
    });
</script>