<?php require "../includes/bootstrap.php"; ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>APRS Track Direct Demo</title>

        <!-- Mobile meta -->
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
        <meta name="apple-mobile-web-app-capable" content="yes"/>
        <meta name="mobile-web-app-capable" content="yes">

        <!-- JS libs used by this website (not a dependency for the track direct js lib) -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/mobile-detect/1.4.5/mobile-detect.min.js" integrity="sha512-1vJtouuOb2tPm+Jh7EnT2VeiCoWv0d7UQ8SGl/2CoOU+bkxhxSX4gDjmdjmbX4OjbsbCBN+Gytj4RGrjV3BLkQ==" crossorigin="anonymous"></script>

        <!-- Stylesheets used by this website (not a dependency for the track direct js lib) -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" integrity="sha512-HK5fgLBL+xu6dm/Ii3z4xhlSUyZgTT9tuc/hSrtw6uzJOvgRr2a9jyxxT1ely+B+xFAmJKVSTbpM/CuL7qxO8w==" crossorigin="anonymous" />

        <!-- Track Direct js dependencies -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js" integrity="sha512-jGsMH83oKe9asCpkOVkBnUrDDTp8wl+adkB2D+//JtlxO4SrLoJdhbOysIFQJloQFD+C4Fl1rMsQZF76JjV0eQ==" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment-with-locales.min.js" integrity="sha512-LGXaggshOkD/at6PFNcp2V2unf9LzFq6LE+sChH7ceMTDP0g2kn6Vxwgg7wkPP7AAtX+lmPqPdxB47A0Nz0cMQ==" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/autolinker/3.14.2/Autolinker.min.js" integrity="sha512-qyoXjTIJ69k6Ik7CxNVKFAsAibo8vW/s3WV3mBzvXz6Gq0yGup/UsdZBDqFwkRuevQaF2g7qhD3E4Fs+OwS4hw==" crossorigin="anonymous"></script>

        <!-- Map api javascripts and related dependencies -->
        <?php $mapapi = $_GET['mapapi'] ?? 'leaflet'; ?>
        <?php if ($mapapi == 'google') : ?>
            <script type="text/javascript" src="//maps.googleapis.com/maps/api/js?key=<insert map key here>&libraries=visualization,geometry"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/OverlappingMarkerSpiderfier/1.0.3/oms.min.js" integrity="sha512-/3oZy+rGpR6XGen3u37AEGv+inHpohYcJupz421+PcvNWHq2ujx0s1QcVYEiSHVt/SkHPHOlMFn5WDBb/YbE+g==" crossorigin="anonymous"></script>

        <?php elseif ($mapapi == 'leaflet' || $mapapi == 'leaflet-vector'): ?>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.min.css" integrity="sha512-1xoFisiGdy9nvho8EgXuXvnpR5GAMSjFwp40gSRE3NwdUdIMIKuPa7bqoUhLD0O/5tPNhteAsE5XyyMi5reQVA==" crossorigin="anonymous" />
            <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.min.js" integrity="sha512-SeiQaaDh73yrb56sTW/RgVdi/mMqNeM2oBwubFHagc5BkixSpP1fvqF47mKzPGWYSSy4RwbBunrJBQ4Co8fRWA==" crossorigin="anonymous"></script>

            <?php if ($mapapi == 'leaflet-vector'): ?>
                <link href="https://api.tiles.mapbox.com/mapbox-gl-js/v0.35.1/mapbox-gl.css" rel='stylesheet' />
                <script src="https://api.tiles.mapbox.com/mapbox-gl-js/v0.35.1/mapbox-gl.js"></script>
                <script src="https://unpkg.com/mapbox-gl-leaflet@0.0.3/leaflet-mapbox-gl.js"></script>
            <?php endif; ?>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet-providers/1.11.0/leaflet-providers.min.js" integrity="sha512-TO+Wd5hbpDsACTmvzSqAZL83jMQCXGRFNoS4WZxcxrlJBTdgMYaT7g5uX49C5+Kbuxzlg2A+TFJ6UqdsXuOKLw==" crossorigin="anonymous"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.heat/0.2.0/leaflet-heat.js" integrity="sha512-KhIBJeCI4oTEeqOmRi2gDJ7m+JARImhUYgXWiOTIp9qqySpFUAJs09erGKem4E5IPuxxSTjavuurvBitBmwE0w==" crossorigin="anonymous"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/OverlappingMarkerSpiderfier-Leaflet/0.2.6/oms.min.js" integrity="sha512-V8RRDnS4BZXrat3GIpnWx+XNYBHQGdK6nKOzMpX4R0hz9SPWt7fltGmmyGzUkVFZUQODO1rE+SWYJJkw3SYMhg==" crossorigin="anonymous"></script>
        <?php endif; ?>

        <!-- Track Direct jslib -->
        <script type="text/javascript" src="/js/trackdirect.min.js"></script>


        <script type="text/javascript" src="/js/main.js"></script>
        <link rel="stylesheet" href="/css/main.css">

        <script>
            // Start everything!!!
            $(document).ready(function() {
                var wsServerUrl = 'ws://<?php echo $_SERVER['HTTP_HOST']; ?>:9000/ws'; // When using this in production you probably need to change this!!
                var mapElementId = 'map-container';

                var options = {};
                options['isMobile'] = false;
                options['useImperialUnit'] = <?php echo (isImperialUnitUser() ? 'true': 'false'); ?>;

                var md = new MobileDetect(window.navigator.userAgent);
                if (md.mobile() !== null) {
                    options['isMobile'] = true;
                }

                options['time'] =       "<?php echo $_GET['time'] ?? '' ?>";        // How many minutes of history to show
                options['center'] =     "<?php echo $_GET['center'] ?? '' ?>";      // Position to center on (for example "46.52108,14.63379")
                options['zoom'] =       "<?php echo $_GET['zoom'] ?? '' ?>";        // Zoom level
                options['timetravel'] = "<?php echo $_GET['timetravel'] ?? '' ?>";  // Unix timestamp to travel to
                options['maptype'] =    "<?php echo $_GET['maptype'] ?? '' ?>";     // May be "roadmap", "terrain" or "satellite"

                options['filters'] = {};
                options['filters']['sid'] = "<?php echo $_GET['sid'] ?? '' ?>";     // Station id to filter on
                options['filters']['sname'] = "<?php echo $_GET['sname'] ?? '' ?>"; // Station name to filter on

                // Tell jslib which html element to use to show connection status and mouse cordinates
                options['statusContainerElementId'] = 'status-container';
                options['cordinatesContainerElementId'] = 'cordinates-container';

                // Use this setting so enlarge some symbols (for example airplanes when using OGN as data source)
                //options['symbolsToScale'] = [[88,47],[94,null]];

                // Set this setting to false if you want to stop animations
                options['animate'] = true;

                // Tip: request position from some ip->location service (here using freegeoip as an example)
                $.getJSON('https://freegeoip.app/json/', function(data) {
                    if (data.latitude && data.longitude) {
                        options['defaultLatitude'] = data.latitude;
                        options['defaultLongitude'] = data.longitude;
                    } else {
                        // Default to Stockholm :-)
                        options['defaultLatitude'] = '59.30928';
                        options['defaultLongitude'] = '18.08830';
                    }

                    <?php if ($mapapi == 'leaflet-vector') : ?>
                        var maptilerKey = '<insert map key here>';
                        options['mapboxGLStyle'] = 'https://api.maptiler.com/maps/bright/style.json?optimize=true&key=' + maptilerKey;
                        options['mapboxGLAttribution'] = 'Map &copy; <a href="https://www.maptiler.com">MapTiler</a>, OpenStreetMap contributors';
                    <?php endif; ?>

                    <?php if ($mapapi == 'leaflet') : ?>
                        // We are using Leaflet -- read about leaflet-providers and select your favorite maps
                        // Make sure to read the license requirements for each provider before launching a public website

                        // Many providers require a map api key, the following is an example for MapBox
                        //L.tileLayer.provider('MapBox', {
                        //    id: '<insert map id here>',
                        //    accessToken: '<insert map key here>'
                        //}).addTo(map);

                        options['supportedMapTypes'] = {};
                        options['supportedMapTypes']['roadmap'] = 'CartoDB.Voyager';
                        options['supportedMapTypes']['terrain'] = 'OpenTopoMap';
                        options['supportedMapTypes']['satellite'] = 'Esri.WorldImagery';
                    <?php endif; ?>

                    // host is used to create url to /heatmaps and /images
                    options['host'] = "<?php echo $_SERVER['HTTP_HOST']; ?>";

                    var supportsWebSockets = 'WebSocket' in window || 'MozWebSocket' in window;
                    if (supportsWebSockets) {
                        trackdirect.init(wsServerUrl, mapElementId, options);
                    } else {
                        alert('This service require HTML 5 features to be able to feed you APRS data in real-time. Please upgrade your browser.');
                    }
                });
            });
        </script>
    </head>
    <body>
        <div class="topnav" id="tdTopnav">
            <a  style="background-color: #af7a4c; color: white;"
                href=""
                onclick="
                    if (location.protocol != 'https:') {
                        trackdirect.setCenter(); // Will go to default position
                    } else {
                        trackdirect.setMapLocationByGeoLocation(
                            function(errorMsg) {
                                var msg = 'We failed to determine your current location by using HTML 5 Geolocation functionality';
                                if (typeof errorMsg !== 'undefined' && errorMsg != '') {
                                    msg += ' (' + errorMsg + ')';
                                }
                                msg += '.';
                                alert(msg);
                            },
                            function() {},
                            5000
                        );
                    }
                    return false;"
                title="Go to my current position">
                My position
            </a>

            <div class="dropdown">
                <button class="dropbtn">Tail length
                    <i class="fa fa-caret-down"></i>
                </button>
                <div class="dropdown-content" id="tdTopnavTimelength">
                    <a href="javascript:void(0);" onclick="trackdirect.setTimeLength(10); $('#tdTopnavTimelength>a').removeClass('dropdown-content-checkbox-active'); $(this).addClass('dropdown-content-checkbox-active');" class="dropdown-content-checkbox">10 minutes</a>
                    <a href="javascript:void(0);" onclick="trackdirect.setTimeLength(30); $('#tdTopnavTimelength>a').removeClass('dropdown-content-checkbox-active'); $(this).addClass('dropdown-content-checkbox-active');" class="dropdown-content-checkbox">30 minutes</a>
                    <a href="javascript:void(0);" id="tdTopnavTimelength60" onclick="trackdirect.setTimeLength(60); $('#tdTopnavTimelength>a').removeClass('dropdown-content-checkbox-active'); $(this).addClass('dropdown-content-checkbox-active');" class="dropdown-content-checkbox dropdown-content-checkbox-active">1 hour</a>
                    <a href="javascript:void(0);" onclick="trackdirect.setTimeLength(180); $('#tdTopnavTimelength>a').removeClass('dropdown-content-checkbox-active'); $(this).addClass('dropdown-content-checkbox-active');" class="dropdown-content-checkbox">3 hours</a>
                    <a href="javascript:void(0);" onclick="trackdirect.setTimeLength(360); $('#tdTopnavTimelength>a').removeClass('dropdown-content-checkbox-active'); $(this).addClass('dropdown-content-checkbox-active');" class="dropdown-content-checkbox">6 hours</a>
                    <a href="javascript:void(0);" onclick="trackdirect.setTimeLength(720); $('#tdTopnavTimelength>a').removeClass('dropdown-content-checkbox-active'); $(this).addClass('dropdown-content-checkbox-active');" class="dropdown-content-checkbox">12 hours</a>
                    <a href="javascript:void(0);" onclick="trackdirect.setTimeLength(1080); $('#tdTopnavTimelength>a').removeClass('dropdown-content-checkbox-active'); $(this).addClass('dropdown-content-checkbox-active');" class="dropdown-content-checkbox">18 hours</a>
                    <a href="javascript:void(0);" onclick="trackdirect.setTimeLength(1440); $('#tdTopnavTimelength>a').removeClass('dropdown-content-checkbox-active'); $(this).addClass('dropdown-content-checkbox-active');" class="dropdown-content-checkbox">24 hours</a>
                </div>
            </div>

            <div class="dropdown">
                <button class="dropbtn">Map API
                    <i class="fa fa-caret-down"></i>
                </button>
                <div class="dropdown-content">
                    <a href="/?mapapi=google" title="Switch to Google Maps" <?= ($mapapi=="google"?"class='dropdown-content-checkbox dropdown-content-checkbox-active'":"class='dropdown-content-checkbox'") ?>>Google Maps API</a>
                    <a href="/?mapapi=leaflet" title="Switch to Leaflet with raster tiles" <?= ($mapapi=="leaflet"?"class='dropdown-content-checkbox  dropdown-content-checkbox-active'":"class='dropdown-content-checkbox'") ?>>Leaflet - Raster Tiles</a>
                    <a href="/?mapapi=leaflet-vector" title="Switch to Leaflet with vector tiles" <?= ($mapapi=="leaflet-vector"?"class='dropdown-content-checkbox dropdown-content-checkbox-active'":"class='dropdown-content-checkbox'") ?>>Leaflet - Vector Tiles</a>
                </div>
            </div>

            <?php if ($mapapi != 'leaflet-vector') : ?>
            <div class="dropdown">
                <button class="dropbtn">Map Type
                    <i class="fa fa-caret-down"></i>
                </button>
                <div class="dropdown-content" id="tdTopnavMapType">
                    <a href="javascript:void(0);" onclick="trackdirect.setMapType('roadmap'); $('#tdTopnavMapType>a').removeClass('dropdown-content-checkbox-active'); $(this).addClass('dropdown-content-checkbox-active');" class="dropdown-content-checkbox dropdown-content-checkbox-active">Roadmap</a>
                    <a href="javascript:void(0);" onclick="trackdirect.setMapType('terrain'); $('#tdTopnavMapType>a').removeClass('dropdown-content-checkbox-active'); $(this).addClass('dropdown-content-checkbox-active');" class="dropdown-content-checkbox">Terrain/Outdoors</a>
                    <a href="javascript:void(0);" onclick="trackdirect.setMapType('satellite'); $('#tdTopnavMapType>a').removeClass('dropdown-content-checkbox-active'); $(this).addClass('dropdown-content-checkbox-active');" class="dropdown-content-checkbox">Satellite</a>

                </div>
            </div>
            <?php endif; ?>

            <div class="dropdown">
                <button class="dropbtn">Settings
                    <i class="fa fa-caret-down"></i>
                </button>
                <div class="dropdown-content" id="tdTopnavSettings">
                    <a href="javascript:void(0);" onclick="trackdirect.toggleImperialUnits(); $(this).toggleClass('dropdown-content-checkbox-active');" class="dropdown-content-checkbox" title="Switch to imperial units">Use imperial units</a>
                    <a href="javascript:void(0);" onclick="trackdirect.toggleStationaryPositions(); $(this).toggleClass('dropdown-content-checkbox-active');" class="dropdown-content-checkbox" title="Hide stations that is not moving">Hide not moving stations</a>

                    <!--
                    <a href="javascript:void(0);" onclick="trackdirect.toggleInternetPositions(); $(this).toggleClass('dropdown-content-checkbox-active');" class="dropdown-content-checkbox" title="Hide stations that sends packet using TCP/UDP">Hide Internet stations</a>
                    <a href="javascript:void(0);" onclick="trackdirect.toggleCwopPositions(); $(this).toggleClass('dropdown-content-checkbox-active');" class="dropdown-content-checkbox" title="Hide CWOP weather stations">Hide CWOP stations</a>
                    <a href="javascript:void(0);" onclick="trackdirect.toggleOgflymPositions(); $(this).toggleClass('dropdown-content-checkbox-active');" class="dropdown-content-checkbox" title="Hide model airplanes (OGFLYM)">Hide model airplanes (OGFLYM)</a>
                    <a href="javascript:void(0);" onclick="trackdirect.toggleUnknownPositions(); $(this).toggleClass('dropdown-content-checkbox-active');" class="dropdown-content-checkbox" title="Hide unknown aircrafts">Hide unknown aircrafts</a>
                    -->
                </div>
            </div>

            <div class="dropdown">
                <button class="dropbtn">Other
                    <i class="fa fa-caret-down"></i>
                </button>
                <div class="dropdown-content">

                    <a href="javascript:void(0);"
                        onclick="
                            $('#modal-station-search-iframe').attr('src', '/search.php?imperialUnits=' + (trackdirect.isImperialUnits() ? '1':'0'));
                            $('#modal-station-search').show();"
                        title="Search for a station/vehicle here!">
                        Station search
                    </a>

                    <a href="javascript:void(0);"
                        onclick="$('#modal-timetravel').show();"
                        title="Select date and time to show what the map looked like then">
                        Travel in time
                    </a>

                    <a class="triple-notselected" href="#" onclick="trackdirect.togglePHGCircles(); return false;" title="Show PHG cirlces, first click will show half PGH circles and second click will show full PHG circles.">
                        Toggle PHG circles
                    </a>
                </div>
            </div>

            <a href="javascript:void(0);"onclick="$('#modal-about').show();">
                About
            </a>

            <a href="javascript:void(0);" class="icon" onclick="toggleTopNav()">&#9776;</a>
        </div>

        <div id="map-container"></div>

        <div id="right-container">
            <div id="right-container-info">
                <div id="status-container"></div>
                <div id="cordinates-container"></div>
            </div>

            <div id="right-container-filtered">
                <div id="right-container-filtered-content"></div>
                <a href="#" onclick="trackdirect.filterOnStationId([]); return false;">reset</a>
            </div>

            <div id="right-container-timetravel">
                <div id="right-container-timetravel-content"></div>
                <a href="#" onclick="trackdirect.setTimeTravelTimestamp(0); $('#right-container-timetravel').hide(); return false;">reset</a>
            </div>
        </div>

        <div id="modal-station-info" class="modal">
            <div class="modal-long-content">
                <div class="modal-content-header">
                    <span class="modal-close" onclick="$('#modal-station-info').hide(); $('#modal-station-info-iframe').attr('src', 'about:blank');">&times;</span>
                    <span class="modal-title">Station information</h2>
                </div>
                <div class="modal-content-body">
                    <iframe id="modal-station-info-iframe"></iframe>
                </div>
            </div>
        </div>

        <div id="modal-timetravel" class="modal">
            <div class="modal-content">
                <div class="modal-content-header">
                    <span class="modal-close" onclick="$('#modal-timetravel').hide();">&times;</span>
                    <span class="modal-title">Travel in time</h2>
                </div>
                <div class="modal-content-body" style="margin: 0px 20px 20px 20px;">
                    <p>Select date and time to show map data for (enter time for your locale time zone). The regular time length select box can still be used to select how old data that should be shown (relative to selected date and time).</p>
                    <p>*Note that the heatmap will still based on data from the latest hour (not the selected date and time).</p>
                    <p>Date and time:</p>

                    <form id="timetravel-form">
                        <select id="timetravel-date" class="timetravel-select form-control">
                            <option value="0" selected>Select date</option>
                            <?php for($i=0; $i <= 10; $i++) : ?>
                                <?php $date = date('Y-m-d', strtotime("-$i days")); ?>
                                <option value="<?php echo $date; ?>"><?php echo $date; ?></option>
                            <?php endfor; ?>
                        </select>

                        <select id="timetravel-time" class="timetravel-select form-control">
                            <option value="0" selected>Select time</option>
                            <option value="00:00">00:00</option>
                            <option value="01:00">01:00</option>
                            <option value="02:00">02:00</option>
                            <option value="03:00">03:00</option>
                            <option value="04:00">04:00</option>
                            <option value="05:00">05:00</option>
                            <option value="06:00">06:00</option>
                            <option value="07:00">07:00</option>
                            <option value="08:00">08:00</option>
                            <option value="09:00">09:00</option>
                            <option value="10:00">10:00</option>
                            <option value="11:00">11:00</option>
                            <option value="12:00">12:00</option>
                            <option value="13:00">13:00</option>
                            <option value="14:00">14:00</option>
                            <option value="15:00">15:00</option>
                            <option value="16:00">16:00</option>
                            <option value="17:00">17:00</option>
                            <option value="18:00">18:00</option>
                            <option value="19:00">19:00</option>
                            <option value="20:00">20:00</option>
                            <option value="21:00">21:00</option>
                            <option value="22:00">22:00</option>
                            <option value="23:00">23:00</option>
                        </select>
                        <input type="submit"
                            value="Ok"
                            onclick="
                                if ($('#timetravel-date').val() != '0' && $('#timetravel-time').val() != '0') {
                                    trackdirect.setTimeLength(60, false);
                                    var ts = moment($('#timetravel-date').val() + ' ' + $('#timetravel-time').val(), 'YYYY-MM-DD HH:mm').unix();
                                    trackdirect.setTimeTravelTimestamp(ts);
                                    $('#right-container-timetravel-content').html('Time travel to ' + $('#timetravel-date').val() + ' ' + $('#timetravel-time').val());
                                    $('#right-container-timetravel').show();
                                } else {
                                    trackdirect.setTimeTravelTimestamp(0, true);
                                    $('#right-container-timetravel').hide();
                                }
                                $('#modal-timetravel').hide();
                                return false;"/>
                    </form>
                </div>
            </div>
        </div>

        <div id="modal-station-search" class="modal">
            <div class="modal-long-content">
                <div class="modal-content-header">
                    <span class="modal-close" onclick="$('#modal-station-search').hide();">&times;</span>
                    <span class="modal-title">Station search</h2>
                </div>
                <div class="modal-content-body">
                    <iframe id="modal-station-search-iframe" src=""></iframe>
                </div>
            </div>
        </div>

        <div id="modal-about" class="modal">
            <div class="modal-content">
                <div class="modal-content-header">
                    <span class="modal-close" onclick="$('#modal-about').hide();">&times;</span>
                    <span class="modal-title">About</h2>
                </div>
                <div class="modal-content-body" style="margin: 0px 20px 20px 20px;">
                    <p>
                        Maintainer of this website: <a href="mailto:no@name.com">No Name</a>
                    </p>

                    <h4>What is APRS?</h4>
                    <p>
                        APRS (Automatic Packet Reporting System) is a digital communications system that uses packet radio to send real time tactical information. The APRS network is used by ham radio operators all over the world.
                    </p>
                    <p>
                        Information shared over the APRS network is for example coordinates, altitude, speed, heading, text messages, alerts, announcements, bulletins and weather data.
                    </p>

                    <h4>APRS Track Direct</h4>
                    <p>
                        This website is based on the APRS Track Direct tools. Read more on <a href="https://github.com/qvarforth/trackdirect" target="_blank">GitHub</a>. But please note that the maintainer of APRS Track Direct has nothing to do with this website.
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>