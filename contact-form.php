<?php
/*
Plugin Name: Contact Form
Plugin URI: https://example.com/contact-form
Description: Plugin de formulaire de contact pour WordPress.
Version: 1.0
Author: zaani hmaza
Author URI: https://example.com/
License: GPL2
*/

// $date_envoi = date('l jS \of F Y h:i:s A');

add_action( 'admin_menu', 'wporg_options_page' );

function wporg_options_page() {
    add_menu_page(
        'contact-form',
        'contact-form',
        'manage_options',
        plugin_dir_path(__FILE__) . 'admin/view.php',
        null,
         'dashicons-slides',
        20
    );
}

register_activation_hook( __FILE__, 'cf_create_table' );

function cf_create_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'contact_form';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        subject VARCHAR(255) NOT NULL,
        name VARCHAR(255) NOT NULL,
        first_name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        date_envoi DATETIME NOT NULL
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}

register_deactivation_hook( __FILE__, 'cf_delete_table' );

function cf_delete_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'contact_form';
    $sql = "DROP TABLE IF EXISTS $table_name;";
    $wpdb->query( $sql );
}

// Enregistrement du shortcode pour afficher le formulaire de contact
add_shortcode('my_contact_form', 'my_contact_form');

// Fonction de traitement du formulaire de contact
function my_contact_form() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'contact_form';

    $result = $wpdb->insert( $table_name, $data );

    if ( $result === false ) {
        // Afficher un message d'erreur
        echo '<div class="error">Une erreur s\'est produite lors de l\'enregistrement du formulaire.</div>';
    } else {
        // Afficher un message de succès
        echo '<div class="updated">Le formulaire a été envoyé avec succès !</div>';
    }
}

add_shortcode( 'my_contact_form', 'my_contact_form_shortcode' );

function my_contact_form_shortcode() {
    // Afficher le formulaire de contact avec le shortcode
    ob_start();
    ?>
<form id="contact-form" method="post" action="">
    <p><label for="subject">Sujet :</label><br />
        <input type="text" name="subject" id="subject" required></p>
    <p><label for="name">Nom :</label><br />
        <input type="text" name="name" id="name" required></p>
    <p><label for="first_name">Prénom :</label><br />
        <input type="text" name="first_name" id="first_name" required></p>
    <p><label for="email">Email :</label><br />
        <input type="email" name="email" id="email" required></p>
    <p><label for="message">Message :</label><br />
        <textarea name="message" id="message" required></textarea></p>
    <p><input type="submit" name="submit" value="Envoyer"></p>
</form>
    <style>
    /* Style for form input fields and textarea */
    input[type="text"],
    input[type="email"],
    textarea {
        width: 100%; /* Make input fields and textarea take up full width of form container */
        padding: 10px; /* Add padding for spacing */
        margin-bottom: 10px; /* Add margin bottom for spacing between fields */
        border: 1px solid #ccc; /* Add border for visual separation */
    }

    /* Style for form submit button */
    input[type="submit"] {
        background-color: #4caf50; /* Set your desired button background color */
        color: #fff; /* Set button text color */
        padding: 10px 20px; /* Add padding for spacing */
        border: none; /* Remove default button border */
        cursor: pointer; /* Add cursor pointer for hover effect */
    }

    /* Style for form submit button on hover */
    input[type="submit"]:hover {
        background-color: #45a048; /* Set your desired button background color on hover */
    }
</style>
    <?php
    return ob_get_clean();
}
// Function to process form data
function process_contact_form() {
    if ( isset( $_POST['submit'] ) && $_POST['submit'] === '1' ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'contact_form';

        // Sanitize form data
        $subject = sanitize_text_field( $_POST['subject'] );
        $first_name = sanitize_text_field( $_POST['first_name'] );
        $last_name = sanitize_text_field( $_POST['last_name'] );
        $email = sanitize_email( $_POST['email'] );
        $message = sanitize_textarea_field( $_POST['message'] );
        $$date_envoi = date('l jS \of F Y h:i:s A');

        // Insert form data into database
        $wpdb->insert(
            $table_name,
            array(
                'subject' => $subject,
                'name' => $last_name,
                'first_name' => $first_name,
                'email' => $email,
                'message' => $message,
                'date_envoi' => $date_envoi,
            ),
        );

        // Redirect to the same page to avoid form resubmission
        // wp_redirect( get_permalink() );
        exit;
    }
}
