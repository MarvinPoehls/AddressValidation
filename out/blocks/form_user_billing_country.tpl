[{if get_class($oView) === 'OxidEsales\Eshop\Application\Controller\UserController'}]
    [{oxscript include=$oViewConf->getModuleUrl('addressvalidation', 'out/src/js/addressvalidation.js')}]
    <input type="hidden" id="baseUrl" value="[{$oViewConf->getSelfActionLink()}]">
    <input type="hidden" id="errorText" value="[{oxmultilang ident='ADDRESSVALIDATION_VALIDATION_ERROR'}]">
[{/if}]
[{$smarty.block.parent}]