$(document).ready(function() {
   $("#subBtn").popover( {
      html: true,
      placement: 'left',
      content: function() {
         return $( "#subBody" ).html();
      }
   } );
});

function subscribe() {
   $("#subBtn").popover( 'hide' );
   var email = $("#email").val();
   $.get( "subscribe.php", { 'email': email } );
   $("#registerSuccess").show();
}
