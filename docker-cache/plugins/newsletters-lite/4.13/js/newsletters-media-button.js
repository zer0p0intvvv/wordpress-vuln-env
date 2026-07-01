/*  Newsletter – Add-Media-row button
    ---------------------------------- */

/* ------------------------------------------------------------------ */
/* Helper: dispatch a native mouse event                              */
/* ------------------------------------------------------------------ */
function nativeMouse(target, type) {
    target.dispatchEvent(
        new MouseEvent(type, {
            view: window,
            bubbles: true,
            cancelable: true,
            buttons: 1
        })
    );
}

(function ($) {

    const PLACEHOLDER = '[newsletter_shortcode]';

    /* -------------------------------------------------------------- */
    /* 1. Open the TinyMCE “Newsletters” dropdown                     */
    /* -------------------------------------------------------------- */
    function openTinyMceMenu(editorID) {

        if (typeof tinymce === 'undefined') {
            return false;
        }

        const ed = tinymce.get(editorID);
        if (!ed || ed.isHidden()) {
            return false;               // not in Visual
        }

        let $btn = $(ed.container)
            .closest('.wp-editor-wrap')
            .find(
                'button[aria-label="Newsletters"],' +
                'button[data-tooltip="Newsletters"],' +
                '.mce-i-Newsletters, .mce-i-newsletters'
            )
            .first()
            .closest('button');

        if (!$btn.length) {
            console.warn('[Newsletter] TinyMCE menubutton not found.');
            return false;
        }

        ed.focus();
        
           // Split buttons need the ▼ “open” part pressed
           const target = $btn.hasClass('mce-btn-split')
                         ? $btn.find('.mce-open').get(0)   // caret half
                         : $btn.get(0);                    // normal button
        
           nativeMouse(target, 'mousedown');
           nativeMouse(target, 'mouseup');
           target.click();          // ensure the click lands
        return true;
    }

    /* -------------------------------------------------------------- */
    /* 2. Text tab  →  switch to Visual, then open the menu           */
    /* -------------------------------------------------------------- */
    function switchToVisualAndOpen(editorID) {
        if (typeof switchEditors === 'undefined') {
            return false;
        }
        switchEditors.go(editorID, 'tmce');
        setTimeout(() => openTinyMceMenu(editorID), 60);
        return true;
    }

    /* -------------------------------------------------------------- */
    /* 3. Ultimate fallback                                           */
    /* -------------------------------------------------------------- */
    function insertPlaceholder() {
        if (typeof QTags !== 'undefined') {
            QTags.insertContent(PLACEHOLDER);
            return true;
        }
        return false;
    }

    /* -------------------------------------------------------------- */
    /* 4. Main delegated handler                                      */
    /* -------------------------------------------------------------- */
    $(document).on('click', '.newsletters-media-button', function (e) {
        e.preventDefault();

        const editorID = $(this).data('editor') || 'content';

        if (openTinyMceMenu(editorID)) { return; }
        if (switchToVisualAndOpen(editorID)) { return; }
        insertPlaceholder();
    });

})(jQuery);
