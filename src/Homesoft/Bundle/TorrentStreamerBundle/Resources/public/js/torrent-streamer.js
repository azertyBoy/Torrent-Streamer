
//Uploading d'un fichier à lire
var uploadTorrentFile;
$('#uploadTorrentFile_torrentFile').on('change', function (event) {
    uploadTorrentFile = event.target.files;
    console.log(uploadTorrentFile[0]);
});

var uploadFileForm = $('#upload-file-form');
uploadFileForm.submit(function (event){
    event.stopPropagation();
    event.preventDefault();

    var data = new FormData();
    data.append('torrentFile', uploadTorrentFile[0]);
    data.append('torrentFileName', uploadTorrentFile[0].name);
    data.append('torrentFileType', uploadTorrentFile[0].type);

    $.ajax({
        type: "POST",
        url: Routing.generate('homesoft_torrent_streamer_play'),
        contentType: false,
        processData: false,
        data: data
    })
        .done(function (response) {
            window.open(response.url,'_blank');
        })
        .fail(function (reponse) {
            console.log(reponse);
            alert('Une erreur à eu lieu durant l\'upload du fichier.');
        });

    return false;
});

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
        url: Routing.generate('homesoft_torrent_streamer_search'),
        data: {search:string}
    })
        .done(function (response) {
            document.getElementById("search-result-container").innerHTML = response.torrentsView;
            document.getElementById("result-count").innerHTML = response.resultCount;
        })
        .fail(function(response) {
            alert('Une erreur à eu lieu durant la recherche.');
        });
};

//Lecture du film trouvé lors de la recherche
playT411Film = function (torrentId){
    $.ajax({
        type: "POST",
        url: Routing.generate('homesoft_torrent_streamer_play_t411_torrent', { torrentId: torrentId})
    })
        .done( function (response) {
            window.open(response.url,'_blank');
        })
        .fail(function (response) {
            alert('Une erreur à eu lieu lors du lancement du streaming à partir d\'un torrent t411.');
        });
};


playCPasBienFilm = function (cpasbienUrl){
    $.ajax({
        type: "POST",
        url: Routing.generate('homesoft_torrent_streamer_play_cpasbien_torrent'),
        data: {'cpasbienUrl': cpasbienUrl}
    })
        .done( function (response) {
            window.open(response.url,'_blank');
        })
        .fail(function (response) {
            alert('Une erreur à eu lieu lors du lancement du streaming à partir d\'un torrent CPasBien.');
        });
};

