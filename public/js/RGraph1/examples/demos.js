//
// This file is (or ideally, will be) included by all demo files
//

// This function gets the source code from a demo page and presents
// it as a PRE section in the HTML page.
//
function showSource ()
{
    var tags   = document.getElementsByTagName('script');
    var footer = document.getElementById('footer');

    for (var i=0; i<tags.length; ++i) {
        if (tags[i].innerHTML.indexOf('new RGraph.') > 0 && tags[i].innerHTML.indexOf('.innerHTML.indexOf(')<= 0) {
            var pre = document.createElement('pre');
                pre.innerHTML = ('    &lt;script&gt;' + tags[i].innerHTML + '&lt;/script&gt;')
                    .replace(/^    /,'')
                    .replace(/\n    /g,'\n');
                pre.className = 'code';
                pre.style.backgroundColor = '#eee';
                pre.style.backgroundImage = 'linear-gradient(-45deg, rgba(255,255,255,0.5), transparent)';
                pre.style.lineHeight = '20px !important';
                pre.style.overflow = 'auto';
                pre.style.padding = '15px';
                pre.style.display = 'block';
            document.getElementById('dynamic-source-code').insertBefore(
                pre,
                null
            );
        }
    }
}