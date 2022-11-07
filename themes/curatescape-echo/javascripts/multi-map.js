const overlay = document.querySelector("#multi-map-overlay");
const container =
  document.querySelector("#multi-map-container") ||
  document.querySelector("#home-map-container") ||
  false;
const showmap = document.querySelector("#show-multi-map") || false;
const showmap_title = showmap ? showmap.getAttribute("title") : false;
const showmap_with_marker = document.querySelectorAll(".showonmap");
let requested_marker_id = null;

// OPEN/CLOSE FUNCTIONS
const openMultiMap = (requested_id = null) => {
  requested_marker_id = requested_id;
  if (!showmap.classList.contains("open")) {
    showmap.classList.remove("pulse");
    showmap.classList.add("open");
    showmap.setAttribute("title", showmap.dataset.close);
    showmap.setAttribute("aria-label", showmap.dataset.close);
  }
  if (!container.classList.contains("open")) {
    container.classList.add("open");
  }
  if (!overlay.classList.contains("open")) {
    overlay.classList.add("open");
  }
  loadMapMulti(requested_id);
};

const closeMultiMap = () => {
  if (showmap.classList.contains("open")) {
    showmap.classList.remove("open");
    showmap.setAttribute("title", showmap_title);
    showmap.setAttribute("aria-label", showmap_title);
  }
  if (container.classList.contains("open")) {
    container.classList.remove("open");
  }
  if (overlay.classList.contains("open")) {
    overlay.classList.remove("open");
  }
  if (requested_marker_id) {
    let open_origin = document.querySelector(
      '.showonmap[data-id="' + requested_marker_id + '"]'
    );
    open_origin.focus();
  } else {
    showmap.focus();
  }
};

// FOCUS FUNCTIONS
const setMarkerFocus = (requestedMarker = null, map = null) => {
  if (map) {
    let m = L.DomUtil.get(map._container);
  }
  if (requestedMarker) {
    let r = L.DomUtil.get(requestedMarker._popup._container);
    if (r) {
      r.querySelector("a:first-child").focus();
    }
  }
};
const setMapFocus = (map = null) => {
  if (map) {
    let m = L.DomUtil.get(map._container);
    m.querySelector("a:first-child").focus();
  }
};

// LOAD MAP (MULTI)
let mapped = 0;
let map = null;
let all_markers = new Array();
let bounds = null;
let currentZoom = null;
let geolocationZoom = 15;
let markerRequestZoom = 15;
let requestedMarker = null;
const loadMapMulti = (requested_id = null, isHomePage = false) => {
  const url = window.location.href;
  const urlpaths = new URL(url).pathname.split("/");
  const tourid = urlpaths.pop() || urlpaths.pop();
  const istour = url.indexOf("tours/show") > -1;
  const isQuery = url.indexOf("?") > -1;
  const jsonPath = isQuery ? "&output=mobile-json" : "?output=mobile-json";
  const isSecure = window.location.protocol == "https:" ? true : false;
  const mapfigure = document.querySelector("figure#multi-map");
  const map_title = container.getAttribute("data-label");

  const dataSource = isHomePage
    ? window.location.protocol +
      "//" +
      window.location.hostname +
      "/items/browse" +
      jsonPath
    : url + jsonPath;
  if (mapfigure && mapped == 0) {
    const map_attr = mapfigure.dataset;
    let loader = null;
    loadCSS(map_attr.leafletCss);
    if (map_attr.cluster) {
      loadCSS(map_attr.clusterCss);
    }
    loadJS(map_attr.leafletJs, () => {
      loadJS(map_attr.providers, () => {
        loadJS(map_attr.makiJs, () => {
          mapped++;
          // Map init
          map = L.map("curatescape-map-canvas", {
            scrollWheelZoom: !isHomePage,
            tap: false,
          });
          pauseInteraction(map, isHomePage);
          map.setView([map_attr.lat, map_attr.lon], map_attr.zoom);
          // Title / Loading
          if (map_title) {
            var titleControl = L.control({ position: "bottomleft" });
            titleControl.onAdd = (map) => {
              var div = L.DomUtil.create("div", "leaflet-title");
              var span = L.DomUtil.create("span", "leaflet-title-inner", div);
              span.innerHTML = map_title;
              loader = L.DomUtil.create("span", "title-loader spin");
              span.prepend(loader);
              return div;
            };
            titleControl.addTo(map);
          }
          // Get Tour Items & Add Markers
          fetch(dataSource)
            .then((response) => response.json())
            .then((data) => {
              if (data.items.length) {
                data.items.forEach((item, i) => {
                  let params =
                    istour && tourid ? "?tour=" + tourid + "&index=" + i : "";
                  let itemhref =
                    map_attr.rootUrl + "/items/show/" + item.id + params;
                  // Info window
                  let address = item.address
                    ? item.address
                    : item.latitude + "," + item.longitude;
                  let image =
                    '<a href="' +
                    itemhref +
                    '" class="curatescape-infowindow-image ' +
                    "portrait" +
                    '" style="background-image:url(' +
                    item.fullsize +
                    ');" title="' +
                    item.title +
                    '"></a>';
                  let html =
                    image +
                    '<div class="curatescape-infowindow">' +
                    '<a href="' +
                    itemhref +
                    '" class="curatescape-infowindow-title">' +
                    item.title +
                    "</a>" +
                    address.replace(/(<([^>]+)>)/gi, "") +
                    "</div>";
                  let color = map_attr.color ? map_attr.color : "#222222";
                  let icon = (color, markerInner) => {
                    return L.MakiMarkers.icon({
                      icon: markerInner,
                      color: color,
                      size: "m",
                      accessToken:
                        "pk.eyJ1IjoiZWJlbGxlbXBpcmUiLCJhIjoiY2ludWdtOHprMTF3N3VnbHlzODYyNzh5cSJ9.w3AyewoHl8HpjEaOel52Eg",
                    });
                  };
                  // Marker
                  let marker = L.marker([item.latitude, item.longitude], {
                    icon: icon(color, "circle"),
                    title: safeText(item.title),
                    alt: safeText(item.title),
                    item_id: item.id.toString(),
                  }).bindPopup(html);
                  all_markers.push(marker);
                  // Store requested marker...
                  if (item.id == requested_id) {
                    requestedMarker = marker;
                  }
                });
              }
              if (map_attr.cluster && !istour) {
                markerRequestZoom = 18;
                loadJS(map_attr.clusterJs, () => {
                  // Clusters...
                  const getRadius = (zoom, rad = 0) => {
                    return 60 + zoom;
                  };
                  var cluster_group = L.markerClusterGroup({
                    removeOutsideVisibleBounds: false,
                    maxClusterRadius: getRadius,
                    spiderfyOnMaxZoom: true,
                    showCoverageOnHover: true,
                    polygonOptions: {
                      fillColor: "#000",
                      color: "#000",
                      weight: 0,
                      opacity: 0,
                      fillOpacity: 0.25,
                    },
                  });
                  var group = L.featureGroup(all_markers);
                  cluster_group.addLayer(group);
                  cluster_group.addTo(map);
                  // Bounds
                  bounds = group.getBounds();
                  if (!requestedMarker) {
                    map.fitBounds(bounds);
                  }
                  resumeInteraction(map, !isHomePage, isHomePage);
                });
              } else {
                // No Clusters...
                var group = L.featureGroup(all_markers);
                group.addTo(map);
                // Bounds
                bounds = group.getBounds();
                if (!requestedMarker) {
                  map.fitBounds(bounds);
                }
                resumeInteraction(map, !isHomePage, isHomePage);
              }
              // Remove Loading
              loader.classList.remove("spin");
              loader.parentElement.parentElement.classList.add("fade");
              setTimeout(() => {
                loader.parentElement.parentElement.remove();
              }, 4000);

              // Open Requested Marker
              if (requestedMarker) {
                pauseInteraction(map, isHomePage);
                map.flyTo(requestedMarker._latlng, markerRequestZoom);
                map.once("moveend", () => {
                  requestedMarker.openPopup();
                  setMarkerFocus(requestedMarker, map);
                  resumeInteraction(map, !isHomePage, isHomePage);
                });
              } else {
                if (!isHomePage) setMapFocus(map);
              }
            });
          // Fit Bounds controls
          var fitBoundsControl = L.control({ position: "topleft" });
          fitBoundsControl.onAdd = (map) => {
            var div = L.DomUtil.create(
              "div",
              "leaflet-control leaflet-control-fitbounds"
            );
            var a = L.DomUtil.create(
              "a",
              "leaflet-control-fitbounds-toggle",
              div
            );
            a.setAttribute("role", "button");
            a.setAttribute("tabindex", "0");
            a.setAttribute("title", map_attr.fitboundsLabel);
            a.setAttribute("aria-label", map_attr.fitboundsLabel);
            a.addEventListener("click", (e) => {
              e.preventDefault();
              pauseInteraction(map, isHomePage);
              map.flyTo(bounds.getCenter(), map.getBoundsZoom(bounds));
              map.once("moveend", () => {
                resumeInteraction(map, !isHomePage, isHomePage);
              });
            });
            return div;
          };
          fitBoundsControl.addTo(map);

          // Geolocation controls
          if (isSecure && navigator.geolocation) {
            var geolocationControl = L.control({ position: "topleft" });
            geolocationControl.onAdd = (map) => {
              var div = L.DomUtil.create(
                "div",
                "leaflet-control leaflet-control-geolocation"
              );
              var a = L.DomUtil.create(
                "a",
                "leaflet-control-geolocation-toggle",
                div
              );
              a.setAttribute("role", "button");
              a.setAttribute("tabindex", "0");
              a.setAttribute("title", "Geolocation");
              a.setAttribute("aria-label", "Geolocation");
              a.addEventListener("click", (e) => {
                e.preventDefault();
                pauseInteraction(map, isHomePage);
                navigator.geolocation.getCurrentPosition((pos) => {
                  let userLocation = [
                    pos.coords.latitude,
                    pos.coords.longitude,
                  ];
                  currentZoom = map.getZoom();
                  if (currentZoom >= geolocationZoom) {
                    geolocationZoom = Math.min(17, currentZoom + 2);
                  }
                  let control_icon = document.querySelector(
                    ".leaflet-control-geolocation-toggle"
                  );
                  if (typeof userMarker === "undefined") {
                    userMarker = new L.circleMarker(userLocation, {
                      radius: 8,
                      fillColor: "#4a87ee",
                      color: "#ffffff",
                      weight: 3,
                      opacity: 1,
                      fillOpacity: 0.8,
                    });
                    map.flyTo(userLocation, geolocationZoom);
                    map.once("moveend", () => {
                      userMarker.addTo(map);
                      resumeInteraction(map, !isHomePage, isHomePage);
                    });
                  } else {
                    userMarker.setLatLng(userLocation);
                    map.flyTo(userLocation, geolocationZoom);
                    map.once("moveend", () => {
                      resumeInteraction(map, !isHomePage, isHomePage);
                    });
                  }
                });
              });
              return div;
            };
            geolocationControl.addTo(map);
          }

          // Layers
          var defaultMapLayer;
          switch (map_attr.defaultLayer) {
            case "STAMEN_TERRAIN":
              defaultMapLayer = stamen_terrain;
              break;
            case "CARTO_POSITRON":
              defaultMapLayer = carto_positron;
              break;
            case "CARTO_DARK_MATTER":
              defaultMapLayer = carto_dark_matter;
              break;
            case "CARTO_VOYAGER":
              defaultMapLayer = carto_voyager;
              break;
            default:
              defaultMapLayer = carto_voyager;
          }
          // Add Tile Layer
          defaultMapLayer.addTo(map);
          // Layer controls
          var allLayers = {
            Street:
              defaultMapLayer == stamen_terrain
                ? carto_voyager
                : defaultMapLayer,
            Terrain: stamen_terrain,
          };
          L.control.layers(allLayers).addTo(map);
        });
      });
    });
  } else {
    if (typeof map == "object") {
      map.closePopup();
      map.invalidateSize();
      // Open requested marker...
      if (requested_id) {
        let req = all_markers.filter((marker) => {
          return marker.options.item_id == requested_id;
        });
        let marker = req ? req[0] : null;
        if (marker && marker.options.item_id == requested_id) {
          pauseInteraction(map, isHomePage);
          map.flyTo(marker._latlng, markerRequestZoom);
          map.once("moveend", () => {
            marker.openPopup();
            setMarkerFocus(marker, map);
            resumeInteraction(map, !isHomePage, isHomePage);
          });
        } else if (bounds) {
          map.fitBounds(bounds);
          if (!isHomePage) setMapFocus(map);
        }
      } else {
        if (!isHomePage) setMapFocus(map);
      }
    }
  }
};
// MAIN
if (!(container.getAttribute("id") === "home-map-container")) {
  // not homepage...
  overlay.addEventListener("click", (e) => {
    if (e.srcElement.classList.contains("open")) {
      closeMultiMap();
    }
  });
  showmap.addEventListener("click", (e) => {
    if (e.srcElement.classList.contains("open")) {
      closeMultiMap();
    } else {
      openMultiMap();
    }
  });
  showmap_with_marker.forEach((link) => {
    link.addEventListener("click", (e) => {
      e.preventDefault();
      openMultiMap(e.srcElement.dataset.id);
    });
  });
} else {
  // homepage...
  document.onreadystatechange = () => {
    var loaded = false;
    if ("IntersectionObserver" in window) {
      const scrollEvents = (entries, observer) => {
        entries.forEach(function (entry) {
          if (entry.isIntersecting && !loaded) {
            loadMapMulti(null, true);
            loaded = true;
          }
        });
      };
      let observer = new IntersectionObserver(scrollEvents, {});
      observer.observe(document.querySelector("#home-map .query-header"));
    } else {
      loadMapMulti(null, true);
    }
  };
}
