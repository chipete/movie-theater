<?php
/**
 * Created by PhpStorm.
 * User: Chris
 * Date: 2/11/16
 * Time: 8:04 PM
 */
?>

<head>
    <script>
wpmt_option_action_in_progress = function(wpmt_progress_id, button_id) {
    document.getElementById(wpmt_progress_id).innerHTML = '<img src="<?php //echo plugins_url('../images/wpmt_indicator.gif', __FILE__); ?>" style=' + "'width: 15px; height: 15px;'" + '" /> In Progress... (this may take a few minutes)';
            document.getElementById(button_id).style.display = 'none';
        }
    </script>
</head>