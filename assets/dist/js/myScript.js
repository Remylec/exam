function afficher(page, id) {
    let getDiv, idBtn;
    idBtn = "btn" + id;
    getDiv = document.getElementById(idBtn);

    getDiv.innerHTML = '<p class="h6">Confirmation</p>' +
    '<div class="float-left mr-3"><form action="index.php?action=delete&page='+page+'&id='+id+'" method="post">'+
    '<input type="hidden" name="confirm" value="1">'+
    '<button type="submit" class="btn btn-info">OUI</button>'+
    '</form></div>'+
    '<button type="button" class="btn btn-info">'+
    '<a href="index.php?page='+page+'" class="text-white"> NON </a>'+
    '</button>';
   
}