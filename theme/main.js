(function($){
  $(".bootstrap-iso .dropdown").on("hide.bs.dropdown", function(event){
      event.preventDefault();
      $(this).children("div").removeClass("show");
      $(this).children("button").attr("aria-expanded", false);
      $(this).style("display", "block");
  });
  $('#dropdownMenu').on('hidden.bs.dropdown', function () {
    $("#dropdownMenu").show();
  });
})(jQuery)

function displayBar(){
  if (jq('#blueBarStatus').val() == 0) {
    console.log("trying");
    var div = '<div id="blueBar" title="Z niebieską" style="width:8px;height:100%;position:absolute;top:0;left:0;background-color:dodgerblue;z-index:8500;"></div>';
    jq("#moduleBody").append(div);
  }
  else {
    jq("#blueBar").remove();
    console.log("remove");
  }
}