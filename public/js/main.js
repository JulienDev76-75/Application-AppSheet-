// ************************************$
window.onload = () => {
    const filtersForm = document.querySelector("#filters");

    // Plusieurs Input pour les sites donc boucle dessus
    document.querySelectorAll("#filters input").forEach(input => {
        input.addEventListener("change", () => {
            // On intercepte les clics ici et on récupère chacune des données du formulaire avec FormData
            const Form = new FormData(filtersForm);

            // On fabrique la "QueryString"
            const Params = new URLSearchParams();

            // Rappel forEach c'est paire clé - valeur
            Form.forEach((value, key) => {
                 Params.append(key, value);
            });

            // On récupère l'URL active
            const Url = new URL(window.location.href);

            // Requête Ajax lancée, méthode différente avec fetch que la méthode dites classique, si une 'Promise' est récupérée, alors on peut lancer un then.
            fetch(Url.pathname + "?" + Params.toString() + "&ajax=1", {
                headers : {
                    "X-Requested-With":"XMLHTTPRequest"
                }
            }).then(response => {
                console.log(response);
            }).catch(e => alert(e));
            
        });
    });
} 

// ************************************$
function toggleFullScreen() {
  if (!document.fullscreenElement) {
      document.documentElement.requestFullscreen();
  } else {
    if (document.exitFullscreen) {
      document.exitFullscreen();
    }
  }
}

//************************************* 
/* Get the documentElement (<html>) to display the page in fullscreen */
var elem = document.documentElement;

/* View in fullscreen */
function openFullscreen() {
  if (elem.requestFullscreen) {
    elem.requestFullscreen();
  } else if (elem.webkitRequestFullscreen) { /* Safari */
    elem.webkitRequestFullscreen();
  } else if (elem.msRequestFullscreen) { /* IE11 */
    elem.msRequestFullscreen();
  }
}

/* Close fullscreen */
function closeFullscreen() {
  if (document.exitFullscreen) {
    document.exitFullscreen();
  } else if (document.webkitExitFullscreen) { /* Safari */
    document.webkitExitFullscreen();
  } else if (document.msExitFullscreen) { /* IE11 */
    document.msExitFullscreen();
  }
}