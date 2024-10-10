<?php require_once '../includes/header.php'; ?>
<div class="main-body">

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
            <div class="row">
                <div class="image-upload-container">
                    <div class="image-upload">
                        <label for="image-upload" class="upload-label">Upload Image</label>
                        <input type="file" id="image-upload" class="upload-input" accept="image/*" />
                    </div>
                    <div id="preview-container"></div>
                </div>
            </div>

            <button type="submit" class="submit-button" id="signUpSubmit">Sign Up</button>
            <div class="form-footer">
                <p class="is-a-member">Already Have An Account? <a href="./signin.php">Log In</a></p>
            </div>
        </form>
    </section>
</div>

<?php require_once '../includes/footer.php'; ?>

<script>
    $(document).ready(function() {
        $('#image-upload').on('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    $('#preview-container').html('<img src="' + event.target.result + '" alt="Image Preview">');
                }
                reader.readAsDataURL(file);
            }
        });

        $(document).ready(function() {
            $('#signUpSubmit').on('click', function(e) {
                e.preventDefault(); // Prevent form submission

                // Create FormData object to hold form values
                var formData = new FormData();

                // Append form fields
                formData.append('action', 'signUp');
                formData.append('firstname', $('#firstname').val());
                formData.append('lastname', $('#lastname').val());
                formData.append('email', $('#email').val());
                formData.append('mobileNumber', $('#mobileNumber').val());
                formData.append('dob', $('#dob').val());
                formData.append('password', $('#password').val());
                formData.append('confirmPassword', $('#confirmPassword').val());

                // Append the profile image file
                var profileImage = $('#image-upload')[0].files[0];
                if (profileImage) {
                    formData.append('profileImage', profileImage);
                }

                // Perform the AJAX request
                $.ajax({
                    type: 'POST',
                    url: '../code/user_management.php',
                    data: formData,
                    processData: false, // Prevent jQuery from processing the data
                    contentType: false, // Prevent jQuery from setting the content type
                    success: function(response) {
                        var response = JSON.parse(response);
                        alert(response.msg);
                    },
                    error: function(xhr, status, error) {
                    }
                });
            });
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