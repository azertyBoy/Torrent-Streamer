
//Lecture d'un fichier
var playFileForm = $("#play-file-form");
playFileForm.submit(function (){
    var searchInput = $('#play-file-input');
    console.log('Chemin du fichier à lire : ' + searchInput.val());
    playTorrentFile(searchInput.val());

    //Stop the submitting
    return false;
});

playTorrentFile = function (torrentFilePath) {
    console.log(torrentFilePath);
    alert('log');
    $.ajax({
        type: "POST",
        url: Routing.generate('homesoft_torrent_streamer_play'),
        data: {filePath:torrentFilePath}
    })
        .done(function (response) {
            console.log(response);
            alert('Le serveur a répondu.');
        })
        .fail(function(response) {
            console.log(response);
            alert('Une erreur à eu lieu durant le traitement du serveur.');
        });
};

//Recherche d'un film
var searchForm = $("#search-form");
searchForm.submit(function (){
    var searchInput = $('#search-input');
    console.log('Mots-clés à chercher : ' + searchInput.val());
    if(!checkFormData(searchInput.val()))
        return false;
    search(searchInput.val());

    //Stop the submitting
    return false;
});

checkFormData = function (string) {
    if (string.length < 3) {
        alert('La recherche doit faire au moins 3 caractères.');
        return false;
    }
    return true;
};

search = function (string) {
    $.ajax({
        type: "POST",
        url: "http://127.0.0.1:8000/app_dev.php/search",
        data: {search:string}
    })
        .done(function (response) {
            console.log(response);
            document.getElementById("search-result-container").innerHTML = response.torrentsView;
            document.getElementById("result-count").innerHTML = response.result.total;

        })
        .fail(function(response) {
            console.log(response);
            alert('Une erreur à eu lieu durant la recherche.');
        });
};

//Lecture du film trouvé lors de la recherche
playFilm = function (torrentId){
    $.ajax({
        type: "POST",
        url: "http://127.0.0.1:8000/app_dev.php/play-t411-torrent-file/" + torrentId
    })
        .done( function (response) {
            console.log('response dl torrent')
            console.log(response);
            playTorrentFile(response.torrentPath);
        })
        .fail(function (response) {
            console.log(response);
        });
};