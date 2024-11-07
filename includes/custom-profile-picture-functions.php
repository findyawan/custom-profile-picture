<?php

defined('ABSPATH') or die('Direct script access disallowed.');

/**
 * Menambahkan field custom di halaman profil user untuk upload foto profil.
 *
 * @param WP_User $user Data user yang sedang diedit.
 */
function cpp_add_profile_fields($user) {
    ?>
    <h3><?php _e("Custom Profile Picture", "custom-profile-picture"); ?></h3>

    <table class="form-table">
        <tr>
            <th><label for="cpp_picture"><?php _e("Profile Picture URL", "custom-profile-picture"); ?></label></th>
            <td>
                <input type="text" name="cpp_picture" id="cpp_picture" value="<?php echo esc_attr(get_user_meta($user->ID, 'cpp_picture', true)); ?>" class="regular-text" readonly />
                <input type="button" class="button-secondary" value="<?php _e('Select Profile Picture', 'custom-profile-picture'); ?>" id="cpp_select_image"/><br/>
                <span class="description"><?php _e("Please select a profile picture from the media library.", "custom-profile-picture"); ?></span>
            </td>
        </tr>
    </table>
    <?php
}

add_action('show_user_profile', 'cpp_add_profile_fields');
add_action('edit_user_profile', 'cpp_add_profile_fields');

/**
 * Menyimpan URL gambar profil ke user meta saat form profil disimpan.
 *
 * @param int $user_id ID user yang sedang diedit.
 */
function cpp_save_profile_fields($user_id) {
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }

    if (isset($_POST['cpp_picture'])) {
        update_user_meta($user_id, 'cpp_picture', $_POST['cpp_picture']);
    }
}

add_action('personal_options_update', 'cpp_save_profile_fields');
add_action('edit_user_profile_update', 'cpp_save_profile_fields');

/**
 * Mengganti avatar user dengan gambar profil yang telah dipilih dari Media Library.
 *
 * @param string $avatar HTML untuk avatar default.
 * @param mixed $id_or_email User ID, email address, atau object.
 * @param int $size Ukuran avatar.
 * @param string $default URL ke default avatar.
 * @param string $alt Alternatif teks untuk avatar.
 * @return string HTML avatar yang diperbarui (jika ada gambar custom).
 */
function cpp_custom_avatar($avatar, $id_or_email, $size, $default, $alt) {
    $user = false;

    if (is_numeric($id_or_email)) {
        $user_id = (int) $id_or_email;
        $user = get_user_by('id', $user_id);
    } elseif (is_object($id_or_email)) {
        if (!empty($id_or_email->user_id)) {
            $user_id = (int) $id_or_email->user_id;
            $user = get_user_by('id', $user_id);
        }
    } elseif (is_string($id_or_email)) {
        $user = get_user_by('email', $id_or_email);
    }

    if ($user && is_object($user)) {
        $custom_avatar_url = get_user_meta($user->ID, 'cpp_picture', true);

        if ($custom_avatar_url) {
            $avatar = sprintf("<img alt='%s' src='%s' class='avatar avatar-%d photo' height='%d' width='%d' />",
                       esc_attr($alt), esc_url($custom_avatar_url), (int)$size, (int)$size, (int)$size);
        }
    }

    return $avatar;
}

add_filter('get_avatar', 'cpp_custom_avatar', 10, 5);

/**
 * Script JavaScript untuk menggunakan media uploader WordPress.
 */
function cpp_media_selector_script() {
    ?>
    <script type='text/javascript'>
    jQuery(document).ready(function($) {
        var file_frame;
        jQuery('#cpp_select_image').on('click', function(event) {
            event.preventDefault();
            // Jika sudah ada, buka frame sebelumnya.
            if (file_frame) {
                file_frame.open();
                return;
            }

            // Ciptakan media frame baru
            file_frame = wp.media.frames.file_frame = wp.media({
                title: '<?php _e('Select a Profile Picture', 'custom-profile-picture'); ?>',
                button: {
                    text: '<?php _e('Use this picture', 'custom-profile-picture'); ?>',
                },
                multiple: false  // Posisikan ke false jika Anda hanya menginginkan satu file.
            });

            // Ketika foto dipilih gunakan foto tersebut
            file_frame.on('select', function() {
                var attachment = file_frame.state().get('selection').first().toJSON();
                jQuery('#cpp_picture').val(attachment.url);
            });

            // Buka frame
            file_frame.open();
        });
    });
    </script>
    <?php
}

add_action('admin_footer', 'cpp_media_selector_script');