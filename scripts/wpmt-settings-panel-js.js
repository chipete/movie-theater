

function wpmt_option_action_in_progress(wpmt_progress_id, button_id, graphic) {
    document.getElementById(wpmt_progress_id).innerHTML = '<img src="' + graphic + '" style=' + "'width: 15px; height: 15px;'" + '" /> In Progress... (this may take a few minutes)';
            document.getElementById(button_id).style.display = 'none';

}
