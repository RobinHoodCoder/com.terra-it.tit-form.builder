<script>
        var root = '<?= WWW_ROOT ?>';

        // Voeg titform.js toe
        var titform = document.createElement('script');
        titform.setAttribute("type", "text/javascript");
        titform.setAttribute("src",
            root + "extensions/com.terra-it.tit-form/js/titform.js");

        (document.getElementsByTagName("body")[0] || document.documentElement).appendChild(titform);
</script>



