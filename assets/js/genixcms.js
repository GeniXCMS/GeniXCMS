$(document).ready(function(){
    $('.alert').alert();


    $('#myGrid').gridEditor({
        content_types: ['summernote'],
        source_textarea: '#content'
    });

    


    // Get resulting html
    // var html = $('#myGrid').gridEditor('getHtml');
    // console.log(html);
    $("#myGrid").hide();
    $(".ge-mainControls").hide();
    $("#toggleEditor").click(
        function(){
            $("#myGrid").toggle();
            $(".note-editor").toggle();
            $(".ge-mainControls").toggle();
        }
    );
    // $("#toggleEditor").click(function(){
    //     $("#myGrid").toggle();
    //     $(".note-editor").toggle();
    //     $(".ge-mainControls").toggle();
    // });
    // gridLoop();
});
