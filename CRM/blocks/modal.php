<!-- Modal -->
<div id="myModal" class="modal fade" data-refresh="0">
    <div id="myModal-container" class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button id="myModal-close" type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h2 class="modal-title" id="myModalLabel"></h2>

            </div>
            <div class="modal-body">
                <iframe frameborder="0"></iframe>
            </div>
        </div>
    </div>
</div>

<script>

    $(document).ready(function() {
    
        $('a.modalBtn').click(function() {
    
            var src = $(this).attr('data-href');
            var height = $(this).attr('data-height') || 700;
            var width = $(this).attr('data-width') || 0;
            var modalTitle = $(this).attr('data-title');
            
            if (width!=0) {
                $("#myModal-container").css('width', width);
            }
    
            /*$("#myModal iframe").attr({'src':src,
                'height': height,
                'width': '100%'});*/
            $("#myModal iframe").attr({'src':src,
                'width': '100%'});        
            var h = window.innerHeight;    
            h = h - 150;
            /*console.log(height);  */
            h = h < height? h: height;
            $("#myModal iframe").css("height",h+"px");
            $('#myModalLabel').html(modalTitle);
            
            $('#myModal').modal('show'); 
            
        });
        
        $('#myModal-close').on('click', function() { 
            $('#myModalLabel').html("");
            $("#myModal iframe").attr({'src':"blocks/loading.php"});
        });
        
        $('#myModal').on('hidden.bs.modal', function () {
            
            var iRefresh = $(this).attr('data-refresh');
            if (iRefresh=='1') {
                refresh();
                SetDataRefresh('0');
            }
          });
            
    });
    
    
    
    
    function setModals() {
    
        $('a.modalBtn').click(function() {
    
            var src = $(this).attr('data-href');
            var height = $(this).attr('data-height') || 700;
            var width = $(this).attr('data-width') || 0;
            var modalTitle = $(this).attr('data-title');
            
            if (width!=0) {
                $("#myModal-container").css('width', width);
            }
    
            /*$("#myModal iframe").attr({'src':src,
                'height': height,
                'width': '100%'});*/
            $("#myModal iframe").attr({'src':src,
                'width': '100%'});        
            var h = window.innerHeight;    
            h = h - 150;
            /*console.log(height);  */
            h = h < height? h: height;
            $("#myModal iframe").css("height",h+"px");
            $('#myModalLabel').html(modalTitle);
            
            $('#myModal').modal('show'); 
            
        });
        
        $('#myModal-close').on('click', function() { 
            $('#myModalLabel').html("");
            $("#myModal iframe").attr({'src':"blocks/loading.php"});
        });
        
        $('#myModal').on('hidden.bs.modal', function () {
            
            var iRefresh = $(this).attr('data-refresh');
            if (iRefresh=='1') {
                refresh();
                SetDataRefresh('0');
            }
          });
        
        
    }








    function SetModalHeader(val) {
        $('#myModalLabel').html(val);
    }
    
    function SetModalHeight(val) {
        $("#myModal iframe").attr({
            'height': val});
    }
    
    function SetModalWidth(val) {
        $("#myModal-container").css({
            'width': val});
    }
    
    function SetDataRefresh(val) {
        $("#myModal").attr('data-refresh', val);
        //alert('SetDataRefresh='+val);
    }
    
    function CloseModal() {
        $("#myModal").modal('hide');
    }
    
    
    


</script>