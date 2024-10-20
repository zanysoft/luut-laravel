tinymce.PluginManager.requireLangPack('youtube', 'en,nl,de');
tinymce.PluginManager.add('youtube', function (editor, url) {
    function openmanager() {
        if (tinymce.majorVersion > 4) {
            tinymce.activeEditor.windowManager.openUrl({title: 'Choose YouTube Video', url: tinyMCE.baseURL + '/plugins/youtube/youtube.html', filetype: 'video', width: 785, height: 630, inline: 1});
        } else {
            tinymce.activeEditor.windowManager.open({title: 'Choose YouTube Video', file: tinyMCE.baseURL + '/plugins/youtube/youtube.html', filetype: 'video', width: 785, height: 630, inline: 1});
        }
    }

    if (tinymce.majorVersion > 4) {
        editor.ui.registry.addIcon('youtube', '<svg width="24" height="24"><path d="M23.761 7.2c0 0-0.234-1.655-0.956-2.381-0.914-0.956-1.936-0.961-2.405-1.017-3.356-0.244-8.395-0.244-8.395-0.244h-0.009c0 0-5.039 0-8.395 0.244-0.469 0.056-1.491 0.061-2.405 1.017-0.722 0.727-0.952 2.381-0.952 2.381s-0.239 1.941-0.239 3.886v1.819c0 1.941 0.239 3.886 0.239 3.886s0.234 1.655 0.952 2.381c0.914 0.956 2.114 0.923 2.648 1.027 1.922 0.183 8.161 0.239 8.161 0.239s5.044-0.009 8.4-0.248c0.469-0.056 1.491-0.061 2.405-1.017 0.722-0.727 0.956-2.381 0.956-2.381s0.239-1.941 0.239-3.886v-1.819c-0.005-1.941-0.244-3.886-0.244-3.886zM9.52 15.112v-6.745l6.483 3.384-6.483 3.361z"></path></svg>');
        editor.ui.registry.addButton('youtube', {icon: 'youtube', tooltip: 'Insert Youtube video', shortcut: 'Ctrl+Q', onAction: openmanager});
        editor.addShortcut('Ctrl+Q', '', openmanager);
        editor.ui.registry.addMenuItem('youtube', {text: 'Insert Youtube video', shortcut: 'Ctrl+Q', onAction: openmanager, context: 'insert'});
    } else {
        editor.addButton('youtube', {icon: true, image: tinyMCE.baseURL + '/plugins/youtube/icon.png', tooltip: 'Insert Youtube video', shortcut: 'Ctrl+Q', onclick: openmanager});
        editor.addShortcut('Ctrl+Q', '', openmanager);
        editor.addMenuItem('youtube', {icon: 'media', text: 'Insert Youtube video', shortcut: 'Ctrl+Q', onclick: openmanager, context: 'insert'});
    }
});