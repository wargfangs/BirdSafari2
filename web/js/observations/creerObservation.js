$(function(){
   $('#observation_form_image_src').removeAttr('required');
   $('#observation_form_image_alt').removeAttr('required');

   var today = new Date();
   //Datetime Picker:
   $('#observation_form_date').datetimepicker({
       maxDate: today.toDateString()
   });
});