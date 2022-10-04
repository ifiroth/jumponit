
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

    let aCitiesAutocompletion = []

    console.log(prestashop.page.page_name)

    oPostalCode.addEventListener('keyup', function () {
        getCityByPostalCode(this.value)
    })

    oCollapseManualGeolocationAction.addEventListener('click', toggleManualGeolocation)
    oCollapseManualGeolocationAction.addEventListener('mouseover', function() { this.style.cursor = "pointer" } )

    function toggleManualGeolocation () {

        oCollapseManualGeolocation.classList.toggle('d-none')
        oCollapseManualGeolocation.classList.toggle('d-inline')
    }

    let url = document.getElementById('ajaxLink').value

    oSubmitLocation.addEventListener('click', (e) => {

        saveCity()
    })

    $('#joiModalLocation').modal({
        backdrop: false,
    })

    console.log(sessionStorage.joi_postCode)

    if (pagesWithLocation.includes(prestashop.page.page_name) && !sessionStorage.joi_postCode) {

        oModalBackDrop.style.backgroundColor = 'hsla(0, 0%, 0%, .5)'
        oModalBackDrop.style.overflow = 'hidden';

        if (!("geolocation" in navigator)) {

            geolocationError()

        } else {

            updateGeolocationState(oGeolocationState, 'info')
            oGeolocationState.innerText = "Localisation en cours...";

            let options = {
                enableHighAccuracy: true,
                maximumAge: 0,
            }

            navigator.geolocation.getCurrentPosition(geolocationSuccess, geolocationError, options);
        }
    } else {

        $('#joiModalLocation').modal('hide')
    }

    function geolocationSuccess(position) {

        const lat = position.coords.latitude
        const long = position.coords.longitude

        updateGeolocationState(oGeolocationState, 'info')
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
        }

        if (img) {
            element.background
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

            /* TODO: Wait for client validation
            oPostalCode.value = data.postal_code
            oCity.value = nom_comm
            oRegion.value = $("<textarea/>").html(data.nom_reg).text()
            oDept.value = $("<textarea/>").html(data.nom_dept).text()

            oPostalCode.classList.add('is-valid')
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

        updateGeolocationState(oGeolocationState, 'success')
        oGeolocationState.innerText = "Position définie";

        setTimeout(() => {
            $('#joiModalLocation').modal('hide')
        }, 2000)

    }
})
