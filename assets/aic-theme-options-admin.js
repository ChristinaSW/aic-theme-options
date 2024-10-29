var $j = jQuery.noConflict();

$j(function (){
    repeaterCollapseButton();
    repeaterCollapse();
});

//================== Functions ========================

function repeaterCollapseButton(){
    $j('.acf-field-repeater').each(function(){
        var fieldKey = $j(this).attr('data-key');
        $j(this).find('.acf-label:first').each(function(){
            var html = `
                <div class="button-wrap">
                    <button class="collapse_button">Collapse All</button>
                </div>
            `;
            $j(this).addClass('repeater-label');
            if( $j(this).find('label').length === 0 ){
                var label = `
                    <label for="`+fieldKey+`"></label>
                `;
                $j(this).append(label);
            }
            $j(this).append(html);
        });
    });
}

function repeaterCollapse(){
    $j('.collapse_button').on('click', function(e){
        e.preventDefault();
        $j('.acf-repeater').each(function(){
            var allCollapsed = false;
            $j(this).find('.acf-row').each(function(){
                if( $j(this).hasClass('-collapsed') ){
                    $j(this).removeClass('-collapsed');
                    $j(this).find('.-collapsed-target').css('min-height', '');
                    allCollapsed = false;
                }else{
                    $j(this).addClass('-collapsed');
                    $j(this).find('.-collapsed-target').css('min-height', '92px');
                    allCollapsed = true;
                }    
            });
            if(allCollapsed){
                $j(e.currentTarget).text('Expand All');
            }else{
                $j(e.currentTarget).text('Collapse All');
            }
        });
    });
}
