function ExpandTree(el,getsubgrid) {    
    //event.preventDefault();
    var currentId = $(el).attr('id').substr(1);    
    if ($('#subgrid'+currentId).css('display')=='none') {
        $.ajax({url: getsubgrid+"?id="+currentId,success:function(result){
            $('#subgrid'+currentId).html(result);                
            }});            
        $('#subgrid'+currentId).show();
        $(el).html("-");
        $(el).removeClass("inline-button tree").addClass("inline-button-selected");
        $('tr#'+currentId+" td").css("font-weight","bold");
    }
    else {
        $('#subgrid'+currentId).hide();
        $(el).html("+");
        $(el).removeClass("inline-button-selected").addClass("inline-button tree");
        $('tr#'+currentId+" td").css("font-weight","normal");
        //$(el).css("background-color", "rgb(200,200,200)")
    }
    
} 

function UnlockCompany(id) {
    //alert(id);

    var myURL = 'unlockcompany.php?id=' + id;
        $.ajax({
           url: myURL,
           success: function(data) {
                window.parent.location.reload(false);                      
           }
           });
    //$.ajax({url: 'unlockcompany.php?id=' + id}});
    //window.parent.location.reload(false);
}


function bindMessageEvents() {
    $(".allmessages-message-reply").click(function() {
        $(this).parent().parent().find(".allmessages-sendmessage").show();
        $(this).parent().parent().find(".allmessages-conversation-controls").hide();        
    });
    
    $(".allmessages-sendmessage-cancel").click(function() {
        $(this).parent().parent().parent().find(".allmessages-sendmessage").hide();
        $(this).parent().parent().parent().find(".allmessages-conversation-controls").show(); 
    });
    
    $(".allmessages-sendmessage-ok").click(function() {
        var el = $(this).parent().parent().find(".allmessages-sendmessage-textarea");
        var el2 = $(this).parent().parent().parent().parent();
        var receiver = el.data("receiver");
        var messageText = el.val();
        var companyid = el.data("companyid");
        var conversation = el.data("conversation");
        $.post('sendMessage.php', {
            receiver: receiver, 
            messageText: messageText,
            companyid: companyid,
            conversation: conversation},
            function(data) {                
                $.post('getAllMessages.php', 
                    {personid: 0, unreadonly: 1},
                    function(data) {                
                        el2.html(data);
                        bindMessageEvents();                    
                    }
                );                    
            }
        );
    });
    
    $(".allmessages-message-ok").click(function() {
        var conversation = $(this).data("conversation");
        var el = $(this).parent().parent();
        $.post('messageOk.php', {id: conversation},
        function(data) {
            el.hide();
            msgCount--;
            if (msgCount==0) {
                $("#message-dock-header-title").css("color","#ccc");
            }
            //$("#message-dock-header-title").html(msgCount+' Μηνύματα');
        });
    });
    
    $(".allmessages-message-delete").click(function() {
        var message = $(this).data("id");
        var el = $(this).parent().parent();
        $.post('messageDel.php', {id: message},
        function(data) {
            el.hide();
        });
    });
    
}


$(function() {

    $(".datepicker").focus(function() {
        $(this).attr( 'autocomplete', 'off' );
    });

});