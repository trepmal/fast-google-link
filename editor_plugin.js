// http://tinymce.moxiecode.com/wiki.php/API3:class.tinymce.Plugin

(function() {

    tinymce.create('tinymce.plugins.FGL', {
        /**
         * Initializes the plugin, this will be executed after the plugin has been created.
         * This call is done before the editor instance has finished its initialization so use the onInit event
         * of the editor instance to intercept that event.
         *
         * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
         * @param {string} url Absolute URL to where the plugin is located.
         */
        init : function(ed, url) {

            //this command will be executed when the button in the toolbar is clicked
            ed.addCommand('mceFGL', function() {

                selection = tinyMCE.activeEditor.selection.getContent();

                jQuery.get( ajaxurl, {
                    action: 'fgl_fetch_link',
                    selection: selection
                }, function( response ) {

                    if ( response.success ) {
                        tinyMCE.activeEditor.selection.setContent('<a href="' + response.data + '">' + selection + '</a>');
                    } else {
                        alert( "No result found" );
                    }

                }, 'json' );

            });

            ed.addButton('FGL', {
                title : 'Fast Google Link',
                cmd : 'mceFGL',
                image : url + '/button.png'
            });

        },

    });

    // Register plugin
    tinymce.PluginManager.add('FGL', tinymce.plugins.FGL);

})();