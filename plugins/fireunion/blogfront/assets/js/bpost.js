editor = CKEDITOR.replace( 'content' );

$( "#draft_save" ).click(function() {
  editor.updateElement();
});

$( "#publish_save" ).click(function() {
  editor.updateElement();
});