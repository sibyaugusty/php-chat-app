<?php require_once '../includes/header.php'; ?>

<section class="signup-form-container">
    <form class="signup-form" method="post">
        <div id="error-message" class="error-message"></div>
        <label for="Title" class="title">Sign Up</label>
        <div class="row">
            <div class="input-group">
                <input type="text" name="firstname" id="firstname" placeholder=" " class="input" autocomplete="off">
                <label for="firstname" class="placeholder">First Name</label>
            </div>

            <div class="input-group">
                <input type="text" name="lastname" placeholder=" " id="lastname" autocomplete="off" class="input">
                <label for="lastname" class="placeholder">Last Name</label>
            </div>
        </div>
        <div class="row">
            <div class="input-group">
                <input type="email" name="email" placeholder=" " id="email" autocomplete="off" class="input">
                <label for="email" class="placeholder">Email</label>
            </div>
        </div>
        <div class="row">
            <div class="input-group">
                <input type="text" name="mobileNumber" id="mobileNumber" placeholder=" " autocomplete="off" class="input">
                <label for="mobileNumber" class="placeholder">Mobile Number</label>
            </div>

            <div class="input-group">
                <input type="date" name="dob" placeholder=" " id="dob" class="input" autocomplete="off">
                <label for="dob" class="placeholder">Date Of Birth</label>
            </div>
        </div>
        <div class="row">
            <div class="input-group">
                <input type="password" name="password" placeholder=" " id="password" class="input" autocomplete="off">
                <label for="password" class="placeholder">Password</label>
                <i class="show-password fa-sharp fa-solid fa-eye"></i>
            </div>
            <div class="input-group">
                <input type="password" name="confirmPassword" placeholder=" " id="confirmPassword" class="input" autocomplete="off">
                <label for="confirmPassword" class="placeholder">Confirm Password</label>
                <i class="show-password fa-sharp fa-solid fa-eye"></i>
            </div>
        </div>

        <button type="submit" class="submit-button" id="signUpSubmit">Sign Up</button>
        <div class="form-footer">
            <p class="is-a-member">Already Have An Account? <a href="./signin.php">Log In</a></p>
        </div>
    </form>
</section>

<?php require_once '../includes/footer.php'; ?>

<script>
    $(document).ready(function() {

        $('.signup-form').on('submit', function(event) {
            event.preventDefault(); // Prevent default form submission

            // Clear previous error messages
            $('#error-message').text('');
            var hasError = false;
            var errorMessage = '';

            // Validate fields
            $('.signup-form .input').each(function() {
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

            // Validate password match
            if (!hasError) {
                var password = $('#password').val().trim();
                var confirmPassword = $('#confirmPassword').val().trim();
                if (password !== confirmPassword) {
                    errorMessage = 'Passwords do not match.';
                    hasError = true;
                }
            }

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
                        action: 'signUp',
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

        $(document).on('click', '.show-password', function(e) {
            e.preventDefault();

            var icon = $(this);
            var input = icon.siblings('.input');


            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                input.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }

        });
    });
</script>