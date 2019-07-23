<div class="map-route" id="{$token}_{$hash}">{$template}</div>

<script>
    new mapRoute.Builder({
        objects: {
            container: '#{$token}_{$hash}',
            map: '%container .map-route-canvas',
            chart: '%container .map-route-chart'
        },

        token: '{$token}',
        data: {$data|json_encode},
        settings: {$settings|json_encode}
    });
</script>