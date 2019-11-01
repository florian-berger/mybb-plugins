/*
Last change: 2019-11-02
© 2019 Florian Berger
License: https://bitbucket.org/Florian_Berger/mybb-plugins/src/master/Extended%20Useradmininfo/LICENSE
 */

function loadGeoInformation() {
    var lastIpElement = document.getElementById('last_user_ip_address');
    if (!lastIpElement) {
        showErrorHint();
        return;
    }

    var lastIp = lastIpElement.innerHTML;
    if (!lastIp || lastIp.length === 0) {
        showErrorHint();
        return;
    }

    var ipLoadUri = 'https://geolocation-db.com/json/' + lastIp;

    var xhr = new XMLHttpRequest();
    xhr.open('GET', ipLoadUri);

    xhr.onload = function() {
        if (xhr.status === 200) {
            parseResponseJson(xhr.responseText);
        } else {
            showErrorHint();
        }
    };
    xhr.send();
}

function showErrorHint() {
    var loadingArea = document.getElementById('geo_info_loading');
    var errorArea = document.getElementById('geo_info_error');

    if (!errorArea || !loadingArea) {
        console.log('Error loading information and could not display message on ui.');
        return;
    }

    errorArea.classList.remove('hidden');
    loadingArea.classList.add('hidden');
}

function parseResponseJson(jsonValue) {
    var resultObj = JSON.parse(jsonValue);
    if (!resultObj) {
        showErrorHint();
        return;
    }

    setElementValue('geo_country', resultObj['country_name'] + ' (' + resultObj['country_code'] + ')');
    setElementValue('geo_city', resultObj['postal'] + ' ' + resultObj['city']);
    setElementValue('geo_region', resultObj['state']);
    setElementValue('geo_lat', resultObj['latitude']);
    setElementValue('geo_long', resultObj['longitude']);

    var loadingArea = document.getElementById('geo_info_loading');
    var dataArea = document.getElementById('geo_info_data');

    dataArea.classList.remove('hidden');
    loadingArea.classList.add('hidden');
}

function setElementValue(elementId, value) {
    var element = document.getElementById(elementId);
    if (!element) {
        console.log('Element with id ' + elementId + ' not found');
        return;
    }

    element.innerText = value;
}