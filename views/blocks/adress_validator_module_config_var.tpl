[{if $module_var == ''}]
    <input type="file" accept="text/csv" name="adressFile">
[{else}]
    [{$smarty.block.parent}]
[{/if}]