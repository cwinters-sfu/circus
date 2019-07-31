(function () {

    function initialScale(img, $vp) {
        var scale = Math.min(1.0, $vp.width() / img.naturalWidth, $vp.height() / img.naturalHeight);
        return scale;
    }

    $(document).ready(function () {
        $('.panzoom').each(function () {
            var $parent = $(this);
            var img = $parent.children('img')[0];

            // Catherine temp: Adjust src on dev.
            $(img).attr('src', $(img).attr('src').replace('/circus/web', 'https://dhil.lib.sfu.ca/circus'));

            var scale = initialScale(img, $parent);
            var $pz = $(img).panzoom({
                maxScale: 2.0,
                rangeStep: 0.05,
                increment: scale / 2,
            });
            $pz.panzoom("zoom", scale, {silent: true});
            $pz.panzoom('pan', ($parent.width() - img.naturalWidth) / 2, ($parent.height() - img.naturalHeight) /  2);
            $pz.parent().on('mousewheel.focal', function (e) {
                e.preventDefault();
                var delta = e.delta || e.originalEvent.wheelDelta;
                var zoomOut = delta ? delta < 0 : e.originalEvent.deltaY > 0;
                $pz.panzoom('zoom', zoomOut, {
                    increment: 0.01,
                    animate: false,
                    focal: e
                });
            });
        });
    });
})();
