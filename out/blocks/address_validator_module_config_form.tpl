[{if $oModule->getInfo('id') === "addressvalidation"}]
    [{if $oView->getInvalidFileError()}]
        <div class="errorbox">[{oxmultilang ident='ADDRESSVALIDATION_FILE_ERROR'}]</div>
    [{/if}]
    [{if $oView->getInvalidHeadersError()}]
    <div class="errorbox">
        [{oxmultilang ident='ADDRESSVALIDATION_HEADERS_ERROR'}]
        <p>Deine Header: [{$oView->getFileHeaders()}] <br><br>Wie die Header aussehen sollen: [{$oView->getVerificationHeaders()}]</p>
    </div>
    [{/if}]
    [{if $oView->getUploadComplete()}]
    <div class="errorbox">[{oxmultilang ident='ADDRESSVALIDATION_UPLOAD_COMPLETE'}]</div>
    [{/if}]
    <dl>
        <dt class="edittext">
            [{oxmultilang ident='ADDRESSFILE'}]
        </dt>
        <dd class="editinput">
            <input type="file" name="addressFile">
        </dd>
    </dl>
    <div class="editinput">
        <input type="submit" value="Speichern">
    </div>
    <script>
        document.getElementById('moduleConfiguration').setAttribute('enctype', 'multipart/form-data')
    </script>
[{/if}]

[{$smarty.block.parent}]