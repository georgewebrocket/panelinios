
<div class="message-dock" style="width: 150px">
    <div id="message-dock-header" class="message-dock-header">
        <span id="message-dock-header-title" style="font-size: 25px; color:#ccc">
            <span class="fa fa-envelope"></span>
        </span> 
        <span id="message-dock-minimize" class="message-dock-minimize invisible" style="color:#666">
            <span class="fa fa-close"></span>
        </span>
        <span title="Refresh" id="message-dock-refresh" class="message-dock-refresh invisible" style="color:#666">
            <span class="fa fa-refresh"></span>
        </span>        
    </div>
    <div style="box-shadow: -3px 3px 5px rgba(0,0,0,0.2);">
        <div id="message-dock-wait" class="align-center invisible padding-20"><img src="img/wait.gif" width="36" height="36" alt="wait"/>
        </div>
        <div id="message-dock-messages" class="message-dock-messages invisible" ></div>
        <div id="message-dock-sendmessage" class="message-dock-sendmessage invisible" >
            
            <div class="message-dock-sendmessage-title">Νέο μήνυμα</div>
            
            <div class="allmessages-sendmessage" style="background-color: rgb(240,240,240)">
                <div class="col-9">
                    <textarea id="message-dock-sendmessage-text" data-receiver="" data-companyid="" data-conversation="0" class="allmessages-sendmessage-textarea"></textarea>
                    <?php
                    
                    if (!isset($receiver)) {
                        $receiver = 0;
                    }
                    
                    $sql = "SELECT id, fullname FROM USERS WHERE active=1 ORDER BY fullname";
                    $m_receiver = new comboBox("m_receiver", $db1, $sql,
                                "id","fullname", $receiver);
                    echo $m_receiver->comboBox_simple();
                    
                    if (!isset($companyid)) {
                        $companyid = 0;
                    }
                    
                    $m_companyid = new textbox("m_companyid","CompanyId",$companyid,"CUSTOMER ID");
                    echo $m_companyid->textboxSimple();
                    ?>
                </div>
                <div class="col-3 align-right">
                    <span id="footer-sendmessage-ok" class="footer-sendmessage-ok button-little">Send</span>&nbsp;
    
                </div>
                <div class="clear"></div>
            </div>
            
        </div>
        <div id="allmessages-link" class="allmessages-link invisible" ><a class="fancybox" href="allmessages.php">View all</a></div>
    </div>
</div>
    

<script type="text/javascript" src="js/functions.js"></script>
<script>

var myInt;
var msgCount = 0;

$(function() {
    
    $(".tooltip").tooltip();
    
    
    $("#message-dock-header-title, .allmessages-link, #message-dock-minimize").click(function() {
        var el1 = $("#message-dock-minimize");
        var el2 = $("#message-dock-messages");
        var el3 = $("#allmessages-link");
        var el4 = $("#message-dock-header");
        var el5 = $("#message-dock-sendmessage");
        var el6 = $("#message-dock-refresh");
        var el7 = $("#message-dock-wait");
        var elTitle = $("#message-dock-header-title");
        
        toggleVisible(el1);
        toggleVisible(el2);
        toggleVisible(el3);
        toggleVisible(el5);
        toggleVisible(el6);
        
        //console.log($(".message-dock").css("width"));
        if (el2.css("display") == "none") {
            $(".message-dock").css("width","150px");
            el7.hide();
        }
        else {            
            refreshMessageList();
            $(".message-dock").css("width","400px");
            //el4.css("background-color", "rgb(100,200,200)");
            
        }
        
    });
    
    function refreshMessageList() {
        var el7 = $("#message-dock-wait");
        el7.show();
        $.post('getAllMessages.php', 
            {personid: 0, unreadonly: 1},
            function(data) {                
                $("#message-dock-messages").html(data);
                bindMessageEvents();
                el7.hide();
            }
        );
    }
    
    /*NEW MESSAGE*/
    $("#footer-sendmessage-ok").click(function() {
        var receiver = $("#m_receiver").val();
        var companyid = $("#m_companyid").val();
        var messageText = $("#message-dock-sendmessage-text").val();
        $.post('sendMessage.php', {
            receiver: receiver, 
            messageText: messageText,
            companyid: companyid,
            conversation: 0},
            function(data) {                
                console.log(data);
                refreshMessageList(); 
                $("#m_receiver").val(0);
                $("#m_companyid").val("");
                $("#message-dock-sendmessage-text").val("");
                msgCount++;
                $("#message-dock-header-title").css("color","red");
                //$("#message-dock-header-title").html(msgCount+' Μηνύματα');
                //<span class="fa fa-envelope"></span>
                //XXXXXXXXXXXXXXX
            }
        );
    });
    
    
    $("#message-dock-refresh").click(function() {
        refreshMessageList();
    });
    
    
    
    getNewConvCount();
    myInt = setInterval(getNewConvCount, 30000);
    
    
});

function getNewConvCount() {
    var el = $("#message-dock-header-title");
    var el2 = $("#message-dock-header");
    $.post('getNewConversationsCount.php', 
        {unreadonly: 1},
        function(data) {                
            //el.html(data+' Μηνύματα');
            if (data!=msgCount) {
                //el2.css("background-color", "#f00");
                $("#message-dock-header-title").css("color","red");
            }
            msgCount = data;
        }
    );
}



</script>