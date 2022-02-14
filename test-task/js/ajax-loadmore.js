jQuery(function($){

    let btn = $( '#loadmore a' )
    let paged = btn.data( 'paged' )
    let maxPages = btn.data( 'max_pages' );

    btn.click( function( event ) {

        event.preventDefault();
        console.log(ajax_data);
        $.ajax({
            type : 'POST',
            url : ajax_data.ajax_url,
            data : {
                paged : paged,
                sort_by : ajax_data.sort_by,
                action : 'loadmore'
            },
            error: function (error){
                alert(error);
            },
            success : function( data ){
                paged++;
                btn.parent().before( data );
                btn.text( 'Load More' );
                if( paged == maxPages ) {
                    btn.remove();
                }

            }

        });

    } );
});