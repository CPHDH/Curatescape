// Tiles
const stamen_terrain = L.tileLayer(
  "//tiles.stadiamaps.com/tiles/stamen_terrain/{z}/{x}/{y}{retina}.png",
  {
    attribution:
      '<a href="https://stadiamaps.com/" target="_blank">Stadia Maps</a> | <a href="https://stamen.com/" target="_blank">Stamen Design</a> | <a href="https://openmaptiles.org/" target="_blank">OpenMapTiles</a> | <a href="https://www.openstreetmap.org/about" target="_blank">OpenStreetMap</a>',
    retina: L.Browser.retina ? "@2x" : "",
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
