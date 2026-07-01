<?php
    global $ays_chart_db_actions;

    $id = (isset($_GET['id'])) ? absint( sanitize_text_field($_GET['id']) ) : 0;

    if (isset($_POST['ays_submit']) || isset($_POST['ays_submit_top'])) {
        $ays_chart_db_actions->add_or_edit_item( $id );
    }


    if(isset($_POST['ays_apply']) || isset($_POST['ays_apply_top'])){
        $_POST['save_type'] = 'apply';
        $ays_chart_db_actions->add_or_edit_item( $id );
    }


    if(isset($_POST['ays_save_new']) || isset($_POST['ays_save_new_top'])){
        $_POST['save_type'] = 'save_new';
        $ays_chart_db_actions->add_or_edit_item( $id );
    }

