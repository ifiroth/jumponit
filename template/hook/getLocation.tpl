<div class="modal fade" tabindex="-1" role="dialog" id="joiModalLocation">
    <input type="hidden" id="ajaxLink" value="{$link->getModuleLink('jumponit', 'city')}">

    <div class="modal-dialog cascading-modal" role="document">
        <div style="
                background-image: url('{$urls.shop_domain_url}/modules/jumponit/assets/img/geolocalisation.jpg');
                background-size: cover;
                background-position: center center;
                width: 300px;
                height: 300px;
                margin: 0 auto;
                transform: translateY(40%);
                border-radius: 50%;
                z-index: 2000;
                position: relative;
                "></div>
        <div class="modal-content border-0 pt-5 color-bg-joi-light-brown" style="border-radius: 50px;">
            <div class="modal-body">
                <p class="alert mx-5">
                <h2 class="modal-title text-center my-2 font-joi-title display-2">Hey !</h2>
                    <span class="font-weight-bold">Dans quelle ville souhaite-tu faire ton shopping ?</span><br>
                    Indique le nom de ta ville ci-dessous afin que nous
                    puissons affiner tes recherches et te proposer les
                    boutiques proches de chez toi !
                </p>
                <form class="form-inline">
                    <div class="input-group m-auto w-100 mx-5 rounded-pill shadow-sm">
                        <input class="form-control" list="postalCodeList" id="postalCodeDataList" placeholder="Code Postal">
                        <button type="button" class="btn btn-primary color-bg-joi-brown" id="submitLocation">Définir</button>
                    </div>
                    <datalist id="postalCodeList">
                        <option value="San Francisco">
                        <option value="New York">
                        <option value="Seattle">
                        <option value="Los Angeles">
                        <option value="Chicago">
                    </datalist>
                </form>
                <div id="modal-geolocation-state" class="alert mt-3"></div>
                <p class="my-3">
                    <a id="collapseManualGeoLocationAction" class="btn btn-outline-secondary">
                        Faire une recherche manuelle
                    </a>
                </p>
                <div class="d-none" id="collapseManualGeoLocation">
                    <div class="autocompletionManualLocation">
                        <input class="form-control my-2" list="regionList" id="regionDataList" placeholder="Région">
                        <datalist id="regionList">
                            <option value="San Francisco">
                            <option value="New York">
                            <option value="Seattle">
                            <option value="Los Angeles">
                            <option value="Chicago">
                        </datalist>
                        <input class="form-control my-2" list="deptList" id="deptDataList" placeholder="Département">
                        <datalist id="deptList">
                            <option value="San Francisco">
                            <option value="New York">
                            <option value="Seattle">
                            <option value="Los Angeles">
                            <option value="Chicago">
                        </datalist>
                        <input class="form-control my-2" list="cityList" id="cityDataList" placeholder="Ville">
                        <datalist id="cityList">
                            <option value="San Francisco">
                            <option value="New York">
                            <option value="Seattle">
                            <option value="Los Angeles">
                            <option value="Chicago">
                        </datalist>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>