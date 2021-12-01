// Tiles
const stamen_terrain = L.tileLayer(
  "//stamen-tiles-{s}.a.ssl.fastly.net/terrain/{z}/{x}/{y}{retina}.png",
  {
    attribution:
      '<a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> | Map Tiles by <a href="http://stamen.com/">Stamen Design</a>',
    retina: L.Browser.retina ? "@2x" : "",
    maxZoom: 18,
    maxNativeZoom: 16,
    subdomains: "abcd",
  }
);
const carto_positron = L.tileLayer(
  "//cartodb-basemaps-{s}.global.ssl.fastly.net/light_all/{z}/{x}/{y}{retina}.png",
  {
    attribution:
      '<a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> | <a href="https://cartodb.com/attributions">CartoDB</a>',
    retina: L.Browser.retina ? "@2x" : "",
  }
);
const carto_dark_matter = L.tileLayer(
  "//cartodb-basemaps-{s}.global.ssl.fastly.net/dark_all/{z}/{x}/{y}{retina}.png",
  {
    attribution:
      '<a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> | <a href="https://cartodb.com/attributions">CartoDB</a>',
    retina: L.Browser.retina ? "@2x" : "",
  }
);
const carto_voyager = L.tileLayer(
  "//cartodb-basemaps-{s}.global.ssl.fastly.net/rastertiles/voyager/{z}/{x}/{y}{retina}.png",
  {
    attribution:
      '<a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> | <a href="https://cartodb.com/attributions">CartoDB</a>',
    retina: L.Browser.retina ? "@2x" : "",
  }
);
