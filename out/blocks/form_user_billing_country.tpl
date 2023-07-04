[{if get_class($oView) === 'OxidEsales\Eshop\Application\Controller\UserController'}]
[{/if}]
[{if true}]
    [{oxscript include=$oViewConf->getModuleUrl('addressvalidation', 'out/src/js/addressvalidation.js')}]
    <input type="hidden" id="baseUrl" value="[{$oViewConf->getSelfActionLink()}]">
    <script>
        console.log("Hallo");
    </script>
[{/if}]

[{$smarty.block.parent}]