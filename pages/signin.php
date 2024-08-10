<?php require_once '../includes/header.php'; ?>
<section class="signIn-form-container">
    <form class="signIn-form" method="post">
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
            <p class="is-a-member" r>Don't Have An Account? </p><a href=" ./signIn.php">Sign Up</a>
        </div>
    </form>

</section>
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
                    $('#error-message').show();

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
                $('#error-message').hide();
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
                        alert(response.msg);
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