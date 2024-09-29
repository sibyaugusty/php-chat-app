<?php require_once '../includes/header.php'; ?>

<section class="user-profile-container">

    <!-- <h1><?php print_r($_SESSION) ?></h1> -->
    <div class="user-profile-navigation">

        <div class="user-profile profile-nav">
            <div class="sub-profile">
                <img src="" alt="">
                <h2 style="text-transform: capitalize;"><?php echo $_SESSION['user_name']; ?></h2>
            </div>
        </div>

    </div>

</section>

<?php require_once '../includes/footer.php' ?>