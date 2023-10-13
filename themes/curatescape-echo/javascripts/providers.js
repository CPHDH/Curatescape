// Tiles
(() => {
  window.getMapTileSets = () => {
    const tiles = [];
    tiles.STAMEN_TERRAIN = L.tileLayer(
      "//tiles.stadiamaps.com/tiles/stamen_terrain/{z}/{x}/{y}{retina}.png",
      {
        attribution:
          '<a href="https://stadiamaps.com/">Stadia Maps</a> | <a href="https://stamen.com/">Stamen Design</a> | <a href="https://openmaptiles.org/">OpenMapTiles</a>',
        retina: L.Browser.retina ? "@2x" : "",
        label: 'Terrain (Stamen Terrain)',
      }
    );
    tiles.STAMEN_TONER = L.tileLayer(
      "//tiles.stadiamaps.com/tiles/stamen_toner_lite/{z}/{x}/{y}{retina}.png",
      {
        attribution:
          '<a href="https://stadiamaps.com/">Stadia Maps</a> | <a href="https://stamen.com/">Stamen Design</a> | <a href="https://openmaptiles.org/">OpenMapTiles</a>',
        retina: L.Browser.retina ? "@2x" : "",
        label: 'Street (Stamen Toner Lite)',
      }
    );
    tiles.CARTO_POSITRON = L.tileLayer(
      "//cartodb-basemaps-{s}.global.ssl.fastly.net/light_all/{z}/{x}/{y}{retina}.png",
      {
        attribution:
          '<a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> | <a href="https://cartodb.com/attributions">CartoDB</a>',
        retina: L.Browser.retina ? "@2x" : "",
        label: 'Street (Carto Positron)',
      }
    );
    tiles.CARTO_DARK_MATTER = L.tileLayer(
      "//cartodb-basemaps-{s}.global.ssl.fastly.net/dark_all/{z}/{x}/{y}{retina}.png",
      {
        attribution:
          '<a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> | <a href="https://cartodb.com/attributions">CartoDB</a>',
        retina: L.Browser.retina ? "@2x" : "",
        label: 'Street (Carto Dark Matter)',
      }
    );
    tiles.CARTO_VOYAGER = L.tileLayer(
      "//cartodb-basemaps-{s}.global.ssl.fastly.net/rastertiles/voyager/{z}/{x}/{y}{retina}.png",
      {
        attribution:
          '<a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> | <a href="https://cartodb.com/attributions">CartoDB</a>',
        retina: L.Browser.retina ? "@2x" : "",
        label: 'Street (Carto Voyager)',
      }
    );
    tiles.HUMANITARIAN = L.tileLayer(
      "//{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png",
      {
        attribution:
          '<a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> | <a href="https://www.hotosm.org">Humanitarian OpenStreetMap Team</a> | <a href="https://openstreetmap.fr">OpenStreetMap France</a>',
          label: 'Street (OSM Humanitarian)',
      }
    );
    tiles.OSM_BRIGHT = L.tileLayer(
      "//tiles.stadiamaps.com/tiles/osm_bright/{z}/{x}/{y}{retina}.png", 
      {
        attribution: '<a href="https://stadiamaps.com/">Stadia Maps</a> | <a href="https://openmaptiles.org/">OpenMapTiles</a>',
        retina: L.Browser.retina ? "@2x" : "",
        label: 'Street (Stadia OSM Bright)',
      }
    );
    tiles.STADIA_OUTDOORS = L.tileLayer(
      "//tiles.stadiamaps.com/tiles/outdoors/{z}/{x}/{y}{retina}.png",
      {
        attribution: '<a href="https://stadiamaps.com/">Stadia Maps</a> | <a href="https://openmaptiles.org/">OpenMapTiles</a>',
        retina: L.Browser.retina ? "@2x" : "",
        label: 'Street (Stadia Outdoors)',
      }
    );
    return tiles;
  }
})();
