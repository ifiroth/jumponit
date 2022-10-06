
/*
 On recherche dans les régions, puis dans les départements et enfin dans les communes/all
 pour définir la zone de livraison du client

*/

$(document).ready(function () {

    pagesWithLocation = [ 'index', 'product', 'category', 'search' ]

    let oSubmitLocation = document.getElementById('submitLocation')
    let oPostalCode = document.getElementById('postalCodeDataList')
    let oCity = document.getElementById('cityDataList')
    let oRegion = document.getElementById('regionDataList')
    let oDept = document.getElementById('deptDataList')
    let oGeolocationState = document.getElementById('modal-geolocation-state')
    let oCollapseManualGeolocationAction = document.getElementById('collapseManualGeoLocationAction')
    let oCollapseManualGeolocation = document.getElementById('collapseManualGeoLocation');
    let oAutocompletionManualLocation = document.getElementById('autocompletionManualLocation')
    let oModalBackDrop = document.getElementById('joiModalLocation')
    let postalCodeCookie = document.getElementById('postalCodeCookie').value
    let aCitiesAutocompletion = []
    let oChangeCity = document.getElementById('joiChangeCity')
    let oForgetCity = document.getElementById('joiForgetCity')

    oPostalCode.addEventListener('keyup', function () {
        getCityByPostalCode(this.value)
    })

    oCollapseManualGeolocationAction.addEventListener('click', toggleManualGeolocation)
    oCollapseManualGeolocationAction.addEventListener('mouseover', function() { this.style.cursor = "pointer" } )

    oChangeCity.addEventListener('click', function () {
        $('#joiModalLocation').modal('show')
    })

    oForgetCity.addEventListener('click', function () {
        window.alert('Destruction de la ville -> A coder. Note à moi-même, cette fonction pourrait plaire a Poutine!')
        /*
        $.ajax({
            type: "GET",
            url: url,
            data: {
                postalCode: postalCode,
                action: 'defineCity',
                ajax: true
            },
            success : (data) => defineCity(JSON.parse(data)),
            error : () => geolocationError()
        })
        */
    })

    function toggleManualGeolocation () {

        oCollapseManualGeolocation.classList.toggle('d-none')
        oCollapseManualGeolocation.classList.toggle('d-inline')
    }



    let url = document.getElementById('ajaxLink').value

    oSubmitLocation.addEventListener('click', (e) => {

        saveCity()
    })

    console.log(postalCodeCookie)

    if (pagesWithLocation.includes(prestashop.page.page_name) && parseInt(postalCodeCookie) === 0) {

        $('#joiModalLocation').modal('show')

        oModalBackDrop.style.backgroundColor = 'hsla(0, 0%, 0%, .5)'
        oModalBackDrop.style.overflow = 'hidden';

        if (!("geolocation" in navigator)) {

            geolocationError()

        } else {

            updateGeolocationState(oGeolocationState, 'info', 'locating', 'locating')
            oGeolocationState.innerText = "Localisation en cours...";

            let options = {
                enableHighAccuracy: true,
                maximumAge: 0,
            }

            navigator.geolocation.getCurrentPosition(geolocationSuccess, geolocationError, options);
        }
    }

    function geolocationSuccess(position) {

        const lat = position.coords.latitude
        const long = position.coords.longitude

        updateGeolocationState(oGeolocationState, 'info', 'locating')
        oGeolocationState.innerText = "Localisation en cours...";

        getCityByGPS(position.coords)
    }

    function updateGeolocationState(element, state, img = null) {

        let states = ['info', 'warning', 'success', 'danger']
        const index =  states.indexOf(state)

        if (index > -1) {
            formatedStates = states.map(s => 'alert-'+ s)
            element.classList.add(formatedStates[index])
            formatedStates.splice(index, 1)
            element.classList.remove(...formatedStates)

            element.style.backgroundImage = "none"
            element.style.paddingLeft = "1.25rem";
        }

        if (img) {
            // TODO: replace gift with a free one
            element.style.backgroundImage = "url('/modules/jumponit/assets/img/locating.gif')"
            element.style.backgroundSize = "24px 37.5px"
            element.style.backgroundRepeat = "no-repeat"
            element.style.backgroundPositionX = "left"
            element.style.backgroundPositionY = "center"
            element.style.paddingLeft = "1.5rem";
        }
    }

    function geolocationError() {

        updateGeolocationState(oGeolocationState, 'warning')
        oGeolocationState.innerText = "Impossible de récupérer vos données de localisation."
    }

    function disableGeoSubmit() {

        oSubmitLocation.classList.add('disabled')
        oPostalCode.classList.remove('is-valid')
    }

    function getCityByPostalCode(postalCode) {

        disableGeoSubmit()
        console.log('Recherche par code postal')

        oPostalCode.value = postalCode.replace(/\D/g, '')

        if (postalCode.length === 5) {

            $.ajax({
                type: "GET",
                url: url,
                data: {
                    postalCode: postalCode,
                    action: 'defineCity',
                    ajax: true
                },
                success : (data) => defineCity(JSON.parse(data)),
                error : () => geolocationError()
            })
        }
    }

    function getCityByGPS(coords) {

        disableGeoSubmit()
        console.log('Recherche par coordonée GPS')

        updateGeolocationState(oGeolocationState, 'info')
        oGeolocationState.innerText = "Récupération du nom de la commune...";

        $.ajax({
            type: "GET",
            url: url,
            data: {
                lat: coords.latitude,
                long: coords.longitude,
                action: 'defineCity',
                ajax: true
            },
            success : (data) => defineCity(JSON.parse(data)),
            error : () => geolocationError()
        })
    }

    function defineCity(data) {

        if (!data) {

            geolocationError()

        } else {

            let nom_comm = $("<textarea/>").html(data.nom_comm).text()

            updateGeolocationState(oGeolocationState, 'success')
            oGeolocationState.innerText = 'Localisé à '+ nom_comm

            oSubmitLocation.classList.remove('disabled')
            oPostalCode.value = data.postal_code
            oPostalCode.classList.add('is-valid')

            /* TODO: Wait for client validation
            oCity.value = nom_comm
            oRegion.value = $("<textarea/>").html(data.nom_reg).text()
            oDept.value = $("<textarea/>").html(data.nom_dept).text()

            oCity.classList.add('is-valid')
            oRegion.classList.add('is-valid')
            oDept.classList.add('is-valid')
            */
        }
    }

    function saveCity(postalCode) {

        // TODO : Implement save method
        console.log('Sauvegarde')

        disableGeoSubmit()

        updateGeolocationState(oGeolocationState, 'info')
        oGeolocationState.innerText = "Définition de la ville...";

        $.ajax({
            type: "GET",
            url: url,
            data: {
                postalCode: postalCode,
                action: 'saveCity',
                ajax: true
            },
            success : (data) => isSavedCity(JSON.parse(data)),
            error : () => geolocationError()
        })
    }

    function isSavedCity(data) {

        if (data) {

            updateGeolocationState(oGeolocationState, 'success')
            oGeolocationState.innerText = "Position définie";

            setTimeout(() => {
                $('#joiModalLocation').modal('hide')
            }, 2000)

        } else {

            // TODO : update mail with the good one
            updateGeolocationState(oGeolocationState, 'danger')
            oGeolocationState.innerHTML = "Erreur lors de l'enregistrement du code postal. Veuillez <a href='mailto:info@jumponit-test.com'>contacter l'administrateur</a> ou vérifier la bonne saisie du code postal.";
        }
    }
})
