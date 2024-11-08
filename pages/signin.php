<?php require_once '../includes/header.php'; ?>
<div class="main-body">
    <section class="signIn-form-container">
        <form class="signIn-form" method="post">
            <div id="error-message" class="error-message"></div>
            <label for="Title" class="title">Sign In</label>
            <div class="row">
                <div class="input-group">
                    <input type="email" name="email" placeholder=" " id="email" class="input" autocomplete="off">
                    <label for="email" class="placeholder">Email</label>
                </div>
            </div>
            <div class="row">
                <div class="input-group">
                    <input type="password" name="password" placeholder=" " id="password" class="input" autocomplete="off">
                    <label for="password" class="placeholder">Password</label>
                </div>
            </div>

            <button type="submit" class="submit-button" id="loginFormSubmit">Log In</button>

            <div class="form-footer">
                <a href="./forgot-password.php" class="forgot-password">Forgot Password</a>
                <p class="is-a-member" r>Don't Have An Account? <a href="./signup.php">Sign Up</a></p>
            </div>
        </form>

    </section>
</div>
<?php require_once '../includes/footer.php' ?>
<script>
    $(document).ready(function() {
        $('.signIn-form').on('submit', function(event) {
            event.preventDefault(); // Prevent default form submission

            // Clear previous error messages
            $('#error-message').text('');
            var hasError = false;
            var errorMessage = '';

            // Validate fields
            $('.signIn-form .input').each(function() {
                var input = $(this);
                if (input.val().trim() === '') {
                    $('#error-message').addClass('show');

                    // Find the associated label
                    var label = $('label[for="' + input.attr('name') + '"]').text();

                    // Set error message
                    errorMessage = 'Please Enter ' + label + '.';

                    hasError = true;
                    return false;
                }
            });

            // Display error message or proceed with AJAX
            if (hasError) {
                $('#error-message').text(errorMessage);
            } else {
                // Gather form data
                $('#error-message').removeClass('show');
                var formData = $(this).serialize();

                // Perform the AJAX request
                $.ajax({
                    type: 'POST',
                    url: '../code/user_management.php',
                    data: {
                        action: 'signIn',
                        formData: formData
                    },
                    success: function(response) {
                        var response = JSON.parse(response);
                        // alert(response.msg);
                        if (response.success == true) {
                            window.location.href = '../pages/chat-body.php';

                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle errors
                        alert('Error: ' + error);
                    }
                });
            }
        });
    });
</script>