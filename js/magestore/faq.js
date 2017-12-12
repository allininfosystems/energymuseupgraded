
function openBox(){
    $('box-tags').show();
}

function closeBox(){
    $('box-tags').hide();
}

function select(id){
    $('tag').value += $('sug-' + id).text + ', ';
}
