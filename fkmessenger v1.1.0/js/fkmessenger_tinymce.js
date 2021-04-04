
$(document).ready(function() {
    
    config = {
        selector: ".fkmessenger-tinymce-textarea",
        theme: "modern",
        language: iso,
        plugins: "colorpicker link image paste pagebreak table contextmenu filemanager table code media textcolor",
        toolbar1: "newdocument,|,code,|,bold,italic,underline,strikethrough,|,alignleft,aligncenter,alignright,alignfull,formatselect,|,blockquote,colorpicker,pasteword,|,bullist,numlist,|,outdent,indent,|,link,unlink,|,cleanup,|,image",
        toolbar2: "fontselect,fontsizeselect",
        font_size_style_values : "8pt, 10pt, 12pt, 14pt, 18pt, 24pt, 36pt",
        external_plugins: {"filemanager": ad + "/filemanager/plugin.min.js"},
        filemanager_title: "File manager" ,
        external_filemanager_path: ad + "/filemanager/",
        statusbar: false,
        relative_urls: false,
        convert_urls: false,
        resize: false,
        height: "300",
        
        menu: {
            edit: {title: 'Edit', items: 'undo redo | cut copy paste | selectall'},
            insert: {title: 'Insert', items: 'image link | pagebreak'},
            view: {title: 'View', items: 'visualaid'},
            format: {title: 'Format', items: 'bold italic underline strikethrough superscript subscript | formats | removeformat'},
            table: {title: 'Table', items: 'inserttable tableprops deletetable | cell row column'},
            tools: {title: 'Tools', items: 'code'}
        }
    }
    
    tinySetup(config);
    
});
