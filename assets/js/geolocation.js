
/*
 On recherche dans les régions, puis dans les départements et enfin dans les communes/all
 pour définir la zone de livraison du client

*/

if ("geolocation" in navigator){

    //navigator.geolocation.getCurrentPosition(success, error);

    // Aix en provence
    position = {
        coords: {
            latitude: 43.5312,
            longitude: 5.4554
        }
    }

	console.log("Locating")

    success(position)

    function success(position) {

        const lat = position.coords.latitude
        const long = position.coords.longitude


        console.log('Latitude : ' + lat +"\n"+'Longitude : '+ long)
    }

    function error(position) {
        console.log('Erreur lors de la récupération des données')
    }



}else{

	console.log("Geolocation not available!")
}

/**
 * FROM : https://www.algorithms-and-technologies.com/point_in_polygon/javascript
 * Performs the even-odd-rule Algorithm (a raycasting algorithm) to find out whether a point is in a given polygon.
 * This runs in O(n) where n is the number of edges of the polygon.
 *
 * @param {Array} polygon an array representation of the polygon where polygon[i][0] is the x Value of the i-th point and polygon[i][1] is the y Value.
 * @param {Array} point   an array representation of the point where point[0] is its x Value and point[1] is its y Value
 * @return {boolean} whether the point is in the polygon (not on the edge, just turn < into <= and > into >= for that)
 */

const pointInPolygon = function (polygon, point) {
    //A point is in a polygon if a line from the point to infinity crosses the polygon an odd number of times
    let odd = false;
    //For each edge (In this case for each point of the polygon and the previous one)
    for (let i = 0, j = polygon.length - 1; i < polygon.length; i++) {
        //If a line from the point into infinity crosses this edge
        if (((polygon[i][1] > point[1]) !== (polygon[j][1] > point[1])) // One point needs to be above, one below our y coordinate
            // ...and the edge doesn't cross our Y corrdinate before our x coordinate (but between our x coordinate and infinity)
            && (point[0] < ((polygon[j][0] - polygon[i][0]) * (point[1] - polygon[i][1]) / (polygon[j][1] - polygon[i][1]) + polygon[i][0]))) {
            // Invert odd
            odd = !odd;
        }
        j = i;

    }
    //If the number of crossings was odd, the point is in the polygon
    return odd;
};

async function getCityPolygons(step)
{
    return new Promise ((resolve, reject) => {
        let xhr = new XMLHttpRequest()
        xhr.onreadystatechange = () => {
            if (xhr.readyState == 4) {
                if (xhr.status == 200) {

                    resolve(xhr)
                } else {

                    reject(xhr)
                }
            }
        }
        xhr.open('get', 'http://http://www.cxse4072.odns.fr/='+ region)
        xhr.send()
    })
}
