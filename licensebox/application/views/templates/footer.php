<div class="content has-text-centered">
    <p>
     Copyright <?php echo date('Y'); ?> <a style="color: inherit;" href="https://www.techdynamics.org">CodeMonks</a>, All rights reserved.
   </p>
</div>
 <script src="<?php echo base_url(); ?>assets/js/jquery.min.js"></script>
 <script src="<?php echo base_url(); ?>assets/js/tags.bulma.js"></script>
 <script src="<?php echo base_url(); ?>assets/vendor/DataTables-1.10.18/js/datatables.min.js"></script>
 <script src="<?php echo base_url(); ?>assets/vendor/DataTables-1.10.18/js/dataTables.responsive.min.js"></script>
 <script src="<?php echo base_url(); ?>assets/js/dataTables.bulma.min.js"></script>
 <script src="<?php echo base_url(); ?>assets/vendor/JQueryDateTimePicker/jquery.datetimepicker.js"></script>
 <script src="<?php echo base_url(); ?>assets/vendor/Dropify/js/dropify.min.js"></script>
 <?php if(($this->router->fetch_class()=='pages')&&($this->router->fetch_method()=='view')): ?>
 <script src="<?php echo base_url(); ?>assets/vendor/CanvasJS/canvasjs.min.js"></script>
 <script>
window.onload = function () {
var chart = new CanvasJS.Chart("chartContainer", {
  theme: "light2",
  animationEnabled: true,
  toolTip: {
    cornerRadius: 5,
    borderColor: "#209cee",
    fontColor: "#4a4a4a",
    borderThickness: 2
  },
  data: [{
    type: "pie",
    startAngle: -90,
    radius: 100,
    showInLegend: true,
    toolTipContent: "{name}: <strong>{y}</strong>",
    indexLabel: "{name}: {y}",
    dataPoints: [
      <?php $lic_res = $this->licenses_model->get_licenses_count_for_chart();
      if(!empty($lic_res['valid'])||!empty($lic_res['invalid'])||!empty($lic_res['blocked'])){ ?>
        { y: <?php echo $lic_res['valid']; ?>, name: "Valid", exploded: true },
        { y: <?php echo $lic_res['invalid']; ?>, name: "Invalid" },
        { y: <?php echo $lic_res['blocked']; ?>, name: "Blocked" }
      <?php }
      ?>
    ]
  }]
});
showDefaultText(chart, "Not enough data for creating chart!");
setTimeout(function() {
       $("#chart-loading").fadeOut()
}, 0);
chart.render();
var chart = new CanvasJS.Chart("chartContainer2", {
  theme: "light2",
  animationEnabled: true,
  axisX: {
    valueFormatString: "DD MMM YYYY",
    lineThickness:0.7,
    tickThickness: 0.7,
  },
    axisY: {
    lineThickness:0.7,
    gridThickness: 0.7,
    tickThickness: 0.7,
    includeZero: false
  },
  toolTip: {
    shared: true,
    cornerRadius: 5,
    borderColor: "#209cee",
    fontColor: "#4a4a4a",
    borderThickness: 2
  },
  legend: {
    cursor: "pointer",
    verticalAlign: "bottom",
    horizontalAlign: "center",
    itemclick: toogleDataSeries
  },
      <?php 
      $chartdata='';
      $chartdata1='';
      $chartdata2='';
      $day='';
      $month='';
      $year='';
      $tday = ($day == "" ) ? "01" : $day;
      $tmonth = ($month == "" ) ? date("m") : $month;
      $tyear = ($year == "" ) ? date("Y") : $year;
     
      $month_sd = date("Y-m-d", strtotime($tmonth.'/'.$tday.'/'.$tyear));
      $month_ed = date("Y-m-d", strtotime('-1 second',strtotime('+1 month',strtotime($tmonth.'/'.$tday.'/'.$tyear))));
      $countf=0;
      while (strtotime($month_sd) <= strtotime($month_ed)) {
        $licenses_res = $this->licenses_model->get_licenses_based_on_date($month_sd,$month_sd." 23:59:59"); 
        $activations_res = $this->installations_model->get_activations_based_on_date($month_sd,$month_sd." 23:59:59"); 
        $updates_res = $this->downloads_model->get_update_downloads_based_on_date($month_sd,$month_sd." 23:59:59");

        $countf+=$licenses_res+$activations_res+$updates_res;

        $time = strtotime($month_sd);
        $dday=date('d', $time);
        $dmonth=(date('m', $time)-1);
        $dyear=date('Y', $time);

        $chartdata.="
        { x: new Date(".$dyear.",".$dmonth.",".$dday."), y: ".$licenses_res." },";
        $chartdata1.="
        { x: new Date(".$dyear.",".$dmonth.",".$dday."), y: ".$activations_res." },";
        $chartdata2.="
        { x: new Date(".$dyear.",".$dmonth.",".$dday."), y: ".$updates_res." },";
        $month_sd = date ("Y-m-d", strtotime("+1 day", strtotime($month_sd)));
      }
    ?>
  data: [{
    type:"line",
    axisYType: "primary",
    name: "License Added/Modified",
    showInLegend: true,
    dataPoints: [  
      <?php if(!empty($countf)){echo $chartdata;}  ?>
    ]
  },
  {
    type: "line",
    lineDashType: "shortDot",
    axisYType: "primary",
    name: "Valid Activations",
    showInLegend: true,
    color: "#A1BF63",
    dataPoints: [
     <?php if(!empty($countf)){echo $chartdata1;} ?>
    ]
  },
  {
    type: "line",
    lineDashType: "shortDashDot",
    axisYType: "primary",
    name: "Updates Downloaded",
    showInLegend: true,
    color: "#51CDA0",
    dataPoints: [
      <?php if(!empty($countf)){echo $chartdata2;} ?>
    ]
  }]
});
showDefaultText(chart, "Not enough data for creating chart!");
setTimeout(function() {
       $("#chart-loading2").fadeOut()
}, 0);
chart.render();
if(chart.axisY[0].get("interval") < 1){
      chart.axisY[0].set("interval", 1);  
}
function showDefaultText(chart, text){
    
   var isEmpty = !(chart.options.data[0].dataPoints && chart.options.data[0].dataPoints.length > 0);
  
   if(!chart.options.subtitles)
    (chart.options.subtitles = []);
   
   if(isEmpty)
    chart.options.subtitles.push({
     text : text,
     verticalAlign : 'center',
   });
   else
    (chart.options.subtitles = []);
 }
function toogleDataSeries(e){
  if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
    e.dataSeries.visible = false;
  } else{
    e.dataSeries.visible = true;
  }
  chart.render();
}
}
</script>
 <?php endif; ?>
 <?php if(($this->router->fetch_class()=='licenses')&&($this->router->fetch_method()=='index')): ?>
 <script>
    $(document).ready(function () {
        $('#licenses_table').DataTable({
            "processing": true,
            "responsive": true,
            "serverSide": true,
            "aLengthMenu": [[10, 25, 50, 100, 500], [10, 25, 50, 100, 500]],
            "iDisplayLength": 25,
            "order": [[ 3, "desc" ]],
            "ajax":{
              "url": "<?php echo base_url('licenses/get_licenses') ?>",
              "dataType": "json",
              "type": "POST",
              "data":{  '<?php echo $this->security->get_csrf_token_name(); ?>' : '<?php echo $this->security->get_csrf_hash(); ?>' }
            },
            error: function(){ 
              $("#licenses_table").html("");
              $("#licenses_table_processing").hide();
            },
            "columnDefs": [
            { "orderable": false, "width": 100, "targets": [7] }
            ],
            language: {
            "processing": "<i class='fas fa-sync-alt  fa-spin'></i> Loading. Please wait..."
            },
            "deferRender": true   

      });
        $('#licenses_table').wrap('<div class="dataTables_scroll" />');
        $.fn.dataTable.ext.errMode = 'throw';
    });
</script>
 <?php endif; ?>
 <?php if(($this->router->fetch_class()=='activations')&&($this->router->fetch_method()=='index')): ?>
<script>
    $(document).ready(function () {
        $('#activations_table').DataTable({
            "processing": true,
            "responsive": true,
            "serverSide": true,
            "aLengthMenu": [[10, 25, 50, 100, 500], [10, 25, 50, 100, 500]],
            "iDisplayLength": 25,
            "order": [[ 5, "desc" ]],
            "ajax":{
              "url": "<?php echo base_url('activations/get_activations') ?>",
              "dataType": "json",
              "type": "POST",
              "data":{  '<?php echo $this->security->get_csrf_token_name(); ?>' : '<?php echo $this->security->get_csrf_hash(); ?>' }
            },
            error: function(){ 
              $("#activations_table").html("");
              $("#activations_table_processing").hide();
            },
            "columnDefs": [
            { "orderable": false, "targets": [7] }
            ],
            language: {
            "processing": "<i class='fas fa-sync-alt  fa-spin'></i> Loading. Please wait..."
            },
            "deferRender": true   

      });
        $('#activations_table').wrap('<div class="dataTables_scroll" />');
        $.fn.dataTable.ext.errMode = 'throw';
    });
</script>
 <?php endif; ?>
  <?php if(($this->router->fetch_class()=='users')&&($this->router->fetch_method()=='activities')): ?>
<script>
    $(document).ready(function () {
        $('#activities_table').DataTable({
            "processing": true,
            "responsive": true,
            "serverSide": true,
            "aLengthMenu": [[10, 25, 50, 100, 500], [10, 25, 50, 100, 500]],
            "iDisplayLength": 25,
            "order": [[ 1, "desc" ]],
            "ajax":{
              "url": "<?php echo base_url('users/get_activities') ?>",
              "dataType": "json",
              "type": "POST",
              "data":{  '<?php echo $this->security->get_csrf_token_name(); ?>' : '<?php echo $this->security->get_csrf_hash(); ?>' }
            },columnDefs: [
            { width: 250, targets: 1 }
            ],
            error: function(){ 
              $("#activities_table").html("");
              $("#activities_table_processing").hide();
            },
            language: {
            "processing": "<i class='fas fa-sync-alt  fa-spin'></i> Loading. Please wait..."
            },
            "deferRender": true   

      });
        $('#activities_table').wrap('<div class="dataTables_scroll" />');
        $.fn.dataTable.ext.errMode = 'throw';
    });
</script>
 <?php endif; ?>
 <?php if(($this->router->fetch_class()=='downloads')&&($this->router->fetch_method()=='index')): ?>
<script>
    $(document).ready(function () {
        $('#downloads_table').DataTable({
            "processing": true,
            "responsive": true,
            "serverSide": true,
            "aLengthMenu": [[10, 25, 50, 100, 500], [10, 25, 50, 100, 500]],
            "iDisplayLength": 25,
            "order": [[ 1, "desc" ]],
            "ajax":{
              "url": "<?php echo base_url('downloads/get_update_downloads') ?>",
              "dataType": "json",
              "type": "POST",
              "data":{  '<?php echo $this->security->get_csrf_token_name(); ?>' : '<?php echo $this->security->get_csrf_hash(); ?>' }
            },
            error: function(){ 
              $("#downloads_table").html("");
              $("#downloads_table_processing").hide();
            },
            language: {
            "processing": "<i class='fas fa-sync-alt  fa-spin'></i> Loading. Please wait..."
            },
            "deferRender": true   

      });
        $('#downloads_table').wrap('<div class="dataTables_scroll" />');
        $.fn.dataTable.ext.errMode = 'throw';
    });
</script>
 <?php endif; ?>
<script>
  bulmaTagsinput.attach();
</script>
 <script>
  document.addEventListener('DOMContentLoaded', function () {
  var $navbarBurgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);
  if ($navbarBurgers.length > 0) {
    $navbarBurgers.forEach(function ($el) {
      $el.addEventListener('click', function () {
        var target = $el.dataset.target;
        var $target = document.getElementById(target);
        $el.classList.toggle('is-active');
        $target.classList.toggle('is-active');
      });
    });
  }
});
  $(document).on('click', '.notification > button.delete', function() {
    $(this).parent().addClass('is-hidden');
    return false;
  });
  $(document).ready( function () {
    $('.date-time-picker').datetimepicker({
      format:'Y-m-d H:i:s'
    });
    $('.ts').DataTable({
      responsive: true,
      "order": []
    });
    $('.ts').wrap('<div class="dataTables_scroll" />');
    $('.nots').wrap('<div class="dataTables_scroll" />');
    $('.dropify').dropify();
  } );
</script>
<script type="text/javascript">
  function ConfirmDelete(name, ext = '')
  { 
    var x = confirm("Are you sure you want to delete this "+ name + "?"+ ext);
    if (x)
    { 
      return true;}
      else
        return false;
    }
</script>
<script>
        $(function(){                
            function mobile_expandable_menu() {
                if( $(window).width() < 768 ) {
                    $('.navbar-link').next('.navbar-dropdown').hide();
                    $('.navbar-link').on('click', function(){
                        $(this).next('.navbar-dropdown').slideToggle();
                    });
                } else {
                    $('.navbar-link').next('.navbar-dropdown').css('display','');
                    $('.navbar-link').unbind('click');
                }
            }
            var screen_resize_timout;
            $(window).on("resize", function (e) {
                clearTimeout(screen_resize_timout);
                screen_resize_timout = setTimeout(mobile_expandable_menu, 500);
            });
            mobile_expandable_menu();
        });
</script>
</body>
</html>