/*
* Developed by Jordi Vicens Farrús
* http://www.jordivicensfarrus.com/
*	This file is included at the very bottom of the HTML page.
*	jQuery 1.11.1 is also already included.
*/

 $(document).ready(function(){
   //Add a function just for a concret link for open it in a new tab.
   if($('#linkContainer').find("a").length){
       $( "#linkContainer a" ).attr({
          class: "external"
      });
   }
    $(".external").click(function() {
       url = $(this).attr("href");
       window.open(url, '_blank');
       return false;
    });

  //Toggle function
    $(".track").click(function() {
        $(this).find(".task2").toggle("slow");

    });


    //Adding class and removing and open in a new tab.
    $(".track").hover(function() {
    $(this).addClass("elementHovered");
    }, function() {
        $(this).removeClass("elementHovered");
    });

    $(".link").click(function() {
       url = $(this).attr("href");
       window.open(url, '_blank');
       return false;
    });

 });
