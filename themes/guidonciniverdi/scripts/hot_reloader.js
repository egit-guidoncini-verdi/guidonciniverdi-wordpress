/*
 * Script provided by browsersync. It fetches and runs a remote script that receives updates 
 * and refreshes the page.
 */

(function() {
  try {
    var script = document.createElement('script');
    if ('async') {
      script.async = true;
    }
    script.src = 'http://HOST:3000/browser-sync/browser-sync-client.js?v=2.29.3'.replace("HOST", location.hostname);
    if (document.body) {
      document.body.appendChild(script);
    } else if (document.head) {
      document.head.appendChild(script);
    }
  } catch (e) {
    console.error("Browsersync: could not append script tag", e);
  }
})()
