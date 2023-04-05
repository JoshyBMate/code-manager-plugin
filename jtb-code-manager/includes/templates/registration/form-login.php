<?php
// Redirect logged-in users
if ( is_user_logged_in() ) {
    wp_redirect( wc_get_account_endpoint_url( 'dashboard' ) );
    exit;
}

// Check if the user clicked on the "Sign in" link
$show_login_form = false;
if ( isset( $_GET['action'] ) && $_GET['action'] === 'login' ) {
    $show_login_form = true;
}

if ( ! $show_login_form ) {
    ?>

<div class="cm-training-container regPadding">
    
    <div class="cm-training-left">
        
        <div class="cm-admin_logo"></div>
        <div class="cm-plus-graphic"></div>
        
    </div>
    
    <div class="cm-training-right">
        <h1>REGISTRATION</h1>
    
    <h2 class="cm-introduction">You've been enrolled to participate in an online course/training that will give you a recognised certificate on completion</h2>
    
    <h5 class="cm-result-direction">Please fill out your details below so that we may use these details to create your account and certify your certificate on completion.</h5>
    
    <form id="custom-registration-form" method="post">
        <input type="hidden" name="action" value="custom_user_registration">
        <?php wp_nonce_field('custom_user_registration', 'custom_user_registration_nonce'); ?>
        
        <div class="col-50"><input placeholder="Username" type="text" name="user_login" id="user_login" required></div>
        <div class="col-50"><input placeholder="Password" type="password" name="user_password" id="user_password" required></div>
        <div class="col-50"><input placeholder="Email Address" type="email" name="user_email" id="user_email" required></div>
        <div class="col-50">
            <select name="user_role" id="user_role">
                <option value="" disabled selected>Role</option>
                <option value="customer">Customer</option>
                <option value="hr_manager">Organisation</option>
            </select></div>
             <div class="cm-form-error" data-element="cm-form-error"></div>
        <div class="col-100"><input class="cm-submit" type="submit" value="Register Account"></div>
    </form>
    <p>Already have an account? <a href="<?php echo esc_url( add_query_arg( 'action', 'login' ) ); ?>">Sign in here</a></p>
    </div>
</div>
 <?php
} else {
    // Show the login form
    ?>
    
    <div class="cm-training-container regPadding">
    <div class="cm-training-left">
        
        <div class="cm-admin_logo"></div>
        <div class="cm-plus-graphic"></div>
        
    </div>
    
    <div class="cm-training-right">
    
        <div class="cm-admin_logo"></div>
        <h1>Login Form</h1>
        <form id="custom-login-form" data-element="cm-form-login" method="post">
            <input type="hidden" name="action" value="custom_user_login">
            <?php wp_nonce_field('custom_user_login', 'custom_user_login_nonce'); ?>
            <div class="col-100"><input placeholder="Username" type="text" name="username" id="username" class="cm-input-text" required></div>
            <div class="col-100"><input placeholder="Password" type="password" name="password" id="password" class="cm-input-text" required></div>
            <div class="cm-form-error" data-element="cm-form-error"></div>
            <div class="col-100"><input type="submit" class="cm-submit" name="login" value="Login"></div>
        </form>
        <p>Don't have an account? <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">Register here</a></p>
    </div>
    </div>
        <?php
}

    // // Handle form submission
    // if ( isset( $_POST['register'] ) ) {
    //     $username = sanitize_user( $_POST['username'] );
    //     $email = sanitize_email( $_POST['email'] );
    //     $password = $_POST['password'];
    //     $nonce = $_POST['registration_nonce'];
        
    //     // Verify nonce
    //     if ( ! wp_verify_nonce( $nonce, 'custom_registration_nonce' ) ) {
    //         wp_die( 'Nonce verification failed' );
    //     }
        
    //     // Create the user
    //     $user_id = wp_create_user( $username, $password, $email );
        
    //     if ( is_wp_error( $user_id ) ) {
    //         wp_die( $user_id->get_error_message() );
    //     }
        
    //     // Log the user in
    //     wp_set_auth_cookie( $user_id, true );
        
    //     // Redirect to the account dashboard
    //     wp_redirect( wc_get_account_endpoint_url( 'dashboard' ) );
    //     exit;
    // }
    
    // action="<?php echo esc_url(admin_url('admin-post.php')); ?>"