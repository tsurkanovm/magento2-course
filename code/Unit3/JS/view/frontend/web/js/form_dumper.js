define(['jquery'],
    function ($) {
   return function (config, form) {
       $(form).on('submit', function(){
           var data = JSON.stringify( $(form).serializeArray() );
           console.log( data );
       })
   }
});
