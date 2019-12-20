 $(document).ready(function(){

 var more_8_project = $(".uchastniki_block:nth-child(8)").nextAll();
 var col_hide_pro = more_8_project.length;
 var col_hide_pro_start = 0;
 var col_hide_pro_stop = 4;
 more_8_project.hide();

 if (col_hide_pro > 0) {$(".more_uch").show();}
 else {$(".mmore_uch").hide();}

 function show_project_4 (){
 	if (col_hide_pro <= 4){
 		more_8_project.slideDown('slow');
 		$(".more_uch").hide();
 	}
 	else{
 		more_8_project.slice(col_hide_pro_start,col_hide_pro_stop).slideDown('slow');
 		col_hide_pro -= 4;
 		col_hide_pro_start = col_hide_pro_stop;
 		col_hide_pro_stop += 4;
 	}
 }

 $(".more_uch").click(function(e) {
 	e.preventDefault();
 	show_project_4();
 });
   var clock;
    
    $(document).ready(function() {
      var clock;

      clock = $('.your-clock').FlipClock({
            language: 'ru',
            clockFace: 'DailyCounter'
        });
            
        clock.setTime(220880);
        clock.setCountdown(true);
        clock.start();

    });
 });