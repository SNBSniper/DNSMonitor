$(".slide-panel-toggle").on('click', function(e){
    var $this = $(this).closest('.slide-panel');
    $this.stop(true,true).toggleClass( "slide-panel-hover", 1000, "easeOutExpo" );
    e.preventDefault();
});

$('.slide-panel-filter').keyup(function(){
    var $this = $(this);
   var valThis = $this.val();

    $this.parent().next().children('li').each(function(){
     var text = $(this).text().toLowerCase();
        (text.indexOf(valThis) == 0) ? $(this).show() : $(this).hide();         
   });
});