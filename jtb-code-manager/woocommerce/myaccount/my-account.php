<?php

/**
 * My Account page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

defined('ABSPATH') || exit;

/**
 * My Account navigation.
 *
 * @since 2.6.0
 */
$current_url = add_query_arg( NULL, NULL );
 
?>

<div class="cm-my-accountPanel">
<div class="cm-leftPanel">
    <a href="https://test.openform.online/staging/codeblue"><div class="cm-admin_logo"></div></a>
<?php
if((is_account_page() && str_contains($current_url,"id")) || (is_account_page() && str_contains($current_url,"registration"))){
    do_action('cm_training_course_left_bar');
}else{
    do_action('woocommerce_account_navigation'); 
}

?>
<a href="#" class="cm-signOut_bt">SIGN-OUT</a>
</div>


<div class="cm-rightPanel">
<div class="woocommerce-MyAccount-content">
    <?php
    /**
     * My Account content.
     *
     * @since 2.6.0
     */
    do_action('woocommerce_account_content');
    ?>
</div>
</div>
</div>