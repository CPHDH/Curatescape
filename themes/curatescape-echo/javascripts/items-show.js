// LOAD MAP (SINGLE ITEM)
const loadMapSingle = () => {
  let isSecure = window.location.protocol == "https:" ? true : false;
  let mapfigure = document.querySelector("figure#story-map");
  if (mapfigure) {
    let data = mapfigure.dataset;
    loadCSS(data.leafletCss);
    loadJS(data.leafletJs, () => {
      loadJS(data.providers, () => {
        loadJS(data.makiJs, () => {
          // console.log("Leaflet initialized...");
          let map = L.map("curatescape-map-canvas", {
            scrollWheelZoom: false,
            tap: false,
          });
          pauseInteraction(map);
          // Set View
          map.setView([data.lat, data.lon], data.zoom);
          // Center on open
          map.on("popupopen", (e) => {
            var px = map.project(e.popup._latlng);
            px.y -= e.popup._container.clientHeight / 2;
            map.panTo(map.unproject(px), { animate: true });
          });
          // Info window
          var address = data.address ? data.address : data.lat + "," + data.lon;
          var image =
            '<a href="javascript:void(0)" class="curatescape-infowindow-image ' +
            data.orientation +
            '" style="background-image:url(' +
            data.image +
            ');" title="' +
            data.title +
            '"></a>';
          var html =
            image +
            '<div class="curatescape-infowindow">' +
            '<div class="curatescape-infowindow-title">' +
            data.title +
            "</div>" +
            address.replace(/(<([^>]+)>)/gi, "") +
            "</div>";
          var color = data.color ? data.color : "#222222";
          var icon = (color, markerInner) => {
            return L.MakiMarkers.icon({
              icon: markerInner,
              color: color,
              size: "m",
              accessToken:
                "pk.eyJ1IjoiZWJlbGxlbXBpcmUiLCJhIjoiY2ludWdtOHprMTF3N3VnbHlzODYyNzh5cSJ9.w3AyewoHl8HpjEaOel52Eg",
            });
          };
          // Marker
          var marker = L.marker([data.lat, data.lon], {
            icon: icon(color, "circle"),
            title: safeText(data.title),
            alt: safeText(data.title),
          }).bindPopup(html);
          marker.addTo(map).openPopup();
          resumeInteraction(map, false);

          // Layers
          var tileprovider = window.getMapTileSets();
          tileprovider[data.primaryLayer].addTo(map);
          // Layer controls
          if(data.secondaryLayer && data.secondaryLayer !== 'NONE'){
            let allLayers = {
             [tileprovider[data.primaryLayer].options.label] : tileprovider[data.primaryLayer],
             [tileprovider[data.secondaryLayer].options.label] : tileprovider[data.secondaryLayer],
            };
            L.control.layers(allLayers).addTo(map); 
          }
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
                pauseInteraction(map);
                if (e.composed) {
                  navigator.geolocation.getCurrentPosition((pos) => {
                    let userLocation = [
                      pos.coords.latitude,
                      pos.coords.longitude,
                    ];
                    let control_icon = document.querySelector(
                      ".leaflet-control-geolocation-toggle"
                    );
                    control_icon.classList.toggle("alt");
                    if (control_icon.classList.contains("alt")) {
                      if (typeof userMarker === "undefined") {
                        userMarker = new L.circleMarker(userLocation, {
                          radius: 8,
                          fillColor: "#4a87ee",
                          color: "#ffffff",
                          weight: 3,
                          opacity: 1,
                          fillOpacity: 0.8,
                        }).addTo(map);
                      } else {
                        userMarker.setLatLng(userLocation);
                      }
                      map.flyTo(userLocation);
                      map.once("moveend", () => {
                        userMarker.addTo(map);
                        resumeInteraction(map, false);
                      });
                    } else {
                      map.flyTo([data.lat, data.lon]);
                      map.once("moveend", () => {
                        resumeInteraction(map, false);
                      });
                    }
                  });
                }
              });
              return div;
            };
            geolocationControl.addTo(map);
          }
          // Fullscreen controls
          var fullscreenControl = L.control({ position: "topleft" });
          fullscreenControl.onAdd = (map) => {
            var div = L.DomUtil.create(
              "div",
              "leaflet-control leaflet-control-fullscreen"
            );
            var a = L.DomUtil.create(
              "a",
              "leaflet-control-fullscreen-toggle",
              div
            );
            a.setAttribute("role", "button");
            a.setAttribute("tabindex", "0");
            a.setAttribute("title", "Fullscreen");
            a.setAttribute("aria-label", "Fullscreen");
            a.addEventListener("click", (e) => {
              e.preventDefault();
              if (e.composed) {
                document
                  .querySelector("#curatescape-map-canvas")
                  .classList.toggle("fullscreen");
                let control_icon = document.querySelector(
                  ".leaflet-control-fullscreen-toggle"
                );
                body.classList.toggle("fullscreen-map");
                control_icon.classList.toggle("alt");
                if (control_icon.classList.contains("alt")) {
                  map.scrollWheelZoom.enable();
                } else {
                  map.scrollWheelZoom.disable();
                }
                map.invalidateSize();
              }
            });
            return div;
          };
          fullscreenControl.addTo(map);
        });
      });
    });
  }
};
// MEDIA PLAYERS/BUTTONS
const streamingMediaControls = () => {
  document.querySelectorAll(".media-player").forEach((player) => {
    player.style.height = 0;
    player.setAttribute("tabindex", "-1");
  });
  let mediabuttons = document.querySelectorAll(".media-button");
  mediabuttons.forEach((button) => {
    button.addEventListener(
      "click",
      (e) => {
        let index = e.currentTarget.getAttribute("data-index");
        let type = e.currentTarget.getAttribute("data-type");
        let activeicon = document.querySelector(
          '.media-button[data-type="' + type + '"][data-index="' + index + '"]'
        );
        if (activeicon) {
          activeicon.classList.toggle("alt");
          activeicon.parentNode.setAttribute("title", "play");
        }
        let newicon = document.querySelector(
          '.media-button[data-type="' + type + '"][data-index="' + index + '"]'
        );
        if (activeicon !== newicon) {
          newicon.classList.toggle("alt");
          newicon.parentNode.setAttribute("title", "pause");
        }
        let activeplayer = document.querySelector(
          '.media-player.active[data-type="' + type + '"]'
        );
        if (activeplayer) {
          activeplayer.classList.remove("active");
          activeplayer.style.height = 0;
          activeplayer.children[0].pause();
          activeplayer.children[0].setAttribute("tabindex", "-1");
        }
        let newplayer = document.querySelector(
          '.media-player[data-type="' + type + '"][data-index="' + index + '"]'
        );
        if (activeplayer !== newplayer) {
          newplayer.classList.add("active");
          newplayer.children[0].setAttribute("tabindex", "0");
          newplayer.style.height = newplayer.children[0].clientHeight + "px";
          newplayer.children[0].play();
          newplayer.children[0].focus();
        }
      },
      false
    );
  });
};

// LOAD IMAGES
const loadImages = () => {
  document.querySelectorAll("[data-style]").forEach((image) => {
    let css = image.dataset.style;
    image.style = css;
  });
};

// IMAGE VIEWER / PHOTOSWIPE
const loadPhotoSwipe = (target) => {
  if (!target) {
    return;
  }
  loadCSS(target.dataset.pswpCss);
  loadCSS(target.dataset.pswpSkinCss);
  loadJS(target.dataset.pswp, () => {
    loadJS(target.dataset.pswpUi, () => {
      // console.log("PhotoSwipe initialized...");
      let html =
        '<div class="pswp" tabindex="-1" role="dialog" aria-hidden="true"><div class="pswp__bg"></div><div class="pswp__scroll-wrap"><div class="pswp__container"><div class="pswp__item"></div><div class="pswp__item"></div><div class="pswp__item"></div></div><div class="pswp__ui pswp__ui--hidden"><div class="pswp__top-bar"><div class="pswp__counter"></div><button class="pswp__button pswp__button--close" title="Close (Esc)"></button><button class="pswp__button pswp__button--share" title="Share"></button><button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button><button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button><div class="pswp__preloader"><div class="pswp__preloader__icn"><div class="pswp__preloader__cut"><div class="pswp__preloader__donut"></div></div></div></div></div><div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap"><div class="pswp__share-tooltip"></div></div><button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)"></button><button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)"></button><div class="pswp__caption"><div class="pswp__caption__center"></div></div></div></div></div>';
      var pswp_controls = document.createElement("div");
      pswp_controls.innerHTML = html;
      document.body.appendChild(pswp_controls);
      var pswpElement = document.querySelectorAll(".pswp")[0];
      var options = {
        index: 0,
      };
      var gallery_items = target.querySelectorAll("a.gallery-image");
      var items = [];
      gallery_items.forEach((item, i) => {
        items.push({
          src: item.href,
          h: item.dataset.pswpHeight,
          w: item.dataset.pswpWidth,
          title: item.nextElementSibling.innerHTML,
        });
        item.onclick = (e) => {
          e.preventDefault();
          let gallery = new PhotoSwipe(
            pswpElement,
            PhotoSwipeUI_Default,
            items,
            {
              showHideOpacity: true,
              bgOpacity: 0.925,
              history: false,
              index: i,
            }
          );
          gallery.init();
        };
      });
    });
  });
};

// ANCHOR LINK SMOOTH SCROLLING
const smoothAnchorLinks = () => {
  let reduced_motion =
    "matchMedia" in window
      ? window.matchMedia("(prefers-reduced-motion: reduce)").matches
      : false;
  let options = reduced_motion ? {} : { behavior: "smooth" };
  let anchors = document.querySelectorAll(
    ".rl-toc [href^='#'],.gallery-image[href^='#']"
  );
  anchors.forEach((a) => {
    a.addEventListener("click", (e) => {
      e.preventDefault();
      if (document.querySelector(e.currentTarget.hash)) {
        document.querySelector(e.currentTarget.hash).scrollIntoView(options);
      }
    });
  });
};

// OBSERVER CALLBACK
// Loads Images on intersection
// Loads PhotoSwipe on intersection
// Loads Map on intersection
// Updates Table of Contents on intersection
let mapped = 0;
let pswp = 0;
let imgs = 0;
const scrollEvents = (entries, observer) => {
  entries.forEach((entry) => {
    if (entry.intersectionRatio) {
      if (
        mapped == 0 &&
        entry.target.parentElement.dataset.toc == "#map-section"
      ) {
        mapped++;
        loadMapSingle();
      }
      if (imgs == 0 && entry.target.parentElement.dataset.toc == "#images") {
        imgs++;
        loadImages();
      }
      if (pswp == 0 && entry.target.parentElement.dataset.toc == "#images") {
        pswp++;
        loadPhotoSwipe(entry.target.parentElement);
      }
      let currents = document.querySelectorAll(
        '.rl-toc ul li a[href="' + entry.target.parentElement.dataset.toc + '"]'
      );
      currents.forEach((current) =>
        current.parentElement.classList.add("current")
      );
    } else {
      if (!checkVisible(entry.target.parentElement)) {
        let removes = document.querySelectorAll(
          '.rl-toc ul li a[href="' +
            entry.target.parentElement.dataset.toc +
            '"]'
        );
        removes.forEach((remove) =>
          remove.parentElement.classList.remove("current")
        );
      }
    }
  });
};
// MAIN
document.onreadystatechange = () => {
  if (document.readyState === "complete") {
    streamingMediaControls();
    smoothAnchorLinks();
    if ("IntersectionObserver" in window) {
      let observer = new IntersectionObserver(scrollEvents, {});
      let sections = document.querySelectorAll("[data-toc] > *");
      sections.forEach((section) => observer.observe(section));
    } else {
      loadImages();
      loadPhotoSwipe(document.querySelector('[data-toc="#images"'));
      loadMapSingle();
    }
    addEventListener("beforeprint", (event) => {
      loadImages();
    });
  }
};
